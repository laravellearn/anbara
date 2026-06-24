<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme" style="overflow-y: auto; height: 90%;">
  <div class="app-brand demo">
    <a href="{{ route('dashboard') }}" class="app-brand-link">
      <span class="app-brand-logo demo">
        <img src="logo-light.png" alt="Anbara Logo" style="max-width: 100px; height: auto;"
          data-app-light-img="logo-light.png" data-app-dark-img="logo-dark.png">
      </span>
    </a>
    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
      <i class="bx menu-toggle-icon d-none d-xl-block fs-4 align-middle"></i>
      <i class="bx bx-x d-block d-xl-none bx-sm align-middle"></i>
    </a>
  </div>
  <div class="menu-inner-shadow"></div>

  <ul class="menu-inner py-1">

    {{-- ==================== داشبورد ==================== --}}
    <li class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
      <a href="{{ route('dashboard') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-home-circle"></i>
        <div>داشبورد</div>
      </a>
    </li>
    {{-- ==================== اطلاعات پایه ==================== --}}
    <li class="menu-header small text-uppercase"><span class="menu-header-text">اطلاعات پایه</span></li>

    {{-- ==================== کالاها و اقلام ==================== --}}

    @canany(['products.view', 'product-categories.view', 'measurement-units.view',
    'brands.view', 'product-attributes.view'])
    <li class="menu-item {{ request()->routeIs('warehouse.products.*', 'warehouse.categories.*', 'warehouse.measurement-units.*', 'warehouse.brands.*', 'warehouse.product-attributes.*','warehouse.product-types.*') ? 'active open' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-package"></i>
        <div>کالاها و اقلام</div>
      </a>
      <ul class="menu-sub">

        @can('products.view')
        <li class="menu-item {{ request()->routeIs('warehouse.products.index') ? 'active' : '' }}">
          <a href="{{ route('warehouse.products.index') }}" class="menu-link">
            <div>لیست کالاها</div>
          </a>
        </li>
        @endcan

        @can('product-categories.view')
        <li class="menu-item {{ request()->routeIs('warehouse.categories.*') ? 'active' : '' }}">
          <a href="{{ route('warehouse.categories.index') }}" class="menu-link">
            <div>دسته‌بندی‌ها</div>
          </a>
        </li>
        @endcan

        @can('measurement-units.view')
        <li class="menu-item {{ request()->routeIs('warehouse.measurement-units.*') ? 'active' : '' }}">
          <a href="{{ route('warehouse.measurement-units.index') }}" class="menu-link">
            <div>واحدهای اندازه‌گیری</div>
          </a>
        </li>
        @endcan

        @can('brands.view')
        <li class="menu-item {{ request()->routeIs('warehouse.brands.*') ? 'active' : '' }}">
          <a href="{{ route('warehouse.brands.index') }}" class="menu-link">
            <div>برندها</div>
          </a>
        </li>
        @endcan

        @can('product-attributes.view')
        <li class="menu-item {{ request()->routeIs('warehouse.product-attributes.*') ? 'active' : '' }}">
          <a href="{{ route('warehouse.product-attributes.index') }}" class="menu-link">
            <div>ویژگی‌ها و مشخصات فنی</div>
          </a>
        </li>
        @endcan

        @can('product-types.view')
        <li class="menu-item {{ request()->routeIs('warehouse.product-types.*') ? 'active' : '' }}">
          <a href="{{ route('warehouse.product-types.index') }}" class="menu-link">
            <div>نوع کالاها</div>
          </a>
        </li>
        @endcan

      </ul>
    </li>
    @endcanany


    {{-- ==================== انبار و مکان‌ها ==================== --}}
    @canAny(['warehouses.view', 'warehouse-locations.view'])
    <li class="menu-item {{ request()->routeIs('warehouse.warehouses.*', 'warehouse.warehouse-locations.*') ? 'active open' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-buildings"></i>
        <div>انبار و مکان‌ها</div>
      </a>
      <ul class="menu-sub">

        @can('warehouses.view')
        <li class="menu-item {{ request()->routeIs('warehouse.warehouses.index') ? 'active' : '' }}">
          <a href="{{ route('warehouse.warehouses.index') }}" class="menu-link">
            <div>انبارها</div>
          </a>
        </li>
        @endcan

        @can('warehouse-locations.view')
        <li class="menu-item {{ request()->routeIs('warehouse.warehouse-locations.*') ? 'active' : '' }}">
          <a href="{{ route('warehouse.warehouse-locations.index') }}" class="menu-link">
            <div>بخش‌ها، قفسه‌ها و موقعیت‌ها</div>
          </a>
        </li>
        @endcan

      </ul>
    </li>
    @endcanany


    {{-- ==================== طرف حساب‌ها ==================== --}}
    @canany(['contacts.view', 'organizational-units.view', 'employees.view','warehouse.cost-centers.view'])
    {{-- ==================== طرف‌های حساب ==================== --}}
    <li class="menu-header small text-uppercase"><span class="menu-header-text">طرف‌های حساب</span></li>

    <li class="menu-item {{ request()->routeIs('contacts.*', 'organizational-units.*', 'employees.*','warehouse.cost-centers.*') ? 'active open' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-user-pin"></i>
        <div>طرف‌های حساب</div>
      </a>
      <ul class="menu-sub">

        @can('contacts.view')
        <li class="menu-item {{ request()->routeIs('contacts.*') ? 'active' : '' }}">
          <a href="{{ route('contacts.index') }}" class="menu-link">
            <div>طرف تجاری‌ها</div>
          </a>
        </li>
        @endcan

        @can('organizational-units.view')
        <li class="menu-item {{ request()->routeIs('organizational-units.*') ? 'active' : '' }}">
          <a href="{{ route('organizational-units.index') }}" class="menu-link">
            <div>واحدهای سازمانی</div>
          </a>
        </li>
        @endcan

        @can('employees.view')
        <li class="menu-item {{ request()->routeIs('employees.*') ? 'active' : '' }}">
          <a href="{{ route('employees.index') }}" class="menu-link">
            <div>کارمندان</div>
          </a>
        </li>
        @endcan


        @can('cost-centers.view')
        <li class="menu-item {{ request()->routeIs('warehouse.cost-centers.*') ? 'active open' : '' }}">
          <a href="{{ route('warehouse.cost-centers.index') }}" class="menu-link">
            <div>مراکز هزینه</div>
          </a>
        </li>
        @endcan

      </ul>
    </li>
    @endcanany



    {{-- ==================== تدارکات و خرید ==================== --}}
    <li class="menu-header small text-uppercase"><span class="menu-header-text">تدارکات و خرید</span></li>

    <li class="menu-item {{ request()->routeIs('purchase-requests.*') ? 'active open' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-cart-add"></i>
        <div>درخواست خرید</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item"><a href="#" class="menu-link">
            <div>ثبت درخواست خرید</div>
          </a></li>
        <li class="menu-item"><a href="#" class="menu-link">
            <div>درخواست‌های من</div>
          </a></li>
        <li class="menu-item"><a href="#" class="menu-link">
            <div>بررسی و تأیید</div>
          </a></li>
        <li class="menu-item"><a href="#" class="menu-link">
            <div>پیگیری درخواست‌ها</div>
          </a></li>
      </ul>
    </li>

    <li class="menu-item {{ request()->routeIs('price-inquiries.*') ? 'active open' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-search-alt"></i>
        <div>استعلام قیمت</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item"><a href="#" class="menu-link">
            <div>ثبت استعلام جدید</div>
          </a></li>
        <li class="menu-item"><a href="#" class="menu-link">
            <div>استعلام‌های در حال انجام</div>
          </a></li>
        <li class="menu-item"><a href="#" class="menu-link">
            <div>مقایسه قیمت‌ها</div>
          </a></li>
      </ul>
    </li>

    <li class="menu-item {{ request()->routeIs('purchase-orders.*') ? 'active open' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-file"></i>
        <div>سفارش خرید</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item"><a href="#" class="menu-link">
            <div>ثبت سفارش خرید</div>
          </a></li>
        <li class="menu-item"><a href="#" class="menu-link">
            <div>سفارش‌های در حال اجرا</div>
          </a></li>
        <li class="menu-item"><a href="#" class="menu-link">
            <div>تأیید و صدور سفارش</div>
          </a></li>
        <li class="menu-item"><a href="#" class="menu-link">
            <div>پیگیری تحویل سفارش</div>
          </a></li>
      </ul>
    </li>

    <li class="menu-item {{ request()->routeIs('purchase-invoices.*') ? 'active open' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-receipt"></i>
        <div>فاکتور خرید</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item"><a href="#" class="menu-link">
            <div>ثبت فاکتور</div>
          </a></li>
        <li class="menu-item"><a href="#" class="menu-link">
            <div>لیست فاکتورها</div>
          </a></li>
        <li class="menu-item"><a href="#" class="menu-link">
            <div>تطبیق با سفارش خرید</div>
          </a></li>
      </ul>
    </li>

    <li class="menu-item {{ request()->routeIs('quality-control.*') ? 'active open' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-badge-check"></i>
        <div>کنترل کیفیت</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item"><a href="#" class="menu-link">
            <div>بررسی ورود کالا (QC)</div>
          </a></li>
        <li class="menu-item"><a href="#" class="menu-link">
            <div>گزارش‌های کنترل کیفیت</div>
          </a></li>
        <li class="menu-item"><a href="#" class="menu-link">
            <div>کالاهای رد شده</div>
          </a></li>
      </ul>
    </li>

    {{-- ==================== درخواست کالا (داخلی) ==================== --}}
    <li class="menu-header small text-uppercase"><span class="menu-header-text">درخواست کالا</span></li>

    <li class="menu-item {{ request()->routeIs('item-requests.*') ? 'active open' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-task"></i>
        <div>درخواست کالا از انبار</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item"><a href="#" class="menu-link">
            <div>ثبت درخواست جدید</div>
          </a></li>
        <li class="menu-item"><a href="#" class="menu-link">
            <div>درخواست‌های من</div>
          </a></li>
        <li class="menu-item"><a href="#" class="menu-link">
            <div>تأیید و بررسی</div>
          </a></li>
        <li class="menu-item"><a href="#" class="menu-link">
            <div>پیگیری درخواست‌ها</div>
          </a></li>
      </ul>
    </li>

    {{-- ==================== عملیات انبار ==================== --}}
    <li class="menu-header small text-uppercase"><span class="menu-header-text">عملیات انبار</span></li>

    <li class="menu-item {{ request()->routeIs('goods-receipt.*') ? 'active open' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-log-in-circle"></i>
        <div>ورود کالا</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item"><a href="#" class="menu-link">
            <div>رسید خرید</div>
          </a></li>
        <li class="menu-item"><a href="#" class="menu-link">
            <div>رسید امانی</div>
          </a></li>
        <li class="menu-item"><a href="#" class="menu-link">
            <div>رسید انتقالی</div>
          </a></li>
        <li class="menu-item"><a href="#" class="menu-link">
            <div>مرجوعی از مشتری</div>
          </a></li>
      </ul>
    </li>

    <li class="menu-item {{ request()->routeIs('goods-issue.*') ? 'active open' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-log-out-circle"></i>
        <div>خروج کالا</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item"><a href="#" class="menu-link">
            <div>حواله خروج</div>
          </a></li>
        <li class="menu-item"><a href="#" class="menu-link">
            <div>خروج مصرفی</div>
          </a></li>
        <li class="menu-item"><a href="#" class="menu-link">
            <div>خروج امانی</div>
          </a></li>
        <li class="menu-item"><a href="#" class="menu-link">
            <div>خروج انتقالی</div>
          </a></li>
        <li class="menu-item"><a href="#" class="menu-link">
            <div>مرجوعی به تأمین‌کننده</div>
          </a></li>
      </ul>
    </li>

    <li class="menu-item {{ request()->routeIs('transfers.*') ? 'active open' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-transfer-alt"></i>
        <div>انتقال کالا</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item"><a href="#" class="menu-link">
            <div>انتقال بین انبارها</div>
          </a></li>
        <li class="menu-item"><a href="#" class="menu-link">
            <div>انتقال داخلی (بین موقعیت‌ها)</div>
          </a></li>
      </ul>
    </li>

    <li class="menu-item {{ request()->routeIs('adjustments.*') ? 'active open' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-edit-alt"></i>
        <div>اصلاحات و تنظیمات</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item"><a href="#" class="menu-link">
            <div>اصلاح موجودی</div>
          </a></li>
        <li class="menu-item"><a href="#" class="menu-link">
            <div>ثبت ضایعات و خسارت</div>
          </a></li>
        <li class="menu-item"><a href="#" class="menu-link">
            <div>انبارگردانی (شمارش فیزیکی)</div>
          </a></li>
      </ul>
    </li>

    {{-- ==================== موجودی و کنترل ==================== --}}
    <li class="menu-header small text-uppercase"><span class="menu-header-text">موجودی و کنترل</span></li>

    <li class="menu-item {{ request()->routeIs('inventory.*') ? 'active open' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-layer"></i>
        <div>وضعیت موجودی</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item"><a href="#" class="menu-link">
            <div>موجودی لحظه‌ای</div>
          </a></li>
        <li class="menu-item"><a href="#" class="menu-link">
            <div>موجودی رزرو شده</div>
          </a></li>
        <li class="menu-item"><a href="#" class="menu-link">
            <div>موجودی در گردش</div>
          </a></li>
        <li class="menu-item"><a href="#" class="menu-link">
            <div>کالاهای راکد / کندگردان</div>
          </a></li>
        <li class="menu-item"><a href="#" class="menu-link">
            <div>کالاهای رو به انقضا</div>
          </a></li>
        <li class="menu-item"><a href="#" class="menu-link">
            <div>نقطه سفارش مجدد (Reorder Point)</div>
          </a></li>
      </ul>
    </li>

    <li class="menu-item {{ request()->routeIs('valuation.*') ? 'active open' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-dollar-circle"></i>
        <div>ارزش‌گذاری موجودی</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item"><a href="#" class="menu-link">
            <div>ارزش موجودی فعلی</div>
          </a></li>
        <li class="menu-item"><a href="#" class="menu-link">
            <div>روش ارزش‌گذاری (FIFO / میانگین)</div>
          </a></li>
      </ul>
    </li>

    {{-- ==================== دارایی ثابت ==================== --}}
    <li class="menu-header small text-uppercase"><span class="menu-header-text">دارایی ثابت</span></li>

    <li class="menu-item {{ request()->routeIs('assets.*') ? 'active open' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-briefcase"></i>
        <div>مدیریت دارایی</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item"><a href="#" class="menu-link">
            <div>لیست دارایی‌ها</div>
          </a></li>
        <li class="menu-item"><a href="#" class="menu-link">
            <div>تخصیص به پرسنل</div>
          </a></li>
        <li class="menu-item"><a href="#" class="menu-link">
            <div>عودت دارایی</div>
          </a></li>
        <li class="menu-item"><a href="#" class="menu-link">
            <div>تعمیر و نگهداری</div>
          </a></li>
        <li class="menu-item"><a href="#" class="menu-link">
            <div>اسقاط و خروج از خدمت</div>
          </a></li>
      </ul>
    </li>

    {{-- ==================== گزارشات و تحلیل ==================== --}}
    <li class="menu-header small text-uppercase"><span class="menu-header-text">گزارشات و تحلیل</span></li>

    <li class="menu-item {{ request()->routeIs('reports.warehouse.*') ? 'active open' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-bar-chart-square"></i>
        <div>گزارشات انبار</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item"><a href="#" class="menu-link">
            <div>کاردکس کالا</div>
          </a></li>
        <li class="menu-item"><a href="#" class="menu-link">
            <div>گردش ورود و خروج</div>
          </a></li>
        <li class="menu-item"><a href="#" class="menu-link">
            <div>موجودی انبارها</div>
          </a></li>
        <li class="menu-item"><a href="#" class="menu-link">
            <div>مغایرت انبار</div>
          </a></li>
        <li class="menu-item"><a href="#" class="menu-link">
            <div>ریز تراکنش‌ها</div>
          </a></li>
      </ul>
    </li>

    <li class="menu-item {{ request()->routeIs('reports.management.*') ? 'active open' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-trending-up"></i>
        <div>گزارشات مدیریتی</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item"><a href="#" class="menu-link">
            <div>تحلیل ABC</div>
          </a></li>
        <li class="menu-item"><a href="#" class="menu-link">
            <div>کالاهای پرمصرف / کم‌مصرف</div>
          </a></li>
        <li class="menu-item"><a href="#" class="menu-link">
            <div>پیش‌بینی تقاضا</div>
          </a></li>
        <li class="menu-item"><a href="#" class="menu-link">
            <div>عملکرد انبار</div>
          </a></li>
        <li class="menu-item"><a href="#" class="menu-link">
            <div>عملکرد تدارکات</div>
          </a></li>
        <li class="menu-item"><a href="#" class="menu-link">
            <div>عملکرد تأمین‌کنندگان</div>
          </a></li>
      </ul>
    </li>

    {{-- ==================== کاربران و دسترسی ==================== --}}
    @canany(['users.view', 'roles.view', 'permissions.view'])
    <li class="menu-header small text-uppercase"><span class="menu-header-text">کاربران و دسترسی</span></li>
    @endcanany

    @can('access', 'users.view')
    <li class="menu-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
      <a href="{{ route('users.index') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-group"></i>
        <div>کاربران سیستم</div>
      </a>
    </li>
    @endcan

    @can('access', 'roles.view')
    <li class="menu-item {{ request()->routeIs('roles.*') ? 'active' : '' }}">
      <a href="{{ route('roles.index') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-shield"></i>
        <div>نقش‌ها و مجوزها</div>
      </a>
    </li>
    @endcan

    {{-- ==================== تنظیمات ==================== --}}
    <li class="menu-header small text-uppercase"><span class="menu-header-text">تنظیمات</span></li>

    <li class="menu-item {{ request()->routeIs('settings.*','billing.license.*') ? 'active open' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-cog"></i>
        <div>تنظیمات سیستم</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item"><a href="#" class="menu-link">
            <div>اطلاعات سازمان</div>
          </a></li>
        <li class="menu-item"><a href="#" class="menu-link">
            <div>تنظیمات انبار</div>
          </a></li>
        <li class="menu-item"><a href="#" class="menu-link">
            <div>گردش کار و تأییدیه‌ها</div>
          </a></li>
        <li class="menu-item"><a href="#" class="menu-link">
            <div>شماره‌گذاری اسناد</div>
          </a></li>
        <li class="menu-item"><a href="#" class="menu-link">
            <div>اعلان‌ها (ایمیل / پیامک)</div>
          </a></li>
        <li class="menu-item"><a href="#" class="menu-link">
            <div>روش ارزش‌گذاری پیش‌فرض</div>
          </a></li>
        @can('license.view')
        <li class="menu-item"><a href="{{ route('billing.license') }}" class="menu-link">
            <div>وضعیت لایسنس</div>
          </a></li>
        @endcan
      </ul>
    </li>

    <li class="menu-item {{ request()->routeIs('logs.*', 'activity-logs.*') ? 'active open' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-history"></i>
        <div>لاگ و نظارت</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item {{ request()->routeIs('activity-logs.*') ? 'active' : '' }}">
          <a href="{{ route('activity-logs.index') }}" class="menu-link">
            <div>فعالیت کاربران</div>
          </a>
        </li>
        <li class="menu-item"><a href="#" class="menu-link">
            <div>تاریخچه عملیات انبار</div>
          </a></li>
        <li class="menu-item"><a href="#" class="menu-link">
            <div>لاگ سیستم</div>
          </a></li>
      </ul>
    </li>

  </ul>
</aside>