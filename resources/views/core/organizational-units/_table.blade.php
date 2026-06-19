<table class="table table-hover align-middle">
    <thead class="table-light">
        <tr>
            <th>#</th>
            <th>عنوان</th>
            <th>والد</th>
            <th>توضیحات</th>
            <th>وضعیت</th>
            <th>عملیات</th>
        </tr>
    </thead>
    <tbody>
        @forelse($units as $unit)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $unit->title }}</td>
            <td>{{ $unit->parent->title ?? '---' }}</td>
            <td>{{ Str::limit($unit->description, 50) ?? '---' }}</td>
            <td>{!! $unit->is_active ? '<span class="badge bg-success">فعال</span>' : '<span class="badge bg-danger">غیرفعال</span>' !!}</td>
            <td>
                <div class="d-flex gap-1">
                    @can('access', 'organizational-units.edit')
                    <button class="btn btn-sm btn-icon btn-outline-warning edit-unit-btn"
                        data-id="{{ $unit->id }}" data-title="{{ $unit->title }}" data-parent="{{ $unit->parent_id }}"
                        data-desc="{{ $unit->description }}" data-active="{{ $unit->is_active }}">
                        <i class="bx bx-edit"></i>
                    </button>
                    @endcan
                    @can('access', 'organizational-units.delete')
                    <form action="{{ route('organizational-units.destroy', $unit) }}" method="POST" class="d-inline delete-form">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-icon btn-outline-danger"><i class="bx bx-trash"></i></button>
                    </form>
                    @endcan
                </div>
            </td>
        </tr>
        @empty
        <tr><td colspan="6" class="text-center text-muted py-5">واحدی یافت نشد.</td></tr>
        @endforelse
    </tbody>
</table>
<div class="card-footer d-flex justify-content-between">
    <small class="text-muted">نمایش {{ $units->firstItem() ?? 0 }} تا {{ $units->lastItem() ?? 0 }} از {{ $units->total() }}</small>
    {{ $units->links() }}
</div>