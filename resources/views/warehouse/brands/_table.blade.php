<table class="table table-hover align-middle">
    <thead class="table-light">
        <tr>
            <th>#</th>
            <th>نام برند</th>
            <th>تعداد کالا</th>
            <th>وضعیت</th>
            <th>عملیات</th>
        </tr>
    </thead>
    <tbody>
        @forelse($brands as $brand)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>
                <div class="fw-medium">{{ $brand->name }}</div>
                @if($brand->description)
                <small class="text-muted">{{ Str::limit($brand->description, 50) }}</small>
                @endif
            </td>
            <td><span class="badge bg-label-info">{{ $brand->products_count ?? $brand->products()->count() }}</span></td>
            <td>
                <span class="badge bg-label-{{ $brand->is_active ? 'success' : 'secondary' }}">
                    {{ $brand->is_active ? 'فعال' : 'غیرفعال' }}
                </span>
            </td>
            <td>
                <div class="d-flex gap-1">
                    @can('access', 'brands.edit')
                    <button class="btn btn-sm btn-icon btn-outline-warning btn-edit-brand"
                        data-id="{{ $brand->id }}"
                        data-name="{{ $brand->name }}"
                        data-description="{{ $brand->description }}"
                        data-is_active="{{ $brand->is_active ? '1' : '0' }}"
                        title="ویرایش">
                        <i class="bx bx-edit"></i>
                    </button>
                    @endcan
                    @can('access', 'brands.delete')
                    <form action="{{ route('warehouse.brands.destroy', $brand) }}" method="POST" class="d-inline delete-form">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-icon btn-outline-danger" title="حذف"><i class="bx bx-trash"></i></button>
                    </form>
                    @endcan
                </div>
            </td>
        </tr>
        @empty
        <tr><td colspan="5" class="text-center text-muted py-5">برندی یافت نشد.</td></tr>
        @endforelse
    </tbody>
</table>
<div class="card-footer d-flex justify-content-between">
    <small class="text-muted">نمایش {{ $brands->firstItem() ?? 0 }} تا {{ $brands->lastItem() ?? 0 }} از {{ $brands->total() }}</small>
    {{ $brands->links() }}
</div>
