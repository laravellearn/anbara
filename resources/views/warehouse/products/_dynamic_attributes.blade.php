@foreach($attributes as $attr)
    <div class="mb-3">
        <label>{{ $attr->name }} @if($attr->pivot->is_required) <span class="text-danger">*</span> @endif</label>
        @if($attr->type == 'text')
            <input type="text" name="attribute_values[{{ $attr->id }}][value]" class="form-control" value="{{ old('attribute_values.'.$attr->id.'.value', $oldValues[$attr->id] ?? '') }}">
        @elseif($attr->type == 'number')
            <input type="number" step="any" name="attribute_values[{{ $attr->id }}][value]" class="form-control" value="{{ old('attribute_values.'.$attr->id.'.value', $oldValues[$attr->id] ?? '') }}">
        @elseif($attr->type == 'select' && $attr->options)
            <select name="attribute_values[{{ $attr->id }}][value]" class="form-select">
                <option value="">انتخاب کنید</option>
                @foreach($attr->options as $option)
                    <option value="{{ $option }}" {{ (old('attribute_values.'.$attr->id.'.value', $oldValues[$attr->id] ?? '') == $option) ? 'selected' : '' }}>{{ $option }}</option>
                @endforeach
            </select>
        @endif
        <input type="hidden" name="attribute_values[{{ $attr->id }}][attribute_id]" value="{{ $attr->id }}">
    </div>
@endforeach