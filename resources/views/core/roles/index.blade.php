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
                    @foreach($roles as $role)
                    <tr>
                        <td>{{ $role->code }}</td>
                        <td>{{ $role->title }}</td>
                        <td>{{ $role->permissions->count() }}</td>
                        <td>{{ $role->is_system ? 'بله' : 'خیر' }}</td>
                        <td>
                            <div class="d-flex gap-1">
                                @can('access', 'roles.edit')
                                <button class="btn btn-sm btn-outline-warning edit-role-btn"
                                    data-id="{{ $role->id }}"
                                    data-code="{{ $role->code }}"
                                    data-title="{{ $role->title }}"
                                    data-description="{{ $role->description }}"
                                    data-permissions="{{ json_encode($role->permissions->pluck('id')) }}">
                                    <i class="bx bx-edit"></i>
                                </button>
                                @endcan
                                @can('access', 'roles.delete')
                                @if(!$role->is_system)
                                <form action="{{ route('roles.destroy', $role) }}" method="POST" class="d-inline delete-form">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bx bx-trash"></i></button>
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
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">ایجاد نقش جدید</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                        <div class="col-12">
                            <label class="form-label">مجوزها</label>
                            <div class="row">
                                @foreach(\App\Models\Permission::all() as $perm)
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $perm->id }}" id="perm_{{ $perm->id }}">
                                        <label class="form-check-label" for="perm_{{ $perm->id }}">{{ $perm->title }}</label>
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
        $('.edit-role-btn').on('click', function() {
            const btn = $(this);
            const id = btn.data('id');
            $('#roleForm').attr('action', `{{ route('roles.update', ':id') }}`.replace(':id', id));
            $('#roleForm').append('<input type="hidden" name="_method" value="PUT">');
            $('#role_code').val(btn.data('code'));
            $('#role_title').val(btn.data('title'));
            $('#role_description').val(btn.data('description'));
            // ریست و انتخاب مجوزها
            $('input[name="permissions[]"]').prop('checked', false);
            const perms = btn.data('permissions') || [];
            perms.forEach(id => $(`#perm_${id}`).prop('checked', true));
            $('#createRoleModal').modal('show');
        });
        $('#createRoleModal').on('hidden.bs.modal', function() {
            $('#roleForm').attr('action', `{{ route('roles.store') }}`);
            $('input[name="_method"]').remove();
            $('#roleForm')[0].reset();
        });

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
    });
</script>
@endpush