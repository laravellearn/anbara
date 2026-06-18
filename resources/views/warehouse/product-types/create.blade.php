@extends('layouts.master')
@section('title', 'ایجاد نوع کالا')
@section('breadcrumb')
    @include('partials.breadcrumb', ['items' => [['label' => 'انبار', 'url' => '#'], ['label' => 'نوع کالا', 'url' => route('warehouse.product-types.index')], ['label' => 'ایجاد']]])
@endsection
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header"><h5>ایجاد نوع کالا</h5></div>
        <form action="{{ route('warehouse.product-types.store') }}" method="POST">
            @csrf
            @include('warehouse.product-types._form', ['productType' => null, 'attributes' => $attributes])
            <div class="card-footer text-end">
                <a href="{{ route('warehouse.product-types.index') }}" class="btn btn-label-secondary me-2">انصراف</a>
                <button type="submit" class="btn btn-primary">ذخیره</button>
            </div>
        </form>
    </div>
</div>
@endsection