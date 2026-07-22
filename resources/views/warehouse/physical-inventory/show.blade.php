@extends('layouts.warehouse')
@section('title', 'انبارگردانی '.$physicalInventory->inventory_number)

@section('content')
<div class="container-fluid">

  <div class="d-flex align-items-center justify-content-between mb-4">
    <div class="d-flex align-items-center gap-3">
      <a href="{{ route('warehouse.physical-inventory.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-right"></i></a>
      <h4 class="mb-0 fw-bold">{{ $physicalInventory->inventory_number }}</h4>
      <span class="badge bg-{{ $physicalInventory->status_color }}-subtle text-{{ $physicalInventory->status_color }} fs-6">{{ $physicalInventory->status_label }}</span>
    </div>
    <div class="d-flex gap-2">
      @if($physicalInventory->isEditable())
        @can('access','physical-inventory.create')
        <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('countForm').submit()">
          <i class="fas fa-save me-1"></i>ذخیره شمارش‌ها
        </button>
        @endcan
      @endif
      @if($physicalInventory->canAdjust())
        @can('access','physical-inventory.adjust')
        <form method="POST" action="{{ route('warehouse.physical-inventory.adjust', $physicalInventory) }}"
          onsubmit="return confirm('سند تعدیل خودکار صادر و موجودی انبار اصلاح می‌شود. ادامه می‌دهید؟')">@csrf
          <button class="btn btn-success btn-sm"><i class="fas fa-magic me-1"></i>صدور سند تعدیل</button>
        </form>
        @endcan
      @endif
    </div>
  </div>

  {{-- اطلاعات --}}
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-3">
          <div class="text-muted small mb-1">انبار</div>
          <div class="fw-medium">{{ $physicalInventory->warehouse?->title }}</div>
        </div>
        <div class="col-md-3">
          <div class="text-muted small mb-1">تاریخ</div>
          <div>{{ \Morilog\Jalali\Jalalian::fromCarbon($physicalInventory->inventory_date)->format('Y/m/d') }}</div>
        </div>
        <div class="col-md-3">
          <div class="text-muted small mb-1">ایجادکننده</div>
          <div>{{ $physicalInventory->creator?->name }}</div>
        </div>
        <div class="col-md-3">
          <div class="text-muted small mb-1">مجموع مغایرت</div>
          @php $diff = $physicalInventory->items->whereNotNull('counted_quantity')->sum('difference'); @endphp
          <div class="fw-bold {{ $diff < 0 ? 'text-danger' : ($diff > 0 ? 'text-warning' : 'text-success') }}">
            {{ number_format($diff, 2) }}
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- جدول شمارش --}}
  <form method="POST" action="{{ route('warehouse.physical-inventory.save-counts', $physicalInventory) }}" id="countForm">
    @csrf
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-transparent fw-semibold">
        اقلام انبار — وارد کردن شمارش فیزیکی
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>#</th>
                <th>کالا</th>
                <th>واحد</th>
                <th class="text-center">موجودی سیستمی</th>
                <th class="text-center" style="width:160px">موجودی شمارش‌شده</th>
                <th class="text-center">مغایرت</th>
                <th class="text-end">ارزش مغایرت</th>
              </tr>
            </thead>
            <tbody>
              @foreach($physicalInventory->items as $i => $item)
              @php
                $diff = $item->counted_quantity !== null ? $item->counted_quantity - $item->system_quantity : null;
                $diffColor = $diff === null ? 'muted' : ($diff < 0 ? 'danger' : ($diff > 0 ? 'warning' : 'success'));
              @endphp
              <tr>
                <td class="text-muted small">{{ $i+1 }}</td>
                <td class="fw-medium">{{ $item->product?->title }}</td>
                <td><small class="text-muted">{{ $item->measurementUnit?->title ?? '—' }}</small></td>
                <td class="text-center fw-medium">{{ number_format($item->system_quantity, 2) }}</td>
                <td class="text-center">
                  @if($physicalInventory->isEditable())
                    <input type="number" name="counts[{{ $item->id }}]"
                      class="form-control form-control-sm text-center diff-input"
                      data-system="{{ $item->system_quantity }}"
                      data-row="{{ $i }}"
                      value="{{ $item->counted_quantity }}"
                      min="0" step="0.0001" placeholder="—">
                  @else
                    {{ $item->counted_quantity !== null ? number_format($item->counted_quantity, 2) : '—' }}
                  @endif
                </td>
                <td class="text-center">
                  <span class="text-{{ $diffColor }} fw-bold diff-cell-{{ $i }}">
                    {{ $diff !== null ? number_format($diff, 2) : '—' }}
                  </span>
                </td>
                <td class="text-end">
                  @if($diff !== null && $item->unit_price)
                    <span class="text-{{ $diffColor }}">{{ number_format(abs($diff) * $item->unit_price) }}</span>
                  @else
                    <span class="text-muted">—</span>
                  @endif
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </form>

</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.diff-input').forEach(input => {
  input.addEventListener('input', function() {
    const system = parseFloat(this.dataset.system) || 0;
    const counted = parseFloat(this.value);
    const cell = document.querySelector('.diff-cell-' + this.dataset.row);
    if (!isNaN(counted)) {
      const diff = counted - system;
      cell.textContent = diff.toFixed(2);
      cell.className = 'fw-bold diff-cell-' + this.dataset.row + ' text-' +
        (diff < 0 ? 'danger' : diff > 0 ? 'warning' : 'success');
    }
  });
});
</script>
@endpush
