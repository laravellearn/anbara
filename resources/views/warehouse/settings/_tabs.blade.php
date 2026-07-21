{{-- تب‌های ناوبری تنظیمات --}}
<ul class="nav nav-tabs mb-4">
    @php
    $tabs = [
        ['key' => 'organization',  'label' => 'اطلاعات سازمان',       'icon' => 'bx-building',    'route' => 'warehouse.settings.organization'],
        ['key' => 'warehouse',     'label' => 'تنظیمات انبار',         'icon' => 'bx-buildings',   'route' => 'warehouse.settings.warehouse'],
        ['key' => 'workflow',      'label' => 'گردش‌کار و تأییدیه‌ها', 'icon' => 'bx-git-branch',  'route' => 'warehouse.settings.workflow'],
        ['key' => 'numbering',     'label' => 'شماره‌گذاری',            'icon' => 'bx-hash',        'route' => 'warehouse.settings.numbering'],
        ['key' => 'notifications', 'label' => 'اعلان‌ها',              'icon' => 'bx-bell',        'route' => 'warehouse.settings.notifications'],
    ];
    @endphp
    @foreach($tabs as $tab)
    <li class="nav-item">
        <a class="nav-link {{ $active === $tab['key'] ? 'active' : '' }}" href="{{ route($tab['route']) }}">
            <i class="bx {{ $tab['icon'] }} me-1"></i> {{ $tab['label'] }}
        </a>
    </li>
    @endforeach
</ul>
