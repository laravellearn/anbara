<table class="table table-hover align-middle">
    <thead class="table-light">
        <tr>
            <th>#</th>
            <th>کالا</th>
            <th>نام بسته</th>
            <th>واحد</th>
            <th>تعداد در بسته</th>
            <th>عملیات</th>
        </tr>
    </thead>
    <tbody>
        @forelse($packagings as $pkg)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $pkg->product->name ?? '---' }}</td>
            <td>{{ $pkg->name }}</td>
            <td>{{ $pkg->unit->title ?? '---' }}</td>
            <td>{{ $pkg->quantity_per_unit }}</td>
            <td>
                <div class="d-flex gap-1">
                    @can('access', 'item-packaging.edit')
                    <button class="btn btn-sm btn-icon btn-outline-warning edit-pkg-btn"
                        data-id="{{ $pkg->id }}" data-product="{{ $pkg->product_id }}" data-unit="{{ $pkg->unit_id }}"
                        data-name="{{ $pkg->name }}" data-qty="{{ $pkg->quantity_per_unit }}">
                        <i class="bx bx-edit"></i>
                    </button>
                    @endcan
                    @can('access', 'item-packaging.delete')
                    <form action="{{ route('admin.item-packaging.destroy', $pkg) }}" method="POST" class="d-inline delete-form">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-icon btn-outline-danger"><i class="bx bx-trash"></i></button>
                    </form>
                    @endcan
                </div>
            </td>
        </tr>
        @empty
        <tr><td colspan="6" class="text-center text-muted py-5">بسته‌بندی یافت نشد.</td></tr>
        @endforelse
    </tbody>
</table>
<div class="card-footer d-flex justify-content-between">
    <small class="text-muted">نمایش {{ $packagings->firstItem() ?? 0 }} تا {{ $packagings->lastItem() ?? 0 }} از {{ $packagings->total() }}</small>
    {{ $packagings->links() }}
</div>