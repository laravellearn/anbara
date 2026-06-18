<table class="table table-hover align-middle">
    <thead class="table-light">
        <tr>
            <th>#</th>
            <th>نام</th>
            <th>SKU</th>
            <th>بارکد</th>
            <th>دسته‌بندی</th>
            <th>واحد</th>
            <th>حداقل موجودی</th>
            <th>وضعیت</th>
            <th>عملیات</th>
        </tr>
    </thead>
    <tbody>
        @forelse($products as $product)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $product->name }}</td>
            <td>{{ $product->sku ?? '---' }}</td>
            <td>{{ $product->barcode ?? '---' }}</td>
            <td>{{ $product->category->name ?? '---' }}</td>
            <td>{{ $product->unit->title ?? '---' }}</td>
            <td>{{ $product->min_stock }}</td>
            <td>
                @if($product->is_active)
                    <span class="badge bg-success">فعال</span>
                @else
                    <span class="badge bg-danger">غیرفعال</span>
                @endif
            </td>
            <td>
                <div class="d-flex gap-1">
                    @can('access', 'items.edit')
                    <button class="btn btn-sm btn-icon btn-outline-warning edit-product-btn"
                        data-id="{{ $product->id }}" data-name="{{ $product->name }}"
                        data-sku="{{ $product->sku }}" data-barcode="{{ $product->barcode }}"
                        data-category="{{ $product->category_id }}" data-unit="{{ $product->unit_id }}"
                        data-min="{{ $product->min_stock }}" data-max="{{ $product->max_stock }}"
                        data-desc="{{ $product->description }}" data-active="{{ $product->is_active }}">
                        <i class="bx bx-edit"></i>
                    </button>
                    @endcan
                    @can('access', 'items.delete')
                    <form action="{{ route('admin.items.destroy', $product) }}" method="POST" class="d-inline delete-form">
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