@extends('layouts.warehouse')
@section('title', $priceList->name)

@section('content')
<div class="container-fluid">

  <div class="d-flex align-items-center justify-content-between mb-4">
    <div class="d-flex align-items-center gap-3">
      <a href="{{ route('warehouse.price-lists.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-right"></i></a>
      <h4 class="mb-0 fw-bold">{{ $priceList->name }}</h4>
      <span class="badge bg-{{ $priceList->type_color }}-subtle text-{{ $priceList->type_color }}">{{ $priceList->type_label }}</span>
      <span class="badge bg-{{ $priceList->is_active ? 'success' : 'secondary' }}-subtle text-{{ $priceList->is_active ? 'success' : 'secondary' }}">
        {{ $priceList->is_active ? 'فعال' : 'غیرفعال' }}
      </span>
    </div>
    <div class="d-flex gap-2">
      @can('access','price-lists.create')
      <a href="{{ route('warehouse.price-lists.edit', $priceList) }}" class="btn btn-outline-primary btn-sm">
        <i class="fas fa-edit me-1"></i>ویرایش
      </a>
      @endcan
    </div>
  </div>

  <div class="row g-4 mb-4">
    <div class="col-md-4">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <h6 class="fw-semibold mb-3">اطلاعات لیست</h6>
          <div class="mb-2 d-flex justify-content-between">
            <span class="text-muted small">نوع</span>
            <span class="badge bg-{{ $priceList->type_color }}-subtle text-{{ $priceList->type_color }}">{{ $priceList->type_label }}</span>
          </div>
          <div class="mb-2 d-flex justify-content-between">
            <span class="text-muted small">اعتبار از</span>
            <span>{{ $priceList->valid_from ? \Morilog\Jalali\Jalalian::fromCarbon($priceList->valid_from)->format('Y/m/d') : '—' }}</span>
          </div>
          <div class="mb-2 d-flex justify-content-between">
            <span class="text-muted small">اعتبار تا</span>
            <span>{{ $priceList->valid_to ? \Morilog\Jalali\Jalalian::fromCarbon($priceList->valid_to)->format('Y/m/d') : '—' }}</span>
          </div>
          <div class="mb-2 d-flex justify-content-between">
            <span class="text-muted small">تعداد کالا</span>
            <span class="fw-bold">{{ $priceList->items->count() }}</span>
          </div>
          @if($priceList->description)
          <hr>
          <p class="text-muted small mb-0">{{ $priceList->description }}</p>
          @endif
        </div>
      </div>
    </div>
    <div class="col-md-8">
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent fw-semibold">قیمت‌های ثبت‌شده</div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th>#</th>
                  <th>کالا</th>
                  <th class="text-end">قیمت واحد</th>
                  <th class="text-center">حداقل مقدار</th>
                  <th class="text-center">تخفیف</th>
                  <th class="text-end text-success">قیمت نهایی</th>
                </tr>
              </thead>
              <tbody>
                @forelse($priceList->items as $i => $item)
                <tr>
                  <td class="text-muted small">{{ $i+1 }}</td>
                  <td class="fw-medium">{{ $item->product?->title }}</td>
                  <td class="text-end">{{ number_format($item->unit_price) }} ﷼</td>
                  <td class="text-center"><small>{{ number_format($item->min_quantity, 0) }}</small></td>
                  <td class="text-center">
                    @if($item->discount_percent > 0)
                      <span class="badge bg-warning-subtle text-warning">{{ $item->discount_percent }}%</span>
                    @else
                      <span class="text-muted">—</span>
                    @endif
                  </td>
                  <td class="text-end text-success fw-bold">{{ number_format($item->final_price) }} ﷼</td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-4">کالایی ثبت نشده.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
