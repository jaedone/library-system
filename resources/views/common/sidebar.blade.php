@php
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Route;
    use Illuminate\Support\Facades\Schema;

    $authUser = auth()->user();

    $roleName = null;

    if ($authUser && isset($authUser->role_id)) {
        $roleName = DB::table('roles')
            ->where('id', $authUser->role_id)
            ->value('role_name');
    }

    $isAdminUser = $authUser && in_array($roleName, [
        'admin',
        'library_staff',
    ], true);

    $isAdminPage = request()->is('admin*') || request()->routeIs('admin.*');

    $useAdminSidebar = $isAdminUser;

    $unreadNotificationsCount = 0;
    $latestNotifications = collect();

    if ($authUser && Schema::hasTable('user_notifications')) {
        $unreadNotificationsCount = DB::table('user_notifications')
            ->where('user_id', $authUser->id)
            ->where('is_read', false)
            ->count();

        $latestNotifications = DB::table('user_notifications')
            ->where('user_id', $authUser->id)
            ->orderBy('is_read')
            ->orderByDesc('created_at')
            ->limit(6)
            ->get();
    }

    $adminMenuItems = [
    [
        'label' => 'Website Management',
        'icon' => 'bi-window-sidebar',
        'url' => Route::has('admin.website.index') ? route('admin.website.index') : url('/admin/website-information'),
        'active' => request()->routeIs('admin.website.*') || request()->is('admin/website-information*'),
    ],
    [
        'label' => 'Services Management',
        'icon' => 'bi-inboxes',
        'url' => Route::has('admin.services-management.index') ? route('admin.services-management.index') : url('/admin/services-management'),
        'active' => request()->routeIs('admin.services-management.*') || request()->is('admin/services-management*'),
    ],
    [
        'label' => 'User Management',
        'icon' => 'bi-people',
        'url' => Route::has('admin.members.index') ? route('admin.members.index') : url('/admin/members'),
        'active' => request()->routeIs('admin.members.*') || request()->is('admin/members*'),
    ],
];

    $publicMenuItems = [
        [
            'label' => 'Home',
            'icon' => 'bi-house-door',
            'url' => url('/'),
            'active' => request()->is('/'),
        ],
        [
            'label' => 'Catalog',
            'icon' => 'bi-journal-bookmark',
            'url' => url('/catalog'),
            'active' => request()->is('catalog*'),
        ],
        [
            'label' => 'Services',
            'icon' => 'bi-grid',
            'url' => url('/services'),
            'active' => request()->is('services*'),
        ],
        [
            'label' => 'Facilities',
            'icon' => 'bi-building',
            'url' => url('/facilities'),
            'active' => request()->is('facilities*'),
        ],
    ];

    $menuItems = $useAdminSidebar ? $adminMenuItems : $publicMenuItems;

    $searchAction = Route::has('site.search')
    ? route('site.search')
    : url('/search');

$searchName = 'q';
$searchPlaceholder = $useAdminSidebar
    ? 'Search admin pages, services, users...'
    : 'Search services, books, facilities...';
@endphp

<aside class="library-sidebar" id="librarySidebar">

    {{-- BRAND / TOGGLE --}}
    <div class="sidebar-header">
        <button class="sidebar-brand-btn" id="sidebarToggle" type="button" title="Toggle Sidebar">
            <div class="brand-icon">
                <i class="bi bi-book-half icon-open"></i>
                <i class="bi bi-book icon-closed"></i>
            </div>

            <div class="brand-text">
                <span class="brand-title">PUP Library</span>
                <small>{{ $useAdminSidebar ? 'Admin Panel' : 'Management System' }}</small>
            </div>
        </button>
    </div>

    {{-- SEARCH --}}
    <form action="{{ $searchAction }}" method="GET" class="sidebar-search">
        <i class="bi bi-search"></i>

        <input
            type="search"
            name="{{ $searchName }}"
            value="{{ request($searchName) }}"
            placeholder="{{ $searchPlaceholder }}"
        >
    </form>

    {{-- MENU --}}
    <nav class="sidebar-menu">
        <p class="sidebar-label">
            {{ $useAdminSidebar ? 'Admin Menu' : 'Menu' }}
        </p>

        @foreach ($menuItems as $item)
            <a href="{{ $item['url'] }}" class="sidebar-link {{ $item['active'] ? 'active' : '' }}">
                <i class="bi {{ $item['icon'] }}"></i>
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>

    {{-- ACCOUNT AREA --}}
    <div class="sidebar-account-area">

        @auth
            <div class="sidebar-notification-wrapper">
                <button
                    class="sidebar-notification-btn {{ $unreadNotificationsCount > 0 ? 'has-notifications' : 'no-notifications' }}"
                    type="button"
                    data-sidebar-notification-toggle
                    aria-expanded="false"
                >
                    <i class="bi bi-bell"></i>
                    <span>Notifications</span>

                    @if ($unreadNotificationsCount > 0)
                        <em class="nav-notification-badge">
                            {{ $unreadNotificationsCount > 9 ? '9+' : $unreadNotificationsCount }}
                        </em>
                    @endif
                </button>

                <div class="sidebar-notification-popover" data-sidebar-notification-popover hidden>
                    <div class="sidebar-notification-popover-header">
                        <div>
                            <span>Inbox</span>
                            <strong>Notifications</strong>
                        </div>

                        @if ($unreadNotificationsCount > 0 && Route::has('notifications.read-all'))
                            <form method="POST" action="{{ route('notifications.read-all') }}">
                                @csrf
                                @method('PATCH')

                                <button type="submit">
                                    Mark all read
                                </button>
                            </form>
                        @endif
                    </div>

                    <div class="sidebar-notification-list">
                        @forelse ($latestNotifications as $notification)
                            @php
                                $data = json_decode($notification->data ?? '{}', true) ?: [];

                                $notificationIcon = match ($notification->type) {
                                    'penalty' => 'bi-cash-coin',
                                    'service-request' => 'bi-clipboard-check',
                                    default => 'bi-bell',
                                };
                            @endphp

                            <article class="sidebar-notification-item {{ $notification->is_read ? 'is-read' : 'is-unread' }}">
                                <div class="sidebar-notification-icon">
                                    <i class="bi {{ $notificationIcon }}"></i>
                                </div>

                                <div class="sidebar-notification-content">
                                    <strong>{{ $notification->title }}</strong>
                                    <p>{{ $notification->message }}</p>

                                    <span>
                                        {{ \Carbon\Carbon::parse($notification->created_at)->format('M d, Y • h:i A') }}
                                    </span>

                                    <div class="sidebar-notification-actions">
                                        @if (!$notification->is_read && Route::has('notifications.read'))
                                            <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                                                @csrf
                                                @method('PATCH')

                                                <button type="submit">
                                                    Read
                                                </button>
                                            </form>
                                        @endif

                                        @if (!empty($data['approved_letter_path']))
                                            <a href="{{ asset('storage/' . $data['approved_letter_path']) }}" target="_blank" rel="noopener">
                                                Letter
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </article>
                        @empty
                            <div class="sidebar-notification-empty">
                                <i class="bi bi-bell-slash"></i>
                                <strong>No notifications</strong>
                                <p>Service updates will appear here.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        @else
            <button class="sidebar-notification-btn no-notifications" type="button" disabled>
                <i class="bi bi-bell"></i>
                <span>Notifications</span>
            </button>
        @endauth

        @auth
            <div class="dropdown dropend w-100 sidebar-account-dropdown">
                <button
                    class="sidebar-account-btn"
                    type="button"
                    data-bs-toggle="dropdown"
                    data-bs-boundary="viewport"
                    aria-expanded="false"
                >
                    <i class="bi bi-person-circle"></i>

                    <span class="sidebar-account-text">
                        {{ auth()->user()->email }}
                    </span>
                </button>

                <ul class="dropdown-menu sidebar-dropdown shadow-lg border-0 p-2">
    <li class="sidebar-dropdown-user px-3 py-3">
        <div class="sidebar-dropdown-avatar">
            <i class="bi bi-person-circle"></i>
        </div>

        <div>
            <span>Signed in as</span>
            <strong>{{ auth()->user()->email }}</strong>
        </div>
    </li>

    <li>
        <hr class="dropdown-divider my-2">
    </li>

    <li>
        <a class="dropdown-item sidebar-dropdown-item d-flex align-items-center gap-2 rounded-3 px-3 py-2"
           href="{{ url('/account/profile') }}">
            <i class="bi bi-person-circle"></i>
            <span>Profile</span>
        </a>
    </li>

    <li>
        <hr class="dropdown-divider my-2">
    </li>

    <li>
        <form action="{{ url('/logout') }}" method="POST">
            @csrf

            <button class="dropdown-item sidebar-dropdown-item danger d-flex align-items-center gap-2 rounded-3 px-3 py-2"
                    type="submit">
                <i class="bi bi-box-arrow-right"></i>
                <span>Logout</span>
            </button>
        </form>
    </li>
</ul>
            </div>
        @else
            <a href="{{ url('/login') }}" class="sidebar-account-btn">
                <i class="bi bi-person-circle"></i>
                <span>Sign In</span>
            </a>
        @endauth

    </div>

</aside>