<table class="table table-hover align-middle">
    <thead class="table-light">
        <tr>
            <th>#</th>
            <th>نام</th>
            <th>والد</th>
            <th>وضعیت</th>
            <th>عملیات</th>
        </tr>
    </thead>
    <tbody>
        @forelse($categories as $cat)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $cat->name }}</td>
            <td>{{ $cat->parent->name ?? '---' }}</td>
            <td>
                @if($cat->is_active)
                    <span class="badge bg-success">فعال</span>
                @else
                    <span class="badge bg-danger">غیرفعال</span>
                @endif
            </td>
            <td>
                <div class="d-flex gap-1">
                    @can('access', 'item-categories.edit')
                    <button class="btn btn-sm btn-icon btn-outline-warning edit-cat-btn"
                        data-id="{{ $cat->id }}" data-name="{{ $cat->name }}" data-parent="{{ $cat->parent_id }}"
                        data-desc="{{ $cat->description }}" data-active="{{ $cat->is_active }}">
                        <i class="bx bx-edit"></i>
                    </button>
                    @endcan
                    @can('access', 'item-categories.delete')
                    <form action="{{ route('admin.item-categories.destroy', $cat) }}" method="POST" class="d-inline delete-form">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-icon btn-outline-danger"><i class="bx bx-trash"></i></button>
                    </form>
                    @endcan
                </div>
            </td>
        </tr>
        @empty
        <tr><td colspan="5" class="text-center text-muted py-5">دسته‌بندی یافت نشد.</td></tr>
        @endforelse
    </tbody>
</table>
<div class="card-footer d-flex justify-content-between">
    <small class="text-muted">نمایش {{ $categories->firstItem() ?? 0 }} تا {{ $categories->lastItem() ?? 0 }} از {{ $categories->total() }}</small>
    {{ $categories->links() }}
</div>