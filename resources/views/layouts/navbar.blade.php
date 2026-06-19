<nav class="layout-navbar navbar navbar-expand-xl align-items-center bg-navbar-theme" id="layout-navbar">
    <div class="container-fluid">
        <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
            <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                <i class="bx bx-menu bx-sm"></i>
            </a>
        </div>

        <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
            <!-- Search -->
            <div class="navbar-nav align-items-center">
                <div class="nav-item navbar-search-wrapper mb-0">
                    <a class="nav-item nav-link search-toggler px-0" href="javascript:void(0);">
                        <i class="bx bx-search-alt bx-sm"></i>
                        <span class="d-none d-md-inline-block text-muted">جستجو <span class="d-inline-block"
                                dir="ltr">(Ctrl+/)</span></span>
                    </a>
                </div>
            </div>
            <!-- /Search -->
            @if(session('impersonator_id'))
            <div class="alert alert-warning mb-0 text-center">
                شما با حساب کاربری {{ auth()->user()->name }} وارد شده‌اید.
                <form action="{{ route('super-admin.impersonate.destroy') }}" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger ms-2">بازگشت به حساب اصلی</button>
                </form>
            </div>
            @endif
            <ul class="navbar-nav flex-row align-items-center ms-auto">
                {{-- تاریخ امروز --}}
                <li class="nav-item d-flex align-items-center me-3">
                    <span class="text-muted small">
                        <i class="bx bx-calendar me-1"></i>
                        {{ \Verta::now()->format('Y/m/d') }}
                    </span>
                </li>

                {{-- وضعیت اشتراک (فقط برای مدیر سازمان) --}}
                @if (auth()->user() && auth()->user()->isTenantAdmin())
                @php
                $tenant = app(\App\Services\TenantManager::class)->getTenant();
                $planService = app(\App\Services\PlanService::class);
                $activeSubscription = $planService->getActiveSubscription();
                $currentPlan = $activeSubscription ? $activeSubscription->plan : null;

                // آیا پلن قابل ارتقا وجود دارد؟
                $hasUpgradable = false;
                if ($currentPlan && $currentPlan->slug !== 'enterprise') {
                $hasUpgradable = \App\Models\Plan::where('is_active', true)
                ->whereRaw('monthly_price > ?', [$currentPlan->monthly_price])
                ->exists();
                }
                @endphp
                <li class="nav-item d-flex align-items-center me-3">
                    @if ($activeSubscription && $currentPlan)
                    @php
                    $remainingDays = 0;
                    $remainingHours = 0;
                    if ($activeSubscription->ends_at) {
                    $now = \Verta::now();
                    $end = \Verta::instance($activeSubscription->ends_at);
                    $remainingDays = $now->diffDays($end, false);
                    $remainingHours = $now->toCarbon()->diffInHours($end->toCarbon(), false);
                    }
                    @endphp
                    <span class="badge bg-label-success me-1">
                        <i class="bx bx-package me-1"></i>
                        {{ $currentPlan->name }}
                        @if ($activeSubscription->ends_at)
                        @if ($remainingDays > 0)
                        | {{ $remainingDays }} روز
                        @elseif($remainingDays == 0)
                        | {{ max(0, $remainingHours) }} ساعت
                        @else
                        | منقضی
                        @endif
                        @else
                        | نامحدود
                        @endif
                    </span>

                    {{-- دکمه ارتقا (اگر پلن فعلی enterprise نباشد و پلن گران‌تر وجود داشته باشد) --}}
                    @if ($hasUpgradable)
                    <a href="{{ route('billing.plans') }}"
                        class="btn btn-xs btn-outline-primary rounded-pill ms-1">
                        <i class="bx bx-up-arrow-alt"></i> ارتقا
                    </a>
                    @endif
                    @else
                    <span class="badge bg-label-danger me-1">
                        <i class="bx bx-x-circle me-1"></i>
                        بدون اشتراک
                    </span>
                    <a href="{{ route('billing.plans') }}"
                        class="btn btn-xs btn-outline-success rounded-pill ms-1">
                        <i class="bx bx-cart"></i> خرید اشتراک
                    </a>
                    @endif
                </li>
                @endif


                @php
                $currentFiscalYear = app(\App\Services\TenantManager::class)->getFiscalYear();
                $allFiscalYears = $currentFiscalYear
                ? \App\Models\FiscalYear::where('tenant_id', $currentFiscalYear->tenant_id)->get()
                : collect();
                @endphp
                <li class="nav-item dropdown me-2">
                    <a class="nav-link dropdown-toggle hide-arrow d-flex align-items-center" href="javascript:void(0);"
                        data-bs-toggle="dropdown">
                        <i class="bx bx-calendar-check me-1"></i>
                        <span class="text-muted small me-1">سال مالی:</span>
                        <span class="fw-medium">{{ $currentFiscalYear->name ?? '---' }}</span>
                        <i class="bx bx-chevron-down ms-1"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        @foreach ($allFiscalYears as $fy)
                        <li>
                            <form action="{{ route('fiscal-year.switch') }}" method="POST">
                                @csrf
                                <input type="hidden" name="fiscal_year_id" value="{{ $fy->id }}">
                                <button type="submit"
                                    class="dropdown-item {{ $currentFiscalYear?->id == $fy->id ? 'active' : '' }}">
                                    <i
                                        class="bx bx-check me-1 {{ $currentFiscalYear?->id == $fy->id ? '' : 'd-none' }}"></i>
                                    {{ $fy->name }}
                                </button>
                            </form>
                        </li>
                        @endforeach
                        {{-- فقط مدیر سازمان گزینهٔ مدیریت را می‌بیند --}}
                        @if (auth()->user()->isTenantAdmin())
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a href="{{ route('fiscal-years.index') }}" class="dropdown-item">
                                <i class="bx bx-cog me-1"></i> مدیریت سال‌های مالی
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>


                @if (auth()->user() && app(\App\Services\TenantManager::class)->getTenantId())
                @php
                $currentCompany = app(\App\Services\TenantManager::class)->getCompany();
                $tenantCompanies = auth()->user()->companies;
                @endphp
                <li class="nav-item dropdown me-2">
                    <a class="nav-link dropdown-toggle hide-arrow d-flex align-items-center"
                        href="javascript:void(0);" data-bs-toggle="dropdown">
                        <i class="bx bx-buildings me-1"></i>
                        <span class="text-muted small me-1">سازمان:</span>
                        <span class="fw-medium">{{ $currentCompany->name ?? '---' }}</span>
                        <i class="bx bx-chevron-down ms-1"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        @foreach ($tenantCompanies as $company)
                        <li>
                            <form action="{{ route('company.switch') }}" method="POST">
                                @csrf
                                <input type="hidden" name="company_id" value="{{ $company->id }}">
                                <button type="submit"
                                    class="dropdown-item {{ $currentCompany?->id == $company->id ? 'active' : '' }}">
                                    <i
                                        class="bx bx-check me-1 {{ $currentCompany?->id == $company->id ? '' : 'd-none' }}"></i>
                                    {{ $company->name }}
                                </button>
                            </form>
                        </li>
                        @endforeach
                        {{-- فقط مدیر سازمان گزینهٔ مدیریت را می‌بیند --}}
                        @if (auth()->user()->isTenantAdmin())
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a href="{{ route('companies.index') }}" class="dropdown-item">
                                <i class="bx bx-cog me-1"></i> مدیریت سازمان‌ها
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif


                <!-- Style Switcher -->
                <li class="nav-item me-2 me-xl-0">
                    <a class="nav-link style-switcher-toggle hide-arrow" href="javascript:void(0);">
                        <i class="bx bx-sm"></i>
                    </a>
                </li>
                <!--/ Style Switcher -->

                <!-- Quick links  -->
                <li class="nav-item dropdown-shortcuts navbar-dropdown dropdown me-2 me-xl-0">
                    <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown"
                        data-bs-auto-close="outside" aria-expanded="false">
                        <i class="bx bx-grid-alt bx-sm"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end py-0">
                        <div class="dropdown-menu-header border-bottom">
                            <div class="dropdown-header d-flex align-items-center py-3">
                                <h5 class="text-body mb-0 me-auto secondary-font">میانبرها</h5>
                                <a href="javascript:void(0)" class="dropdown-shortcuts-add text-body"
                                    data-bs-toggle="tooltip" data-bs-placement="top" title="افزودن میانبر"><i
                                        class="bx bx-sm bx-plus-circle"></i></a>
                            </div>
                        </div>
                        <div class="dropdown-shortcuts-list scrollable-container">
                            <div class="row row-bordered overflow-visible g-0">
                                <div class="dropdown-shortcuts-item col">
                                    <span class="dropdown-shortcuts-icon bg-label-secondary rounded-circle mb-2">
                                        <i class="bx bx-calendar fs-4"></i>
                                    </span>
                                    <a href="app-calendar.html" class="stretched-link">تقویم</a>
                                    <small class="text-muted mb-0">قرارهای ملاقات</small>
                                </div>
                                <div class="dropdown-shortcuts-item col">
                                    <span class="dropdown-shortcuts-icon bg-label-secondary rounded-circle mb-2">
                                        <i class="bx bx-food-menu fs-4"></i>
                                    </span>
                                    <a href="app-invoice-list.html" class="stretched-link">برنامه صورتحساب</a>
                                    <small class="text-muted mb-0">مدیریت حساب‌ها</small>
                                </div>
                            </div>
                            <div class="row row-bordered overflow-visible g-0">
                                <div class="dropdown-shortcuts-item col">
                                    <span class="dropdown-shortcuts-icon bg-label-secondary rounded-circle mb-2">
                                        <i class="bx bx-user fs-4"></i>
                                    </span>
                                    <a href="app-user-list.html" class="stretched-link">برنامه کاربر</a>
                                    <small class="text-muted mb-0">مدیریت کاربران</small>
                                </div>
                                <div class="dropdown-shortcuts-item col">
                                    <span class="dropdown-shortcuts-icon bg-label-secondary rounded-circle mb-2">
                                        <i class="bx bx-check-shield fs-4"></i>
                                    </span>
                                    <a href="app-access-roles.html" class="stretched-link">مدیریت نقش‌‌ها</a>
                                    <small class="text-muted mb-0">مجوزها</small>
                                </div>
                            </div>
                            <div class="row row-bordered overflow-visible g-0">
                                <div class="dropdown-shortcuts-item col">
                                    <span class="dropdown-shortcuts-icon bg-label-secondary rounded-circle mb-2">
                                        <i class="bx bx-pie-chart-alt-2 fs-4"></i>
                                    </span>
                                    <a href="index.html" class="stretched-link">داشبورد</a>
                                    <small class="text-muted mb-0">پروفایل کاربر</small>
                                </div>
                                <div class="dropdown-shortcuts-item col">
                                    <span class="dropdown-shortcuts-icon bg-label-secondary rounded-circle mb-2">
                                        <i class="bx bx-cog fs-4"></i>
                                    </span>
                                    <a href="pages-account-settings-account.html" class="stretched-link">تنظیمات</a>
                                    <small class="text-muted mb-0">تنظیمات حساب</small>
                                </div>
                            </div>
                            <div class="row row-bordered overflow-visible g-0">
                                <div class="dropdown-shortcuts-item col">
                                    <span class="dropdown-shortcuts-icon bg-label-secondary rounded-circle mb-2">
                                        <i class="bx bx-help-circle fs-4"></i>
                                    </span>
                                    <a href="pages-help-center-landing.html" class="stretched-link">مرکز راهنمایی</a>
                                    <small class="text-muted mb-0">سوالات متداول و مقالات</small>
                                </div>
                                <div class="dropdown-shortcuts-item col">
                                    <span class="dropdown-shortcuts-icon bg-label-secondary rounded-circle mb-2">
                                        <i class="bx bx-window-open fs-4"></i>
                                    </span>
                                    <a href="modal-examples.html" class="stretched-link">مودال‌ها</a>
                                    <small class="text-muted mb-0">پاپ‌آپ‌های کاربردی</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
                <!-- Quick links -->

                <!-- Notification -->
                <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-3 me-xl-2">
                    <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown"
                        data-bs-auto-close="outside" aria-expanded="false">
                        <i class="bx bx-bell bx-sm"></i>
                        <span class="badge bg-danger rounded-pill badge-notifications">5</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end py-0">
                        <li class="dropdown-menu-header border-bottom">
                            <div class="dropdown-header d-flex align-items-center py-3">
                                <h5 class="text-body mb-0 me-auto secondary-font">اعلان‌ها</h5>
                                <a href="javascript:void(0)" class="dropdown-notifications-all text-body"
                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Mark all as read"><i
                                        class="bx fs-4 bx-envelope-open"></i></a>
                            </div>
                        </li>
                        <li class="dropdown-notifications-list scrollable-container">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item list-group-item-action dropdown-notifications-item">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="avatar">
                                                <img src="{{ $userLogin->avatar }}" alt
                                                    class="w-px-40 h-auto rounded-circle">
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">تبریک می‌گوییم کلارک</h6>
                                            <p class="mb-1">شما نشان فروشنده برتر ماه را برنده شدید</p>
                                            <small class="text-muted">1 ساعت قبل</small>
                                        </div>
                                        <div class="flex-shrink-0 dropdown-notifications-actions">
                                            <a href="javascript:void(0)" class="dropdown-notifications-read"><span
                                                    class="badge badge-dot"></span></a>
                                            <a href="javascript:void(0)" class="dropdown-notifications-archive"><span
                                                    class="bx bx-x"></span></a>
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item list-group-item-action dropdown-notifications-item">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="avatar">
                                                <span class="avatar-initial rounded-circle bg-label-danger">اک</span>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">دیوید بکهام</h6>
                                            <p class="mb-1">درخواست شما را قبول کرد.</p>
                                            <small class="text-muted">12 ساعت قبل</small>
                                        </div>
                                        <div class="flex-shrink-0 dropdown-notifications-actions">
                                            <a href="javascript:void(0)" class="dropdown-notifications-read"><span
                                                    class="badge badge-dot"></span></a>
                                            <a href="javascript:void(0)" class="dropdown-notifications-archive"><span
                                                    class="bx bx-x"></span></a>
                                        </div>
                                    </div>
                                </li>
                                <li
                                    class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="avatar">
                                                <img src="../../assets/img/avatars/2.png" alt
                                                    class="w-px-40 h-auto rounded-circle">
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">پیام جدید</h6>
                                            <p class="mb-1">شما پیام جدید از ناتالی دارید</p>
                                            <small class="text-muted">1 ساعت قبل</small>
                                        </div>
                                        <div class="flex-shrink-0 dropdown-notifications-actions">
                                            <a href="javascript:void(0)" class="dropdown-notifications-read"><span
                                                    class="badge badge-dot"></span></a>
                                            <a href="javascript:void(0)" class="dropdown-notifications-archive"><span
                                                    class="bx bx-x"></span></a>
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item list-group-item-action dropdown-notifications-item">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="avatar">
                                                <span class="avatar-initial rounded-circle bg-label-success"><i
                                                        class="bx bx-cart"></i></span>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">هورا! شما سفارش جدید دارید</h6>
                                            <p class="mb-1">شرکت گوگل یک سفارش جدید ثبت کرد</p>
                                            <small class="text-muted">1 روز قبل</small>
                                        </div>
                                        <div class="flex-shrink-0 dropdown-notifications-actions">
                                            <a href="javascript:void(0)" class="dropdown-notifications-read"><span
                                                    class="badge badge-dot"></span></a>
                                            <a href="javascript:void(0)" class="dropdown-notifications-archive"><span
                                                    class="bx bx-x"></span></a>
                                        </div>
                                    </div>
                                </li>
                                <li
                                    class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="avatar">
                                                <img src="../../assets/img/avatars/9.png" alt
                                                    class="w-px-40 h-auto rounded-circle">
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">برنامه مورد تایید قرار گرفت</h6>
                                            <p class="mb-1">برنامه پروژه مدیریت شما پذیرفته شد.</p>
                                            <small class="text-muted">2 روز قبل</small>
                                        </div>
                                        <div class="flex-shrink-0 dropdown-notifications-actions">
                                            <a href="javascript:void(0)" class="dropdown-notifications-read"><span
                                                    class="badge badge-dot"></span></a>
                                            <a href="javascript:void(0)" class="dropdown-notifications-archive"><span
                                                    class="bx bx-x"></span></a>
                                        </div>
                                    </div>
                                </li>
                                <li
                                    class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="avatar">
                                                <span class="avatar-initial rounded-circle bg-label-success"><i
                                                        class="bx bx-pie-chart-alt"></i></span>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">گزارش ماهانه ایجاد شد</h6>
                                            <p class="mb-1">گزارش ماهانه ماه خرداد ایجاد شد</p>
                                            <small class="text-muted">3 روز قبل</small>
                                        </div>
                                        <div class="flex-shrink-0 dropdown-notifications-actions">
                                            <a href="javascript:void(0)" class="dropdown-notifications-read"><span
                                                    class="badge badge-dot"></span></a>
                                            <a href="javascript:void(0)" class="dropdown-notifications-archive"><span
                                                    class="bx bx-x"></span></a>
                                        </div>
                                    </div>
                                </li>
                                <li
                                    class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="avatar">
                                                <img src="../../assets/img/avatars/5.png" alt
                                                    class="w-px-40 h-auto rounded-circle">
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">ارسال درخواست ارتباط</h6>
                                            <p class="mb-1">پیتر یک درخواست ارتباط برای شما ارسال کرد</p>
                                            <small class="text-muted">4 روز قبل</small>
                                        </div>
                                        <div class="flex-shrink-0 dropdown-notifications-actions">
                                            <a href="javascript:void(0)" class="dropdown-notifications-read"><span
                                                    class="badge badge-dot"></span></a>
                                            <a href="javascript:void(0)" class="dropdown-notifications-archive"><span
                                                    class="bx bx-x"></span></a>
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item list-group-item-action dropdown-notifications-item">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="avatar">
                                                <img src="../../assets/img/avatars/6.png" alt
                                                    class="w-px-40 h-auto rounded-circle">
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">پیام جدید از جین</h6>
                                            <p class="mb-1">شما پیام جدید از سمت جین دارید</p>
                                            <small class="text-muted">5 روز قبل</small>
                                        </div>
                                        <div class="flex-shrink-0 dropdown-notifications-actions">
                                            <a href="javascript:void(0)" class="dropdown-notifications-read"><span
                                                    class="badge badge-dot"></span></a>
                                            <a href="javascript:void(0)" class="dropdown-notifications-archive"><span
                                                    class="bx bx-x"></span></a>
                                        </div>
                                    </div>
                                </li>
                                <li
                                    class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="avatar">
                                                <span class="avatar-initial rounded-circle bg-label-warning"><i
                                                        class="bx bx-error"></i></span>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">میزان مصرف CPU بالاست</h6>
                                            <p class="mb-1">میران مصرف CPU در حال حاضر 88.63% است</p>
                                            <small class="text-muted">5 روز قبل</small>
                                        </div>
                                        <div class="flex-shrink-0 dropdown-notifications-actions">
                                            <a href="javascript:void(0)" class="dropdown-notifications-read"><span
                                                    class="badge badge-dot"></span></a>
                                            <a href="javascript:void(0)" class="dropdown-notifications-archive"><span
                                                    class="bx bx-x"></span></a>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </li>
                        <li class="dropdown-menu-footer border-top">
                            <a href="javascript:void(0);" class="dropdown-item d-flex justify-content-center p-3">
                                مشاهده همه اعلان‌ها
                            </a>
                        </li>
                    </ul>
                </li>
                <!--/ Notification -->

                <!-- User -->
                <li class="nav-item navbar-dropdown dropdown-user dropdown">
                    <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);"
                        data-bs-toggle="dropdown">
                        <div class="avatar avatar-online">
                            <img src="{{ $userLogin->avatar }}" alt class="rounded-circle">
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="avatar avatar-online">
                                            <img src="{{ $userLogin->avatar }}" alt class="rounded-circle">
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <span class="fw-semibold d-block">{{ $userLogin->name }}</span>
                                        @php
                                        $roleName = $currentRoleName ?? 'کاربر';

                                        $badgeClass = match ($roleName) {
                                        'مدیر کل سامانه' => 'bg-label-danger',
                                        'مدیر سازمان' => 'bg-label-warning',
                                        'مدیر انبار' => 'bg-label-info',
                                        'انباردار' => 'bg-label-primary',
                                        'سازمان انتخاب نشده' => 'bg-label-secondary',
                                        default => 'bg-label-secondary',
                                        };
                                        @endphp

                                        <span class="badge {{ $badgeClass }} rounded-pill"
                                            style="font-size: 11px;padding: 5px;border-radius: 10px !important;">
                                            {{ $roleName }}
                                        </span>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li>
                            <div class="dropdown-divider"></div>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                <i class="bx bx-user me-2"></i>
                                <span class="align-middle">پروفایل من</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="pages-account-settings-account.html">
                                <i class="bx bx-cog me-2"></i>
                                <span class="align-middle">تنظیمات من</span>
                            </a>
                        </li>
                        {{-- <li>
              <a class="dropdown-item" href="pages-account-settings-billing.html">
                <span class="d-flex align-items-center align-middle">
                  <i class="flex-shrink-0 bx bx-credit-card me-2"></i>
                  <span class="flex-grow-1 align-middle">صورتحساب</span>
                  <span class="flex-shrink-0 badge badge-center rounded-pill bg-danger w-px-20 h-px-20">4</span>
                </span>
              </a>
            </li> --}}
                        <li>
                            <div class="dropdown-divider"></div>
                        </li>
                        <li>
                            <a class="dropdown-item" href="pages-help-center-landing.html">
                                <i class="bx bx-support me-2"></i>
                                <span class="align-middle">پشتیبانی</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('billing.license') }}">
                                <i class="bx bx-help-circle me-2"></i>
                                <span class="align-middle">مدیریت لایسنس</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('billing.plans') }}">
                                <i class="bx bx-dollar me-2"></i>
                                <span class="align-middle">خرید یا ارتقا اشتراک</span>
                            </a>
                        </li>
                        <li>
                            <div class="dropdown-divider"></div>
                        </li>
                        <li>
                            <a class="dropdown-item" href="/logout">
                                <i class="bx bx-power-off me-2"></i>
                                <span class="align-middle">خروج</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <!--/ User -->
            </ul>
        </div>

        <!-- Search Small Screens -->
        <div class="navbar-search-wrapper search-input-wrapper d-none">
            <input type="text" class="form-control search-input container-fluid border-0" placeholder="جستجو ..."
                aria-label="Search...">
            <i class="bx bx-x bx-sm search-toggler cursor-pointer"></i>
        </div>
    </div>
</nav>