<table class="table table-hover align-middle">
    <thead class="table-light">
        <tr>
            <th>#</th>
            <th>نام</th>
            <th>نوع</th>
            <th>گزینه‌ها</th>
            <th>وضعیت</th>
            <th>عملیات</th>
        </tr>
    </thead>
    <tbody>
        @forelse($attributes as $attr)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $attr->name }}</td>
            <td>
                @if($attr->type == 'text') متن
                @elseif($attr->type == 'number') عدد
                @else انتخاب
                @endif
            </td>
            <td>
                @if($attr->type == 'select' && $attr->options)
                    @foreach(json_decode($attr->options) as $opt)
                        <span class="badge bg-label-info me-1">{{ $opt }}</span>
                    @endforeach
                @else
                    <span class="text-muted">—</span>
                @endif
            </td>
            <td>
                {!! $attr->is_active ? '<span class="badge bg-success">فعال</span>' : '<span class="badge bg-danger">غیرفعال</span>' !!}
            </td>
            <td>
                <div class="d-flex gap-1">
                    @can('access', 'product-attributes.edit')
                    <button class="btn btn-sm btn-icon btn-outline-warning edit-attr-btn"
                        data-id="{{ $attr->id }}" 
                        data-name="{{ $attr->name }}" 
                        data-type="{{ $attr->type }}"
                        data-options="{{ json_encode(json_decode($attr->options)) }}"
                        data-active="{{ $attr->is_active }}">
                        <i class="bx bx-edit"></i>
                    </button>
                    @endcan
                    @can('access', 'product-attributes.delete')
                    <form action="{{ route('warehouse.product-attributes.destroy', $attr) }}" method="POST" class="d-inline delete-form">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-icon btn-outline-danger"><i class="bx bx-trash"></i></button>
                    </form>
                    @endcan
                </div>
            </td>
        </tr>
        @empty
        <tr><td colspan="6" class="text-center text-muted py-5">ویژگی یافت نشد.</td></tr>
        @endforelse
    </tbody>
</table>
<div class="card-footer d-flex justify-content-between">
    <small class="text-muted">نمایش {{ $attributes->firstItem() ?? 0 }} تا {{ $attributes->lastItem() ?? 0 }} از {{ $attributes->total() }}</small>
    {{ $attributes->links() }}
</div>