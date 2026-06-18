<table class="table table-hover align-middle">
    <thead class="table-light">
        <tr>
            <th>#</th>
            <th>کالا</th>
            <th>جایگزین</th>
            <th>عملیات</th>
        </tr>
    </thead>
    <tbody>
        @forelse($alternatives as $alt)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $alt->product->name ?? '---' }}</td>
            <td>{{ $alt->alternativeProduct->name ?? '---' }}</td>
            <td>
                <div class="d-flex gap-1">
                    @can('access', 'item-alternatives.edit')
                    <button class="btn btn-sm btn-icon btn-outline-warning edit-alt-btn"
                        data-id="{{ $alt->id }}" data-product="{{ $alt->product_id }}"
                        data-alternative="{{ $alt->alternative_product_id }}">
                        <i class="bx bx-edit"></i>
                    </button>
                    @endcan
                    @can('access', 'item-alternatives.delete')
                    <form action="{{ route('admin.item-alternatives.destroy', $alt) }}" method="POST" class="d-inline delete-form">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-icon btn-outline-danger"><i class="bx bx-trash"></i></button>
                    </form>
                    @endcan
                </div>
            </td>
        </tr>
        @empty
        <tr><td colspan="4" class="text-center text-muted py-5">جایگزینی یافت نشد.</td></tr>
        @endforelse
    </tbody>
</table>
<div class="card-footer d-flex justify-content-between">
    <small class="text-muted">نمایش {{ $alternatives->firstItem() ?? 0 }} تا {{ $alternatives->lastItem() ?? 0 }} از {{ $alternatives->total() }}</small>
    {{ $alternatives->links() }}
</div>