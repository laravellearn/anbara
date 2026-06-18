@extends('layouts.master')
@section('title', 'ویرایش نقش')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header"><h5>ویرایش {{ $role->title }}</h5></div>
        <form action="{{ route('admin.roles.update', $role) }}" method="POST">
            @csrf @method('PUT')
            @include('roles._form')
            <div class="card-footer text-end">
                <a href="{{ route('admin.roles.index') }}" class="btn btn-label-secondary me-2">انصراف</a>
                <button type="submit" class="btn btn-warning">بروزرسانی</button>
            </div>
        </form>
    </div>
</div>
@endsection
@push('scripts')
<script>
    // همان اسکریپت‌های انتخاب گروهی که در index دارید، اینجا هم کپی شود
    $(function() {
        function updateGroupCounter(groupSlug) { /* ... مشابه */ }
        function updateAllCounters() { /* ... */ }
        $('.select-group-btn').on('click', function() { /* ... */ });
        $('.deselect-group-btn').on('click', function() { /* ... */ });
        $('#selectAllGroups').on('click', function() { /* ... */ });
        $('#deselectAllGroups').on('click', function() { /* ... */ });
        $(document).on('change', '.permission-checkbox', function() { /* ... */ });
        
        // مقداردهی اولیه شمارنده‌ها با توجه به rolePermissions
        updateAllCounters();
    });
</script>
@endpush