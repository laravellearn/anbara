@extends('super-admin.layouts.master')
@section('title', 'لیست سازمان‌ها')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">سازمان‌ها (Tenant)</h5>
        <a href="{{ route('super-admin.tenants.create') }}" class="btn btn-primary btn-sm">
            <i class="bx bx-plus"></i> ایجاد سازمان جدید
        </a>
    </div>
    <div class="table-responsive text-nowrap">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>نام</th>
                    <th>slug</th>
                    <th>ایمیل</th>
                    <th>وضعیت</th>
                    <th>عملیات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tenants as $tenant)
                <tr>
                    <td>{{ $tenant->name }}</td>
                    <td>{{ $tenant->slug }}</td>
                    <td>{{ $tenant->email ?? '—' }}</td>
                    <td>
                        <span class="badge bg-{{ $tenant->is_active ? 'success' : 'secondary' }}">
                            {{ $tenant->is_active ? 'فعال' : 'غیرفعال' }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('super-admin.tenants.edit', $tenant) }}" class="btn btn-sm btn-icon btn-outline-warning">
                            <i class="bx bx-edit"></i>
                        </a>
                        <form action="{{ route('super-admin.tenants.destroy', $tenant) }}" method="POST" class="d-inline" onsubmit="return confirm('آیا مطمئن هستید؟')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-icon btn-outline-danger"><i class="bx bx-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center">هیچ سازمانی یافت نشد.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        {{ $tenants->links() }}
    </div>
</div>
@endsection