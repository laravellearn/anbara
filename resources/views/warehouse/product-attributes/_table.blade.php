<table class="table table-hover align-middle">
    <thead class="table-light">
        <tr>
            <th>#</th>
            <th>نام</th>
            <th>نوع</th>
            <th>گزینه‌ها</th>
            <th>عملیات</th>
        </tr>
    </thead>
    <tbody>
        @forelse($attributes as $attr)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $attr->name }}</td>
            <td>{{ $attr->type }}</td>
            <td>{{ is_array($attr->options) ? implode(', ', $attr->options) : $attr->options }}</td>
            <td>
                <div class="d-flex gap-1">
                    @can('access', 'item-attributes.edit')
                    <button class="btn btn-sm btn-icon btn-outline-warning edit-attr-btn"
                        data-id="{{ $attr->id }}" data-name="{{ $attr->name }}"
                        data-type="{{ $attr->type }}" data-options="{{ json_encode($attr->options) }}">
                        <i class="bx bx-edit"></i>
                    </button>
                    @endcan
                    @can('access', 'item-attributes.delete')
                    <form action="{{ route('admin.item-attributes.destroy', $attr) }}" method="POST" class="d-inline delete-form">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-icon btn-outline-danger"><i class="bx bx-trash"></i></button>
                    </form>
                    @endcan
                </div>
            </td>
        </tr>
        @empty
        <tr><td colspan="5" class="text-center text-muted py-5">ویژگی یافت نشد.</td></tr>
        @endforelse
    </tbody>
</table>
<div class="card-footer d-flex justify-content-between">
    <small class="text-muted">نمایش {{ $attributes->firstItem() ?? 0 }} تا {{ $attributes->lastItem() ?? 0 }} از {{ $attributes->total() }}</small>
    {{ $attributes->links() }}
</div>