<table class="table table-hover align-middle">
    <thead class="table-light">
        <tr>
            <th>#</th>
            <th>کالا</th>
            <th>انبار</th>
            <th>نوع</th>
            <th>مقدار</th>
            <th>قیمت واحد</th>
            <th>وضعیت</th>
            <th>تاریخ</th>
            <th>عملیات</th>
        </tr>
    </thead>
    <tbody>
        @forelse($transactions as $tx)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>
                <div class="fw-medium">{{ $tx->product->title ?? '—' }}</div>
                <small class="text-muted">{{ $tx->product->sku ?? '' }}</small>
            </td>
            <td>{{ $tx->warehouse->title ?? '—' }}</td>
            <td>
                <span class="badge bg-label-{{ $tx->type->color() }}">{{ $tx->type->label() }}</span>
            </td>
            <td class="{{ $tx->isInbound() ? 'text-success' : 'text-danger' }} fw-medium">
                {{ $tx->isInbound() ? '+' : '-' }}{{ number_format($tx->quantity, 2) }}
                <small class="text-muted">{{ $tx->measurementUnit->title ?? '' }}</small>
            </td>
            <td>{{ $tx->unit_price ? number_format($tx->unit_price) . ' ﷼' : '—' }}</td>
            <td>
                <span class="badge bg-label-{{ $tx->status->color() }}">{{ $tx->status->label() }}</span>
            </td>
            <td><small>{{ $tx->created_at->format('Y/m/d') }}</small></td>
            <td>
                <div class="d-flex gap-1">
                    @can('access', 'stock-transactions.view')
                    <a href="{{ route('warehouse.stock-transactions.show', $tx) }}" class="btn btn-sm btn-icon btn-outline-info" title="جزئیات">
                        <i class="bx bx-show"></i>
                    </a>
                    @endcan
                    @can('access', 'stock-transactions.edit')
                    @if($tx->status->value === 'draft')
                    <a href="{{ route('warehouse.stock-transactions.edit', $tx) }}" class="btn btn-sm btn-icon btn-outline-warning" title="ویرایش">
                        <i class="bx bx-edit"></i>
                    </a>
                    @endif
                    @endcan
                    @can('access', 'stock-transactions.approve')
                    @if($tx->status->value === 'pending')
                    <form action="{{ route('warehouse.stock-transactions.approve', $tx) }}" method="POST" class="d-inline">
                        @csrf
                        <button class="btn btn-sm btn-icon btn-outline-success" title="تأیید"><i class="bx bx-check"></i></button>
                    </form>
                    <form action="{{ route('warehouse.stock-transactions.reject', $tx) }}" method="POST" class="d-inline">
                        @csrf
                        <button class="btn btn-sm btn-icon btn-outline-danger" title="رد"><i class="bx bx-x"></i></button>
                    </form>
                    @endif
                    @endcan
                    @can('access', 'stock-transactions.delete')
                    @if($tx->status->value === 'draft')
                    <form action="{{ route('warehouse.stock-transactions.destroy', $tx) }}" method="POST" class="d-inline delete-form">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-icon btn-outline-danger" title="حذف"><i class="bx bx-trash"></i></button>
                    </form>
                    @endif
                    @endcan
                </div>
            </td>
        </tr>
        @empty
        <tr><td colspan="9" class="text-center text-muted py-5">تراکنشی یافت نشد.</td></tr>
        @endforelse
    </tbody>
</table>
<div class="card-footer d-flex justify-content-between">
    <small class="text-muted">نمایش {{ $transactions->firstItem() ?? 0 }} تا {{ $transactions->lastItem() ?? 0 }} از {{ $transactions->total() }}</small>
    {{ $transactions->links() }}
</div>
