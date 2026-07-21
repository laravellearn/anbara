<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<title>چاپ {{ $type === 'qr' ? 'QR' : 'بارکد' }}</title>
<style>
  body { font-family: Tahoma, Arial, sans-serif; font-size: 11px; direction: rtl; }
  .grid { display: flex; flex-wrap: wrap; gap: 8px; padding: 10px; }
  .label-card { border: 1px solid #ccc; padding: 6px; text-align: center; width: 140px; break-inside: avoid; }
  .label-card p { margin: 3px 0 0; font-size: 10px; }
  .no-print { margin-bottom: 10px; }
  @media print { .no-print { display: none; } }
</style>
</head>
<body>
<div class="no-print">
  <button onclick="window.print()">چاپ</button>
  <button onclick="window.close()">بستن</button>
  <span style="margin-right:10px">تعداد در هر کالا: {{ $copies }}</span>
</div>

<div class="grid">
  @foreach($products as $product)
    @for($c = 0; $c < $copies; $c++)
    <div class="label-card" data-code="{{ $product->sku ?? $product->id }}" data-title="{{ $product->title }}" data-type="{{ $type }}">
      <div class="barcode-placeholder" style="height:60px;background:#f9f9f9;display:flex;align-items:center;justify-content:center">
        <svg id="svg-{{ $product->id }}-{{ $c }}"></svg>
      </div>
      <p>{{ $product->title }}</p>
      <p><strong>{{ $product->sku ?? $product->id }}</strong></p>
    </div>
    @endfor
  @endforeach
</div>

{{-- JsBarcode برای بارکد، QRCode.js برای QR --}}
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
document.querySelectorAll('.label-card').forEach(card => {
  const code  = card.dataset.code;
  const type  = card.dataset.type;
  const ph    = card.querySelector('.barcode-placeholder');

  if (type === 'qr') {
    ph.innerHTML = '';
    new QRCode(ph, { text: code, width: 80, height: 80, correctLevel: QRCode.CorrectLevel.M });
  } else {
    const svg = ph.querySelector('svg');
    try {
      JsBarcode(svg, code, { format: 'CODE128', width: 1.5, height: 50, displayValue: false, margin: 2 });
    } catch (e) {
      ph.innerHTML = '<span style="color:red;font-size:9px">کد نامعتبر</span>';
    }
  }
});
window.addEventListener('load', () => setTimeout(() => window.print(), 800));
</script>
</body>
</html>
