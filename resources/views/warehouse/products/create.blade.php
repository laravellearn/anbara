@extends('layouts.master')

@section('title', 'ایجاد کالای جدید')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card shadow-none border">
        <div class="card-header"><h5>ایجاد کالای جدید</h5></div>

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

@push('scripts')
<script>
    let unitIndex = 0;
    $('#add-unit').click(function() {
        const row = `<div class="row mb-2 unit-row">
            <div class="col-5">
                <select name="measurement_units[${unitIndex}][id]" class="form-select">
                    <option value="">انتخاب واحد</option>
                    @foreach($measurementUnits as $mu)
                        <option value="{{ $mu->id }}">{{ $mu->title }} ({{ $mu->symbol }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-4">
                <input type="number" step="any" name="measurement_units[${unitIndex}][conversion_factor]" class="form-control" value="1">
            </div>
            <div class="col-2">
                <div class="form-check mt-2">
                    <input type="checkbox" name="measurement_units[${unitIndex}][is_default]" value="1" class="form-check-input">
                    <label class="form-check-label">پیش‌فرض</label>
                </div>
            </div>
            <div class="col-1">
                <button type="button" class="btn btn-sm btn-danger remove-unit"><i class="bx bx-x"></i></button>
            </div>
        </div>`;
        $('#additional-units').append(row);
        unitIndex++;
    });
    $(document).on('click', '.remove-unit', function() {
        $(this).closest('.unit-row').remove();
    });

    // بارگذاری ویژگی‌های داینامیک
    $('#product_type_id').on('change', function() {
        const typeId = $(this).val();
        if (typeId) {
            $.get('{{ route('warehouse.product-types.attributes', ':typeId') }}'.replace(':typeId', typeId), function(response) {
                $('#dynamic-attributes').html(response.html);
            });
        } else {
            $('#dynamic-attributes').html('');
        }
    });
</script>
@endpush