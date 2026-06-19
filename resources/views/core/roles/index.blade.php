@extends('layouts.master')

@section('title', 'مدیریت نقش‌ها')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- کارت‌های آماری --}}
    <div class="row g-4 mb-4" id="statsCards">
        @include('core.roles._stats', ['stats' => $stats])
    </div>

    <div class="card shadow-none border">
        <div class="card-header border-bottom d-flex flex-wrap justify-content-between align-items-center gap-3">
            <h5 class="card-title mb-0">
                <i class="bx bx-shield me-1"></i>لیست نقش‌ها
                <small class="text-muted ms-2">({{ $roles->total() }} نقش)</small>
            </h5>
            <div class="d-flex gap-2 flex-wrap">
                {{-- Export placeholder --}}
                <div class="btn-group">
                    <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bx bx-export"></i> خروجی
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item disabled" href="#"><i class="bx bx-file me-1"></i> Excel (به‌زودی)</a></li>
                        <li><a class="dropdown-item disabled" href="#"><i class="bx bxs-file-pdf me-1"></i> PDF (به‌زودی)</a></li>
                        <li><a class="dropdown-item disabled" href="#"><i class="bx bx-printer me-1"></i> چاپ (به‌زودی)</a></li>
                    </ul>
                </div>

                @can('access', 'roles.create')
                <a href="{{ route('roles.create') }}" class="btn btn-primary">
                    <i class="bx bx-plus"></i> نقش جدید
                </a>
                @endcan
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
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
                    @forelse ($roles as $role)
                    <tr>
                        <td>{{ $role->code }}</td>
                        <td>{{ $role->title }}</td>
                        <td>{{ $role->permissions->count() }}</td>
                        <td>{{ $role->is_system ? 'بله' : 'خیر' }}</td>
                        <td>
                            <div class="d-flex gap-1">
                                @can('access', 'roles.edit')
                                <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-sm btn-outline-warning">
                                    <i class="bx bx-edit"></i>
                                </a>
                                @endcan
                                @can('access', 'roles.delete')
                                @if (!$role->is_system)
                                <form action="{{ route('roles.destroy', $role->id) }}" method="POST" class="d-inline delete-form">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </form>
                                @endif
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-5">هیچ نقشی یافت نشد.</td>
                    </tr>
                    @endforelse
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
        // ========== حذف نقش با تایید SweetAlert ==========
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