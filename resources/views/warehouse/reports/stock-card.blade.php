@extends('layouts.warehouse')
@section('title', 'کارت موجودی کالا')

@section('content')
<div class="container-fluid">

  <div class="d-flex align-items-center mb-4 gap-3">
    <h4 class="mb-0 fw-bold"><i class="fas fa-address-card me-2 text-primary"></i>کارت موجودی کالا (Stock Card)</h4>
  </div>

  {{-- فیلتر --}}
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
      <form method="GET" class="row g-3 align-items-end">
        <div class="col-md-3">
          <label class="form-label small fw-semibold">کالا <span class="text-danger">*</span></label>
          <select name="product_id" class="form-select" required>
            <option value="">انتخاب کالا...</option>
            @foreach($products as $p)
            <option value="{{ $p->id }}" {{ request('product_id') == $p->id ? 'selected' : '' }}>{{ $p->title }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label small fw-semibold">انبار</label>
          <select name="warehouse_id" class="form-select">
            <option value="">همه انبارها</option>
            @foreach($warehouses as $wh)
            <option value="{{ $wh->id }}" {{ request('warehouse_id') == $wh->id ? 'selected' : '' }}>{{ $wh->title }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label small fw-semibold">از تاریخ</label>
          <input type="date" name="date_from" class="form-control" value="{{ request('date_from', now()->startOfMonth()->format('Y-m-d')) }}">
        </div>
        <div class="col-md-2">
          <label class="form-label small fw-semibold">تا تاریخ</label>
          <input type="date" name="date_to" class="form-control" value="{{ request('date_to', now()->format('Y-m-d')) }}">
        </div>
        <div class="col-md-3 d-flex gap-2">
          <button type="submit" class="btn btn-primary flex-fill">نمایش</button>
          @if($product)
          <a href="{{ request()->fullUrlWithQuery(['export'=>'excel']) }}" class="btn btn-outline-success">
            <i class="fas fa-file-excel"></i>
          </a>
          @endif
        </div>
      </form>
    </div>
  </div>

  @if($product)
  {{-- خلاصه --}}
  <div class="row g-3 mb-4">
    <div class="col-6 col-md-2">
      <div class="card text-center border-0 shadow-sm"><div class="card-body py-2">
        <div class="fs-5 fw-bold text-secondary">{{ number_format($openingBal, 2) }}</div>
        <div class="small text-muted">موجودی افتتاحی</div>
      </div></div>
    </div>
    <div class="col-6 col-md-2">
      <div class="card text-center border-0 shadow-sm"><div class="card-body py-2">
        <div class="fs-5 fw-bold text-success">{{ number_format($summary['total_in'], 2) }}</div>
        <div class="small text-muted">جمع ورودی</div>
      </div></div>
    </div>
    <div class="col-6 col-md-2">
      <div class="card text-center border-0 shadow-sm"><div class="card-body py-2">
        <div class="fs-5 fw-bold text-danger">{{ number_format($summary['total_out'], 2) }}</div>
        <div class="small text-muted">جمع خروجی</div>
      </div></div>
    </div>
    <div class="col-6 col-md-2">
      <div class="card text-center border-0 shadow-sm border-primary"><div class="card-body py-2">
        <div class="fs-5 fw-bold text-primary">{{ number_format($closingBal, 2) }}</div>
        <div class="small text-muted">موجودی پایانی</div>
      </div></div>
    </div>
    <div class="col-6 col-md-2">
      <div class="card text-center border-0 shadow-sm"><div class="card-body py-2">
        <div class="fs-5 fw-bold">{{ number_format($summary['value_in']) }}</div>
        <div class="small text-muted">ارزش ورودی</div>
      </div></div>
    </div>
    <div class="col-6 col-md-2">
      <div class="card text-center border-0 shadow-sm"><div class="card-body py-2">
        <div class="fs-5 fw-bold">{{ number_format($summary['tx_count']) }}</div>
        <div class="small text-muted">تعداد تراکنش</div>
      </div></div>
    </div>
  </div>

  {{-- جدول --}}
  <div class="card border-0 shadow-sm">
    <div class="card-header bg-transparent fw-semibold">
      کارت موجودی: <strong>{{ $product->title }}</strong>
      @if($product->sku) <span class="text-muted ms-2 small">({{ $product->sku }})</span>@endif
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-sm table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>تاریخ</th>
              <th>نوع تراکنش</th>
              <th>انبار</th>
              <th>توضیحات</th>
              <th class="text-end text-success">ورودی</th>
              <th class="text-end text-danger">خروجی</th>
              <th class="text-end">قیمت واحد</th>
              <th class="text-end">ارزش ردیف</th>
              <th class="text-end text-primary fw-bold">موجودی</th>
              <th>کاربر</th>
            </tr>
          </thead>
          <tbody>
            {{-- ردیف افتتاحی --}}
            <tr class="table-secondary">
              <td colspan="8" class="fw-semibold">موجودی افتتاحی بازه</td>
              <td class="text-end fw-bold text-primary">{{ number_format($openingBal, 2) }}</td>
              <td></td>
            </tr>
            @forelse($rows as $r)
            <tr>
              <td>{{ \Carbon\Carbon::parse($r->transaction_date)->format('Y-m-d') }}</td>
              <td>
                <span class="badge bg-{{ $r->qty_in > 0 ? 'success' : 'danger' }}-subtle text-{{ $r->qty_in > 0 ? 'success' : 'danger' }}">
                  {{ \App\Enums\InventoryTransactionType::from($r->type)->label() }}
                </span>
              </td>
              <td>{{ $r->warehouse_title }}</td>
              <td class="small text-muted">{{ $r->description }}</td>
              <td class="text-end text-success">{{ $r->qty_in > 0 ? number_format($r->qty_in, 2) : '—' }}</td>
              <td class="text-end text-danger">{{ $r->qty_out > 0 ? number_format($r->qty_out, 2) : '—' }}</td>
              <td class="text-end">{{ $r->unit_price ? number_format($r->unit_price) : '—' }}</td>
              <td class="text-end">{{ number_format($r->line_value) }}</td>
              <td class="text-end fw-bold text-primary">{{ number_format($r->balance, 2) }}</td>
              <td class="small">{{ $r->user_name }}</td>
            </tr>
            @empty
            <tr><td colspan="10" class="text-center text-muted py-4">تراکنشی در این بازه وجود ندارد.</td></tr>
            @endforelse
            {{-- ردیف پایانی --}}
            @if($rows->count())
            <tr class="table-primary fw-bold">
              <td colspan="8" class="text-end">موجودی پایان بازه:</td>
              <td class="text-end fs-5">{{ number_format($closingBal, 2) }}</td>
              <td></td>
            </tr>
            @endif
          </tbody>
        </table>
      </div>
    </div>
  </div>
  @else
  <div class="alert alert-info"><i class="fas fa-info-circle me-1"></i> ابتدا یک کالا را انتخاب کنید.</div>
  @endif

</div>
@endsection
