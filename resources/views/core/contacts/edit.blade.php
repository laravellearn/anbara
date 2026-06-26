@extends('layouts.master')

@section('title', 'ویرایش مخاطب')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card shadow-none border">
        <div class="card-header">
            <h5 class="card-title">ویرایش {{ $contact->full_name ?? $contact->company_name }}</h5>
        </div>

        @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show m-3 mb-0" role="alert">
            <strong><i class="bx bx-error-circle me-1"></i> خطا!</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <form action="{{ route('contacts.update', $contact) }}" method="POST">
            @csrf 
            @method('PUT')
            @include('core.contacts._form', ['contact' => $contact])
            <div class="card-footer text-end">
                <a href="{{ route('contacts.index') }}" class="btn btn-label-secondary me-2">انصراف</a>
                <button type="submit" class="btn btn-warning">بروزرسانی</button>
            </div>
        </form>
    </div>
</div>
@endsection