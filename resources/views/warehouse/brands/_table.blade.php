<table class="table table-hover align-middle">
    <thead class="table-light">
        <tr>
            <th>#</th>
            <th>عنوان</th>
            <th>توضیحات</th>
            <th>وضعیت</th>
            <th>عملیات</th>
        </tr>
    </thead>
    <tbody>
        @forelse($brands as $brand)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $brand->title }}</td>
            <td>{{ Str::limit($brand->description, 50) ?? '---' }}</td>
            <td>{!! $brand->is_active ? '<span class="badge bg-success">فعال</span>' : '<span class="badge bg-danger">غیرفعال</span>' !!}</td>
            <td>
                <div class="d-flex gap-1">
                    @can('access', 'brands.edit')
                    <button class="btn btn-sm btn-icon btn-outline-warning edit-brand-btn"
                        data-id="{{ $brand->id }}" data-title="{{ $brand->title }}" data-desc="{{ $brand->description }}"
                        data-active="{{ $brand->is_active }}">
                        <i class="bx bx-edit"></i>
                    </button>
                    @endcan
                    @can('access', 'brands.delete')
                    <form action="{{ route('warehouse.brands.destroy', $brand) }}" method="POST" class="d-inline delete-form">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-icon btn-outline-danger"><i class="bx bx-trash"></i></button>
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