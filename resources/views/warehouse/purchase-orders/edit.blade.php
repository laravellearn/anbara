@extends('layouts.master')
@section('title', 'ویرایش سفارش خرید')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0"><i class="bx bx-edit me-2"></i> ویرایش سفارش — {{ $purchaseOrder->po_number }}</h4>
        <a href="{{ route('warehouse.purchase-orders.show', $purchaseOrder) }}" class="btn btn-outline-secondary btn-sm">
            <i class="bx bx-arrow-back me-1"></i> بازگشت
        </a>
    </div>

    @if($errors->any())
    <div class="alert alert-danger mb-4">
        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    <form method="POST" action="{{ route('warehouse.purchase-orders.update', $purchaseOrder) }}">
        @csrf @method('PUT')
        @php $existingItems = $purchaseOrder->items->toArray(); @endphp
        @include('warehouse.purchase-orders._form')
        <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary"><i class="bx bx-save me-1"></i> ذخیره تغییرات</button>
            <a href="{{ route('warehouse.purchase-orders.show', $purchaseOrder) }}" class="btn btn-outline-secondary">انصراف</a>
        </div>
    </form>
</div>
@endsection
