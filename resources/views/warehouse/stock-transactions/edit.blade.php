@extends('layouts.master')
@section('title', 'ویرایش تراکنش')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card shadow-none border">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0"><i class="bx bx-edit me-1"></i> ویرایش تراکنش #{{ $stockTransaction->id }}</h5>
            <a href="{{ route('warehouse.stock-transactions.show', $stockTransaction) }}" class="btn btn-sm btn-outline-secondary">
                <i class="bx bx-arrow-back me-1"></i> بازگشت
            </a>
        </div>

        @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show m-3 mb-0">
            <strong><i class="bx bx-error-circle me-1"></i> خطا!</strong>
            <ul class="mb-0 mt-2">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <form action="{{ route('warehouse.stock-transactions.update', $stockTransaction) }}" method="POST">
            @csrf @method('PUT')
            @include('warehouse.stock-transactions._form', ['locations' => $locations])
            <div class="card-footer text-end">
                <a href="{{ route('warehouse.stock-transactions.show', $stockTransaction) }}" class="btn btn-label-secondary me-2">انصراف</a>
                <button type="submit" class="btn btn-primary"><i class="bx bx-save me-1"></i> ذخیره تغییرات</button>
            </div>
        </form>
    </div>
</div>
@endsection
