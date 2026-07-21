<div class="row g-3">
  <div class="col-md-4">
    <label class="form-label fw-medium">کد دارایی <span class="text-danger">*</span></label>
    <input type="text" name="asset_code" class="form-control @error('asset_code') is-invalid @enderror"
      value="{{ old('asset_code', $fixedAsset->asset_code ?? '') }}" required>
    @error('asset_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
  <div class="col-md-8">
    <label class="form-label fw-medium">عنوان دارایی <span class="text-danger">*</span></label>
    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
      value="{{ old('title', $fixedAsset->title ?? '') }}" required>
    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
  <div class="col-md-4">
    <label class="form-label fw-medium">شماره سریال</label>
    <input type="text" name="serial_number" class="form-control"
      value="{{ old('serial_number', $fixedAsset->serial_number ?? '') }}">
  </div>
  <div class="col-md-4">
    <label class="form-label fw-medium">دسته‌بندی</label>
    <select name="category" class="form-select">
      <option value="">انتخاب کنید...</option>
      @foreach($categories as $val => $label)
      <option value="{{ $val }}" @selected(old('category', $fixedAsset->category ?? '') === $val)>{{ $label }}</option>
      @endforeach
    </select>
  </div>
  <div class="col-md-4">
    <label class="form-label fw-medium">وضعیت <span class="text-danger">*</span></label>
    <select name="status" class="form-select @error('status') is-invalid @enderror" required>
      @foreach($statuses as $val => $label)
      <option value="{{ $val }}" @selected(old('status', $fixedAsset->status ?? 'active') === $val)>{{ $label }}</option>
      @endforeach
    </select>
    @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
  <div class="col-md-6">
    <label class="form-label fw-medium">محل فیزیکی</label>
    <input type="text" name="location" class="form-control"
      value="{{ old('location', $fixedAsset->location ?? '') }}" placeholder="دفتر مرکزی، انبار شماره ۲، ...">
  </div>
  <div class="col-md-3">
    <label class="form-label fw-medium">قیمت خرید (ریال)</label>
    <input type="number" name="purchase_price" class="form-control" min="0"
      value="{{ old('purchase_price', $fixedAsset->purchase_price ?? '') }}">
  </div>
  <div class="col-md-3">
    <label class="form-label fw-medium">ارزش جاری (ریال)</label>
    <input type="number" name="current_value" class="form-control" min="0"
      value="{{ old('current_value', $fixedAsset->current_value ?? '') }}">
  </div>
  <div class="col-md-3">
    <label class="form-label fw-medium">تاریخ خرید</label>
    <input type="date" name="purchase_date" class="form-control"
      value="{{ old('purchase_date', isset($fixedAsset) ? $fixedAsset->purchase_date?->format('Y-m-d') : '') }}">
  </div>
  <div class="col-md-3">
    <label class="form-label fw-medium">انقضای ضمانت</label>
    <input type="date" name="warranty_expiry" class="form-control"
      value="{{ old('warranty_expiry', isset($fixedAsset) ? $fixedAsset->warranty_expiry?->format('Y-m-d') : '') }}">
  </div>
  <div class="col-12">
    <label class="form-label fw-medium">توضیحات</label>
    <textarea name="description" class="form-control" rows="3">{{ old('description', $fixedAsset->description ?? '') }}</textarea>
  </div>
</div>
