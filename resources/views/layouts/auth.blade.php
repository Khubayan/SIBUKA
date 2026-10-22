<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>@yield('title', 'Masuk') — SIBUKA</title>

    <link href="{{ asset('assets/sb-admin-2/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="{{ asset('assets/sb-admin-2/css/sb-admin-2.min.css') }}" rel="stylesheet">
</head>
<body class="bg-gradient-primary">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-10 col-md-8 col-lg-5 col-xl-4">
                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-4 p-sm-5">
                        <div class="text-center">
                            <h1 class="h4 text-gray-900 mb-2">SIBUKA</h1>
                            <p class="text-muted small mb-4">{{ config('sibuka.school_name') }}</p>
                        </div>
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('partials.scripts')
</body>
</html>
