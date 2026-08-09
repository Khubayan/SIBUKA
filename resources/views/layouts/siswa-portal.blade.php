<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Portal Siswa — SIBUKA">
    <title>@yield('title', 'Portal Siswa') — SIBUKA</title>

    <link href="{{ asset('assets/sb-admin-2/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="{{ asset('assets/sb-admin-2/css/sb-admin-2.min.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body id="page-top">
    <div id="wrapper">
        @include('partials.siswa-sidebar')

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                @include('partials.siswa-topbar')

                <div class="container-fluid">
                    @include('partials.alerts')
                    @yield('content')
                </div>
            </div>

            @include('partials.footer')
        </div>
    </div>

    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    @include('partials.logout-modal')
    @include('partials.scripts')
    @stack('scripts')
</body>
</html>
