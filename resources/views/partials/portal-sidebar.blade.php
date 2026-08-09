<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('portal.index') }}">
        <div class="sidebar-brand-icon">
            <i class="fas fa-child"></i>
        </div>
        <div class="sidebar-brand-text mx-3">Portal Ortu</div>
    </a>

    <hr class="sidebar-divider my-0">

    <li class="nav-item {{ request()->routeIs('portal.index') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('portal.index') }}">
            <i class="fas fa-fw fa-home"></i>
            <span>Beranda</span>
        </a>
    </li>

    <li class="nav-item {{ request()->routeIs('portal.notifications') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('portal.notifications') }}">
            <i class="fas fa-fw fa-bell"></i>
            <span>Notifikasi</span>
            @php $unread = auth()->user()->unreadNotifications()->count(); @endphp
            @if($unread > 0)
                <span class="badge badge-danger badge-counter ml-1">{{ $unread }}</span>
            @endif
        </a>
    </li>

    <hr class="sidebar-divider d-none d-md-block">

    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>
