@extends('layouts.master')
@section('title', 'فاکتور خرید جدید')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0"><i class="bx bx-receipt me-2 text-primary"></i> فاکتور خرید جدید</h4>
        <a href="{{ route('warehouse.purchase-invoices.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bx bx-arrow-back me-1"></i> بازگشت</a>
    </div>
    <form method="POST" action="{{ route('warehouse.purchase-invoices.store') }}">
        @csrf
        @include('warehouse.purchase-invoices._form')
    </form>
</div>
@endsection
