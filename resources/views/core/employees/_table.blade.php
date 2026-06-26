<table class="table table-hover align-middle">
    <thead class="table-light">
        <tr>
            <th>#</th>
            <th>نام</th>
            <th>کد کارمندی</th>
            <th>واحد</th>
            <th>سمت</th>
            <th>موبایل</th>
            <th>ایمیل</th>
            <th>وضعیت</th>
            <th>عملیات</th>
        </tr>
    </thead>
    <tbody>
        @forelse($employees as $emp)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $emp->name }}</td>
            <td>{{ $emp->employee_code ?? '---' }}</td>
            <td>{{ $emp->organizationalUnit->name ?? '---' }}</td>
            <td>{{ $emp->position ?? '---' }}</td>
            <td>{{ $emp->mobile ?? '---' }}</td>
            <td>{{ $emp->contact->email ?? '---' }}</td>
            <td>{!! $emp->is_active ? '<span class="badge bg-success">فعال</span>' : '<span class="badge bg-danger">غیرفعال</span>' !!}</td>
            <td>
                <div class="d-flex gap-1">
                    @can('access', 'employees.edit')
                    <button class="btn btn-sm btn-icon btn-outline-warning edit-employee-btn"
                        data-id="{{ $emp->id }}"
                        data-name="{{ $emp->name }}"
                        data-code="{{ $emp->employee_code }}"
                        data-national_code="{{ $emp->national_code }}"
                        data-unit="{{ $emp->organizational_unit_id }}"
                        data-user="{{ $emp->user_id }}"
                        data-position="{{ $emp->position }}"
                        data-mobile="{{ $emp->mobile }}"
                        data-phone="{{ $emp->phone }}"
                        data-email="{{ $emp->contact->email ?? '' }}"
                        data-employment_date="{{ $emp->employment_date ? $emp->employment_date->format('Y-m-d') : '' }}"
                        data-address="{{ $emp->address }}"
                        data-desc="{{ $emp->description }}"
                        data-active="{{ $emp->is_active }}">
                        <i class="bx bx-edit"></i>
                    </button>
                    @endcan
                    @can('access', 'employees.delete')
                    <form action="{{ route('employees.destroy', $emp) }}" method="POST" class="d-inline delete-form">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-icon btn-outline-danger"><i class="bx bx-trash"></i></button>
                    </form>
                    @endcan
                </div>
            </td>
        </tr>
        @empty
        <tr><td colspan="9" class="text-center text-muted py-5">کارمندی یافت نشد.</td></tr>
        @endforelse
    </tbody>
</table>
<div class="card-footer d-flex justify-content-between">
    <small class="text-muted">نمایش {{ $employees->firstItem() ?? 0 }} تا {{ $employees->lastItem() ?? 0 }} از {{ $employees->total() }}</small>
    {{ $employees->links() }}
</div>