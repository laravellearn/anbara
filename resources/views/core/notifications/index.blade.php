@extends('layouts.app')

@section('title', 'اعلان‌ها')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="row">
    <div class="col-12">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="fw-bold mb-0">اعلان‌ها
          @if($unreadCount > 0)
            <span class="badge bg-danger ms-1">{{ $unreadCount }}</span>
          @endif
        </h4>
        <div class="d-flex gap-2">
          <a href="{{ request()->fullUrlWithQuery(['unread' => 1]) }}" class="btn btn-outline-secondary btn-sm">فقط خوانده‌نشده</a>
          @if($unreadCount > 0)
          <button class="btn btn-primary btn-sm" id="markAllRead">علامت‌گذاری همه</button>
          @endif
        </div>
      </div>

      <div class="card">
        <div class="card-body p-0">
          @forelse($notifications as $n)
          <div class="d-flex align-items-start p-3 border-bottom {{ $n->is_read ? '' : 'bg-light' }}">
            <div class="flex-shrink-0 me-3">
              <span class="badge bg-label-{{ $n->color }} rounded-circle p-2">
                <i data-feather="{{ $n->icon }}" class="icon-sm"></i>
              </span>
            </div>
            <div class="flex-grow-1">
              <div class="d-flex justify-content-between">
                <h6 class="mb-1 {{ $n->is_read ? 'text-muted fw-normal' : 'fw-bold' }}">{{ $n->title }}</h6>
                <small class="text-muted">{{ $n->created_at->diffForHumans() }}</small>
              </div>
              @if($n->body)
              <p class="text-muted small mb-1">{{ $n->body }}</p>
              @endif
            </div>
            <div class="flex-shrink-0 ms-2 d-flex gap-1">
              @if(!$n->is_read)
              <button class="btn btn-sm btn-icon btn-outline-primary mark-read-btn" data-id="{{ $n->id }}" title="علامت خوانده">
                <i data-feather="check"></i>
              </button>
              @endif
              @if($n->action_url)
              <a href="{{ $n->action_url }}" class="btn btn-sm btn-icon btn-outline-info" title="مشاهده">
                <i data-feather="arrow-left"></i>
              </a>
              @endif
              <button class="btn btn-sm btn-icon btn-outline-danger delete-notif-btn" data-id="{{ $n->id }}" title="حذف">
                <i data-feather="trash-2"></i>
              </button>
            </div>
          </div>
          @empty
          <div class="text-center py-5 text-muted">
            <i data-feather="bell-off" class="icon-lg mb-2"></i>
            <p>اعلانی وجود ندارد.</p>
          </div>
          @endforelse
        </div>
        @if($notifications->hasPages())
        <div class="card-footer">{{ $notifications->links() }}</div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.mark-read-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    fetch(`/notifications/${btn.dataset.id}/read`, {method:'POST', headers:{'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content}})
      .then(() => location.reload());
  });
});

document.getElementById('markAllRead')?.addEventListener('click', () => {
  fetch('/notifications/read-all', {method:'POST', headers:{'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content}})
    .then(() => location.reload());
});

document.querySelectorAll('.delete-notif-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    if (!confirm('حذف شود؟')) return;
    fetch(`/notifications/${btn.dataset.id}`, {method:'DELETE', headers:{'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content}})
      .then(() => btn.closest('.d-flex.align-items-start').remove());
  });
});
</script>
@endpush
