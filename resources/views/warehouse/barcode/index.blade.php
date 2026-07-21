@extends('layouts.app')
@section('title', 'بارکد / QR کالاها')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="row g-3">
    {{-- اسکنر بارکد --}}
    <div class="col-12">
      <div class="card mb-2">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h6 class="mb-0">اسکن بارکد / QR</h6>
          <button class="btn btn-sm btn-outline-primary" id="startScan">شروع اسکن</button>
        </div>
        <div class="card-body">
          <div id="scannerWrapper" style="display:none;max-width:420px">
            <div id="interactive" class="viewport" style="position:relative;width:100%;"></div>
            <div id="scanResult" class="mt-2"></div>
          </div>
          <div class="input-group mt-2" style="max-width:400px">
            <input type="text" id="manualCode" class="form-control" placeholder="یا کد را دستی وارد کنید...">
            <button class="btn btn-outline-secondary" id="manualSearch">جستجو</button>
          </div>
          <div id="productCard" class="mt-3" style="display:none">
            <div class="alert alert-info" id="productInfo"></div>
          </div>
        </div>
      </div>
    </div>

    {{-- فیلتر --}}
    <div class="col-12">
      <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-4">
          <input type="text" name="search" class="form-control form-control-sm" placeholder="نام یا کد کالا..." value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
          <select name="category_id" class="form-select form-select-sm">
            <option value="">همه دسته‌بندی‌ها</option>
            @foreach($categories as $cat)
              <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->title }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2">
          <button class="btn btn-primary btn-sm w-100">فیلتر</button>
        </div>
      </form>
    </div>

    {{-- لیست کالا با checkbox --}}
    <div class="col-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <span>انتخاب کالاها برای چاپ بارکد</span>
          <div class="d-flex gap-2">
            <button class="btn btn-sm btn-outline-secondary" id="selectAll">انتخاب همه</button>
            <a href="#" class="btn btn-sm btn-outline-primary" id="printBarcode">چاپ بارکد</a>
            <a href="#" class="btn btn-sm btn-outline-success" id="printQr">چاپ QR</a>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table align-middle">
            <thead class="table-light">
              <tr>
                <th><input type="checkbox" id="checkAll"></th>
                <th>نام کالا</th><th>کد (SKU)</th><th>دسته‌بندی</th><th>موجودی</th>
              </tr>
            </thead>
            <tbody>
              @forelse($products as $product)
              <tr>
                <td><input type="checkbox" class="product-check" value="{{ $product->id }}" {{ $selected->contains($product->id) ? 'checked' : '' }}></td>
                <td>{{ $product->title }}</td>
                <td><code>{{ $product->sku ?? $product->id }}</code></td>
                <td>{{ $product->category?->title ?? '—' }}</td>
                <td>{{ $product->currentStock() }}</td>
              </tr>
              @empty
              <tr><td colspan="5" class="text-center text-muted">کالایی یافت نشد.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
        @if($products->hasPages())
        <div class="card-footer">{{ $products->links() }}</div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
{{-- Quagga2 برای اسکن بارکد --}}
<script src="https://cdn.jsdelivr.net/npm/@ericblade/quagga2@1/dist/quagga.min.js"></script>
<script>
(function () {
  const csrf = document.querySelector('meta[name=csrf-token]')?.content;

  // ─── اسکنر ────────────────────────────────────────────────────────────────
  let scanning = false;
  document.getElementById('startScan').addEventListener('click', function () {
    const wrapper = document.getElementById('scannerWrapper');
    if (!scanning) {
      wrapper.style.display = '';
      Quagga.init({
        inputStream: { type: 'LiveStream', target: document.getElementById('interactive'), constraints: { facingMode: 'environment' } },
        decoder: { readers: ['code_128_reader', 'ean_reader', 'ean_8_reader', 'code_39_reader', 'qr_reader'] }
      }, err => { if (err) { alert('دسترسی به دوربین مقدور نیست.'); return; } Quagga.start(); });
      Quagga.onDetected(data => {
        const code = data.codeResult.code;
        Quagga.stop(); wrapper.style.display = 'none'; scanning = false;
        this.textContent = 'شروع اسکن';
        fetchProduct(code);
      });
      scanning = true;
      this.textContent = 'توقف اسکن';
    } else {
      Quagga.stop(); wrapper.style.display = 'none';
      scanning = false; this.textContent = 'شروع اسکن';
    }
  });

  document.getElementById('manualSearch').addEventListener('click', () => {
    fetchProduct(document.getElementById('manualCode').value.trim());
  });
  document.getElementById('manualCode').addEventListener('keydown', e => {
    if (e.key === 'Enter') { e.preventDefault(); fetchProduct(e.target.value.trim()); }
  });

  function fetchProduct(code) {
    if (!code) return;
    fetch('/warehouse/barcode/scan?code=' + encodeURIComponent(code))
      .then(r => r.json())
      .then(p => {
        if (p.error) { showInfo('danger', p.error); return; }
        showInfo('info', `<strong>${p.title}</strong> | کد: ${p.sku} | واحد: ${p.unit ?? '—'} | موجودی: ${p.stock} | قیمت: ${Number(p.price).toLocaleString('fa-IR')}`);
      })
      .catch(() => showInfo('danger', 'خطا در ارتباط با سرور.'));
  }

  function showInfo(type, msg) {
    const el = document.getElementById('productInfo');
    el.className = 'alert alert-' + type;
    el.innerHTML = msg;
    document.getElementById('productCard').style.display = '';
  }

  // ─── انتخاب و چاپ ─────────────────────────────────────────────────────────
  document.getElementById('checkAll').addEventListener('change', function () {
    document.querySelectorAll('.product-check').forEach(cb => cb.checked = this.checked);
  });
  document.getElementById('selectAll').addEventListener('click', () => {
    document.querySelectorAll('.product-check').forEach(cb => cb.checked = true);
  });

  function getSelectedIds() {
    return [...document.querySelectorAll('.product-check:checked')].map(cb => cb.value).join(',');
  }

  document.getElementById('printBarcode').addEventListener('click', e => {
    e.preventDefault();
    const ids = getSelectedIds();
    if (!ids) { alert('ابتدا کالا انتخاب کنید.'); return; }
    window.open('/warehouse/barcode/print?type=barcode&ids=' + ids, '_blank');
  });

  document.getElementById('printQr').addEventListener('click', e => {
    e.preventDefault();
    const ids = getSelectedIds();
    if (!ids) { alert('ابتدا کالا انتخاب کنید.'); return; }
    window.open('/warehouse/barcode/print?type=qr&ids=' + ids, '_blank');
  });
})();
</script>
@endpush
