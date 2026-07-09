@php
    $user = auth()->user();
    $role = $user?->role;
    $avatarInitials =
        collect(preg_split('/\s+/', trim((string) ($user?->name ?? 'User')), -1, PREG_SPLIT_NO_EMPTY))
            ->map(fn($part) => strtoupper(substr($part, 0, 1)))
            ->take(2)
            ->implode('') ?:
        'U';
    $menus = match ($role) {
        'admin' => [
            [
                'label' => 'Dashboard',
                'route' => 'admin.dashboard',
                'active' => 'admin.dashboard',
                'icon' => 'icon-home',
            ],
            [
                'label' => 'Kelola Akun',
                'route' => 'admin.accounts.index',
                'active' => 'admin.accounts.*',
                'icon' => 'icon-users',
            ],
            [
                'label' => 'Data Siswa',
                'route' => 'admin.students.index',
                'active' => 'admin.students.*',
                'icon' => 'icon-user',
            ],
            [
                'label' => 'Data Orang Tua',
                'route' => 'admin.parents.index',
                'active' => 'admin.parents.*',
                'icon' => 'icon-user-check',
            ],
            [
                'label' => 'Data Pembina',
                'route' => 'admin.coaches.index',
                'active' => 'admin.coaches.*',
                'icon' => 'icon-award',
            ],
            [
                'label' => 'Data Eskul',
                'route' => 'admin.extracurriculars.index',
                'active' => 'admin.extracurriculars.*',
                'icon' => 'icon-layers',
            ],
            [
                'label' => 'Laporan',
                'route' => 'admin.reports.index',
                'active' => 'admin.reports.*',
                'icon' => 'icon-file-text',
            ],
        ],
        'siswa' => [
            [
                'label' => 'Dashboard',
                'route' => 'student.dashboard',
                'active' => 'student.dashboard',
                'icon' => 'icon-home',
            ],
            [
                'label' => 'Daftar Eskul',
                'route' => 'student.extracurriculars.index',
                'active' => 'student.extracurriculars.*',
                'icon' => 'icon-layers',
            ],
            [
                'label' => 'Absensi',
                'route' => 'student.attendances.index',
                'active' => 'student.attendances.*',
                'icon' => 'icon-check-square',
            ],
            [
                'label' => 'Laporan Saya',
                'route' => 'student.reports.index',
                'active' => 'student.reports.*',
                'icon' => 'icon-file-text',
            ],
        ],
        'orang_tua' => [
            [
                'label' => 'Dashboard',
                'route' => 'parent.dashboard',
                'active' => 'parent.dashboard',
                'icon' => 'icon-home',
            ],
            [
                'label' => 'Validasi Pendaftaran',
                'route' => 'parent.registrations.index',
                'active' => 'parent.registrations.*',
                'icon' => 'icon-check-circle',
            ],
            [
                'label' => 'Laporan Anak',
                'route' => 'parent.reports.index',
                'active' => 'parent.reports.*',
                'icon' => 'icon-file-text',
            ],
        ],
        'pembina' => [
            [
                'label' => 'Dashboard',
                'route' => 'coach.dashboard',
                'active' => 'coach.dashboard',
                'icon' => 'icon-home',
            ],
            [
                'label' => 'Validasi Pendaftaran',
                'route' => 'coach.registrations.index',
                'active' => 'coach.registrations.*',
                'icon' => 'icon-check-circle',
            ],
            [
                'label' => 'Jadwal Eskul',
                'route' => 'coach.schedules.index',
                'active' => 'coach.schedules.*',
                'icon' => 'icon-calendar',
            ],
            [
                'label' => 'Kelola Absensi',
                'route' => 'coach.attendances.index',
                'active' => 'coach.attendances.*',
                'icon' => 'icon-check-square',
            ],
            [
                'label' => 'Input Penilaian',
                'route' => 'coach.assessments.index',
                'active' => 'coach.assessments.*',
                'icon' => 'icon-edit',
            ],
            [
                'label' => 'Buat Laporan',
                'route' => 'coach.reports.index',
                'active' => 'coach.reports.*',
                'icon' => 'icon-file-text',
            ],
        ],
        default => [],
    };
@endphp
<!DOCTYPE html>
<html lang="id">

<head>
    <title>@yield('title', 'Sistem Eskul') - SMK Yappika Legok</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('logo.png') }}" type="image/png">
    <link rel="stylesheet" href="{{ asset('template/dist/assets/css/style.css') }}">
    <style>
        .brand-title {
            color: #fff;
            font-weight: 700;
            letter-spacing: 0;
            font-size: 15px;
        }

        .pcoded-header .m-header .app-logo {
            display: inline-block;
            width: 38px;
            height: 38px;
            object-fit: contain;
        }

        .alphabet-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #ffffff;
            color: #1f4fb2;
            font-weight: 700;
            letter-spacing: 0;
            text-transform: uppercase;
        }

        .pro-head .alphabet-avatar {
            margin-right: 10px;
            vertical-align: middle;
        }

        .table td,
        .table th {
            vertical-align: middle;
        }

        .action-row {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
        }

        .empty-state {
            border: 1px dashed #d8dce6;
            border-radius: 6px;
            padding: 24px;
            text-align: center;
            color: #6c757d;
        }

        @media print {

            .pcoded-navbar,
            .pcoded-header,
            .page-header,
            .btn,
            form,
            .no-print {
                display: none !important;
            }

            .pcoded-main-container {
                margin: 0 !important;
            }

            .pcoded-content {
                padding: 0 !important;
            }

            .card {
                box-shadow: none !important;
                border: 1px solid #ddd !important;
            }
        }
    </style>
</head>

<body>
    <div class="loader-bg">
        <div class="loader-track">
            <div class="loader-fill"></div>
        </div>
    </div>

    <nav class="pcoded-navbar menu-light brand-blue">
        <div class="navbar-wrapper">
            <div class="navbar-content scroll-div">
                <ul class="nav pcoded-inner-navbar">
                    <li class="nav-item pcoded-menu-caption">
                        <label>{{ strtoupper(str_replace('_', ' ', $role ?? 'Menu')) }}</label>
                    </li>
                    @foreach ($menus as $item)
                        <li class="nav-item {{ request()->routeIs($item['active']) ? 'active' : '' }}">
                            <a href="{{ route($item['route']) }}" class="nav-link">
                                <span class="pcoded-micon"><i class="feather {{ $item['icon'] }}"></i></span>
                                <span class="pcoded-mtext">{{ $item['label'] }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </nav>

    <header class="navbar pcoded-header navbar-expand-lg navbar-light header-blue">
        <div class="m-header">
            <a class="mobile-menu" id="mobile-collapse" href="#!"><span></span></a>
            <a href="{{ route('dashboard') }}" class="b-brand">
                <img src="{{ asset('logo.png') }}" alt="Logo" class="logo-thumb app-logo mr-2
                ">
                <span class="brand-title">Sistem Eskul</span>
            </a>
            <a href="#!" class="mob-toggler"><i class="feather icon-more-vertical"></i></a>
        </div>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <span class="text-white">SMK Yappika Legok</span>
                </li>
            </ul>
            <ul class="navbar-nav ml-auto">
                <li>
                    <div class="dropdown drp-user">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="feather icon-user"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right profile-notification">
                            <div class="pro-head">
                                <span class="alphabet-avatar" aria-label="Avatar {{ $user?->name }}">
                                    {{ $avatarInitials }}
                                </span>
                                <span>{{ $user?->name }}</span>
                                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="dud-logout border-0 bg-transparent" title="Logout" type="submit">
                                        <i class="feather icon-log-out"></i>
                                    </button>
                                </form>
                            </div>
                            <ul class="pro-body">
                                <li><span class="dropdown-item"><i class="feather icon-shield"></i>
                                        {{ strtoupper(str_replace('_', ' ', $role ?? '-')) }}</span></li>
                                <li><span class="dropdown-item"><i class="feather icon-mail"></i>
                                        {{ $user?->email }}</span></li>
                            </ul>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </header>

    <div class="pcoded-main-container">
        <div class="pcoded-wrapper">
            <div class="pcoded-content">
                <div class="pcoded-inner-content">
                    <div class="main-body">
                        <div class="page-wrapper">
                            <div class="page-header">
                                <div class="page-block">
                                    <div class="row align-items-center">
                                        <div class="col-md-12">
                                            <div class="page-header-title">
                                                <h5 class="m-b-10">@yield('title', 'Dashboard')</h5>
                                            </div>
                                            <ul class="breadcrumb">
                                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i
                                                            class="feather icon-home"></i></a></li>
                                                <li class="breadcrumb-item"><a href="#!">@yield('title', 'Dashboard')</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if (session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif
                            @if (session('error'))
                                <div class="alert alert-danger">{{ session('error') }}</div>
                            @endif
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <strong>Periksa input:</strong>
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @yield('content')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('template/dist/assets/js/vendor-all.min.js') }}"></script>
    <script src="{{ asset('template/dist/assets/js/plugins/bootstrap.min.js') }}"></script>
    <script src="{{ asset('template/dist/assets/js/ripple.js') }}"></script>
    <script src="{{ asset('template/dist/assets/js/pcoded.js') }}"></script>
    @stack('scripts')
</body>

</html>
