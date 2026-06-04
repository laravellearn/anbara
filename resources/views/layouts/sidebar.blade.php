<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">

    {{-- Brand --}}
    <div class="app-brand demo">
        <a href="{{ route('dashboard') }}" class="app-brand-link">
            <span class="app-brand-logo demo">
                <img src="/logo-light.png"
                     alt="Anbara"
                     style="max-width: 100px;height:auto;">
            </span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="bx menu-toggle-icon d-none d-xl-block fs-4"></i>
        </a>
    </div>

    <div class="menu-divider mt-0"></div>

    <ul class="menu-inner py-1">

        @foreach(config('menu') as $section)

            @php
                $hasChildren = isset($section['children']);
                $icon = $section['icon'] ?? 'bx-circle';
            @endphp

            {{-- ================= SINGLE ITEM ================= --}}
            @if(!$hasChildren)

                @if(!isset($section['permission']) || auth()->user()?->can($section['permission']))
                    <li class="menu-item {{ request()->routeIs($section['route'] ?? '') ? 'active' : '' }}">
                        <a href="{{ route($section['route']) }}" class="menu-link">
                            <i class="menu-icon bx {{ $icon }}"></i>
                            <span>{{ $section['title'] }}</span>
                        </a>
                    </li>
                @endif

            {{-- ================= PARENT MENU ================= --}}
            @else

                @php
                    $isOpen = false;

                    foreach ($section['children'] as $child) {
                        if (request()->routeIs($child['route'] ?? '')) {
                            $isOpen = true;
                            break;
                        }
                    }
                @endphp

                <li class="menu-item {{ $isOpen ? 'open active' : '' }}">

                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <i class="menu-icon bx {{ $icon }}"></i>
                        <span>{{ $section['title'] }}</span>
                    </a>

                    <ul class="menu-sub">

                        @foreach($section['children'] as $child)

                            @if(!isset($child['permission']) || auth()->user()?->can($child['permission']))

                                <li class="menu-item {{ request()->routeIs($child['route']) ? 'active' : '' }}">
                                    <a href="{{ route($child['route']) }}" class="menu-link">
                                        <span>{{ $child['title'] }}</span>
                                    </a>
                                </li>

                            @endif

                        @endforeach

                    </ul>

                </li>

            @endif

        @endforeach

    </ul>
</aside>