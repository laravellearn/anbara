@extends('layouts.app')
@section('title', 'وارد کردن اطلاعات از فایل')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="row justify-content-center">
    <div class="col-lg-7">

      @if(session('import_result'))
      @php $res = session('import_result'); @endphp
      <div class="alert alert-{{ count($res['errors'])>0?'warning':'success' }} mb-4">
        <strong>نتیجه import:</strong>
        {{ $res['created'] }} کالا ایجاد شد،
        {{ $res['updated'] }} کالا بروزرسانی شد.
        @if(count($res['errors'])>0)
          <hr>
          <ul class="mb-0 small">
            @foreach($res['errors'] as $e)<li>{{ $e }}</li>@endforeach
          </ul>
        @endif
      </div>
      @endif

      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">وارد کردن کالاها از CSV</h5>
          <a href="{{ route('warehouse.import.product-template') }}" class="btn btn-sm btn-outline-success">
            <i data-feather="download" class="me-1"></i>دانلود قالب
          </a>
        </div>
        <div class="card-body">
          <div class="alert alert-info small mb-3">
            <strong>راهنما:</strong> فایل CSV باید حاوی ستون‌های زیر باشد:<br>
            <code>title, sku, unit, sale_price, buy_price, min_stock, description</code><br>
            ستون <code>title</code> اجباری است. اگر <code>sku</code> موجود باشد، کالا بروزرسانی می‌شود.
          </div>
          <form action="{{ route('warehouse.import.products') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @error('file')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror
            <div class="mb-3">
              <label class="form-label">فایل CSV <span class="text-danger">*</span></label>
              <input type="file" name="file" class="form-control" accept=".csv,.txt" required>
              <div class="form-text">حداکثر ۵ مگابایت — فرمت CSV با encoding UTF-8</div>
            </div>
            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-primary">وارد کردن</button>
              <a href="{{ route('warehouse.barcode.index') }}" class="btn btn-outline-secondary">انصراف</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
