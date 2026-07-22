@extends('layouts.warehouse')
@section('title', 'برنامه‌ریزی خرید (Reorder)')

@section('content')
<div class="container-fluid">

  <div class="d-flex align-items-center justify-content-between mb-4">
    <h4 class="mb-0 fw-bold"><i class="fas fa-shopping-cart me-2 text-warning"></i>برنامه‌ریزی خرید — نقطه سفارش</h4>
    @can('access','reorder.manage')
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRuleModal">
      <i class="fas fa-plus me-1"></i> افزودن قانون
    </button>
    @endcan
  </div>

  {{-- پیشنهادهای خودکار --}}
  @if($suggestions->count())
  <div class="card border-0 shadow-sm border-warning mb-4">
    <div class="card-header bg-warning-subtle text-warning fw-semibold">
      <i class="fas fa-bell me-1"></i> {{ $suggestions->count() }} کالا نیاز به سفارش دارد
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>کالا</th>
              <th>انبار</th>
              <th class="text-center">موجودی فعلی</th>
              <th class="text-center">نقطه سفارش</th>
              <th class="text-center">مقدار پیشنهادی</th>
              <th>تأمین‌کننده پیشنهادی</th>
              <th>لید تایم</th>
              <th class="text-center">عملیات</th>
            </tr>
          </thead>
          <tbody>
            @foreach($suggestions as $s)
            <tr class="table-warning">
              <td class="fw-medium">{{ $s->product?->title }}</td>
              <td><small class="text-muted">{{ $s->warehouse?->title ?? 'همه انبارها' }}</small></td>
              <td class="text-center">
                <span class="badge bg-danger-subtle text-danger fw-bold">{{ number_format($s->current_stock, 2) }}</span>
              </td>
              <td class="text-center">{{ number_format($s->reorder_point, 2) }}</td>
              <td class="text-center">
                <span class="badge bg-primary-subtle text-primary fw-bold">{{ number_format($s->suggested_order, 2) }}</span>
              </td>
              <td><small>{{ $s->preferredSupplier?->name ?? '—' }}</small></td>
              <td><small class="text-muted">{{ $s->lead_time_days }} روز</small></td>
              <td class="text-center">
                <a href="{{ route('warehouse.purchase-orders.create') }}?product_id={{ $s->product_id }}&quantity={{ ceil($s->suggested_order) }}&supplier_id={{ $s->preferred_supplier_id }}"
                   class="btn btn-sm btn-outline-primary" title="ایجاد سفارش خرید">
                  <i class="fas fa-file-invoice me-1"></i>سفارش خرید
                </a>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
  @else
  <div class="alert alert-success border-0 shadow-sm mb-4">
    <i class="fas fa-check-circle me-2"></i> همه کالاها بالای نقطه سفارش هستند.
  </div>
  @endif

  {{-- قوانین --}}
  <div class="card border-0 shadow-sm">
    <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
      <span class="fw-semibold">قوانین نقطه سفارش</span>
      <form method="GET" class="d-flex gap-2">
        <select name="warehouse_id" class="form-select form-select-sm" style="width:180px">
          <option value="">همه انبارها</option>
          @foreach($warehouses as $wh)
            <option value="{{ $wh->id }}" @selected(request('warehouse_id')==$wh->id)>{{ $wh->title }}</option>
          @endforeach
        </select>
        <label class="form-check form-switch mb-0 d-flex align-items-center gap-1">
          <input class="form-check-input" type="checkbox" name="active_only" value="1" @checked(request('active_only')) onchange="this.form.submit()">
          <span class="small">فقط فعال</span>
        </label>
      </form>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>کالا</th>
              <th>انبار</th>
              <th class="text-center">نقطه سفارش</th>
              <th class="text-center">مقدار سفارش</th>
              <th class="text-center">ذخیره ایمنی</th>
              <th class="text-center">لید تایم</th>
              <th>تأمین‌کننده</th>
              <th class="text-center">فعال</th>
              <th class="text-center">عملیات</th>
            </tr>
          </thead>
          <tbody>
            @forelse($rules as $rule)
            <tr>
              <td class="fw-medium">{{ $rule->product?->title }}</td>
              <td><small class="text-muted">{{ $rule->warehouse?->title ?? 'همه انبارها' }}</small></td>
              <td class="text-center">{{ number_format($rule->reorder_point, 2) }}</td>
              <td class="text-center">{{ number_format($rule->reorder_quantity, 2) }}</td>
              <td class="text-center">{{ number_format($rule->safety_stock, 2) }}</td>
              <td class="text-center"><span class="badge bg-secondary-subtle text-secondary">{{ $rule->lead_time_days }} روز</span></td>
              <td><small>{{ $rule->preferredSupplier?->name ?? '—' }}</small></td>
              <td class="text-center">
                <span class="badge bg-{{ $rule->is_active ? 'success' : 'secondary' }}-subtle text-{{ $rule->is_active ? 'success' : 'secondary' }}">
                  {{ $rule->is_active ? 'فعال' : 'غیرفعال' }}
                </span>
              </td>
              <td class="text-center">
                @can('access','reorder.manage')
                <button class="btn btn-sm btn-icon btn-outline-primary"
                  onclick="editRule({{ $rule->id }}, {{ $rule->reorder_point }}, {{ $rule->reorder_quantity }}, {{ $rule->safety_stock }}, {{ $rule->lead_time_days }}, {{ (int)$rule->is_active }})"
                  title="ویرایش"><i class="fas fa-edit"></i></button>
                <form method="POST" action="{{ route('warehouse.reorder.destroy', $rule) }}" class="d-inline"
                  onsubmit="return confirm('این قانون حذف شود؟')">@csrf @method('DELETE')
                  <button class="btn btn-sm btn-icon btn-outline-danger"><i class="fas fa-trash"></i></button>
                </form>
                @endcan
              </td>
            </tr>
            @empty
            <tr><td colspan="9" class="text-center text-muted py-5">قانونی ثبت نشده است.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="p-3">{{ $rules->links() }}</div>
    </div>
  </div>
</div>

{{-- مودال افزودن قانون --}}
<div class="modal fade" id="addRuleModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="ruleModalTitle">افزودن قانون سفارش</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" action="{{ route('warehouse.reorder.store') }}" id="ruleForm">
        @csrf
        <input type="hidden" name="_method" id="ruleMethod" value="POST">
        <input type="hidden" name="rule_id" id="ruleId">
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">کالا <span class="text-danger">*</span></label>
            <select name="product_id" id="ruleProductId" class="form-select" required>
              <option value="">انتخاب کنید</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">انبار <small class="text-muted">(اختیاری — خالی = همه انبارها)</small></label>
            <select name="warehouse_id" id="ruleWarehouseId" class="form-select">
              <option value="">همه انبارها</option>
            </select>
          </div>
          <div class="row g-3">
            <div class="col-6">
              <label class="form-label">نقطه سفارش <span class="text-danger">*</span></label>
              <input type="number" name="reorder_point" id="ruleReorderPoint" class="form-control" min="0" step="0.01" required>
            </div>
            <div class="col-6">
              <label class="form-label">مقدار سفارش <span class="text-danger">*</span></label>
              <input type="number" name="reorder_quantity" id="ruleReorderQty" class="form-control" min="0.0001" step="0.01" required>
            </div>
            <div class="col-6">
              <label class="form-label">ذخیره ایمنی</label>
              <input type="number" name="safety_stock" id="ruleSafetyStock" class="form-control" min="0" step="0.01" value="0">
            </div>
            <div class="col-6">
              <label class="form-label">لید تایم (روز) <span class="text-danger">*</span></label>
              <input type="number" name="lead_time_days" id="ruleLeadTime" class="form-control" min="0" step="1" value="7" required>
            </div>
          </div>
          <div class="mt-3">
            <label class="form-label">تأمین‌کننده پیشنهادی</label>
            <select name="preferred_supplier_id" id="ruleSupplier" class="form-select">
              <option value="">—</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">انصراف</button>
          <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>ذخیره</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
// بارگذاری داده‌های فرم modal از API
document.getElementById('addRuleModal').addEventListener('show.bs.modal', function() {
  if (document.getElementById('ruleProductId').options.length > 1) return;
  fetch('{{ route("warehouse.reorder.form-data") }}')
    .then(r => r.json())
    .then(data => {
      const ps = document.getElementById('ruleProductId');
      data.products.forEach(p => ps.add(new Option(p.title + (p.sku?' ('+p.sku+')':''), p.id)));
      const ws = document.getElementById('ruleWarehouseId');
      data.warehouses.forEach(w => ws.add(new Option(w.title, w.id)));
      const ss = document.getElementById('ruleSupplier');
      data.suppliers.forEach(s => ss.add(new Option(s.name, s.id)));
    });
});

function editRule(id, rp, rq, ss, lt, active) {
  document.getElementById('ruleModalTitle').textContent = 'ویرایش قانون سفارش';
  document.getElementById('ruleId').value        = id;
  document.getElementById('ruleMethod').value    = 'PUT';
  document.getElementById('ruleForm').action     = '{{ route("warehouse.reorder.index") }}/' + id;
  document.getElementById('ruleReorderPoint').value = rp;
  document.getElementById('ruleReorderQty').value   = rq;
  document.getElementById('ruleSafetyStock').value  = ss;
  document.getElementById('ruleLeadTime').value     = lt;
  new bootstrap.Modal(document.getElementById('addRuleModal')).show();
}
</script>
@endpush
