<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bambudua - Login</title>

    <!-- Meta -->
    <meta name="description" content="Halaman Login SIMRS Bambu Dua Clinic">
    <meta property="og:title" content="Bambudua - Login">
    <meta property="og:description" content="Halaman Login SIMRS Bambu Dua Clinic">
    <meta property="og:type" content="Website">
    <link rel="shortcut icon" href="{{ asset('images/logo.png') }}">

    <!-- *************
   ************ CSS Files *************
  ************* -->
    <link rel="stylesheet" href="{{ asset('fonts/remix/remixicon.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main.min.css') }}">

</head>

<body class="login-bg">

    <!-- Container starts -->
    <div class="container">

        <!-- Auth wrapper starts -->
        <div class="auth-wrapper">

            <div class="auth-box">
                <a href="{{ route('login') }}" class="auth-logo mb-4">
                    <img src="{{ asset('images/logo2.png') }}" alt="Bootstrap Gallery">
                </a>

                <h4 class="mb-4">Login</h4>
                <form action="{{ route('login') }}" method="POST" class="g-3 needs-validation" novalidate>
                    @csrf
                    <div class="mb-3">
                        <label class="form-label" for="username">Username <span class="text-danger">*</span></label>
                        <input type="text" name="username" id="username" class="form-control"
                            placeholder="Username" value="{{ old('username') }}" required>
                        <div class="invalid-feedback">
                            Kolom Username kosong
                        </div>
                        <p class="text-danger">{{ $errors->first('username') }}</p>
                    </div>

                    <div class="mb-2">
                        <label class="form-label" for="pwd">Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" name="password" id="pwd" class="form-control"
                                placeholder="Enter password" value="{{ old('password') }}" required>
                            <button class="btn btn-outline-secondary" type="button">
                                <i class="ri-eye-line text-primary"></i>
                            </button>
                            <div class="invalid-feedback">
                                Kolom Password kosong
                            </div>
                        </div>
                    </div>

                    <div class="mb-3 d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Login</button>
                    </div>
                </form>
            </div>

        </div>
        <!-- Auth wrapper ends -->

    </div>
    <!-- Container ends -->

    <!-- Validations -->
    <script src="{{ asset('js/validations.js') }}"></script>

</body>

</html>
