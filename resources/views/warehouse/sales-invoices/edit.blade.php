@extends('layouts.app')
@section('title', 'ویرایش فاکتور فروش')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="row justify-content-center">
    <div class="col-12 col-xl-11">
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">ویرایش فاکتور {{ $salesInvoice->invoice_number }}</h5>
        </div>
        <form action="{{ route('warehouse.sales-invoices.update', $salesInvoice) }}" method="POST" id="invoiceForm">
          @csrf @method('PUT')
          <div class="card-body">
            @include('warehouse.sales-invoices._form')
          </div>
          <div class="card-footer d-flex justify-content-end gap-2">
            <a href="{{ route('warehouse.sales-invoices.show', $salesInvoice) }}" class="btn btn-outline-secondary">انصراف</a>
            <button type="submit" class="btn btn-primary">ذخیره تغییرات</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
@include('warehouse.sales-invoices._scripts')
@endpush
