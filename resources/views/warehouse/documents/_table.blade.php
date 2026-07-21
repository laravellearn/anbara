@php use App\Models\WarehouseDocument; @endphp
<table class="table table-hover align-middle">
    <thead class="table-light">
        <tr>
            <th>شماره سند</th>
            <th>نوع</th>
            <th>انبار</th>
            <th>تاریخ</th>
            <th>اقلام</th>
            <th>وضعیت</th>
            <th>ثبت‌کننده</th>
            <th>عملیات</th>
        </tr>
    </thead>
    <tbody>
        @forelse($documents as $doc)
        <tr>
            <td><a href="{{ route('warehouse.documents.show', $doc) }}" class="fw-medium text-primary">{{ $doc->document_number }}</a></td>
            <td><span class="badge bg-label-{{ $doc->type_color }}">{{ $doc->type_label }}</span></td>
            <td>
                {{ $doc->warehouse->title ?? '—' }}
                @if($doc->type === WarehouseDocument::TYPE_TRANSFER && $doc->destinationWarehouse)
                <i class="bx bx-right-arrow-alt text-muted"></i>
                {{ $doc->destinationWarehouse->title }}
                @endif
            </td>
            <td><small>{{ $doc->document_date?->format('Y/m/d') }}</small></td>
            <td><span class="badge bg-label-info">{{ $doc->items_count ?? $doc->items()->count() }} قلم</span></td>
            <td><span class="badge bg-label-{{ $doc->status_color }}">{{ $doc->status_label }}</span></td>
            <td><small class="text-muted">{{ $doc->creator->name ?? '—' }}</small></td>
            <td>
                <div class="d-flex gap-1">
                    @can('access', 'warehouse-documents.view')
                    <a href="{{ route('warehouse.documents.show', $doc) }}" class="btn btn-sm btn-icon btn-outline-info" title="جزئیات">
                        <i class="bx bx-show"></i>
                    </a>
                    @endcan
                    @can('access', 'warehouse-documents.edit')
                    @if($doc->isEditable())
                    <a href="{{ route('warehouse.documents.edit', $doc) }}" class="btn btn-sm btn-icon btn-outline-warning" title="ویرایش">
                        <i class="bx bx-edit"></i>
                    </a>
                    @endif
                    @endcan
                    @can('access', 'warehouse-documents.submit')
                    @if($doc->status === 'draft')
                    <form action="{{ route('warehouse.documents.submit', $doc) }}" method="POST" class="d-inline">
                        @csrf
                        <button class="btn btn-sm btn-icon btn-outline-primary" title="ارسال برای تأیید"><i class="bx bx-send"></i></button>
                    </form>
                    @endif
                    @endcan
                    @can('access', 'warehouse-documents.approve')
                    @if($doc->isPending())
                    <form action="{{ route('warehouse.documents.approve', $doc) }}" method="POST" class="d-inline">
                        @csrf
                        <button class="btn btn-sm btn-icon btn-outline-success" title="تأیید"><i class="bx bx-check"></i></button>
                    </form>
                    <form action="{{ route('warehouse.documents.reject', $doc) }}" method="POST" class="d-inline">
                        @csrf
                        <button class="btn btn-sm btn-icon btn-outline-danger" title="رد"><i class="bx bx-x"></i></button>
                    </form>
                    @endif
                    @endcan
                    @can('access', 'warehouse-documents.delete')
                    @if($doc->isEditable())
                    <form action="{{ route('warehouse.documents.destroy', $doc) }}" method="POST" class="d-inline delete-form">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-icon btn-outline-danger" title="حذف"><i class="bx bx-trash"></i></button>
                    </form>
                    @endif
                    @endcan
                </div>
            </td>
        </tr>
        @empty
        <tr><td colspan="8" class="text-center text-muted py-5">سندی یافت نشد.</td></tr>
        @endforelse
    </tbody>
</table>
<div class="card-footer d-flex justify-content-between align-items-center flex-wrap gap-2">
    <small class="text-muted">نمایش {{ $documents->firstItem() ?? 0 }} تا {{ $documents->lastItem() ?? 0 }} از {{ $documents->total() }}</small>
    {{ $documents->links() }}
</div>
