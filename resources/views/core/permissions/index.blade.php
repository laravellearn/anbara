@extends('layouts.master')

@section('title', 'سطوح دسترسی')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">لیست تمام مجوزها</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>نام سیستمی</th>
                        <th>عنوان</th>
                        <th>توضیحات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($permissions as $perm)
                    <tr>
                        <td><code>{{ $perm->name }}</code></td>
                        <td>{{ $perm->title }}</td>
                        <td>{{ $perm->description ?? '---' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection