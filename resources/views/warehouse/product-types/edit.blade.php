@extends('layouts.master')
@section('title', 'ویرایش نوع کالا')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header"><h5>ویرایش {{ $productType->title }}</h5></div>
        <form action="{{ route('warehouse.product-types.update', $productType) }}" method="POST">
            @csrf @method('PUT')
            @include('warehouse.product-types._form', ['productType' => $productType, 'attributes' => $attributes])
            <div class="card-footer text-end">
                <a href="{{ route('warehouse.product-types.index') }}" class="btn btn-label-secondary me-2">انصراف</a>
                <button type="submit" class="btn btn-warning">بروزرسانی</button>
            </div>
        </form>
    </div>
</div>
@endsection