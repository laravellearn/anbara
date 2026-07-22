@extends('layouts.warehouse')
@section('title', 'انبارگردانی جدید')

@section('content')
<div class="container-fluid">
  <div class="d-flex align-items-center mb-4 gap-3">
    <a href="{{ route('warehouse.physical-inventory.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-right"></i></a>
    <h4 class="mb-0 fw-bold">انبارگردانی جدید</h4>
    <span class="badge bg-secondary-subtle text-secondary">{{ $number }}</span>
  </div>

  <div class="row justify-content-center">
    <div class="col-lg-6">
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent fw-semibold">اطلاعات پایه</div>
        <div class="card-body">
          <form method="POST" action="{{ route('warehouse.physical-inventory.store') }}">
            @csrf
            <div class="mb-3">
              <label class="form-label">انبار <span class="text-danger">*</span></label>
              <select name="warehouse_id" class="form-select @error('warehouse_id') is-invalid @enderror" required>
                <option value="">انتخاب انبار</option>
                @foreach($warehouses as $wh)
                  <option value="{{ $wh->id }}" @selected(old('warehouse_id')==$wh->id)>{{ $wh->title }}</option>
                @endforeach
              </select>
              @error('warehouse_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
              <label class="form-label">تاریخ انبارگردانی <span class="text-danger">*</span></label>
              <input type="date" name="inventory_date" class="form-control @error('inventory_date') is-invalid @enderror"
                value="{{ old('inventory_date', now()->format('Y-m-d')) }}" required>
              @error('inventory_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
              <label class="form-label">یادداشت</label>
              <textarea name="notes" class="form-control" rows="3" placeholder="توضیحات...">{{ old('notes') }}</textarea>
            </div>
            <div class="alert alert-info py-2 small mb-3">
              <i class="fas fa-info-circle me-1"></i>
              پس از ایجاد، موجودی سیستمی همه کالاهای انبار انتخابی به‌طور خودکار بارگذاری می‌شود.
            </div>
            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-primary flex-fill">
                <i class="fas fa-play me-1"></i> شروع انبارگردانی
              </button>
              <a href="{{ route('warehouse.physical-inventory.index') }}" class="btn btn-outline-secondary">انصراف</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
