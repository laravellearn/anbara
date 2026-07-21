@extends('layouts.master')
@section('title', 'درخواست کالا از انبار - جدید')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0"><i class="bx bx-task me-2 text-primary"></i> درخواست کالا از انبار - جدید</h4>
        <a href="{{ route('warehouse.item-requests.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bx bx-arrow-back me-1"></i> بازگشت</a>
    </div>
    <form method="POST" action="{{ route('warehouse.item-requests.store') }}">
        @csrf
        @include('warehouse.item-requests._form')
    </form>
</div>
@endsection
