@extends('layouts.app')
@section('title', 'سریال‌ها و بچ‌ها')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="row g-3 mb-4">
    @foreach([['کل','total','primary'],['موجود','in_stock','success'],['منقضی ۳۰ روز','expiring_soon','warning'],['منقضی شده','expired','danger']] as [$l,$k,$c])
    <div class="col-6 col-xl-3">
      <div class="card text-center"><div class="card-body">
        <h6 class="text-muted">{{ $l }}</h6><h3 class="text-{{ $c }}">{{ number_format($stats[$k]) }}</h3>
      </div></div>
    </div>
    @endforeach
  </div>

  <div class="card mb-3">
    <div class="card-body">
      <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-2">
          <input type="text" name="search" class="form-control form-control-sm" placeholder="سریال / بچ..." value="{{ request('search') }}">
        </div>
        <div class="col-md-2">
          <select name="product_id" class="form-select form-select-sm">
            <option value="">همه کالاها</option>
            @foreach($products as $p)
              <option value="{{ $p->id }}" {{ request('product_id')==$p->id?'selected':'' }}>{{ $p->title }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2">
          <select name="status" class="form-select form-select-sm">
            <option value="">همه وضعیت‌ها</option>
            @foreach(\App\Models\SerialBatch::statusLabels() as $k=>$v)
              <option value="{{ $k }}" {{ request('status')===$k?'selected':'' }}>{{ $v }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2">
          <select name="type" class="form-select form-select-sm">
            <option value="">سریال و بچ</option>
            <option value="serial" {{ request('type')==='serial'?'selected':'' }}>سریال</option>
            <option value="batch" {{ request('type')==='batch'?'selected':'' }}>بچ</option>
          </select>
        </div>
        <div class="col-md-2">
          <select name="expiring_days" class="form-select form-select-sm">
            <option value="">بدون فیلتر انقضا</option>
            <option value="7" {{ request('expiring_days')=='7'?'selected':'' }}>منقضی ۷ روز</option>
            <option value="30" {{ request('expiring_days')=='30'?'selected':'' }}>منقضی ۳۰ روز</option>
          </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
          <button class="btn btn-primary btn-sm flex-fill">فیلتر</button>
          <a href="{{ route('warehouse.serial-batch.index') }}" class="btn btn-outline-secondary btn-sm">پاک</a>
        </div>
      </form>
    </div>
  </div>

  <div class="row g-3">
    {{-- فرم ثبت جدید --}}
    <div class="col-lg-4">
      <div class="card">
        <div class="card-header"><h6 class="mb-0">ثبت سریال / بچ جدید</h6></div>
        <div class="card-body">
          <form action="{{ route('warehouse.serial-batch.store') }}" method="POST">
            @csrf
            <div class="mb-2">
              <label class="form-label small">کالا *</label>
              <select name="product_id" class="form-select form-select-sm" required>
                <option value="">انتخاب</option>
                @foreach($products as $p)
                  <option value="{{ $p->id }}">{{ $p->title }}</option>
                @endforeach
              </select>
            </div>
            <div class="mb-2">
              <label class="form-label small">انبار</label>
              <select name="warehouse_id" class="form-select form-select-sm">
                <option value="">انتخاب</option>
                @foreach($warehouses as $w)
                  <option value="{{ $w->id }}">{{ $w->title }}</option>
                @endforeach
              </select>
            </div>
            <div class="mb-2">
              <label class="form-label small">نوع ردیابی *</label>
              <select name="tracking_type" class="form-select form-select-sm" id="trackingType" required>
                <option value="batch">بچ (دسته)</option>
                <option value="serial">سریال</option>
              </select>
            </div>
            <div class="mb-2" id="batchField">
              <label class="form-label small">شماره بچ *</label>
              <input type="text" name="batch_number" class="form-control form-control-sm">
            </div>
            <div class="mb-2 d-none" id="serialField">
              <label class="form-label small">شماره سریال *</label>
              <input type="text" name="serial_number" class="form-control form-control-sm">
            </div>
            <div class="row g-2 mb-2">
              <div class="col-6">
                <label class="form-label small">تاریخ تولید</label>
                <input type="date" name="manufacture_date" class="form-control form-control-sm">
              </div>
              <div class="col-6">
                <label class="form-label small">تاریخ انقضا</label>
                <input type="date" name="expiry_date" class="form-control form-control-sm">
              </div>
            </div>
            <div class="mb-2">
              <label class="form-label small">مقدار *</label>
              <input type="number" name="quantity" class="form-control form-control-sm" step="0.001" min="0.001" value="1" required>
            </div>
            <div class="mb-3">
              <label class="form-label small">توضیحات</label>
              <textarea name="notes" class="form-control form-control-sm" rows="2"></textarea>
            </div>
            <button type="submit" class="btn btn-primary btn-sm w-100">ثبت</button>
          </form>
        </div>
      </div>
    </div>

    {{-- جدول --}}
    <div class="col-lg-8">
      <div class="card">
        <div class="table-responsive">
          <table class="table align-middle table-hover">
            <thead class="table-light">
              <tr><th>کالا</th><th>شماره</th><th>نوع</th><th>انقضا</th><th class="text-end">مقدار</th><th>وضعیت</th><th></th></tr>
            </thead>
            <tbody>
              @forelse($items as $item)
              @php $sc=\App\Models\SerialBatch::statusColors();$sl=\App\Models\SerialBatch::statusLabels(); @endphp
              <tr class="{{ $item->expiry_date && $item->expiry_date->isPast() && $item->status==='in_stock' ? 'table-danger' : '' }}">
                <td>{{ $item->product?->title }}</td>
                <td><code>{{ $item->serial_number ?? $item->batch_number }}</code></td>
                <td><span class="badge bg-label-{{ $item->tracking_type==='serial'?'info':'primary' }}">{{ $item->tracking_type==='serial'?'سریال':'بچ' }}</span></td>
                <td>
                  @if($item->expiry_date)
                    <span class="{{ $item->expiry_date->isPast()?'text-danger fw-bold':($item->expiry_date->diffInDays(now())<=30?'text-warning':'') }}">
                      {{ $item->expiry_date->format('Y-m-d') }}
                    </span>
                  @else —
                  @endif
                </td>
                <td class="text-end">{{ number_format($item->quantity,2) }}</td>
                <td><span class="badge bg-label-{{ $sc[$item->status]??'secondary' }}">{{ $sl[$item->status]??$item->status }}</span></td>
                <td>
                  <form action="{{ route('warehouse.serial-batch.update',$item) }}" method="POST" class="d-inline">
                    @csrf @method('PUT')
                    <select name="status" class="form-select form-select-sm d-inline w-auto" onchange="this.form.submit()">
                      @foreach($sl as $k=>$v)
                        <option value="{{ $k }}" {{ $item->status===$k?'selected':'' }}>{{ $v }}</option>
                      @endforeach
                    </select>
                  </form>
                </td>
              </tr>
              @empty
              <tr><td colspan="7" class="text-center text-muted py-4">رکوردی یافت نشد.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
        @if($items->hasPages())<div class="card-footer">{{ $items->links() }}</div>@endif
      </div>
    </div>
  </div>
</div>
@endsection
@push('scripts')
<script>
document.getElementById('trackingType').addEventListener('change', function(){
  const isSer = this.value === 'serial';
  document.getElementById('batchField').classList.toggle('d-none', isSer);
  document.getElementById('serialField').classList.toggle('d-none', !isSer);
});
</script>
@endpush
