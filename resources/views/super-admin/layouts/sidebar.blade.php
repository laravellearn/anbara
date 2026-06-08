<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ route('super-admin.dashboard') }}" class="app-brand-link">
            <span class="app-brand-text demo menu-text fw-bold ms-2">پنل مدیریت مرکزی</span>
        </a>
    </div>
    <div class="menu-inner-shadow"></div>
    <ul class="menu-inner py-1">

        {{-- ==================== داشبورد ==================== --}}
        <li class="menu-item {{ request()->routeIs('super-admin.dashboard') ? 'active' : '' }}">
            <a href="{{ route('super-admin.dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div>داشبورد مدیریت</div>
            </a>
        </li>

        {{-- ==================== عملیات اصلی ==================== --}}
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">عملیات</span>
        </li>

        <li class="menu-item {{ request()->routeIs('super-admin.tenants.*') ? 'active' : '' }}">
            <a href="{{ route('super-admin.tenants.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-buildings"></i>
                <div>سازمان‌ها (Tenant)</div>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('super-admin.plans.*') ? 'active' : '' }}">
            <a href="{{ route('super-admin.plans.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-package"></i>
                <div>تعرفه‌ها (پلن‌ها)</div>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('super-admin.subscriptions.*') ? 'active' : '' }}">
            <a href="{{ route('super-admin.subscriptions.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-credit-card"></i>
                <div>اشتراک‌های فعال</div>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('super-admin.payments.*') ? 'active' : '' }}">
            <a href="{{ route('super-admin.payments.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-dollar-circle"></i>
                <div>تراکنش‌ها و پرداخت‌ها</div>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('super-admin.licenses.*') ? 'active' : '' }}">
            <a href="{{ route('super-admin.licenses.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-check-shield"></i>
                <div>لایسنس سازمان‌ها</div>
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

        <li class="menu-item {{ request()->routeIs('super-admin.roles.*') ? 'active' : '' }}">
            <a href="{{ route('super-admin.roles.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-user-badge"></i>
                <div>نقش‌های سوپر ادمین</div>
            </a>
        </li>

        {{-- ==================== ارتباطات ==================== --}}
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">ارتباطات</span>
        </li>

        <li class="menu-item {{ request()->routeIs('super-admin.tickets.*') ? 'active' : '' }}">
            <a href="{{ route('super-admin.tickets.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-headphone"></i>
                <div>پشتیبانی (تیکت‌ها)</div>
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
                <div>تنظیمات کلی</div>
            </a>
        </li>

        <li class="menu-item">
            <form action="{{ route('super-admin.tools.sync-permissions') }}" method="POST" class="d-inline w-100">
                @csrf
                <button type="submit" class="menu-link border-0 bg-transparent w-100">
                    <i class="menu-icon tf-icons bx bx-sync"></i>
                    <div>همگام‌سازی دسترسی‌ها</div>
                </button>
            </form>
        </li>

        <li class="menu-item">
            <form action="{{ route('super-admin.tools.clear-cache') }}" method="POST">
                @csrf
                <button type="submit" class="menu-link border-0 bg-transparent w-100 text-danger">
                    <i class="menu-icon tf-icons bx bx-refresh"></i>
                    <div>پاک‌سازی کش</div>
                </button>
            </form>
        </li>

        <li class="menu-item">
            <a href="" class="menu-link">
                <i class="menu-icon tf-icons bx bx-user-circle"></i>
                <div>کاربران پنل</div>
            </a>
        </li>

        <li class="menu-item">
            <a href="" class="menu-link">
                <i class="menu-icon tf-icons bx bx-cloud-download"></i>
                <div>بکاپ‌گیری</div>
            </a>
        </li>

    </ul>
</aside>