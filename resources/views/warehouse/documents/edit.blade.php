@extends('layouts.master')
@section('title', 'ویرایش سند — ' . $document->document_number)

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card shadow-none border">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="bx bx-edit me-1"></i> ویرایش سند
                <span class="fw-bold ms-1">{{ $document->document_number }}</span>
            </h5>
            <a href="{{ route('warehouse.documents.show', $document) }}" class="btn btn-sm btn-outline-secondary">
                <i class="bx bx-arrow-back me-1"></i> بازگشت
            </a>
        </div>

        @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show m-3 mb-0">
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <form action="{{ route('warehouse.documents.update', $document) }}" method="POST">
            @csrf @method('PUT')
            @include('warehouse.documents._form', ['isCreate' => false])
            <div class="card-footer text-end">
                <a href="{{ route('warehouse.documents.show', $document) }}" class="btn btn-label-secondary me-2">انصراف</a>
                <button type="submit" class="btn btn-primary"><i class="bx bx-save me-1"></i> ذخیره تغییرات</button>
            </div>
        </form>
    </div>
</div>
@endsection
