@extends('layouts.master')

@section('title', 'ایجاد مخاطب جدید')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card shadow-none border">
        <div class="card-header">
            <h5 class="card-title">مخاطب جدید</h5>
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

        <form action="{{ route('contacts.store') }}" method="POST">
            @csrf
            @include('core.contacts._form', ['contact' => null])
            <div class="card-footer text-end">
                <a href="{{ route('contacts.index') }}" class="btn btn-label-secondary me-2">انصراف</a>
                <button type="submit" class="btn btn-primary">ذخیره</button>
            </div>
        </form>
    </div>
</div>
@endsection


@push('scripts')
<script>
    $(function() {
        // فقط اگر کشور انتخاب شده باشد (old پس از خطا) استان‌ها را لود کن
        var selectedCountry = $('#country_id').val();
        if (selectedCountry) {
            loadProvinces(selectedCountry, '{{ old('province_id') }}');
        }
        // تعریف تابع loadProvinces (همان که در _form بود) یا می‌توانید اینجا مستقیماً AJAX بزنید
        function loadProvinces(countryId, selectedId) {
            var $province = $('#province_id');
            $province.empty().append('<option value="">بارگذاری...</option>');
            $.get('{{ route('api.countries.provinces', ':id') }}'.replace(':id', countryId), function(data) {
                $province.empty().append('<option value="">انتخاب کنید</option>');
                $.each(data, function(i, item) {
                    $province.append('<option value="'+item.id+'">'+item.name+'</option>');
                });
                if (selectedId) {
                    $province.val(selectedId);
                }
                $province.trigger('change.select2');
            });
        }
    });
</script>
@endpush