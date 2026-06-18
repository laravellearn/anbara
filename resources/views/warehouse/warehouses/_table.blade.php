<table class="table table-hover align-middle">
    <thead class="table-light">
        <tr>
            <th>#</th>
            <th>کد</th>
            <th>عنوان</th>
            <th>موجودی منفی</th>
            <th>وضعیت</th>
            <th>عملیات</th>
        </tr>
    </thead>
    <tbody>
        @forelse($warehouses as $warehouse)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $warehouse->code }}</td>
            <td>{{ $warehouse->title }}</td>
            <td>{{ $warehouse->allow_negative_stock ? 'بله' : 'خیر' }}</td>
            <td>{!! $warehouse->is_active ? '<span class="badge bg-success">فعال</span>' : '<span class="badge bg-danger">غیرفعال</span>' !!}</td>
            <td>
                <div class="d-flex gap-1">
                    @can('access', 'warehouses.edit')
                    <a href="{{ route('warehouse.warehouses.edit', $warehouse) }}" class="btn btn-sm btn-icon btn-outline-warning">
                        <i class="bx bx-edit"></i>
                    </a>
                    @endcan
                    @can('access', 'warehouses.delete')
                    <form action="{{ route('warehouse.warehouses.destroy', $warehouse) }}" method="POST" class="d-inline delete-form">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-icon btn-outline-danger"><i class="bx bx-trash"></i></button>
                    </form>
                    @endcan
                </div>
            </td>
        </tr>
        @empty
        <tr><td colspan="6" class="text-center text-muted py-5">انباری یافت نشد.</td></tr>
        @endforelse
    </tbody>
</table>
<div class="card-footer d-flex justify-content-between">
    <small class="text-muted">نمایش {{ $warehouses->firstItem() ?? 0 }} تا {{ $warehouses->lastItem() ?? 0 }} از {{ $warehouses->total() }}</small>
    {{ $warehouses->links() }}
</div>