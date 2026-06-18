<table class="table table-hover align-middle">
    <thead class="table-light">
        <tr><th>#</th><th>عنوان</th><th>توضیحات</th><th>وضعیت</th><th>عملیات</th></tr>
    </thead>
    <tbody>
        @forelse($productTypes as $pt)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $pt->title }}</td>
            <td>{{ Str::limit($pt->description, 50) ?? '---' }}</td>
            <td>{!! $pt->is_active ? '<span class="badge bg-success">فعال</span>' : '<span class="badge bg-danger">غیرفعال</span>' !!}</td>
            <td>
                <div class="d-flex gap-1">
                    @can('access', 'product-types.edit')
                    <a href="{{ route('warehouse.product-types.edit', $pt) }}" class="btn btn-sm btn-icon btn-outline-warning"><i class="bx bx-edit"></i></a>
                    @endcan
                    @can('access', 'product-types.delete')
                    <form action="{{ route('warehouse.product-types.destroy', $pt) }}" method="POST" class="d-inline delete-form">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-icon btn-outline-danger"><i class="bx bx-trash"></i></button>
                    </form>
                    @endcan
                </div>
            </td>
        </tr>
        @empty
        <tr><td colspan="5" class="text-center text-muted py-5">نوع کالایی یافت نشد.</td></tr>
        @endforelse
    </tbody>
</table>
<div class="card-footer d-flex justify-content-between">
    <small class="text-muted">نمایش {{ $productTypes->firstItem() ?? 0 }} تا {{ $productTypes->lastItem() ?? 0 }} از {{ $productTypes->total() }}</small>
    {{ $productTypes->links() }}
</div>