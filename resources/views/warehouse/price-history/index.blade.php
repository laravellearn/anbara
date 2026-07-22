@extends('layouts.master')
@section('title', 'تاریخچه قیمت کالا')

@section('content')
<div class="container-fluid py-4">

  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h4 class="mb-1"><i class="bx bx-history me-2 text-info"></i>تاریخچه قیمت کالا</h4>
      <p class="text-muted mb-0">قیمت خرید کالا به تفکیک تأمین‌کننده در طول زمان</p>
    </div>
  </div>

  <div class="row g-3">
    {{-- ─── فیلتر ─── --}}
    <div class="col-lg-4">
      <div class="card h-100">
        <div class="card-header"><h6 class="mb-0">جستجو</h6></div>
        <div class="card-body">
          <form method="GET" action="{{ route('warehouse.price-history.index') }}">
            <div class="mb-3">
              <label class="form-label small fw-semibold">کالا <span class="text-danger">*</span></label>
              <select name="product_id" class="form-select form-select-sm" required>
                <option value="">— انتخاب کالا —</option>
                @foreach($products as $p)
                <option value="{{ $p->id }}" {{ request('product_id')==$p->id?'selected':'' }}>{{ $p->title }}</option>
                @endforeach
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label small fw-semibold">تأمین‌کننده (اختیاری)</label>
              <select name="supplier_id" class="form-select form-select-sm">
                <option value="">— همه تأمین‌کنندگان —</option>
                @foreach($suppliers as $s)
                <option value="{{ $s->id }}" {{ request('supplier_id')==$s->id?'selected':'' }}>{{ $s->full_name }}</option>
                @endforeach
              </select>
            </div>
            <button type="submit" class="btn btn-primary btn-sm w-100">
              <i class="bx bx-search me-1"></i>نمایش تاریخچه
            </button>
          </form>
        </div>

        {{-- ─── ثبت قیمت جدید ─── --}}
        @can('access', 'price-history.create')
        @if($product)
        <div class="card-footer">
          <h6 class="small fw-semibold mb-2"><i class="bx bx-plus-circle me-1"></i>ثبت قیمت جدید</h6>
          <form method="POST" action="{{ route('warehouse.price-history.store') }}">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">
            <div class="mb-2">
              <select name="supplier_id" class="form-select form-select-sm" required>
                <option value="">— تأمین‌کننده —</option>
                @foreach($suppliers as $s)
                <option value="{{ $s->id }}">{{ $s->full_name }}</option>
                @endforeach
              </select>
            </div>
            <div class="row g-1 mb-2">
              <div class="col-7">
                <input type="number" name="unit_price" class="form-control form-control-sm" placeholder="قیمت واحد" step="0.01" required>
              </div>
              <div class="col-5">
                <input type="text" name="currency" class="form-control form-control-sm" value="IRR" placeholder="ارز">
              </div>
            </div>
            <div class="mb-2">
              <input type="date" name="price_date" class="form-control form-control-sm" value="{{ date('Y-m-d') }}" required>
            </div>
            <div class="mb-2">
              <input type="text" name="notes" class="form-control form-control-sm" placeholder="یادداشت (اختیاری)">
            </div>
            <button type="submit" class="btn btn-success btn-sm w-100">ثبت قیمت</button>
          </form>
        </div>
        @endif
        @endcan
      </div>
    </div>

    {{-- ─── نتایج ─── --}}
    <div class="col-lg-8">
      @if($product)
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h6 class="mb-0">{{ $product->title }} @if($supplier) — {{ $supplier->full_name }} @endif</h6>
          <span class="badge bg-info">{{ $rows->total() }} رکورد</span>
        </div>
        <div class="card-body p-0">
          @if($rows->isEmpty())
            <div class="text-center py-5 text-muted">
              <i class="bx bx-history fs-1"></i><p>قیمتی ثبت نشده</p>
            </div>
          @else
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th>تاریخ</th><th>تأمین‌کننده</th><th class="text-end">قیمت واحد</th><th>ارز</th><th>منبع</th><th>یادداشت</th><th>ثبت‌کننده</th><th></th>
                </tr>
              </thead>
              <tbody>
                @foreach($rows as $ph)
                <tr>
                  <td>{{ $ph->price_date->format('Y-m-d') }}</td>
                  <td>{{ $ph->supplier?->full_name ?? '—' }}</td>
                  <td class="text-end fw-semibold">{{ number_format($ph->unit_price, 2) }}</td>
                  <td><span class="badge bg-light text-dark">{{ $ph->currency }}</span></td>
                  <td>{{ $ph->source_label }}</td>
                  <td class="text-muted small">{{ $ph->notes ?? '—' }}</td>
                  <td class="small">{{ $ph->recorder?->name ?? '—' }}</td>
                  <td>
                    @can('access', 'price-history.delete')
                    <form method="POST" action="{{ route('warehouse.price-history.destroy', $ph) }}" class="d-inline">
                      @csrf @method('DELETE')
                      <button class="btn btn-xs btn-outline-danger btn-sm" onclick="return confirm('حذف؟')">
                        <i class="bx bx-trash"></i>
                      </button>
                    </form>
                    @endcan
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          <div class="p-3">{{ $rows->links() }}</div>
          @endif
        </div>
      </div>
      @else
      <div class="card">
        <div class="card-body text-center py-5 text-muted">
          <i class="bx bx-search-alt fs-1"></i>
          <p>ابتدا یک کالا انتخاب کنید</p>
        </div>
      </div>
      @endif
    </div>
  </div>

</div>
@endsection
