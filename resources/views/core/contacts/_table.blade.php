<table class="table table-hover align-middle">
    <thead class="table-light">
        <tr>
            <th>#</th>
            <th>نام</th>
            <th>نوع</th>
            <th>موبایل</th>
            <th>شرکت</th>
            <th>وضعیت</th>
            <th>عملیات</th>
        </tr>
    </thead>
    <tbody>
        @forelse($contacts as $contact)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ trim($contact->first_name . ' ' . $contact->last_name) ?: $contact->company_name ?? '---' }}</td>
            <td>
                @if($contact->type)
                <span class="badge bg-{{ $contact->type->color() }}">{{ $contact->type->label() }}</span>
                @else
                ---
                @endif
            </td>
            <td>{{ $contact->mobile ?? '---' }}</td>
            <td>{{ $contact->company_name ?? '---' }}</td>
            <td>{!! $contact->is_active ? '<span class="badge bg-success">فعال</span>' : '<span class="badge bg-danger">غیرفعال</span>' !!}</td>
            <td>
                <div class="d-flex gap-1">
                    @can('access', 'contacts.edit')
                    <a href="{{ route('contacts.edit', $contact) }}" class="btn btn-sm btn-icon btn-outline-warning">
                        <i class="bx bx-edit"></i>
                    </a>
                    @endcan
                    @can('access', 'contacts.delete')
                    <form action="{{ route('contacts.destroy', $contact) }}" method="POST" class="d-inline delete-form">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-icon btn-outline-danger"><i class="bx bx-trash"></i></button>
                    </form>
                    @endcan
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="7" class="text-center text-muted py-5">مخاطبی یافت نشد.</td>
        </tr>
        @endforelse
    </tbody>
</table>
<div class="card-footer d-flex justify-content-between">
    <small class="text-muted">نمایش {{ $contacts->firstItem() ?? 0 }} تا {{ $contacts->lastItem() ?? 0 }} از {{ $contacts->total() }}</small>
    {{ $contacts->links() }}
</div>