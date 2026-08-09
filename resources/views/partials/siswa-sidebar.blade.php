<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('siswa.index') }}">
        <div class="sidebar-brand-icon">
            <i class="fas fa-user-graduate"></i>
        </div>
        <div class="sidebar-brand-text mx-3">Portal Siswa</div>
    </a>

    <hr class="sidebar-divider my-0">

    <li class="nav-item {{ request()->routeIs('siswa.index') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('siswa.index') }}">
            <i class="fas fa-fw fa-home"></i>
            <span>Beranda</span>
        </a>
    </li>

    <li class="nav-item {{ request()->routeIs('siswa.biodata.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('siswa.biodata.edit') }}">
            <i class="fas fa-fw fa-id-card"></i>
            <span>Biodata</span>
        </a>
    </li>

    <li class="nav-item {{ request()->routeIs('siswa.pelanggaran') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('siswa.pelanggaran') }}">
            <i class="fas fa-fw fa-exclamation-triangle"></i>
            <span>Pelanggaran</span>
        </a>
    </li>

    <li class="nav-item {{ request()->routeIs('siswa.poin') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('siswa.poin') }}">
            <i class="fas fa-fw fa-star"></i>
            <span>Sisa Poin</span>
        </a>
    </li>

    <hr class="sidebar-divider d-none d-md-block">

    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>
