{{-- resources/views/core/contacts/_form.blade.php --}}
<div class="card-body">
    <div class="row g-3">
<div class="col-md-6">
    <label class="form-label">نوع <span class="text-danger">*</span></label>
    <select name="type" class="form-select" required>
        @foreach(\App\Enums\ContactType::options() as $value => $label)
            <option value="{{ $value }}" {{ old('type', $contact->type->value ?? '') == $value ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
    </select>
</div>        <div class="col-md-6">
            <label class="form-label">کد</label>
            <input type="text" name="code" class="form-control" value="{{ old('code', $contact->code ?? '') }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">نام</label>
            <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $contact->first_name ?? '') }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">نام خانوادگی</label>
            <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $contact->last_name ?? '') }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">نام شرکت</label>
            <input type="text" name="company_name" class="form-control" value="{{ old('company_name', $contact->company_name ?? '') }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">موبایل</label>
            <input type="text" name="mobile" class="form-control" maxlength="20" value="{{ old('mobile', $contact->mobile ?? '') }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">تلفن</label>
            <input type="text" name="phone" class="form-control" maxlength="20" value="{{ old('phone', $contact->phone ?? '') }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">ایمیل</label>
            <input type="email" name="email" class="form-control" value="{{ old('email', $contact->email ?? '') }}">
        </div>

{{-- بخش انتخاب کشور (بدون تغییر) --}}
<div class="col-md-4">
    <label class="form-label">کشور</label>
    <select name="country_id" id="country_id" class="form-select select2">
        <option value="">انتخاب کنید</option>
        @foreach($countries as $country)
            <option value="{{ $country->id }}" {{ old('country_id', $contact->country_id ?? '') == $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
        @endforeach
    </select>
</div>

{{-- بخش استان --}}
<div class="col-md-4">
    <label class="form-label">استان</label>
    <select name="province_id" id="province_id" class="form-select select2">
        <option value="">ابتدا کشور را انتخاب کنید</option>
        @if(isset($provinces) && $provinces->isNotEmpty())
            @foreach($provinces as $province)
                <option value="{{ $province->id }}" {{ old('province_id', $contact->province_id ?? '') == $province->id ? 'selected' : '' }}>{{ $province->name }}</option>
            @endforeach
        @endif
    </select>
</div>

{{-- بخش شهر (ورودی دستی) --}}
<div class="col-md-4">
    <label class="form-label">شهر</label>
    <input type="text" name="city" class="form-control" value="{{ old('city', $contact->city ?? '') }}" placeholder="نام شهر را وارد کنید">
</div>
        <div class="col-12">
            <div class="form-check">
                <input type="hidden" name="is_active" value="0">
                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', $contact->is_active ?? true) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">فعال</label>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(function(){
        if ($.fn.select2) {
            $('.select2').select2({
                placeholder: 'جستجو...',
                language: "fa"
            });
        }

        // بارگذاری استان‌ها هنگام تغییر کشور
        $('#country_id').on('change', function() {
            var countryId = $(this).val();
            var $province = $('#province_id');
            $province.empty().append('<option value="">بارگذاری...</option>');
            if (countryId) {
                $.get('{{ route('api.countries.provinces', ':id') }}'.replace(':id', countryId), function(data) {
                    $province.empty().append('<option value="">انتخاب کنید</option>');
                    $.each(data, function(i, item) {
                        $province.append('<option value="'+item.id+'">'+item.name+'</option>');
                    });
                    $province.val('').trigger('change.select2'); // ریست انتخاب
                });
            } else {
                $province.empty().append('<option value="">ابتدا کشور را انتخاب کنید</option>');
            }
        });
    });

    
        // رویداد تغییر کشور
        $('#country_id').on('change', function() {
            var countryId = $(this).val();
            loadProvinces(countryId);
        });

        // بارگذاری اولیه
        var initialCountry = $('#country_id').val();
        if (initialCountry) {
            loadProvinces(initialCountry, '{{ old('province_id', $contact->province_id ?? '') }}');
        }
    });
        </script>
@endpush