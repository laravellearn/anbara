@extends('layouts.master')

@section('title', 'ویرایش موقعیت')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">ویرایش {{ $location->title }}</h5>
        </div>
        <form action="{{ route('warehouse.warehouse-locations.update', $location) }}" method="POST">
            @csrf
            @method('PUT')
            @include('warehouse.warehouse-locations._form')
            <div class="card-footer text-end">
                <a href="{{ route('warehouse.warehouse-locations.index') }}" class="btn btn-label-secondary me-2">انصراف</a>
                <button type="submit" class="btn btn-warning">بروزرسانی</button>
            </div>
        </form>
    </div>
</div>
@endsection