@extends('layouts.master')

@section('title', 'ایجاد کالای جدید')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">ایجاد کالای جدید</h5>
        </div>
        <form action="{{ route('warehouse.products.store') }}" method="POST">
            @csrf
            @include('warehouse.products._form')
            <div class="card-footer text-end">
                <a href="{{ route('warehouse.products.index') }}" class="btn btn-label-secondary me-2">انصراف</a>
                <button type="submit" class="btn btn-primary">ذخیره</button>
            </div>
        </form>
    </div>
</div>
@endsection