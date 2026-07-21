<div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
            <tr>
                <th>شماره PO</th>
                <th>تأمین‌کننده</th>
                <th>انبار</th>
                <th>تاریخ سفارش</th>
                <th>تحویل پیش‌بینی</th>
                <th>تعداد اقلام</th>
                <th>وضعیت</th>
                <th class="text-center">عملیات</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
            <tr>
                <td>
                    <a href="{{ route('warehouse.purchase-orders.show', $order) }}" class="fw-medium text-primary">
                        {{ $order->po_number }}
                    </a>
                    @if($order->reference_number)
                    <br><small class="text-muted">مرجع: {{ $order->reference_number }}</small>
                    @endif
                </td>
                <td>{{ $order->supplier?->name ?? '—' }}</td>
                <td>{{ $order->warehouse?->title ?? '—' }}</td>
                <td>{{ $order->order_date?->format('Y/m/d') }}</td>
                <td>
                    @if($order->expected_delivery_date)
                        @php $late = $order->expected_delivery_date->isPast() && !in_array($order->status, ['received','closed','cancelled']); @endphp
                        <span class="{{ $late ? 'text-danger fw-bold' : '' }}">
                            {{ $order->expected_delivery_date->format('Y/m/d') }}
                            @if($late) <i class="bx bx-error-circle text-danger"></i> @endif
                        </span>
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </td>
                <td><span class="badge bg-label-secondary">{{ $order->items_count }} قلم</span></td>
                <td>
                    <span class="badge bg-label-{{ $order->status_color }}">{{ $order->status_label }}</span>
                </td>
                <td class="text-center">
                    <div class="dropdown">
                        <button class="btn btn-sm btn-icon btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="bx bx-dots-vertical-rounded"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('warehouse.purchase-orders.show', $order) }}">
                                <i class="bx bx-show me-2"></i> مشاهده
                            </a></li>
                            @if($order->isEditable())
                            @can('access', 'purchase-orders.edit')
                            <li><a class="dropdown-item" href="{{ route('warehouse.purchase-orders.edit', $order) }}">
                                <i class="bx bx-edit me-2"></i> ویرایش
                            </a></li>
                            @endcan
                            @can('access', 'purchase-orders.confirm')
                            <li>
                                <form method="POST" action="{{ route('warehouse.purchase-orders.confirm', $order) }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-primary">
                                        <i class="bx bx-check-circle me-2"></i> تأیید
                                    </button>
                                </form>
                            </li>
                            @endcan
                            @can('access', 'purchase-orders.delete')
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('warehouse.purchase-orders.destroy', $order) }}"
                                      onsubmit="return confirm('حذف شود؟')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bx bx-trash me-2"></i> حذف
                                    </button>
                                </form>
                            </li>
                            @endcan
                            @endif
                            @if($order->canReceive())
                            @can('access', 'purchase-orders.receive')
                            <li><a class="dropdown-item text-success" href="{{ route('warehouse.purchase-orders.receive-form', $order) }}">
                                <i class="bx bx-import me-2"></i> ثبت دریافت
                            </a></li>
                            @endcan
                            @endif
                        </ul>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="text-center text-muted py-5">سفارشی یافت نشد.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
