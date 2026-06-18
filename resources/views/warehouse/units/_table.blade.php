<table class="table table-hover align-middle">
    <thead class="table-light">
        <tr>
            <th>#</th>
            <th>عنوان</th>
            <th>نماد</th>
            <th>وضعیت</th>
            <th>عملیات</th>
        </tr>
    </thead>
    <tbody>
        @forelse($units as $unit)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $unit->title }}</td>
            <td>{{ $unit->symbol ?? '---' }}</td>
            <td>
                @if($unit->is_active)
                    <span class="badge bg-success">فعال</span>
                @else
                    <span class="badge bg-danger">غیرفعال</span>
                @endif
            </td>
            <td>
                <div class="d-flex gap-1">
                    @can('access', 'units.edit')
                    <button class="btn btn-sm btn-icon btn-outline-warning edit-unit-btn"
                        data-id="{{ $unit->id }}" data-title="{{ $unit->title }}"
                        data-symbol="{{ $unit->symbol }}" data-active="{{ $unit->is_active }}">
                        <i class="bx bx-edit"></i>
                    </button>
                    @endcan
                    @can('access', 'units.delete')
                    <form action="{{ route('admin.units.destroy', $unit) }}" method="POST" class="d-inline delete-form">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-icon btn-outline-danger"><i class="bx bx-trash"></i></button>
                    </form>
                    @endcan
                </div>
            </td>
        </tr>
        @empty
        <tr><td colspan="5" class="text-center text-muted py-5">واحدی یافت نشد.</td></tr>
        @endforelse
    </tbody>
</table>
<div class="card-footer d-flex justify-content-between">
    <small class="text-muted">نمایش {{ $units->firstItem() ?? 0 }} تا {{ $units->lastItem() ?? 0 }} از {{ $units->total() }} واحد</small>
    {{ $units->links() }}
</div>