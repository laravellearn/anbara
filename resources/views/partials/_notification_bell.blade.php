{{-- Notification Bell Partial — include in navbar --}}
<li class="nav-item dropdown-notifications navbar-dropdown me-3 me-xl-1" id="notif-dropdown">
  <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown"
     data-bs-auto-close="outside" aria-expanded="false" id="notifToggle">
    <span class="position-relative">
      <i class="bx bx-bell bx-sm"></i>
      <span class="badge bg-danger badge-notifications badge-dot" id="notifBadge"
            style="{{ auth()->user() ? '' : 'display:none' }}"></span>
    </span>
  </a>

  <ul class="dropdown-menu dropdown-menu-end py-0" style="min-width:22rem">
    <li class="dropdown-menu-header border-bottom">
      <div class="dropdown-header d-flex align-items-center py-3">
        <h5 class="text-body mb-0 me-auto fw-semibold">اعلان‌ها</h5>
        <a href="{{ route('notifications.index') }}" class="dropdown-notifications-all text-body small">همه</a>
        <button class="btn btn-icon btn-sm btn-text-secondary mark-all-btn ms-1" title="خواندن همه">
          <span class="bx bx-check-double"></span>
        </button>
      </div>
    </li>

    <li class="dropdown-notifications-list scrollable-container" id="notifList" style="max-height:300px;overflow-y:auto">
      <ul class="list-group list-group-flush" id="notifItems">
        <li class="list-group-item text-center text-muted py-4" id="notifEmpty" style="display:none">
          <i class="bx bx-bell-off d-block mb-1 fs-4"></i> اعلانی وجود ندارد
        </li>
      </ul>
    </li>

    <li class="border-top">
      <div class="d-grid p-2">
        <a class="btn btn-primary btn-sm" href="{{ route('notifications.index') }}">مشاهده همه اعلان‌ها</a>
      </div>
    </li>
  </ul>
</li>

@push('scripts')
<script>
(function () {
  const badge      = document.getElementById('notifBadge');
  const list       = document.getElementById('notifItems');
  const empty      = document.getElementById('notifEmpty');
  const markAllBtn = document.querySelector('.mark-all-btn');
  const csrf       = document.querySelector('meta[name=csrf-token]')?.content;

  function typeColor(type) {
    const map = {low_stock:'warning',doc_approved:'success',doc_rejected:'danger',
      doc_submitted:'info',payment_success:'success',subscription_expiring:'warning'};
    return map[type] || 'primary';
  }

  function load() {
    fetch('/notifications/latest')
      .then(r => r.json())
      .then(({ notifications, unread_count }) => {
        badge.textContent = unread_count > 0 ? unread_count : '';
        badge.style.display = unread_count > 0 ? '' : 'none';

        // پاک کردن ردیف‌های قدیمی (نه empty placeholder)
        list.querySelectorAll('.notif-item').forEach(el => el.remove());

        if (!notifications.length) {
          empty.style.display = '';
          return;
        }
        empty.style.display = 'none';

        notifications.forEach(n => {
          const li = document.createElement('li');
          li.className = 'list-group-item list-group-item-action notif-item' + (n.is_read ? '' : ' unread');
          li.innerHTML = `
            <div class="d-flex">
              <div class="flex-shrink-0 me-2 mt-1">
                <span class="avatar avatar-xs bg-label-${typeColor(n.type)} rounded-circle">
                  <i class="bx bx-${n.icon || 'bell'} bx-xs"></i>
                </span>
              </div>
              <div class="flex-grow-1">
                <p class="mb-0 small fw-${n.is_read ? 'normal text-muted' : 'semibold'}">${n.title}</p>
                ${n.body ? `<p class="mb-0 text-muted" style="font-size:0.75rem">${n.body}</p>` : ''}
              </div>
            </div>`;

          if (!n.is_read) {
            li.style.cursor = 'pointer';
            li.addEventListener('click', () => {
              fetch(`/notifications/${n.id}/read`, {method:'POST', headers:{'X-CSRF-TOKEN': csrf}})
                .then(() => load());
              if (n.action_url) window.location = n.action_url;
            });
          }
          list.appendChild(li);
        });
      })
      .catch(() => {});
  }

  // بارگذاری اولیه پس از باز شدن dropdown
  document.getElementById('notifToggle')?.addEventListener('click', load);

  // علامت‌گذاری همه
  markAllBtn?.addEventListener('click', e => {
    e.preventDefault();
    fetch('/notifications/read-all', {method:'POST', headers:{'X-CSRF-TOKEN': csrf}})
      .then(() => load());
  });

  // بارگذاری خودکار هر ۶۰ ثانیه
  setInterval(load, 60000);
  load();
})();
</script>
@endpush
