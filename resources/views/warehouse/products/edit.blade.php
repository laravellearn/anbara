@extends('layouts.master')

@section('title', 'ویرایش کالا')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card shadow-none border">
        <div class="card-header"><h5>ویرایش {{ $product->title }}</h5></div>

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

        <form action="{{ route('warehouse.products.update', $product) }}" method="POST">
            @csrf @method('PUT')
            @include('warehouse.products._form')
            <div class="card-footer text-end">
                <a href="{{ route('warehouse.products.index') }}" class="btn btn-label-secondary me-2">انصراف</a>
                <button type="submit" class="btn btn-warning">بروزرسانی</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let unitIndex = {{ $product->measurementUnits->count() }};
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

    


      // بارگذاری ویژگی‌های داینامیک بر اساس نوع کالا
    $('#product_type_id').on('change', function() {
        const typeId = $(this).val();
        if (typeId) {
            let url = '{{ route('warehouse.product-types.attributes', ':typeId') }}'.replace(':typeId', typeId);
            // اگر محصول وجود دارد، product_id را هم بفرست تا مقادیر قبلی بارگذاری شوند
            @if(isset($product))
                url += '?product_id={{ $product->id }}';
            @endif
            $.get(url, function(response) {
                $('#dynamic-attributes').html(response.html);
            });
        } else {
            $('#dynamic-attributes').html('');
        }
    });

    // اگر محصول از قبل نوع کالا داشته باشد، ویژگی‌ها را بارگذاری کن
    @if($product->product_type_id)
        $('#product_type_id').trigger('change');
    @endif
</script>
@endpush