<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ route('super-admin.dashboard') }}" class="app-brand-link">
            <span class="app-brand-logo demo">
                <img src="/logo-light.png" alt="Anbara" style="max-width:100px;height:auto;"
                    data-app-light-img="logo-light.png" data-app-dark-img="logo-dark.png">
            </span>
        </a>
        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="bx menu-toggle-icon d-none d-xl-block fs-4 align-middle"></i>
            <i class="bx bx-x d-block d-xl-none bx-sm align-middle"></i>
        </a>
    </div>

    {{-- نشانگر جعل هویت --}}
    @if(session('impersonator_id'))
    <div class="alert alert-warning mx-3 my-2 py-2 text-center small">
        <i class="bx bx-user-check me-1"></i> حالت Impersonation
        <form action="{{ route('super-admin.impersonate.destroy') }}" method="POST" class="d-inline">
            @csrf @method('DELETE')
            <button class="btn btn-xs btn-link text-danger p-0 ms-2 small">خروج</button>
        </form>
    </div>
    @endif

    <div class="menu-inner-shadow"></div>
    <ul class="menu-inner py-1">

        {{-- ==================== داشبورد ==================== --}}
        <li class="menu-item {{ request()->routeIs('super-admin.dashboard') ? 'active' : '' }}">
            <a href="{{ route('super-admin.dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div>داشبورد</div>
            </a>
        </li>

        {{-- ==================== عملیات اصلی ==================== --}}
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">مدیریت سازمان‌ها</span>
        </li>

        <li class="menu-item {{ request()->routeIs('super-admin.tenants.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-buildings"></i>
                <div>سازمان‌ها</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('super-admin.tenants.index') ? 'active' : '' }}">
                    <a href="{{ route('super-admin.tenants.index') }}" class="menu-link">
                        <div>لیست سازمان‌ها</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('super-admin.tenants.create') ? 'active' : '' }}">
                    <a href="{{ route('super-admin.tenants.create') }}" class="menu-link">
                        <div>سازمان جدید</div>
                    </a>
                </li>
            </ul>
        </li>

        <li class="menu-item {{ request()->routeIs('super-admin.users.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-group"></i>
                <div>کاربران</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('super-admin.users.index') ? 'active' : '' }}">
                    <a href="{{ route('super-admin.users.index') }}" class="menu-link">
                        <div>لیست کاربران</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('super-admin.users.create') ? 'active' : '' }}">
                    <a href="{{ route('super-admin.users.create') }}" class="menu-link">
                        <div>کاربر جدید</div>
                    </a>
                </li>
            </ul>
        </li>

        {{-- ==================== صورت‌حساب ==================== --}}
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">صورت‌حساب</span>
        </li>

        <li class="menu-item {{ request()->routeIs('super-admin.plans.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-package"></i>
                <div>تعرفه‌ها</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('super-admin.plans.index') ? 'active' : '' }}">
                    <a href="{{ route('super-admin.plans.index') }}" class="menu-link">
                        <div>لیست پلن‌ها</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('super-admin.plans.create') ? 'active' : '' }}">
                    <a href="{{ route('super-admin.plans.create') }}" class="menu-link">
                        <div>پلن جدید</div>
                    </a>
                </li>
            </ul>
        </li>

        <li class="menu-item {{ request()->routeIs('super-admin.subscriptions.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-credit-card"></i>
                <div>اشتراک‌ها</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('super-admin.subscriptions.index') ? 'active' : '' }}">
                    <a href="{{ route('super-admin.subscriptions.index') }}" class="menu-link">
                        <div>لیست اشتراک‌ها</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('super-admin.subscriptions.create') ? 'active' : '' }}">
                    <a href="{{ route('super-admin.subscriptions.create') }}" class="menu-link">
                        <div>اشتراک جدید</div>
                    </a>
                </li>
            </ul>
        </li>

        <li class="menu-item {{ request()->routeIs('super-admin.payments.*') ? 'active' : '' }}">
            <a href="{{ route('super-admin.payments.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-dollar-circle"></i>
                <div>تراکنش‌ها</div>
            </a>
        </li>

        {{-- ==================== امنیت و نظارت ==================== --}}
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">امنیت و نظارت</span>
        </li>

        <li class="menu-item {{ request()->routeIs('super-admin.activity-logs.*') ? 'active' : '' }}">
            <a href="{{ route('super-admin.activity-logs.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-history"></i>
                <div>لاگ‌های سیستمی</div>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('super-admin.roles.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-user-badge"></i>
                <div>نقش‌ها و دسترسی‌ها</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('super-admin.roles.index') ? 'active' : '' }}">
                    <a href="{{ route('super-admin.roles.index') }}" class="menu-link">
                        <div>لیست نقش‌ها</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('super-admin.roles.create') ? 'active' : '' }}">
                    <a href="{{ route('super-admin.roles.create') }}" class="menu-link">
                        <div>نقش جدید</div>
                    </a>
                </li>
            </ul>
        </li>

        {{-- ==================== ارتباطات ==================== --}}
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">ارتباطات</span>
        </li>

        <li class="menu-item {{ request()->routeIs('super-admin.tickets.*') ? 'active' : '' }}">
            <a href="{{ route('super-admin.tickets.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-headphone"></i>
                <div>تیکت‌های پشتیبانی</div>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('super-admin.notifications.*') ? 'active' : '' }}">
            <a href="{{ route('super-admin.notifications.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-megaphone"></i>
                <div>اطلاع‌رسانی</div>
            </a>
        </li>

        {{-- ==================== سیستم ==================== --}}
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">سیستم</span>
        </li>

        <li class="menu-item {{ request()->routeIs('super-admin.settings.*') ? 'active' : '' }}">
            <a href="{{ route('super-admin.settings.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-cog"></i>
                <div>تنظیمات سیستم</div>
            </a>
        </li>

        <li class="menu-item">
            <a href="{{ route('dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-store"></i>
                <div>بازگشت به پنل انبار</div>
            </a>
        </li>

        <li class="menu-item">
            <a href="{{ route('logout') }}" class="menu-link text-danger"
               onclick="event.preventDefault(); document.getElementById('sa-logout').submit();">
                <i class="menu-icon tf-icons bx bx-power-off text-danger"></i>
                <div>خروج از سیستم</div>
            </a>
            <form id="sa-logout" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
        </li>

    </ul>
</aside>