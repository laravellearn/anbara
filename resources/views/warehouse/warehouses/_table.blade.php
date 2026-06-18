<table class="table table-hover align-middle">
    <thead class="table-light">
        <tr>
            <th>#</th>
            <th>نام</th>
            <th>کد</th>
            <th>آدرس</th>
            <th>مدیر</th>
            <th>ظرفیت</th>
            <th>وضعیت</th>
            <th>عملیات</th>
        </tr>
    </thead>
    <tbody>
        @forelse($warehouses as $wh)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $wh->name }}</td>
            <td>{{ $wh->code ?? '---' }}</td>
            <td>{{ $wh->address ?? '---' }}</td>
            <td>{{ $wh->manager->name ?? '---' }}</td>
            <td>{{ $wh->capacity ?? '---' }}</td>
            <td>
                @if($wh->is_active)
                    <span class="badge bg-success">فعال</span>
                @else
                    <span class="badge bg-danger">غیرفعال</span>
                @endif
            </td>
            <td>
                <div class="d-flex gap-1">
                    @can('access', 'warehouses.edit')
                    <button class="btn btn-sm btn-icon btn-outline-warning edit-warehouse-btn"
                        data-id="{{ $wh->id }}" data-name="{{ $wh->name }}" data-code="{{ $wh->code }}"
                        data-address="{{ $wh->address }}" data-manager="{{ $wh->manager_user_id }}"
                        data-capacity="{{ $wh->capacity }}" data-active="{{ $wh->is_active }}">
                        <i class="bx bx-edit"></i>
                    </button>
                    @endcan
                    @can('access', 'warehouses.delete')
                    <form action="{{ route('admin.warehouses.destroy', $wh) }}" method="POST" class="d-inline delete-form">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-icon btn-outline-danger"><i class="bx bx-trash"></i></button>
                    </form>
                    @endcan
                </div>
            </td>
        </tr>
        @empty
        <tr><td colspan="8" class="text-center text-muted py-5">انباری یافت نشد.</td></tr>
        @endforelse
    </tbody>
</table>
<div class="card-footer d-flex justify-content-between">
    <small class="text-muted">نمایش {{ $warehouses->firstItem() ?? 0 }} تا {{ $warehouses->lastItem() ?? 0 }} از {{ $warehouses->total() }}</small>
    {{ $warehouses->links() }}
</div>