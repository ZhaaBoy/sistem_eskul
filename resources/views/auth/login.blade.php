<!DOCTYPE html>
<html lang="id">
<head>
    <title>Login - Sistem Eskul SMK Yappika Legok</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="icon" href="{{ asset('template/dist/assets/images/favicon.ico') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('template/dist/assets/css/style.css') }}">
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-content">
            <div class="card">
                <div class="row align-items-center text-center">
                    <div class="col-md-12">
                        <div class="card-body">
                            <img src="{{ asset('template/dist/assets/images/logo-dark.png') }}" alt="Logo" class="img-fluid mb-4">
                            <h4 class="mb-2 f-w-400">Sistem Informasi Eskul</h4>
                            <p class="text-muted mb-4">SMK Yappika Legok</p>

                            @if ($errors->any())
                                <div class="alert alert-danger text-left">{{ $errors->first() }}</div>
                            @endif

                            <form method="POST" action="{{ route('login.store') }}">
                                @csrf
                                <div class="form-group mb-3 text-left">
                                    <label class="floating-label" for="email">Email</label>
                                    <input type="email" name="email" value="{{ old('email') }}" class="form-control" id="email" required autofocus>
                                </div>
                                <div class="form-group mb-4 text-left">
                                    <label class="floating-label" for="password">Password</label>
                                    <input type="password" name="password" class="form-control" id="password" required>
                                </div>
                                <div class="custom-control custom-checkbox text-left mb-4 mt-2">
                                    <input type="checkbox" name="remember" value="1" class="custom-control-input" id="remember">
                                    <label class="custom-control-label" for="remember">Ingat saya</label>
                                </div>
                                <button class="btn btn-block btn-primary mb-3" type="submit">Masuk</button>
                            </form>
                            <p class="mb-0 text-muted">Akun siswa, orang tua, dan pembina dibuat oleh admin.</p>
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
</body>
</html>
