<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <a class="sidebar-brand d-flex align-items-center justify-content-center"
       href="{{ auth()->user()->isOrangTua() ? route('portal.index') : route('dashboard') }}">
        <div class="sidebar-brand-icon">
            <i class="fas fa-book"></i>
        </div>
        <div class="sidebar-brand-text mx-3">SIBUKA</div>
    </a>

    <hr class="sidebar-divider my-0">

    @auth
        @if(auth()->user()->isOrangTua())
            <li class="nav-item {{ request()->routeIs('portal.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('portal.index') }}">
                    <i class="fas fa-fw fa-child"></i>
                    <span>Portal Anak</span>
                </a>
            </li>
        @else
            <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('dashboard') }}">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <hr class="sidebar-divider">
            <div class="sidebar-heading">Menu Utama</div>

            @if(auth()->user()->isAdmin() || auth()->user()->isGuruBk() || auth()->user()->isWaliKelas())
                <li class="nav-item {{ request()->routeIs('students.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('students.index') }}">
                        <i class="fas fa-fw fa-user-graduate"></i>
                        <span>Data Siswa</span>
                    </a>
                </li>
            @endif

            @if(auth()->user()->isAdmin() || auth()->user()->isGuruBk() || auth()->user()->isWaliKelas())
                <li class="nav-item {{ request()->routeIs('cases.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('cases.index') }}">
                        <i class="fas fa-fw fa-clipboard-list"></i>
                        <span>Kasus</span>
                    </a>
                </li>
            @endif

            @if(auth()->user()->isAdmin() || auth()->user()->isGuruBk() || auth()->user()->isWaliKelas())
                <li class="nav-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('reports.index') }}">
                        <i class="fas fa-fw fa-file-alt"></i>
                        <span>Laporan</span>
                    </a>
                </li>
            @endif

            @if(auth()->user()->isAdmin())
                <hr class="sidebar-divider">
                <div class="sidebar-heading">Master Data</div>

                <li class="nav-item {{ request()->routeIs('jurusan.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('jurusan.index') }}">
                        <i class="fas fa-fw fa-school"></i>
                        <span>Jurusan</span>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('kelas.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('kelas.index') }}">
                        <i class="fas fa-fw fa-users"></i>
                        <span>Kelas</span>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('case-types.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('case-types.index') }}">
                        <i class="fas fa-fw fa-tags"></i>
                        <span>Jenis Kasus</span>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('users.index') }}">
                        <i class="fas fa-fw fa-user-cog"></i>
                        <span>Pengguna</span>
                    </a>
                </li>
            @elseif(auth()->user()->isGuruBk())
                <li class="nav-item {{ request()->routeIs('case-types.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('case-types.index') }}">
                        <i class="fas fa-fw fa-tags"></i>
                        <span>Jenis Kasus</span>
                    </a>
                </li>
            @endif
        @endif
    @endauth

    <hr class="sidebar-divider d-none d-md-block">

    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>
