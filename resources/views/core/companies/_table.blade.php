<table class="table table-hover align-middle">
    <thead class="table-light">
        <tr>
            <th style="width: 50px;">#</th>
            <th class="cursor-pointer sortable" data-sort="title">
                نام سازمان
                <i class="bx bx-sort ms-1 text-muted sort-icon"></i>
            </th>
            <th>کد سازمان</th>
            <th>سازمان مادر</th>
            <th>وضعیت</th>
            <th>تاریخ ایجاد</th>
            <th>عملیات</th>
        </tr>
    </thead>
    <tbody>
        @forelse($companies as $cmp)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $cmp->name }}</td>
            <td>{{ $cmp->code }}</td>
            <td>{{ $cmp->parent->name ?? '---' }}</td>
            <td>
                @if($cmp->is_active)
                    <span class="badge bg-success">فعال</span>
                @else
                    <span class="badge bg-danger">غیرفعال</span>
                @endif
            </td>
            <td>
                {{ \Verta::instance($cmp->created_at)->format('Y/m/d-H:i:s') }}
            </td>
            <td>
                <div class="d-flex gap-1">
                    <button class="btn btn-sm btn-icon btn-outline-warning edit-company-btn"
                        data-bs-toggle="tooltip" title="ویرایش"
                        data-id="{{ $cmp->id }}"
                        data-title="{{ $cmp->title }}"
                        data-description="{{ $cmp->description }}"
                        data-parent-id="{{ $cmp->parent_id }}"
                        data-is-active="{{ $cmp->is_active ? 1 : 0 }}">
                        <i class="bx bx-edit"></i>
                    </button>
                    <form action="{{ route('companies.destroy', $cmp) }}" method="POST"
                        onsubmit="return confirm('آیا از حذف این سازمان اطمینان دارید؟')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-icon btn-outline-danger" title="حذف">
                            <i class="bx bx-trash"></i>
                        </button>
                    </form>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="7" class="text-center text-muted py-5">
                <i class="bx bx-buildings bx-lg d-block mb-2"></i>
                هیچ سازمانی یافت نشد.
            </td>
        </tr>
        @endforelse
    </tbody>
</table>
<div class="card-footer d-flex justify-content-between align-items-center">
    <small class="text-muted">
        نمایش {{ $companies->firstItem() ?? 0 }} تا {{ $companies->lastItem() ?? 0 }} از {{ $companies->total() }} سازمان
    </small>
    {{ $companies->appends(request()->query())->links() }}
</div>