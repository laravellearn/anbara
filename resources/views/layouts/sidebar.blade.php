<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
  <div class="app-brand demo">
    <a href="{{ route('dashboard') }}" class="app-brand-link">
      <span class="app-brand-logo demo">
        <img src="logo-light.png" alt="Anbara Logo" style="max-width: 100px;height:auto;"
          data-app-light-img="logo-light.png" data-app-dark-img="logo-dark.png">
      </span>
    </a>

    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
      <i class="bx menu-toggle-icon d-none d-xl-block fs-4 align-middle"></i>
      <i class="bx bx-x d-block d-xl-none bx-sm align-middle"></i>
    </a>
  </div>

  <div class="menu-divider mt-0"></div>
  <div class="menu-inner-shadow"></div>

  <ul class="menu-inner py-1">

    {{-- ==================== داشبوردها ==================== --}}
    <li class="menu-item active open">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-home-circle"></i>
        <div>داشبوردها</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item active">
          <a href="index.html" class="menu-link">
            <div>داشبورد اصلی</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="dashboards-ecommerce.html" class="menu-link">
            <div>شاخص‌های کلیدی</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="index.html" class="menu-link">
            <div>هشدارها</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="dashboards-ecommerce.html" class="menu-link">
            <div>فعالیت‌های اخیر</div>
          </a>
        </li>
      </ul>
    </li>

    {{-- ==================== کالا و اقلام ==================== --}}
    <li class="menu-header small text-uppercase"><span class="menu-header-text">کالا و اقلام</span></li>

    {{-- اطلاعات پایه کالا --}}
    <li class="menu-item">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-package"></i>
        <div>اطلاعات پایه کالا</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item">
          <a href="app-invoice-list.html" class="menu-link">
            <div>کالاها</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="app-invoice-preview.html" class="menu-link">
            <div>دسته‌بندی کالا</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="app-invoice-edit.html" class="menu-link">
            <div>واحدهای اندازه‌گیری</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="app-invoice-edit.html" class="menu-link">
            <div>کالاهای جایگزین</div>
          </a>
        </li>
      </ul>
    </li>

    {{-- مشخصات کالا --}}
    <li class="menu-item">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-barcode-reader"></i>
        <div>مشخصات کالا</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item">
          <a href="app-user-list.html" class="menu-link">
            <div>بارکدها</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="javascript:void(0);" class="menu-link">
            <div>ویژگی‌های کالا</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="javascript:void(0);" class="menu-link">
            <div>فایل‌ها و مستندات</div>
          </a>
        </li>
      </ul>
    </li>

    {{-- ==================== انبارها ==================== --}}
    <li class="menu-header small text-uppercase"><span class="menu-header-text">انبارها</span></li>

    {{-- ساختار انبار --}}
    <li class="menu-item">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-buildings"></i>
        <div>ساختار انبار</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item">
          <a href="app-invoice-edit.html" class="menu-link">
            <div>انبارها</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="app-invoice-list.html" class="menu-link">
            <div>بخش‌ها و قفسه‌ها</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="app-invoice-preview.html" class="menu-link">
            <div>موقعیت‌های نگهداری</div>
          </a>
        </li>
      </ul>
    </li>

    {{-- مدیریت ظرفیت --}}
    <li class="menu-item">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-pie-chart"></i>
        <div>مدیریت ظرفیت</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item">
          <a href="app-user-list.html" class="menu-link">
            <div>وضعیت اشغال</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="javascript:void(0);" class="menu-link">
            <div>ظرفیت انبار</div>
          </a>
        </li>
      </ul>
    </li>

    {{-- ==================== عملیات انبار ==================== --}}
    <li class="menu-header small text-uppercase"><span class="menu-header-text">عملیات انبار</span></li>

    {{-- ورود کالا --}}
    <li class="menu-item">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-log-in-circle"></i>
        <div>ورود کالا</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item">
          <a href="app-invoice-edit.html" class="menu-link">
            <div>رسید انبار</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="app-invoice-list.html" class="menu-link">
            <div>ورود خرید</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="app-invoice-preview.html" class="menu-link">
            <div>ورود امانی</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="app-invoice-preview.html" class="menu-link">
            <div>ورود انتقالی</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="app-invoice-preview.html" class="menu-link">
            <div>مرجوعی کالا</div>
          </a>
        </li>
      </ul>
    </li>

    {{-- خروج کالا --}}
    <li class="menu-item">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-log-out-circle"></i>
        <div>خروج کالا</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item">
          <a href="app-user-list.html" class="menu-link">
            <div>حواله خروج</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="javascript:void(0);" class="menu-link">
            <div>خروج مصرفی</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="javascript:void(0);" class="menu-link">
            <div>خروج امانی</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="javascript:void(0);" class="menu-link">
            <div>خروج انتقالی</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="javascript:void(0);" class="menu-link">
            <div>مرجوعی کالا</div>
          </a>
        </li>
      </ul>
    </li>

    {{-- انتقال --}}
    <li class="menu-item">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-transfer-alt"></i>
        <div>انتقال</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item">
          <a href="app-user-list.html" class="menu-link">
            <div>انتقال بین انبارها</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="javascript:void(0);" class="menu-link">
            <div>انتقال داخلی</div>
          </a>
        </li>
      </ul>
    </li>

    {{-- اصلاحات --}}
    <li class="menu-item">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-edit-alt"></i>
        <div>اصلاحات</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item">
          <a href="app-user-list.html" class="menu-link">
            <div>اصلاح موجودی</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="javascript:void(0);" class="menu-link">
            <div>ثبت ضایعات</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="javascript:void(0);" class="menu-link">
            <div>انبارگردانی (شمارش)</div>
          </a>
        </li>
      </ul>
    </li>

    {{-- ==================== درخواست‌ها ==================== --}}
    <li class="menu-header small text-uppercase"><span class="menu-header-text">درخواست‌ها</span></li>

    {{-- درخواست کالا --}}
    <li class="menu-item">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-task"></i>
        <div>درخواست کالا</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item">
          <a href="app-invoice-edit.html" class="menu-link">
            <div>درخواست جدید</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="app-invoice-list.html" class="menu-link">
            <div>تأیید درخواست‌ها</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="app-invoice-preview.html" class="menu-link">
            <div>پیگیری درخواست‌ها</div>
          </a>
        </li>
      </ul>
    </li>

    {{-- درخواست انتقال --}}
    <li class="menu-item">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-move-horizontal"></i>
        <div>درخواست انتقال</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item">
          <a href="app-user-list.html" class="menu-link">
            <div>درخواست انتقال بین انبارها</div>
          </a>
        </li>
      </ul>
    </li>

    {{-- ==================== موجودی ==================== --}}
    <li class="menu-header small text-uppercase"><span class="menu-header-text">موجودی</span></li>

    {{-- کنترل موجودی --}}
    <li class="menu-item">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-layer"></i>
        <div>کنترل موجودی</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item">
          <a href="app-invoice-edit.html" class="menu-link">
            <div>موجودی لحظه‌ای</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="app-invoice-list.html" class="menu-link">
            <div>موجودی رزرو شده</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="app-invoice-preview.html" class="menu-link">
            <div>موجودی در گردش</div>
          </a>
        </li>
      </ul>
    </li>

    {{-- هشدارها --}}
    <li class="menu-item">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-bell"></i>
        <div>هشدارها</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item">
          <a href="app-user-list.html" class="menu-link">
            <div>حداقل موجودی</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="app-user-list.html" class="menu-link">
            <div>نقطه سفارش</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="app-user-list.html" class="menu-link">
            <div>کالاهای راکد</div>
          </a>
        </li>
      </ul>
    </li>

    {{-- ==================== دارایی‌ها ==================== --}}
    <li class="menu-header small text-uppercase"><span class="menu-header-text">دارایی‌ها</span></li>

    {{-- دارایی‌ها --}}
    <li class="menu-item">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-briefcase"></i>
        <div>دارایی‌ها</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item">
          <a href="app-invoice-edit.html" class="menu-link">
            <div>لیست دارایی</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="app-invoice-list.html" class="menu-link">
            <div>ثبت دارایی</div>
          </a>
        </li>
      </ul>
    </li>

    {{-- تخصیص --}}
    <li class="menu-item">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-user-check"></i>
        <div>تخصیص</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item">
          <a href="app-user-list.html" class="menu-link">
            <div>تخصیص دارایی</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="app-user-list.html" class="menu-link">
            <div>عودت دارایی</div>
          </a>
        </li>
      </ul>
    </li>

    {{-- تعمیرات --}}
    <li class="menu-item">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-wrench"></i>
        <div>تعمیرات</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item">
          <a href="app-user-list.html" class="menu-link">
            <div>درخواست تعمیر</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="app-user-list.html" class="menu-link">
            <div>سوابق تعمیر</div>
          </a>
        </li>
      </ul>
    </li>

    {{-- اسقاط --}}
    <li class="menu-item">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-trash"></i>
        <div>اسقاط</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item">
          <a href="app-user-list.html" class="menu-link">
            <div>دارایی‌های اسقاطی</div>
          </a>
        </li>
      </ul>
    </li>

    {{-- ==================== طرف حساب‌ها ==================== --}}
    <li class="menu-header small text-uppercase"><span class="menu-header-text">طرف حساب‌ها</span></li>

    {{-- تأمین‌کنندگان --}}
    <li class="menu-item">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-user-pin"></i>
        <div>تأمین‌کنندگان</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item">
          <a href="app-invoice-edit.html" class="menu-link">
            <div>لیست تأمین‌کنندگان</div>
          </a>
        </li>
      </ul>
    </li>

    {{-- واحدهای سازمانی --}}
    <li class="menu-item">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-buildings"></i>
        <div>واحدهای سازمانی</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item">
          <a href="app-user-list.html" class="menu-link">
            <div>دپارتمان‌ها</div>
          </a>
        </li>
      </ul>
    </li>

    {{-- تحویل‌گیرندگان --}}
    <li class="menu-item">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-user-voice"></i>
        <div>تحویل‌گیرندگان</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item">
          <a href="app-user-list.html" class="menu-link">
            <div>پرسنل دریافت‌کننده</div>
          </a>
        </li>
      </ul>
    </li>

    {{-- ==================== گزارشات ==================== --}}
    <li class="menu-header small text-uppercase"><span class="menu-header-text">گزارشات</span></li>

    {{-- گزارش کالا --}}
    <li class="menu-item">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-bar-chart-square"></i>
        <div>گزارش کالا</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item">
          <a href="app-invoice-edit.html" class="menu-link">
            <div>موجودی</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="app-invoice-edit.html" class="menu-link">
            <div>گردش کالا</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="app-invoice-edit.html" class="menu-link">
            <div>کاردکس انبار</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="app-invoice-edit.html" class="menu-link">
            <div>ورود و خروج</div>
          </a>
        </li>
      </ul>
    </li>

    {{-- گردش انبار --}}
    <li class="menu-item">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-line-chart"></i>
        <div>گردش انبار</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item">
          <a href="app-user-list.html" class="menu-link">
            <div>عملکرد انبارها</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="app-user-list.html" class="menu-link">
            <div>موجودی انبارها</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="app-user-list.html" class="menu-link">
            <div>گزارش مغایرت</div>
          </a>
        </li>
      </ul>
    </li>

    {{-- گزارش دارایی --}}
    <li class="menu-item">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-briefcase-alt-2"></i>
        <div>گزارش دارایی</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item">
          <a href="app-user-list.html" class="menu-link">
            <div>تخصیص</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="app-user-list.html" class="menu-link">
            <div>تعمیرات</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="app-user-list.html" class="menu-link">
            <div>اسقاط</div>
          </a>
        </li>
      </ul>
    </li>

    {{-- گزارش مدیریتی --}}
    <li class="menu-item">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-trending-up"></i>
        <div>گزارش مدیریتی</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item">
          <a href="app-user-list.html" class="menu-link">
            <div>ارزش موجودی</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="app-user-list.html" class="menu-link">
            <div>کالاهای پرمصرف</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="app-user-list.html" class="menu-link">
            <div>کالاهای کم‌مصرف</div>
          </a>
        </li>
      </ul>
    </li>

    {{-- ==================== کاربران و دسترسی ==================== --}}
    <li class="menu-header small text-uppercase"><span class="menu-header-text">کاربران و دسترسی</span></li>

    {{-- کاربران --}}
    @can('access', 'users.view')
    <li class="menu-item">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-group"></i>
        <div>کاربران</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item">
          <a href="{{ route('users.index') }}" class="menu-link">
            <div>کاربران سیستم</div>
          </a>
        </li>
      </ul>
    </li>
    @endcan

    {{-- نقش‌ها --}}
    <li class="menu-item">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-shield"></i>
        <div>نقش‌ها</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item">
          <a href="app-user-list.html" class="menu-link">
            <div>نقش‌ها</div>
          </a>
        </li>
      </ul>
    </li>

    {{-- مجوزها --}}
    <li class="menu-item">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-key"></i>
        <div>مجوزها</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item">
          <a href="app-user-list.html" class="menu-link">
            <div>سطوح دسترسی</div>
          </a>
        </li>
      </ul>
    </li>

    {{-- ==================== تنظیمات ==================== --}}
    <li class="menu-header small text-uppercase"><span class="menu-header-text">تنظیمات</span></li>

    {{-- تنظیمات اصلی --}}
    <li class="menu-item">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-cog"></i>
        <div>تنظیمات اصلی</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item">
          <a href="app-invoice-edit.html" class="menu-link">
            <div>اشتراک‌ها</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="app-invoice-edit.html" class="menu-link">
            <div>تنظیمات سامانه</div>
          </a>
        </li>
      </ul>
    </li>

    {{-- پایه --}}
    <li class="menu-item">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-slider"></i>
        <div>پایه</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item">
          <a href="app-invoice-edit.html" class="menu-link">
            <div>تنظیمات سازمان</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="app-invoice-edit.html" class="menu-link">
            <div>تنظیمات انبار</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="app-invoice-edit.html" class="menu-link">
            <div>تنظیمات گردش کار</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="app-invoice-edit.html" class="menu-link">
            <div>تنظیمات نوع (تیپ) کالا</div>
          </a>
        </li>
      </ul>
    </li>

    {{-- شماره‌گذاری --}}
    <li class="menu-item">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-hash"></i>
        <div>شماره‌گذاری</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item">
          <a href="app-user-list.html" class="menu-link">
            <div>شماره اسناد</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="app-user-list.html" class="menu-link">
            <div>قالب کد کالا</div>
          </a>
        </li>
      </ul>
    </li>

    {{-- اعلان‌ها --}}
    <li class="menu-item">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-envelope"></i>
        <div>اعلان‌ها</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item">
          <a href="app-user-list.html" class="menu-link">
            <div>پیامک</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="app-user-list.html" class="menu-link">
            <div>ایمیل</div>
          </a>
        </li>
      </ul>
    </li>

    {{-- لاگ‌ها --}}
    <li class="menu-item">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-history"></i>
        <div>لاگ‌ها</div>
      </a>
      <ul class="menu-sub">
        @can('access', 'activity_logs.view')
        <li class="menu-item">
          <a href="{{ route('activity-logs.index') }}" class="menu-link">
            <div>فعالیت کاربران</div>
          </a>
        </li>
        @endcan
        <li class="menu-item">
          <a href="app-user-list.html" class="menu-link">
            <div>عملیات سیستم</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="app-user-list.html" class="menu-link">
            <div>تاریخچه موجودی</div>
          </a>
        </li>
      </ul>
    </li>

    {{-- ==================== متفرقه ==================== --}}
    <li class="menu-header small text-uppercase"><span class="menu-header-text">متفرقه</span></li>

    <li class="menu-item">
      <a href="https://www.rtl-theme.com" target="_blank" class="menu-link">
        <i class="menu-icon tf-icons bx bx-shield-quarter"></i>
        <div>لایسنس</div>
      </a>
    </li>
    <li class="menu-item">
      <a href="https://www.rtl-theme.com" target="_blank" class="menu-link">
        <i class="menu-icon tf-icons bx bx-cloud-download"></i>
        <div>عملیات بکاپ‌گیری</div>
      </a>
    </li>
    <li class="menu-item">
      <a href="https://www.rtl-theme.com" target="_blank" class="menu-link">
        <i class="menu-icon tf-icons bx bx-support"></i>
        <div>پشتیبانی</div>
      </a>
    </li>
    <li class="menu-item">
      <a href="https://v3dboy.ir/previews/html/frest/documentation" target="_blank" class="menu-link">
        <i class="menu-icon tf-icons bx bx-book"></i>
        <div>مستندات</div>
      </a>
    </li>

    {{-- ==================== مدیریت مرکزی (فقط سوپر ادمین) ==================== --}}
    @if(auth()->user() && auth()->user()->isSuperAdmin())
    <li class="menu-header small text-uppercase">
      <span class="menu-header-text">مدیریت مرکزی سامانه</span>
    </li>

    <li class="menu-item">
      <a href="" class="menu-link">
        <i class="menu-icon tf-icons bx bx-buildings"></i>
        <div>سازمان‌های ثبت‌نامی</div>
      </a>
    </li>

    <li class="menu-item">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-package"></i>
        <div>مدیریت اشتراک</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item">
          <a href="" class="menu-link">
            <div>تعرفه‌ها (پلن‌ها)</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="" class="menu-link">
            <div>اشتراک‌های فعال</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="" class="menu-link">
            <div>درخواست‌های ارتقا</div>
          </a>
        </li>
      </ul>
    </li>

    {{-- اشتراک و لایسنس (فقط مدیر سازمان) --}}
    @if(auth()->user() && auth()->user()->isTenantAdmin())
    <li class="menu-header small text-uppercase"><span class="menu-header-text">مالی و اشتراک</span></li>
    <li class="menu-item">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-credit-card"></i>
        <div>اشتراک و لایسنس</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item">
          <a href="{{ route('billing.license') }}" class="menu-link">
            <div>وضعیت لایسنس</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="{{ route('billing.plans') }}" class="menu-link">
            <div>تعرفه‌ها و ارتقا</div>
          </a>
        </li>
        @can('access', 'subscriptions.history')
        <li class="menu-item">
          <a href="{{ route('billing.history') }}" class="menu-link">
            <div>تاریخچه اشتراک ها</div>
          </a>
        </li>
        @endcan
      </ul>
    </li>
    @endif


    <li class="menu-item">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-cog"></i>
        <div>ابزارهای مدیریت</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item">
          <a href="" class="menu-link">
            <div>تنظیمات کلی سامانه</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="" class="menu-link">
            <div>درگاه‌های پرداخت</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="" class="menu-link">
            <div>تنظیمات پیامک و ایمیل</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="" class="menu-link">
            <div>لاگ‌های سیستمی</div>
          </a>
        </li>
      </ul>
    </li>
    @endif

  </ul>
</aside>