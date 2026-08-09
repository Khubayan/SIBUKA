<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>

    <h6 class="d-none d-sm-inline-block ml-2 my-auto text-gray-600">
        {{ config('sibuka.school_name') }}
    </h6>

    <ul class="navbar-nav ml-auto">
        @if(auth()->user()->isAdmin() || auth()->user()->isGuruBk())
            @php $unread = auth()->user()->unreadNotifications()->count(); @endphp
            <li class="nav-item dropdown no-arrow mx-1">
                <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-bell fa-fw"></i>
                    @if($unread > 0)
                        <span class="badge badge-danger badge-counter">{{ $unread }}</span>
                    @endif
                </a>
                <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                     aria-labelledby="alertsDropdown">
                    <h6 class="dropdown-header">Notifikasi</h6>
                    @forelse(auth()->user()->unreadNotifications()->limit(5)->get() as $notification)
                        <a class="dropdown-item d-flex align-items-center" href="{{ route('notifications.index') }}">
                            <div class="mr-3">
                                <div class="icon-circle bg-warning">
                                    <i class="fas fa-exclamation-triangle text-white"></i>
                                </div>
                            </div>
                            <div>
                                <div class="small text-gray-500">{{ $notification->created_at->diffForHumans() }}</div>
                                <span class="font-weight-bold">{{ $notification->data['message'] ?? 'Notifikasi baru' }}</span>
                            </div>
                        </a>
                    @empty
                        <span class="dropdown-item text-center small text-gray-500">Tidak ada notifikasi baru</span>
                    @endforelse
                    <a class="dropdown-item text-center small text-gray-500" href="{{ route('notifications.index') }}">
                        Lihat Semua Notifikasi
                    </a>
                </div>
            </li>
        @endif

        <div class="topbar-divider d-none d-sm-block"></div>

        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small">{{ auth()->user()->name }}</span>
                <span class="badge badge-primary mr-2 d-none d-lg-inline">{{ auth()->user()->role->label() }}</span>
                <img class="img-profile rounded-circle" src="{{ asset('assets/sb-admin-2/img/undraw_profile.svg') }}" alt="">
            </a>
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                <a class="dropdown-item" href="#">
                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                    Profil
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                    Keluar
                </a>
            </div>
        </li>
    </ul>
</nav>
