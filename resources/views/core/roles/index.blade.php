@extends('layouts.master')

@section('title', 'مدیریت نقش‌ها')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">نقش‌ها</h5>
                @can('access', 'roles.create')
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createRoleModal">
                        <i class="bx bx-plus"></i> نقش جدید
                    </button>
                @endcan
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>کد</th>
                            <th>عنوان</th>
                            <th>تعداد مجوزها</th>
                            <th>سیستمی</th>
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($roles as $role)
                            <tr>
                                <td>{{ $role->code }}</td>
                                <td>{{ $role->title }}</td>
                                <td>{{ $role->permissions->count() }}</td>
                                <td>{{ $role->is_system ? 'بله' : 'خیر' }}</td>
                                <td>
                                    <div class="d-flex gap-1">
                                        @can('access', 'roles.edit')
                                            <button class="btn btn-sm btn-outline-warning edit-role-btn"
                                                data-id="{{ $role->id }}" data-code="{{ $role->code }}"
                                                data-title="{{ $role->title }}" data-description="{{ $role->description }}"
                                                data-permissions="{{ json_encode($role->permissions->pluck('id')) }}">
                                                <i class="bx bx-edit"></i>
                                            </button>
                                        @endcan
                                        @can('access', 'roles.delete')
                                            @if (!$role->is_system)
                                                <form action="{{ route('roles.destroy', $role) }}" method="POST"
                                                    class="d-inline delete-form">
                                                    @csrf @method('DELETE')
                                                    <button class="btn btn-sm btn-outline-danger"><i
                                                            class="bx bx-trash"></i></button>
                                                </form>
                                            @endif
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                {{ $roles->links() }}
            </div>
        </div>
    </div>

{{-- مودال ایجاد/ویرایش نقش --}}
<div class="modal fade" id="createRoleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-white">ایجاد نقش جدید</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('roles.store') }}" method="POST" id="roleForm">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">کد <span class="text-danger">*</span></label>
                            <input type="text" name="code" id="role_code" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">عنوان <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="role_title" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">توضیحات</label>
                            <textarea name="description" id="role_description" class="form-control" rows="2"></textarea>
                        </div>
                        
                        {{-- مجوزها با گروه‌بندی و دکمه‌های گروهی --}}
                        <div class="col-12">
                            <hr>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <label class="form-label fw-bold">مجوزها (دسترسی‌ها)</label>
                                <div class="d-flex gap-2">
                                    <button type="button" id="selectAllGroups" class="btn btn-sm btn-outline-success">
                                        <i class="bx bx-check-double"></i> انتخاب همه گروه‌ها
                                    </button>
                                    <button type="button" id="deselectAllGroups" class="btn btn-sm btn-outline-danger">
                                        <i class="bx bx-x-circle"></i> حذف همه گروه‌ها
                                    </button>
                                </div>
                            </div>
                            
                            <div class="accordion mt-2" id="permissionsAccordion">
                                @foreach($groupedPermissions as $groupName => $permissions)
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" 
                                                type="button" 
                                                data-bs-toggle="collapse" 
                                                data-bs-target="#collapse{{ Str::slug($groupName) }}">
                                            <strong>
                                                <i class="bx bx-folder-open me-2"></i>
                                                {{ $groupName }}
                                            </strong>
                                            <span class="badge bg-primary ms-2" id="badge_{{ Str::slug($groupName) }}">
                                                {{ count($permissions) }}
                                            </span>
                                        </button>
                                    </h2>
                                    <div id="collapse{{ Str::slug($groupName) }}" 
                                         class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}">
                                        <div class="accordion-body">
                                            {{-- دکمه‌های گروهی داخل هر آکاردئون --}}
                                            <div class="d-flex gap-2 mb-3 pb-2 border-bottom">
                                                <button type="button" class="btn btn-sm btn-outline-primary select-group-btn" 
                                                        data-group="{{ Str::slug($groupName) }}">
                                                    <i class="bx bx-check-all"></i> انتخاب همه ({{ count($permissions) }})
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-secondary deselect-group-btn" 
                                                        data-group="{{ Str::slug($groupName) }}">
                                                    <i class="bx bx-x"></i> حذف همه
                                                </button>
                                                <span class="ms-auto text-muted small" id="counter_{{ Str::slug($groupName) }}">
                                                    انتخاب شده: <span class="fw-bold">0</span> / {{ count($permissions) }}
                                                </span>
                                            </div>
                                            
                                            <div class="row">
                                                @foreach($permissions as $perm)
                                                <div class="col-md-4 col-lg-3 mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input permission-checkbox group-{{ Str::slug($groupName) }}" 
                                                               type="checkbox" 
                                                               name="permissions[]" 
                                                               value="{{ $perm->id }}" 
                                                               id="perm_{{ $perm->id }}"
                                                               data-group="{{ Str::slug($groupName) }}">
                                                        <label class="form-check-label" for="perm_{{ $perm->id }}">
                                                            <i class="bx bx-check-shield text-info me-1"></i>
                                                            {{ $perm->title }}
                                                        </label>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">انصراف</button>
                    <button type="submit" class="btn btn-primary">ذخیره</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function() {
        // ========== به‌روزرسانی شمارنده هر گروه ==========
        function updateGroupCounter(groupSlug) {
            const $groupCheckboxes = $(`.group-${groupSlug}`);
            const total = $groupCheckboxes.length;
            const checked = $groupCheckboxes.filter(':checked').length;
            $(`#counter_${groupSlug}`).find('span.fw-bold').text(checked);
            
            // به‌روزرسانی badge گروه
            const $badge = $(`#badge_${groupSlug}`);
            if (checked === total && total > 0) {
                $badge.removeClass('bg-primary').addClass('bg-success').html(`✓ ${checked}/${total}`);
            } else if (checked > 0) {
                $badge.removeClass('bg-primary').addClass('bg-warning').html(`${checked}/${total}`);
            } else {
                $badge.removeClass('bg-success bg-warning').addClass('bg-primary').html(total);
            }
        }
        
        // ========== به‌روزرسانی همه شمارنده‌ها ==========
        function updateAllCounters() {
            $('.permission-checkbox').each(function() {
                const groupSlug = $(this).data('group');
                if (groupSlug) updateGroupCounter(groupSlug);
            });
        }
        
        // ========== انتخاب همه مجوزهای یک گروه ==========
        $('.select-group-btn').on('click', function() {
            const groupSlug = $(this).data('group');
            $(`.group-${groupSlug}`).prop('checked', true).trigger('change');
            updateGroupCounter(groupSlug);
        });
        
        // ========== حذف همه مجوزهای یک گروه ==========
        $('.deselect-group-btn').on('click', function() {
            const groupSlug = $(this).data('group');
            $(`.group-${groupSlug}`).prop('checked', false).trigger('change');
            updateGroupCounter(groupSlug);
        });
        
        // ========== انتخاب همه گروه‌ها (کل مجوزها) ==========
        $('#selectAllGroups').on('click', function() {
            $('.permission-checkbox').prop('checked', true).trigger('change');
            updateAllCounters();
            
            // نمایش toast یا alert اختیاری
            Swal.fire({
                icon: 'success',
                title: 'همه مجوزها انتخاب شدند',
                showConfirmButton: false,
                timer: 1500,
                toast: true,
                position: 'top-end'
            });
        });
        
        // ========== حذف همه گروه‌ها (کل مجوزها) ==========
        $('#deselectAllGroups').on('click', function() {
            $('.permission-checkbox').prop('checked', false).trigger('change');
            updateAllCounters();
            
            Swal.fire({
                icon: 'info',
                title: 'همه مجوزها حذف شدند',
                showConfirmButton: false,
                timer: 1500,
                toast: true,
                position: 'top-end'
            });
        });
        
        // ========== هنگام تغییر هر چک‌باکس، شمارنده را به‌روز کن ==========
        $(document).on('change', '.permission-checkbox', function() {
            const groupSlug = $(this).data('group');
            if (groupSlug) {
                updateGroupCounter(groupSlug);
            }
        });
        
        // ========== مودال ویرایش ==========
        $('.edit-role-btn').on('click', function() {
            const btn = $(this);
            const id = btn.data('id');
            const url = `{{ route('roles.update', ':id') }}`.replace(':id', id);
            
            $('#roleForm').attr('action', url);
            $('#roleForm').append('<input type="hidden" name="_method" value="PUT">');
            
            $('#role_code').val(btn.data('code'));
            $('#role_title').val(btn.data('title'));
            $('#role_description').val(btn.data('description'));
            
            // ریست و انتخاب مجوزها
            $('.permission-checkbox').prop('checked', false);
            const perms = btn.data('permissions') || [];
            perms.forEach(permId => {
                $(`#perm_${permId}`).prop('checked', true);
            });
            
            // به‌روزرسانی همه شمارنده‌ها
            updateAllCounters();
            
            $('#createRoleModal').modal('show');
        });
        
        // ========== پاک کردن فرم هنگام بسته شدن مودال ==========
        $('#createRoleModal').on('hidden.bs.modal', function() {
            $('#roleForm').attr('action', `{{ route('roles.store') }}`);
            $('input[name="_method"]').remove();
            $('#roleForm')[0].reset();
            $('.permission-checkbox').prop('checked', false);
            
            // ریست شمارنده‌ها
            updateAllCounters();
        });
        
        // ========== حذف نقش با تایید ==========
        $('.delete-form').on('submit', function(e) {
            e.preventDefault();
            const form = this;
            Swal.fire({
                title: 'آیا مطمئن هستید؟',
                text: "این عملیات قابل بازگشت نیست!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'بله، حذف کن',
                cancelButtonText: 'لغو',
                customClass: {
                    confirmButton: 'btn btn-danger me-3',
                    cancelButton: 'btn btn-label-secondary'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
        
        // مقداردهی اولیه شمارنده‌ها
        updateAllCounters();
    });
</script>
@endpush
