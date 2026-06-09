<div class="table-responsive">
    <table class="table table-hover align-middle" id="usersTable">
        <thead class="table-light">
            <tr>
                <th style="width: 50px;">#</th>
                <th class="cursor-pointer sortable" data-sort="name">
                    نام
                    <i class="bx bx-sort ms-1 text-muted sort-icon"></i>
                </th>
                <th>موبایل</th>
                <th>ایمیل</th>
                <th>شرکت‌ها</th>
                <th>نقش‌ها</th>
                <th>وضعیت</th>
                <th class="cursor-pointer sortable" data-sort="last_login_at">
                    آخرین ورود
                    <i class="bx bx-sort ms-1 text-muted sort-icon"></i>
                </th>
                <th class="cursor-pointer sortable" data-sort="created_at">
                    تاریخ ثبت
                    <i class="bx bx-sort-down ms-1 text-primary sort-icon"></i>
                </th>
                <th>عملیات</th>
            </tr>
        </thead>
        <tbody id="usersTableBody">
            @forelse($users as $user)
                <tr data-name="{{ $user->name }}" data-last-login="{{ $user->last_login_at?->timestamp ?? 0 }}"
                    data-created="{{ $user->created_at->timestamp }}">
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-sm me-3">
                                <a href="{{ route('users.show', $user->id) }}">
                                    <img src="{{ $user->avatar }}" alt="{{ $user->name }}" class="rounded-circle">
                                </a>
                            </div>
                            <div>
                                <a href="{{ route('users.show', $user->id) }}"
                                    class="text-body fw-medium text-decoration-none">
                                    {{ $user->name }}
                                </a>
                                @if ($user->last_login_at && $user->last_login_at->gt(now()->subMinutes(5)))
                                    <span class="badge bg-success ms-1"
                                        style="width: 8px; height: 8px; display: inline-block; border-radius: 50%;"
                                        title="آنلاین (۵ دقیقه اخیر)"></span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td dir="ltr">
                        <a href="tel:{{ $user->mobile }}" class="text-decoration-none">{{ $user->mobile }}</a>
                    </td>
                    <td>
                        @if ($user->email)
                            <a href="mailto:{{ $user->email }}" class="text-decoration-none">{{ $user->email }}</a>
                        @else
                            <span class="text-muted">---</span>
                        @endif
                    </td>
                    <td>
                        @forelse($user->companies as $company)
                            <span class="badge bg-label-secondary me-1 mb-1">
                                {{ $company->name }}
                                @if ($company->pivot->is_default)
                                    <i class="bx bx-star text-warning ms-1"></i>
                                @endif
                            </span>
                        @empty
                            <span class="text-muted">---</span>
                        @endforelse
                    </td>
                    <td>
                        @php
                            $defaultCompanyUser = $user->companyUsers()->where('is_default', true)->first();
                            $userRoles = $defaultCompanyUser ? $defaultCompanyUser->roles : collect();
                        @endphp
                        @forelse($userRoles as $role)
                            <span class="badge bg-label-info me-1 mb-1">{{ $role->title }}</span>
                        @empty
                            <span class="text-muted">---</span>
                        @endforelse
                    </td>
                    <td>
                        @if ($user->is_active)
                            <span class="badge bg-success">فعال</span>
                        @else
                            <span class="badge bg-danger">غیرفعال</span>
                        @endif
                    </td>
                    <td>
                        @if ($user->last_login_at)
                            <span class="small" title="{{ \Verta::instance($user->last_login_at)->format('Y/m/d-H:i:s') }}">
                                {{ \Verta::instance($user->last_login_at)->format('Y/m/d-H:i:s') }}
                            </span>
                            <br>
                            <small class="text-muted"
                                dir="ltr">{{ $user->last_login_at->diffForHumans() }}</small>
                        @else
                            <span class="badge bg-label-secondary">بدون ورود</span>
                        @endif
                    </td>
                    <td>
                        <small>
                            {{ \Verta::instance($user->created_at)->format('Y/m/d') }}
                        </small>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            @can('access', 'users.edit')
                                @php
                                    $userCompanyRoles = [];
                                    foreach ($user->companyUsers as $cu) {
                                        $userCompanyRoles[$cu->company_id] = $cu->roles->pluck('id')->toArray();
                                    }
                                @endphp

                                <button class="btn btn-sm btn-icon btn-outline-warning edit-user-btn"
                                    data-bs-toggle="tooltip" title="ویرایش" data-id="{{ $user->id }}"
                                    data-name="{{ $user->name }}" data-mobile="{{ $user->mobile }}"
                                    data-email="{{ $user->email }}" data-is-active="{{ $user->is_active ? 1 : 0 }}"
                                    data-companies="{{ json_encode($user->companies->pluck('id')) }}"
                                    data-company-roles="{{ json_encode($userCompanyRoles) }}"
                                    data-default-company="{{ $user->companies()->wherePivot('is_default', true)->first()?->id }}">
                                    <i class="bx bx-edit"></i>
                                </button>
                            @endcan
                            @can('access', 'users.delete')
                                @can('access', 'users.delete')
                                    @if ($user->id !== auth()->id())
                                        <button type="button" class="btn btn-sm btn-icon btn-outline-danger delete-user-btn"
                                            data-bs-toggle="tooltip" title="حذف"
                                            data-url="{{ route('users.destroy', $user) }}" data-name="{{ $user->name }}"
                                            data-id="{{ $user->id }}">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    @else
                                        <button class="btn btn-sm btn-icon btn-outline-secondary" disabled
                                            data-bs-toggle="tooltip" title="نمی‌توانید خود را حذف کنید">
                                            <i class="bx bx-block"></i>
                                        </button>
                                    @endif
                                @endcan
                            @endcan
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center text-muted py-5">
                        <i class="bx bx-search-alt bx-lg d-block mb-2"></i>
                        هیچ کاربری با این شرایط یافت نشد.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="card-footer d-flex justify-content-between align-items-center">
    <small class="text-muted">
        نمایش {{ $users->firstItem() ?? 0 }} تا {{ $users->lastItem() ?? 0 }} از {{ $users->total() }} کاربر
    </small>
    {{ $users->appends(request()->query())->links() }}
</div>

{{-- ==================== استایل و اسکریپت مرتب‌سازی ==================== --}}
<style>
    .cursor-pointer {
        cursor: pointer;
        user-select: none;
        transition: background-color 0.2s;
    }

    .cursor-pointer:hover {
        background-color: rgba(0, 0, 0, 0.05);
    }

    .sort-icon.active {
        color: #696cff !important;
    }
</style>

<script>
    $(function() {
        // ========== مرتب‌سازی جدول ==========
        let currentSort = 'created_at'; // پیش‌فرض: تاریخ ثبت
        let currentDirection = 'desc'; // نزولی

        // تنظیم آیکون اولیه
        updateSortIcons();

        $(document).on('click', '.sortable', function() {
            const column = $(this).data('sort');

            // اگر روی همان ستون کلیک شد، جهت را معکوس کن
            if (currentSort === column) {
                currentDirection = currentDirection === 'asc' ? 'desc' : 'asc';
            } else {
                currentSort = column;
                currentDirection = 'asc';
            }

            sortTable();
            updateSortIcons();
        });

        function sortTable() {
            const rows = $('#usersTableBody tr').toArray();

            rows.sort(function(a, b) {
                let valA, valB;

                if (currentSort === 'name') {
                    valA = $(a).data('name') || '';
                    valB = $(b).data('name') || '';
                    return currentDirection === 'asc' ?
                        valA.localeCompare(valB, 'fa') :
                        valB.localeCompare(valA, 'fa');
                } else {
                    valA = parseInt($(a).data(currentSort === 'last_login_at' ? 'last-login' :
                        'created')) || 0;
                    valB = parseInt($(b).data(currentSort === 'last_login_at' ? 'last-login' :
                        'created')) || 0;
                    return currentDirection === 'asc' ? valA - valB : valB - valA;
                }
            });

            $('#usersTableBody').empty().append(rows);

            // بروزرسانی شماره ردیف‌ها
            $('#usersTableBody tr').each(function(index) {
                $(this).find('td:first').text(index + 1);
            });
        }

        function updateSortIcons() {
            // ریست همه آیکون‌ها
            $('.sort-icon').removeClass('active bx-sort-up bx-sort-down').addClass('bx-sort text-muted');

            // فعال کردن آیکون ستون جاری
            const $activeHeader = $(`.sortable[data-sort="${currentSort}"]`);
            const $icon = $activeHeader.find('.sort-icon');
            $icon.removeClass('bx-sort text-muted').addClass('active');

            if (currentDirection === 'asc') {
                $icon.addClass('bx-sort-up');
            } else {
                $icon.addClass('bx-sort-down');
            }
        }
    });
</script>
