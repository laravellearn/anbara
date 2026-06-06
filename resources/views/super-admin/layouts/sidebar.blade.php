<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
  <div class="app-brand demo">
      <a href="{{ route('super-admin.dashboard') }}" class="app-brand-link">
          <span class="app-brand-text demo menu-text fw-bold ms-2">پنل مدیریت مرکزی</span>
      </a>
  </div>
  <div class="menu-inner-shadow"></div>
  <ul class="menu-inner py-1">
      <li class="menu-item {{ request()->routeIs('super-admin.dashboard') ? 'active' : '' }}">
          <a href="{{ route('super-admin.dashboard') }}" class="menu-link">
              <i class="menu-icon tf-icons bx bx-home-circle"></i>
              <div>داشبورد مدیریت</div>
          </a>
      </li>

      <li class="menu-header small text-uppercase">
          <span class="menu-header-text">مدیریت مرکزی</span>
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

      <li class="menu-item {{ request()->routeIs('super-admin.activity-logs.*') ? 'active' : '' }}">
          <a href="{{ route('super-admin.activity-logs.index') }}" class="menu-link">
              <i class="menu-icon tf-icons bx bx-history"></i>
              <div>لاگ‌های سیستمی</div>
          </a>
      </li>
  </ul>
</aside>