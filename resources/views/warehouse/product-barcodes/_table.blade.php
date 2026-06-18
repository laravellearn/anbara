<table class="table table-hover align-middle">
    <thead class="table-light">
        <tr>
            <th>#</th>
            <th>کالا</th>
            <th>بارکد</th>
            <th>پیش‌فرض</th>
            <th>عملیات</th>
        </tr>
    </thead>
    <tbody>
        @forelse($barcodes as $bc)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $bc->product->name ?? '---' }}</td>
            <td dir="ltr">{{ $bc->barcode }}</td>
            <td>{{ $bc->is_default ? 'بله' : 'خیر' }}</td>
            <td>
                <div class="d-flex gap-1">
                    @can('access', 'barcodes.edit')
                    <button class="btn btn-sm btn-icon btn-outline-warning edit-bc-btn"
                        data-id="{{ $bc->id }}" data-product="{{ $bc->product_id }}"
                        data-barcode="{{ $bc->barcode }}" data-default="{{ $bc->is_default }}">
                        <i class="bx bx-edit"></i>
                    </button>
                    @endcan
                    @can('access', 'barcodes.delete')
                    <form action="{{ route('admin.barcodes.destroy', $bc) }}" method="POST" class="d-inline delete-form">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-icon btn-outline-danger"><i class="bx bx-trash"></i></button>
                    </form>
                    @endcan
                </div>
            </td>
        </tr>
        @empty
        <tr><td colspan="5" class="text-center text-muted py-5">بارکدی یافت نشد.</td></tr>
        @endforelse
    </tbody>
</table>
<div class="card-footer d-flex justify-content-between">
    <small class="text-muted">نمایش {{ $barcodes->firstItem() ?? 0 }} تا {{ $barcodes->lastItem() ?? 0 }} از {{ $barcodes->total() }}</small>
    {{ $barcodes->links() }}
</div>