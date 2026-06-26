<table class="table table-hover align-middle">
    <thead class="table-light">
        <tr>
            <th>#</th>
            <th>عنوان</th>
            <th>SKU</th>
            <th>دسته‌بندی</th>
            <th>واحد پایه</th>
            <th>حداقل موجودی</th>
            <th>وضعیت</th>
            <th>عملیات</th>
        </tr>
    </thead>
    <tbody>
        @forelse($products as $product)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $product->title }}</td>
            <td>{{ $product->sku ?? '---' }}</td>
            <td>{{ $product->category->title ?? '---' }}</td>
            <td>{{ $product->baseMeasurementUnit->title ?? '---' }}</td>
            <td>{{ $product->minimum_stock }}</td>
            <td>{!! $product->is_active ? '<span class="badge bg-success">فعال</span>' : '<span class="badge bg-danger">غیرفعال</span>' !!}</td>
            <td>
                <div class="d-flex gap-1">
                    @can('access', 'products.edit')
                    <a href="{{ route('warehouse.products.edit', $product) }}" class="btn btn-sm btn-icon btn-outline-warning">
                        <i class="bx bx-edit"></i>
                    </a>
                    @endcan
                    @can('access', 'products.delete')
                    <form action="{{ route('warehouse.products.destroy', $product) }}" method="POST" class="d-inline delete-form">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-icon btn-outline-danger"><i class="bx bx-trash"></i></button>
                    </form>
                    @endcan
                </div>
            </td>
        </tr>
        @empty
        <tr><td colspan="9" class="text-center text-muted py-5">کالایی یافت نشد.</td></tr>
        @endforelse
    </tbody>
</table>
<div class="card-footer d-flex justify-content-between">
    <small class="text-muted">نمایش {{ $products->firstItem() ?? 0 }} تا {{ $products->lastItem() ?? 0 }} از {{ $products->total() }}</small>
    {{ $products->links() }}
</div>