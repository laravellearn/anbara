<table class="table table-hover align-middle">
    <thead class="table-light">
        <tr>
            <th style="width: 50px;">#</th>
            <th class="cursor-pointer sortable" data-sort="name">
                نام سال مالی
                <i class="bx bx-sort ms-1 text-muted sort-icon @if(request('sort')=='name') active text-primary @endif"></i>
            </th>
            <th class="cursor-pointer sortable" data-sort="start_date">
                تاریخ شروع
                <i class="bx bx-sort ms-1 text-muted sort-icon"></i>
            </th>
            <th class="cursor-pointer sortable" data-sort="end_date">
                تاریخ پایان
                <i class="bx bx-sort ms-1 text-muted sort-icon"></i>
            </th>
            <th>وضعیت</th>
            <th>عملیات</th>
        </tr>
    </thead>
    <tbody>
        @forelse($fiscalYears as $fy)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $fy->name }}</td>
            <td>{{ \Verta::instance($fy->start_date)->format('Y/m/d') }}</td>
            <td>{{ \Verta::instance($fy->end_date)->format('Y/m/d') }}</td>
            <td>
                @if($fy->is_active)
                    <span class="badge bg-success">فعال</span>
                @elseif($fy->is_closed)
                    <span class="badge bg-secondary">بسته شده</span>
                @else
                    <span class="badge bg-warning">غیرفعال</span>
                @endif
            </td>
            <td>
                <div class="d-flex gap-1">
                    @if(!$fy->is_closed)
                    <button class="btn btn-sm btn-icon btn-outline-warning edit-fy-btn"
                        data-bs-toggle="tooltip" title="ویرایش"
                        data-id="{{ $fy->id }}"
                        data-name="{{ $fy->name }}"
                        data-start-date="{{ $fy->start_date->format('Y-m-d') }}"
                        data-end-date="{{ $fy->end_date->format('Y-m-d') }}"
                        data-is-active="{{ $fy->is_active ? 1 : 0 }}">
                        <i class="bx bx-edit"></i>
                    </button>
                    @endif
                    @if(!$fy->is_active && !$fy->is_closed)
                    <form action="{{ route('fiscal-years.activate', $fy) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-icon btn-outline-success" title="فعال‌سازی">
                            <i class="bx bx-play"></i>
                        </button>
                    </form>
                    @endif
                    @if($fy->is_active && !$fy->is_closed)
                    <form action="{{ route('fiscal-years.close', $fy) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-icon btn-outline-danger" title="بستن سال">
                            <i class="bx bx-lock"></i>
                        </button>
                    </form>
                    @endif
                    @if(!$fy->is_active && !$fy->is_closed)
                    <form action="{{ route('fiscal-years.destroy', $fy) }}" method="POST" class="d-inline delete-form">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-icon btn-outline-danger" title="حذف">
                            <i class="bx bx-trash"></i>
                        </button>
                    </form>
                    @endif
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="6" class="text-center text-muted py-5">
                <i class="bx bx-calendar-x bx-lg d-block mb-2"></i>
                هیچ سال مالی یافت نشد.
            </td>
        </tr>
        @endforelse
    </tbody>
</table>
<div class="card-footer d-flex justify-content-between align-items-center">
    <small class="text-muted">
        نمایش {{ $fiscalYears->firstItem() ?? 0 }} تا {{ $fiscalYears->lastItem() ?? 0 }} از {{ $fiscalYears->total() }} سال مالی
    </small>
    {{ $fiscalYears->appends(request()->query())->links() }}
</div>