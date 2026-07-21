<tr>
    <td>
        <select name="items[{{ $idx }}][product_id]" class="form-select form-select-sm" required>
            <option value="">انتخاب کالا...</option>
            @foreach($products as $p)
            <option value="{{ $p->id }}" @selected(($item['product_id'] ?? '') == $p->id)>
                {{ $p->title }}{{ $p->sku ? ' ('.$p->sku.')' : '' }}
            </option>
            @endforeach
        </select>
    </td>
    <td>
        <select name="items[{{ $idx }}][measurement_unit_id]" class="form-select form-select-sm">
            <option value="">—</option>
            @foreach($units as $u)
            <option value="{{ $u->id }}" @selected(($item['measurement_unit_id'] ?? '') == $u->id)>{{ $u->title }}</option>
            @endforeach
        </select>
    </td>
    <td>
        <input type="number" name="items[{{ $idx }}][quantity_ordered]" class="form-control form-control-sm"
               min="0.0001" step="0.0001" value="{{ $item['quantity_ordered'] ?? 1 }}" required>
    </td>
    <td>
        <input type="number" name="items[{{ $idx }}][unit_price]" class="form-control form-control-sm"
               min="0" step="0.01" value="{{ $item['unit_price'] ?? '' }}" placeholder="ریال">
    </td>
    <td>
        <input type="number" name="items[{{ $idx }}][discount_percent]" class="form-control form-control-sm"
               min="0" max="100" step="0.01" value="{{ $item['discount_percent'] ?? 0 }}">
    </td>
    <td class="text-end fw-medium lineTotal">۰</td>
    <td>
        <input type="text" name="items[{{ $idx }}][description]" class="form-control form-control-sm"
               value="{{ $item['description'] ?? '' }}" placeholder="توضیح اختیاری">
    </td>
    <td>
        <button type="button" class="btn btn-sm btn-icon btn-outline-danger removeRow" title="حذف ردیف">
            <i class="bx bx-trash"></i>
        </button>
    </td>
</tr>
