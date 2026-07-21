<script>
(function () {
  const body     = document.getElementById('itemsBody');
  const addBtn   = document.getElementById('addRow');
  const products = @json($products->map(fn($p) => ['id'=>$p->id,'title'=>$p->title,'price'=>$p->sale_price??0]));
  const units    = @json($units->map(fn($u) => ['id'=>$u->id,'title'=>$u->title]));

  function rowIndex() { return body.querySelectorAll('.item-row').length; }

  function buildProductOptions(selectedId = '') {
    return products.map(p => `<option value="${p.id}" data-price="${p.price}" ${p.id == selectedId ? 'selected' : ''}>${p.title}</option>`).join('');
  }

  function buildUnitOptions(selectedId = '') {
    return `<option value="">—</option>` + units.map(u => `<option value="${u.id}" ${u.id == selectedId ? 'selected' : ''}>${u.title}</option>`).join('');
  }

  function addRow() {
    const idx = rowIndex();
    const tr  = document.createElement('tr');
    tr.className = 'item-row';
    tr.innerHTML = `
      <td><select name="items[${idx}][product_id]" class="form-select form-select-sm product-sel" required>
        <option value="">انتخاب کالا</option>${buildProductOptions()}</select></td>
      <td><select name="items[${idx}][measurement_unit_id]" class="form-select form-select-sm">${buildUnitOptions()}</select></td>
      <td><input type="number" name="items[${idx}][quantity]" class="form-control form-control-sm qty" step="0.001" min="0.001" required></td>
      <td><input type="number" name="items[${idx}][unit_price]" class="form-control form-control-sm price" step="1" min="0" required></td>
      <td><input type="number" name="items[${idx}][discount_amount]" class="form-control form-control-sm disc" step="1" min="0" value="0"></td>
      <td><span class="row-total text-muted">—</span></td>
      <td><button type="button" class="btn btn-xs btn-icon btn-outline-danger remove-row"><i data-feather="trash-2"></i></button></td>`;
    body.appendChild(tr);
    feather.replace();
    bindRow(tr);
    recalc();
  }

  function bindRow(tr) {
    const sel   = tr.querySelector('.product-sel');
    const qty   = tr.querySelector('.qty');
    const price = tr.querySelector('.price');
    const disc  = tr.querySelector('.disc');

    sel.addEventListener('change', () => {
      const opt = sel.selectedOptions[0];
      if (opt && opt.dataset.price) price.value = opt.dataset.price;
      recalc();
    });

    [qty, price, disc].forEach(el => el.addEventListener('input', recalc));

    tr.querySelector('.remove-row').addEventListener('click', () => {
      if (body.querySelectorAll('.item-row').length > 1) { tr.remove(); recalc(); }
    });
  }

  function recalc() {
    let subtotal = 0;
    body.querySelectorAll('.item-row').forEach(tr => {
      const qty   = parseFloat(tr.querySelector('.qty')?.value)   || 0;
      const price = parseFloat(tr.querySelector('.price')?.value) || 0;
      const disc  = parseFloat(tr.querySelector('.disc')?.value)  || 0;
      const total = Math.max(0, qty * price - disc);
      tr.querySelector('.row-total').textContent = total.toLocaleString('fa-IR');
      subtotal += total;
    });

    const discPct = parseFloat(document.getElementById('discPct')?.value) || 0;
    const taxPct  = parseFloat(document.getElementById('taxPct')?.value)  || 0;
    const discAmt = subtotal * discPct / 100;
    const after   = subtotal - discAmt;
    const taxAmt  = after * taxPct / 100;
    const total   = after + taxAmt;

    const fmt = v => v.toLocaleString('fa-IR');
    document.getElementById('sumSubtotal').textContent = fmt(subtotal);
    document.getElementById('sumDiscount').textContent = fmt(discAmt);
    document.getElementById('sumTax').textContent      = fmt(taxAmt);
    document.getElementById('sumTotal').textContent    = fmt(total);
  }

  // bind existing rows
  body.querySelectorAll('.item-row').forEach(bindRow);

  addBtn.addEventListener('click', addRow);

  document.getElementById('discPct')?.addEventListener('input', recalc);
  document.getElementById('taxPct')?.addEventListener('input', recalc);

  recalc();
})();
</script>
