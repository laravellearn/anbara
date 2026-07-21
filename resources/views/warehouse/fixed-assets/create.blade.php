@extends('layouts.master')
@section('title', 'ثبت دارایی جدید')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="card shadow-none border">
    <div class="card-header border-bottom d-flex align-items-center gap-2">
      <a href="{{ route('warehouse.fixed-assets.index') }}" class="btn btn-sm btn-icon btn-outline-secondary"><i class="bx bx-arrow-back"></i></a>
      <h5 class="card-title mb-0">ثبت دارایی جدید</h5>
    </div>
    <div class="card-body">
      <form action="{{ route('warehouse.fixed-assets.store') }}" method="POST">
        @csrf
        @include('warehouse.fixed-assets._form')
        <div class="mt-4 d-flex gap-2">
          <button type="submit" class="btn btn-primary"><i class="bx bx-save me-1"></i> ذخیره</button>
          <a href="{{ route('warehouse.fixed-assets.index') }}" class="btn btn-outline-secondary">انصراف</a>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
