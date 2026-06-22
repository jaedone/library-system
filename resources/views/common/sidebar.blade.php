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
                <small>Management System</small>
            </div>
        </button>
    </div>

    {{-- SEARCH --}}
    <form action="{{ url('/catalog') }}" method="GET" class="sidebar-search">
        <i class="bi bi-search"></i>

        <input
            type="search"
            name="q"
            value="{{ request('q') }}"
            placeholder="Search services..."
        >
    </form>

    {{-- MENU --}}
    <nav class="sidebar-menu">
        <p class="sidebar-label">Menu</p>

        <a href="{{ url('/') }}" class="sidebar-link {{ request()->is('/') ? 'active' : '' }}">
            <i class="bi bi-house-door"></i>
            <span>Home</span>
        </a>

        <a href="{{ url('/catalog') }}" class="sidebar-link {{ request()->is('catalog*') ? 'active' : '' }}">
            <i class="bi bi-journal-bookmark"></i>
            <span>Catalog</span>
        </a>

        <a href="{{ url('/services') }}" class="sidebar-link {{ request()->is('services*') ? 'active' : '' }}">
            <i class="bi bi-grid"></i>
            <span>Services</span>
        </a>

        <a href="{{ url('/facilities') }}" class="sidebar-link {{ request()->is('facilities*') ? 'active' : '' }}">
            <i class="bi bi-building"></i>
            <span>Facilities</span>
        </a>
    </nav>

    {{-- ACCOUNT AREA --}}
    <div class="sidebar-account-area">

        <button class="sidebar-notification-btn" type="button">
            <i class="bi bi-bell"></i>
            <span>Notifications</span>
        </button>

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

                <ul class="dropdown-menu sidebar-dropdown">
                    <li>
                        <a class="dropdown-item" href="{{ url('/account') }}">
                            <i class="bi bi-speedometer2 me-2"></i>
                            Dashboard
                        </a>
                    </li>

                    <li>
                        <a class="dropdown-item" href="{{ url('/profile') }}">
                            <i class="bi bi-person-circle me-2"></i>
                            Profile
                        </a>
                    </li>

                    <li>
                        <hr class="dropdown-divider">
                    </li>

                    <li>
                        <form action="{{ url('/logout') }}" method="POST">
                            @csrf

                            <button class="dropdown-item text-danger" type="submit">
                                <i class="bi bi-box-arrow-right me-2"></i>
                                Logout
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