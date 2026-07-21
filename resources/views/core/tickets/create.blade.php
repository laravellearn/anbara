@extends('layouts.app')
@section('title', 'ثبت تیکت پشتیبانی')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="row justify-content-center">
    <div class="col-lg-7">
      <div class="card">
        <div class="card-header"><h5 class="mb-0">ثبت تیکت جدید</h5></div>
        <form action="{{ route('tickets.store') }}" method="POST">
          @csrf
          <div class="card-body">
            @if($errors->any())
            <div class="alert alert-danger mb-3">
              <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
            @endif
            <div class="mb-3">
              <label class="form-label">موضوع <span class="text-danger">*</span></label>
              <input type="text" name="subject" class="form-control @error('subject')is-invalid@enderror" value="{{ old('subject') }}" required>
              @error('subject')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="row g-3 mb-3">
              <div class="col-md-6">
                <label class="form-label">دسته‌بندی</label>
                <select name="category" class="form-select">
                  @foreach(\App\Models\Ticket::categoryLabels() as $k=>$v)
                    <option value="{{ $k }}" {{ old('category')===$k?'selected':'' }}>{{ $v }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">اولویت</label>
                <select name="priority" class="form-select">
                  @foreach(\App\Models\Ticket::priorityLabels() as $k=>$v)
                    <option value="{{ $k }}" {{ old('priority','normal')===$k?'selected':'' }}>{{ $v }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label">شرح مشکل <span class="text-danger">*</span></label>
              <textarea name="description" rows="6" class="form-control @error('description')is-invalid@enderror" required>{{ old('description') }}</textarea>
              @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>
          <div class="card-footer d-flex justify-content-end gap-2">
            <a href="{{ route('tickets.index') }}" class="btn btn-outline-secondary">انصراف</a>
            <button type="submit" class="btn btn-primary">ثبت تیکت</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
