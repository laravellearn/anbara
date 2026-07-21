@extends('super-admin.layouts.master')
@section('title', 'مدیریت نقش‌ها')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

  <div class="d-flex align-items-center justify-content-between mb-4">
    <h4 class="fw-bold mb-0"><i class="bx bx-user-badge text-primary me-2"></i>مدیریت نقش‌ها</h4>
    <a href="{{ route('super-admin.roles.create') }}" class="btn btn-primary btn-sm">
      <i class="bx bx-plus me-1"></i> نقش جدید
    </a>
  </div>

  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
  @endif
  @if($errors->has('error'))
    <div class="alert alert-danger">{{ $errors->first('error') }}</div>
  @endif

  <div class="card border-0 shadow-sm">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr><th>نام</th><th>عنوان</th><th>کاربران</th><th class="text-center">عملیات</th></tr>
        </thead>
        <tbody>
          @forelse($roles as $role)
          <tr>
            <td><code>{{ $role->name }}</code></td>
            <td>{{ $role->title }}</td>
            <td><span class="badge bg-label-info">{{ $role->users_count }}</span></td>
            <td>
              <div class="d-flex gap-1 justify-content-center">
                <a href="{{ route('super-admin.roles.edit', $role) }}" class="btn btn-sm btn-icon btn-outline-warning" title="ویرایش">
                  <i class="bx bx-edit"></i>
                </a>
                @if($role->users_count == 0)
                <form action="{{ route('super-admin.roles.destroy', $role) }}" method="POST" class="d-inline"
                  onsubmit="return confirm('حذف نقش؟')">
                  @csrf @method('DELETE')
                  <button class="btn btn-sm btn-icon btn-outline-danger"><i class="bx bx-trash"></i></button>
                </form>
                @endif
              </div>
            </td>
          </tr>
          @empty
          <tr><td colspan="4" class="text-center text-muted py-5">هیچ نقشی تعریف نشده است.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>
@endsection
