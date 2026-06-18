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
