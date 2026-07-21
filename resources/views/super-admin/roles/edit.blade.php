@extends('super-admin.layouts.master')
@section('title', 'ویرایش نقش - ' . $role->title)

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="d-flex align-items-center mb-4 gap-2">
    <a href="{{ route('super-admin.roles.index') }}" class="btn btn-sm btn-icon btn-outline-secondary">
      <i class="bx bx-arrow-back"></i>
    </a>
    <h4 class="fw-bold mb-0">ویرایش نقش: {{ $role->title }}</h4>
  </div>

  <form action="{{ route('super-admin.roles.update', $role) }}" method="POST">
    @csrf @method('PUT')
    @if($errors->any())
      <div class="alert alert-danger mb-3">
        <ul class="mb-0 small">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
      </div>
    @endif

    <div class="row g-4">
      <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
          <div class="card-header py-3"><h6 class="mb-0">اطلاعات نقش</h6></div>
          <div class="card-body">
            <div class="mb-3">
              <label class="form-label">نام (کد)</label>
              <input type="text" name="name" class="form-control"
                value="{{ old('name', $role->name) }}" required>
            </div>
            <div class="mb-3">
              <label class="form-label">عنوان نمایشی</label>
              <input type="text" name="title" class="form-control"
                value="{{ old('title', $role->title) }}" required>
            </div>
            <button type="submit" class="btn btn-warning w-100"><i class="bx bx-save me-1"></i>به‌روزرسانی</button>
          </div>
        </div>
      </div>
      <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
          <div class="card-header d-flex align-items-center justify-content-between py-3">
            <h6 class="mb-0">مجوزها</h6>
            <div class="d-flex gap-2">
              <button type="button" class="btn btn-xs btn-outline-primary btn-sm" onclick="toggleAll(true)">انتخاب همه</button>
              <button type="button" class="btn btn-xs btn-outline-secondary btn-sm" onclick="toggleAll(false)">حذف همه</button>
            </div>
          </div>
          <div class="card-body" style="max-height:500px;overflow-y:auto;">
            @foreach($permissions as $group => $perms)
            <div class="mb-3">
              <h6 class="text-muted small text-uppercase fw-bold mb-2">{{ $group }}</h6>
              <div class="row g-1">
                @foreach($perms as $perm)
                <div class="col-md-6">
                  <div class="form-check">
                    <input type="checkbox" name="permissions[]" value="{{ $perm->id }}"
                      class="form-check-input perm-check" id="perm_{{ $perm->id }}"
                      {{ in_array($perm->id, old('permissions', $rolePermissions)) ? 'checked' : '' }}>
                    <label class="form-check-label small" for="perm_{{ $perm->id }}">{{ $perm->title }}</label>
                  </div>
                </div>
                @endforeach
              </div>
            </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>
  </form>
</div>
@push('scripts')
<script>
function toggleAll(state) {
  document.querySelectorAll('.perm-check').forEach(c => c.checked = state);
}
</script>
@endpush
@endsection
