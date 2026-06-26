<table class="table table-hover align-middle">
    <thead class="table-light">
        <tr>
            <th>#</th>
            <th>کد</th>
            <th>عنوان</th>
            <th>توضیحات</th>
            <th>وضعیت</th>
            <th>عملیات</th>
        </tr>
    </thead>
    <tbody>
        @forelse($costCenters as $cc)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $cc->code }}</td>
            <td>{{ $cc->title }}</td>
            <td>{{ Str::limit($cc->description, 50) ?? '---' }}</td>
            <td>{!! $cc->is_active ? '<span class="badge bg-success">فعال</span>' : '<span class="badge bg-danger">غیرفعال</span>' !!}</td>
            <td>
                <div class="d-flex gap-1">
                    @can('access', 'cost-centers.edit')
                    <button class="btn btn-sm btn-icon btn-outline-warning edit-cost-center-btn"
                        data-id="{{ $cc->id }}"
                        data-title="{{ $cc->title }}"
                        data-code="{{ $cc->code }}"
                        data-desc="{{ $cc->description }}"
                        data-active="{{ $cc->is_active }}">
                        <i class="bx bx-edit"></i>
                    </button>
                    @endcan
                    @can('access', 'cost-centers.delete')
                    <form action="{{ route('warehouse.cost-centers.destroy', $cc) }}" method="POST" class="d-inline delete-form">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-icon btn-outline-danger"><i class="bx bx-trash"></i></button>
                    </form>
                    @endcan
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="6" class="text-center text-muted py-5">مرکز هزینه‌ای یافت نشد.</td>
        </tr>
        @endforelse
    </tbody>
</table>
<div class="card-footer d-flex justify-content-between">
    <small class="text-muted">نمایش {{ $costCenters->firstItem() ?? 0 }} تا {{ $costCenters->lastItem() ?? 0 }} از {{ $costCenters->total() }}</small>
    {{ $costCenters->links() }}
</div>