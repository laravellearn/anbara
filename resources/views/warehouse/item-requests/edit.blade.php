@extends('layouts.master')
@section('title', 'ویرایش درخواست کالا')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0"><i class="bx bx-edit me-2 text-warning"></i> ویرایش درخواست {{ $itemRequest->ir_number }}</h4>
        <a href="{{ route('warehouse.item-requests.show', $itemRequest) }}" class="btn btn-outline-secondary btn-sm"><i class="bx bx-arrow-back me-1"></i> بازگشت</a>
    </div>
    <form method="POST" action="{{ route('warehouse.item-requests.update', $itemRequest) }}">
        @csrf @method('PUT')
        @include('warehouse.item-requests._form')
    </form>
</div>
@endsection
