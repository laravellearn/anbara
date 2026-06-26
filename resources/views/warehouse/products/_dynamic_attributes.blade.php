{{-- resources/views/warehouse/products/_dynamic_attributes.blade.php --}}
@if($attributes->count())
    @foreach($attributes as $attr)
        @php
            $required = $attr->pivot->is_required ?? false;
            $oldValue = $oldValues->get($attr->id, '');
        @endphp
        <div class="mb-3">
            <label>
                {{ $attr->name }}
                @if($required) <span class="text-danger">*</span> @endif
            </label>
            @if($attr->type == 'text')
                <input type="text" 
                       name="attribute_values[{{ $attr->id }}][value]" 
                       class="form-control dynamic-attr {{ $required ? 'dynamic-attr-required' : '' }}"
                       value="{{ old('attribute_values.'.$attr->id.'.value', $oldValue) }}"
                       {{ $required ? 'required' : '' }}>
            @elseif($attr->type == 'number')
                <input type="number" step="any" 
                       name="attribute_values[{{ $attr->id }}][value]" 
                       class="form-control dynamic-attr {{ $required ? 'dynamic-attr-required' : '' }}"
                       value="{{ old('attribute_values.'.$attr->id.'.value', $oldValue) }}"
                       {{ $required ? 'required' : '' }}>
            @elseif($attr->type == 'select' && $attr->options)
                @php $options = is_string($attr->options) ? json_decode($attr->options, true) : $attr->options; @endphp
                <select name="attribute_values[{{ $attr->id }}][value]" 
                        class="form-select dynamic-attr {{ $required ? 'dynamic-attr-required' : '' }}"
                        {{ $required ? 'required' : '' }}>
                    <option value="">انتخاب کنید</option>
                    @if(is_array($options))
                        @foreach($options as $option)
                            <option value="{{ $option }}" {{ old('attribute_values.'.$attr->id.'.value', $oldValue) == $option ? 'selected' : '' }}>{{ $option }}</option>
                        @endforeach
                    @endif
                </select>
            @endif
            <input type="hidden" name="attribute_values[{{ $attr->id }}][attribute_id]" value="{{ $attr->id }}">
        </div>
    @endforeach
@else
    <div class="text-muted">این نوع کالا ویژگی خاصی ندارد.</div>
@endif