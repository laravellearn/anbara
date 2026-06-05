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
            <!-- Dashboards -->
            <li class="menu-item active open">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Dashboards">داشبوردها</div>
              </a>
              <ul class="menu-sub">
              <li class="menu-item active">
                  <a href="index.html" class="menu-link">
                    <div data-i18n="Analytics">داشبورد اصلی</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="dashboards-ecommerce.html" class="menu-link">
                    <div data-i18n="eCommerce">شاخص های کلیدی</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="index.html" class="menu-link">
                    <div data-i18n="Analytics">هشدار ها</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="dashboards-ecommerce.html" class="menu-link">
                    <div data-i18n="eCommerce">فعالیت های اخیر</div>
                  </a>
                </li>
              </ul>
            </li>

            <!-- Apps & Pages -->
            <li class="menu-header small text-uppercase"><span class="menu-header-text">کالا و اقلام</span></li>
            <li class="menu-item">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-food-menu"></i>
                <div data-i18n="Invoice">اطلاعات پایه کالا</div>
              </a>
              <ul class="menu-sub">
                <li class="menu-item">
                  <a href="app-invoice-list.html" class="menu-link">
                    <div data-i18n="List">کالا ها</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="app-invoice-preview.html" class="menu-link">
                    <div data-i18n="Preview">دسته بندی کالا</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="app-invoice-edit.html" class="menu-link">
                    <div data-i18n="Edit">واحد های اندازه گیری</div>
                  </a>
                </li>
              </ul>
            </li>
            <li class="menu-item">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-user"></i>
                <div data-i18n="Users">مشخصات کالا</div>
              </a>
              <ul class="menu-sub">
                <li class="menu-item">
                  <a href="app-user-list.html" class="menu-link">
                    <div data-i18n="List">بارکد ها</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="javascript:void(0);" class="menu-link">
                    <div data-i18n="View">ویژگی های کالا</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="javascript:void(0);" class="menu-link">
                    <div data-i18n="View">فایل ها و مستندات</div>
                  </a>
                </li>
              </ul>
            </li>



            <li class="menu-header small text-uppercase"><span class="menu-header-text">انبار ها</span></li>
            <li class="menu-item">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-food-menu"></i>
                <div data-i18n="Invoice">ساختار انبار</div>
              </a>
              <ul class="menu-sub">
              <li class="menu-item">
                  <a href="app-invoice-edit.html" class="menu-link">
                    <div data-i18n="Edit">انبار ها</div>
                  </a>
                </li>

                <li class="menu-item">
                  <a href="app-invoice-list.html" class="menu-link">
                    <div data-i18n="List">بخش ها و قفسه ها</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="app-invoice-preview.html" class="menu-link">
                    <div data-i18n="Preview">موقعیت های نگهداری</div>
                  </a>
                </li>
              </ul>
            </li>
            <li class="menu-item">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-user"></i>
                <div data-i18n="Users">مدیریت ظرفیت</div>
              </a>
              <ul class="menu-sub">
                <li class="menu-item">
                  <a href="app-user-list.html" class="menu-link">
                    <div data-i18n="List">وضعیت اشغال</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="javascript:void(0);" class="menu-link">
                    <div data-i18n="View">ظرفیت انبار</div>
                  </a>
                </li>
              </ul>
            </li>



            <li class="menu-header small text-uppercase"><span class="menu-header-text">عملیات انبار</span></li>
            <li class="menu-item">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-food-menu"></i>
                <div data-i18n="Invoice">ورود کالا</div>
              </a>
              <ul class="menu-sub">
              <li class="menu-item">
                  <a href="app-invoice-edit.html" class="menu-link">
                    <div data-i18n="Edit">رسید انبار</div>
                  </a>
                </li>

                <li class="menu-item">
                  <a href="app-invoice-list.html" class="menu-link">
                    <div data-i18n="List">ورود خرید</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="app-invoice-preview.html" class="menu-link">
                    <div data-i18n="Preview">ورود امانی</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="app-invoice-preview.html" class="menu-link">
                    <div data-i18n="Preview">ورود انتقالی</div>
                  </a>
                </li>
              </ul>
            </li>
            <li class="menu-item">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-user"></i>
                <div data-i18n="Users">خروج کالا</div>
              </a>
              <ul class="menu-sub">
                <li class="menu-item">
                  <a href="app-user-list.html" class="menu-link">
                    <div data-i18n="List">حواله خروج</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="javascript:void(0);" class="menu-link">
                    <div data-i18n="View">خروج مصرفی</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="javascript:void(0);" class="menu-link">
                    <div data-i18n="View">خروج امانی</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="javascript:void(0);" class="menu-link">
                    <div data-i18n="View">خروج انتقالی</div>
                  </a>
                </li>
              </ul>
            </li>
            <li class="menu-item">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-user"></i>
                <div data-i18n="Users">انتقال</div>
              </a>
              <ul class="menu-sub">
                <li class="menu-item">
                  <a href="app-user-list.html" class="menu-link">
                    <div data-i18n="List">انتقال بین انبار ها</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="javascript:void(0);" class="menu-link">
                    <div data-i18n="View">انتقال داخلی</div>
                  </a>
                </li>
              </ul>
            </li>
            <li class="menu-item">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-user"></i>
                <div data-i18n="Users">اصلاحات</div>
              </a>
              <ul class="menu-sub">
                <li class="menu-item">
                  <a href="app-user-list.html" class="menu-link">
                    <div data-i18n="List">اصلاح موجودی</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="javascript:void(0);" class="menu-link">
                    <div data-i18n="View">ثبت ضایعات</div>
                  </a>
                </li>
              </ul>
            </li>





            <li class="menu-header small text-uppercase"><span class="menu-header-text">درخواست ها</span></li>
            <li class="menu-item">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-food-menu"></i>
                <div data-i18n="Invoice">درخواست کالا</div>
              </a>
              <ul class="menu-sub">
              <li class="menu-item">
                  <a href="app-invoice-edit.html" class="menu-link">
                    <div data-i18n="Edit">درخواست جدید</div>
                  </a>
                </li>

                <li class="menu-item">
                  <a href="app-invoice-list.html" class="menu-link">
                    <div data-i18n="List">تائید درخواست ها</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="app-invoice-preview.html" class="menu-link">
                    <div data-i18n="Preview">پیگیری درخواست ها</div>
                  </a>
                </li>
              </ul>
            </li>
            <li class="menu-item">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-user"></i>
                <div data-i18n="Users">درخواست انتقال</div>
              </a>
              <ul class="menu-sub">
                <li class="menu-item">
                  <a href="app-user-list.html" class="menu-link">
                    <div data-i18n="List">درخواست انتقال بین انبار ها</div>
                  </a>
                </li>
              </ul>
            </li>




            <li class="menu-header small text-uppercase"><span class="menu-header-text">موجودی</span></li>
            <li class="menu-item">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-food-menu"></i>
                <div data-i18n="Invoice">کنترل موجودی</div>
              </a>
              <ul class="menu-sub">
              <li class="menu-item">
                  <a href="app-invoice-edit.html" class="menu-link">
                    <div data-i18n="Edit">موجودی لحظه ای</div>
                  </a>
                </li>

                <li class="menu-item">
                  <a href="app-invoice-list.html" class="menu-link">
                    <div data-i18n="List">موجودی رزرو شده</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="app-invoice-preview.html" class="menu-link">
                    <div data-i18n="Preview">موجودی در گردش</div>
                  </a>
                </li>
              </ul>
            </li>
            <li class="menu-item">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-user"></i>
                <div data-i18n="Users">هشدار ها</div>
              </a>
              <ul class="menu-sub">
              <li class="menu-item">
                  <a href="app-user-list.html" class="menu-link">
                    <div data-i18n="List">حداقل موجودی</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="app-user-list.html" class="menu-link">
                    <div data-i18n="List">نقطه سفارش</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="app-user-list.html" class="menu-link">
                    <div data-i18n="List">کالاهای راکد</div>
                  </a>
                </li>
              </ul>
            </li>






            <li class="menu-header small text-uppercase"><span class="menu-header-text">دارایی ها</span></li>
            <li class="menu-item">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-food-menu"></i>
                <div data-i18n="Invoice">دارایی ها</div>
              </a>
              <ul class="menu-sub">
              <li class="menu-item">
                  <a href="app-invoice-edit.html" class="menu-link">
                    <div data-i18n="Edit">لیست دارایی</div>
                  </a>
                </li>

                <li class="menu-item">
                  <a href="app-invoice-list.html" class="menu-link">
                    <div data-i18n="List">ثبت دارایی</div>
                  </a>
                </li>
              </ul>
            </li>
            <li class="menu-item">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-user"></i>
                <div data-i18n="Users">تخصیص</div>
              </a>
              <ul class="menu-sub">
              <li class="menu-item">
                  <a href="app-user-list.html" class="menu-link">
                    <div data-i18n="List">تخصیص دارایی</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="app-user-list.html" class="menu-link">
                    <div data-i18n="List">عودت دارایی</div>
                  </a>
                </li>
              </ul>
            </li>
            <li class="menu-item">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-user"></i>
                <div data-i18n="Users">تعمیرات</div>
              </a>
              <ul class="menu-sub">
              <li class="menu-item">
                  <a href="app-user-list.html" class="menu-link">
                    <div data-i18n="List">درخواست تعمیر</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="app-user-list.html" class="menu-link">
                    <div data-i18n="List">سوابق تعمیر</div>
                  </a>
                </li>
              </ul>
            </li>
            <li class="menu-item">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-user"></i>
                <div data-i18n="Users">اسقاط</div>
              </a>
              <ul class="menu-sub">
              <li class="menu-item">
                  <a href="app-user-list.html" class="menu-link">
                    <div data-i18n="List">دارایی های اسقاطی</div>
                  </a>
                </li>
              </ul>
            </li>







            <li class="menu-header small text-uppercase"><span class="menu-header-text">طرف حساب ها</span></li>
            <li class="menu-item">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-food-menu"></i>
                <div data-i18n="Invoice">تامین کنندگان</div>
              </a>
              <ul class="menu-sub">
              <li class="menu-item">
                  <a href="app-invoice-edit.html" class="menu-link">
                    <div data-i18n="Edit">لیست تامین کنندگان</div>
                  </a>
                </li>
              </ul>
            </li>
            <li class="menu-item">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-user"></i>
                <div data-i18n="Users">واحد های سازمانی</div>
              </a>
              <ul class="menu-sub">
              <li class="menu-item">
                  <a href="app-user-list.html" class="menu-link">
                    <div data-i18n="List">دپارتمان ها</div>
                  </a>
                </li>
              </ul>
            </li>
            <li class="menu-item">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-user"></i>
                <div data-i18n="Users">تحویل گیرندگان</div>
              </a>
              <ul class="menu-sub">
              <li class="menu-item">
                  <a href="app-user-list.html" class="menu-link">
                    <div data-i18n="List">پرسنل دریافت کننده</div>
                  </a>
                </li>
              </ul>
            </li>







            <li class="menu-header small text-uppercase"><span class="menu-header-text">گزارشات</span></li>
            <li class="menu-item">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-food-menu"></i>
                <div data-i18n="Invoice">گزارش کالا</div>
              </a>
              <ul class="menu-sub">
              <li class="menu-item">
                  <a href="app-invoice-edit.html" class="menu-link">
                    <div data-i18n="Edit">موجودی</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="app-invoice-edit.html" class="menu-link">
                    <div data-i18n="Edit">گردش کالا</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="app-invoice-edit.html" class="menu-link">
                    <div data-i18n="Edit">کاردکس انبار</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="app-invoice-edit.html" class="menu-link">
                    <div data-i18n="Edit">ورود و خروج</div>
                  </a>
                </li>
              </ul>
            </li>
            <li class="menu-item">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-user"></i>
                <div data-i18n="Users">گردش انبار</div>
              </a>
              <ul class="menu-sub">
              <li class="menu-item">
                  <a href="app-user-list.html" class="menu-link">
                    <div data-i18n="List">عملکرد انبار ها</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="app-user-list.html" class="menu-link">
                    <div data-i18n="List">موجودی انبار ها</div>
                  </a>
                </li>
              </ul>
            </li>
            <li class="menu-item">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-user"></i>
                <div data-i18n="Users">گزارش دارایی</div>
              </a>
              <ul class="menu-sub">
              <li class="menu-item">
                  <a href="app-user-list.html" class="menu-link">
                    <div data-i18n="List">تخصیص</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="app-user-list.html" class="menu-link">
                    <div data-i18n="List">تعمیرات</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="app-user-list.html" class="menu-link">
                    <div data-i18n="List">اسقاط</div>
                  </a>
                </li>
              </ul>
            </li>
            <li class="menu-item">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-user"></i>
                <div data-i18n="Users">گزارش مدیریتی</div>
              </a>
              <ul class="menu-sub">
              <li class="menu-item">
                  <a href="app-user-list.html" class="menu-link">
                    <div data-i18n="List">ارزش موجودی</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="app-user-list.html" class="menu-link">
                    <div data-i18n="List">کالاهای پرمصرف</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="app-user-list.html" class="menu-link">
                    <div data-i18n="List">کالاهای کم مصرف</div>
                  </a>
                </li>
              </ul>
            </li>







            <li class="menu-header small text-uppercase"><span class="menu-header-text">کاربران و دسترسی</span></li>
            <li class="menu-item">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-food-menu"></i>
                <div data-i18n="Invoice">کاربران</div>
              </a>
              <ul class="menu-sub">
              <li class="menu-item">
                  <a href="app-invoice-edit.html" class="menu-link">
                    <div data-i18n="Edit">کاربران سیستم</div>
                  </a>
                </li>
              </ul>
            </li>
            <li class="menu-item">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-user"></i>
                <div data-i18n="Users">نقش ها</div>
              </a>
              <ul class="menu-sub">
              <li class="menu-item">
                  <a href="app-user-list.html" class="menu-link">
                    <div data-i18n="List">نقش ها</div>
                  </a>
                </li>
              </ul>
            </li>
            <li class="menu-item">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-user"></i>
                <div data-i18n="Users">مجوز ها</div>
              </a>
              <ul class="menu-sub">
              <li class="menu-item">
                  <a href="app-user-list.html" class="menu-link">
                    <div data-i18n="List">سطوح دسترسی</div>
                  </a>
                </li>
              </ul>
            </li>




            <li class="menu-header small text-uppercase"><span class="menu-header-text">تنظیمات</span></li>
            <li class="menu-item">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-food-menu"></i>
                <div data-i18n="Invoice">تنظیمات اصلی</div>
              </a>
              <ul class="menu-sub">
              <li class="menu-item">
                  <a href="app-invoice-edit.html" class="menu-link">
                    <div data-i18n="Edit">اشتراک ها</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="app-invoice-edit.html" class="menu-link">
                    <div data-i18n="Edit">تنظیمات سامانه</div>
                  </a>
                </li>
              </ul>
            </li>
            <li class="menu-item">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-food-menu"></i>
                <div data-i18n="Invoice">پایه</div>
              </a>
              <ul class="menu-sub">
              <li class="menu-item">
                  <a href="app-invoice-edit.html" class="menu-link">
                    <div data-i18n="Edit">تنظیمات سازمان</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="app-invoice-edit.html" class="menu-link">
                    <div data-i18n="Edit">تنظیمات انبار</div>
                  </a>
                </li>
              </ul>
            </li>
            <li class="menu-item">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-user"></i>
                <div data-i18n="Users">شماره گذاری</div>
              </a>
              <ul class="menu-sub">
              <li class="menu-item">
                  <a href="app-user-list.html" class="menu-link">
                    <div data-i18n="List">شماره اسناد</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="app-user-list.html" class="menu-link">
                    <div data-i18n="List">قالب کد کالا</div>
                  </a>
                </li>
              </ul>
            </li>
            <li class="menu-item">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-user"></i>
                <div data-i18n="Users">اعلان ها</div>
              </a>
              <ul class="menu-sub">
              <li class="menu-item">
                  <a href="app-user-list.html" class="menu-link">
                    <div data-i18n="List">پیامک</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="app-user-list.html" class="menu-link">
                    <div data-i18n="List">ایمیل</div>
                  </a>
                </li>
              </ul>
            </li>
            <li class="menu-item">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-user"></i>
                <div data-i18n="Users">لاگ ها</div>
              </a>
              <ul class="menu-sub">
              <li class="menu-item">
                  <a href="app-user-list.html" class="menu-link">
                    <div data-i18n="List">فعالیت کاربران</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="app-user-list.html" class="menu-link">
                    <div data-i18n="List">عملیات سیستم</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="app-user-list.html" class="menu-link">
                    <div data-i18n="List">تاریخچه موجودی</div>
                  </a>
                </li>
              </ul>
            </li>



            <!-- Misc -->
            <li class="menu-header small text-uppercase"><span class="menu-header-text">متفرقه</span></li>
            <li class="menu-item">
              <a href="https://www.rtl-theme.com" target="_blank" class="menu-link">
                <i class="menu-icon tf-icons bx bx-support"></i>
                <div data-i18n="Support">لایسنس</div>
              </a>
            </li>
            <li class="menu-item">
              <a href="https://www.rtl-theme.com" target="_blank" class="menu-link">
                <i class="menu-icon tf-icons bx bx-support"></i>
                <div data-i18n="Support">عملیات بکاپ گیری</div>
              </a>
            </li>
            <li class="menu-item">
              <a href="https://www.rtl-theme.com" target="_blank" class="menu-link">
                <i class="menu-icon tf-icons bx bx-support"></i>
                <div data-i18n="Support">پشتیبانی</div>
              </a>
            </li>
            <li class="menu-item">
              <a href="https://v3dboy.ir/previews/html/frest/documentation" target="_blank" class="menu-link">
                <i class="menu-icon tf-icons bx bx-file"></i>
                <div data-i18n="Documentation">مستندات</div>
              </a>
            </li>
          </ul>
        </aside>
