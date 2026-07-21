@extends('layouts.master')
@section('title', 'ویرایش درخواست خرید')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0"><i class="bx bx-edit me-2 text-warning"></i> ویرایش درخواست {{ $purchaseRequest->pr_number }}</h4>
        <a href="{{ route('warehouse.purchase-requests.show', $purchaseRequest) }}" class="btn btn-outline-secondary btn-sm"><i class="bx bx-arrow-back me-1"></i> بازگشت</a>
    </div>
    <form method="POST" action="{{ route('warehouse.purchase-requests.update', $purchaseRequest) }}">
        @csrf @method('PUT')
        @include('warehouse.purchase-requests._form')
    </form>
</div>
@endsection
