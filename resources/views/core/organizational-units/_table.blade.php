<table class="table table-hover align-middle">
    <thead class="table-light">
        <tr>
            <th>#</th>
            <th>کد</th>
            <th>نام</th>
            <th>والد</th>
            <th>مدیر</th>
            <th>وضعیت</th>
            <th>عملیات</th>
        </tr>
    </thead>
    <tbody>
        @forelse($units as $unit)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $unit->code ?? '---' }}</td>
            <td>{{ $unit->name }}</td>
            <td>{{ $unit->parent->name ?? '---' }}</td>
            <td>{{ $unit->manager->name ?? '---' }}</td>
            <td>{!! $unit->is_active ? '<span class="badge bg-success">فعال</span>' : '<span class="badge bg-danger">غیرفعال</span>' !!}</td>
            <td>
                <div class="d-flex gap-1">
                    @can('access', 'organizational-units.edit')
                    <button class="btn btn-sm btn-icon btn-outline-warning edit-unit-btn"
                        data-id="{{ $unit->id }}"
                        data-name="{{ $unit->name }}"
                        data-code="{{ $unit->code }}"
                        data-parent="{{ $unit->parent_id }}"
                        data-manager="{{ $unit->manager_user_id }}"
                        data-desc="{{ $unit->description }}"
                        data-active="{{ $unit->is_active }}">
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
        <tr><td colspan="7" class="text-center text-muted py-5">واحدی یافت نشد.</td></tr>
        @endforelse
    </tbody>
</table>
<div class="card-footer d-flex justify-content-between">
    <small class="text-muted">نمایش {{ $units->firstItem() ?? 0 }} تا {{ $units->lastItem() ?? 0 }} از {{ $units->total() }}</small>
    {{ $units->links() }}
</div>