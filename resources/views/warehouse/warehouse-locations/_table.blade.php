<table class="table table-hover align-middle">
    <thead class="table-light">
        <tr>
            <th>#</th>
            <th>انبار</th>
            <th>کد</th>
            <th>نام</th>
            <th>نوع</th>
            <th>والد</th>
            <th>ظرفیت</th>
            <th>وضعیت</th>
            <th>عملیات</th>
        </tr>
    </thead>
    <tbody>
        @forelse($locations as $loc)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $loc->warehouse->name ?? '---' }}</td>
            <td>{{ $loc->code }}</td>
            <td>{{ $loc->name ?? '---' }}</td>
            <td>{{ $loc->type }}</td>
            <td>{{ $loc->parent->code ?? '---' }}</td>
            <td>{{ $loc->capacity ?? '---' }}</td>
            <td>
                @if($loc->is_active)
                    <span class="badge bg-success">فعال</span>
                @else
                    <span class="badge bg-danger">غیرفعال</span>
                @endif
            </td>
            <td>
                <div class="d-flex gap-1">
                    @can('access', 'warehouse-locations.edit')
                    <button class="btn btn-sm btn-icon btn-outline-warning edit-loc-btn"
                        data-id="{{ $loc->id }}" data-warehouse="{{ $loc->warehouse_id }}" data-parent="{{ $loc->parent_id }}"
                        data-code="{{ $loc->code }}" data-name="{{ $loc->name }}" data-type="{{ $loc->type }}"
                        data-capacity="{{ $loc->capacity }}" data-active="{{ $loc->is_active }}">
                        <i class="bx bx-edit"></i>
                    </button>
                    @endcan
                    @can('access', 'warehouse-locations.delete')
                    <form action="{{ route('admin.warehouse-locations.destroy', $loc) }}" method="POST" class="d-inline delete-form">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-icon btn-outline-danger"><i class="bx bx-trash"></i></button>
                    </form>
                    @endcan
                </div>
            </td>
        </tr>
        @empty
        <tr><td colspan="9" class="text-center text-muted py-5">موقعیتی یافت نشد.</td></tr>
        @endforelse
    </tbody>
</table>
<div class="card-footer d-flex justify-content-between">
    <small class="text-muted">نمایش {{ $locations->firstItem() ?? 0 }} تا {{ $locations->lastItem() ?? 0 }} از {{ $locations->total() }}</small>
    {{ $locations->links() }}
</div>