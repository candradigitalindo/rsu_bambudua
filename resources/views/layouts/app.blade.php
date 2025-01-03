<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title> @yield('title') &mdash; Bambudua</title>

    <!-- Meta -->
    <meta name="description" content="Dashboard SIMRS Bambudua">
    <meta property="og:title" content="Bambudua">
    <meta property="og:description" content="Dashboard SIMRS Bambudua">
    <meta property="og:type" content="Website">
    <link rel="shortcut icon" href="{{ asset('images/logo.png') }}">

    <!-- *************
  ************ CSS Files *************
 ************* -->
    <link rel="stylesheet" href="{{ asset('fonts/remix/remixicon.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main.min.css') }}">
    @stack('style')
</head>

<body>

    <!-- Page wrapper starts -->
    <div class="page-wrapper">

        <!-- Main container starts -->
        <div class="main-container">

            <!-- Sidebar wrapper starts -->
            <nav id="sidebar" class="sidebar-wrapper">

                <!-- Brand container starts -->
                <div class="brand-container d-flex align-items-center justify-content-between">

                    <!-- App brand starts -->
                    <div class="app-brand ms-3">
                        <a href="index.html">
                            <img src="{{ asset('images/logo2.png') }}" class="logo" alt="Medicare Admin Template">
                        </a>
                    </div>
                    <!-- App brand ends -->

                    <!-- Pin sidebar starts -->
                    <button type="button" class="pin-sidebar me-3">
                        <i class="ri-menu-line"></i>
                    </button>
                    <!-- Pin sidebar ends -->

                </div>
                <!-- Brand container ends -->

                <!-- Sidebar profile starts -->
                <div class="sidebar-profile">
                    <img src="{{ asset('images/doctor6.png') }}" class="rounded-5" alt="Hospital Admin Templates">
                    <h6 class="mb-1 profile-name text-nowrap text-truncate text-primary">{{ auth()->user()->name }}</h6>
                    <small class="profile-name text-nowrap text-truncate">
                        @switch(auth()->user()->role)
                            @case(1)
                                <span class="badge bg-primary-subtle text-primary fs-6">Owner</span>
                                @break
                            @case(2)

                                @break
                            @default

                        @endswitch
                    </small>
                    <a href="" class="btn btn-primary mt-2">
                        <i class="ri-account-pin-circle-line"></i> Profile
                    </a>
                </div>
                <!-- Sidebar profile ends -->

                <!-- Sidebar menu starts -->
                @include('components.sidebar')

                <!-- Sidebar menu ends -->

                <!-- Sidebar contact starts -->
                <div class="sidebar-contact">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-logout-box-line"></i> Keluar Aplikasi
                        </button>
                    </form>

                </div>
                <!-- Sidebar contact ends -->

            </nav>
            <!-- Sidebar wrapper ends -->

            <!-- App container starts -->
            <div class="app-container">

                <!-- App header starts -->
                <div class="app-header d-flex align-items-center">

                    <!-- Brand container sm starts -->
                    <div class="brand-container-sm d-xl-none d-flex align-items-center">

                        <!-- App brand starts -->
                        <div class="app-brand">
                            <a href="index.html">
                                <img src="{{ asset('images/logo2.png') }}" class="logo" alt="logo">
                            </a>
                        </div>
                        <!-- App brand ends -->

                        <!-- Toggle sidebar starts -->
                        <button type="button" class="toggle-sidebar">
                            <i class="ri-menu-line"></i>
                        </button>
                        <!-- Toggle sidebar ends -->

                    </div>
                    <!-- Brand container sm ends -->

                    <!-- App header actions starts -->
                    <div class="header-actions text-center">

                        <span>Aplikasi SIMRS Bambu Dua Clinic</span>

                    </div>
                    <!-- App header actions ends -->

                </div>
                <!-- App header ends -->

                <!-- App hero header starts -->
                <div class="app-hero-header d-flex align-items-center">

                    <!-- Breadcrumb starts -->
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="index.html">
                                <i class="ri-home-3-line"></i>
                            </a>
                        </li>
                        <li class="breadcrumb-item text-primary" aria-current="page">
                            Hospital Dashboard
                        </li>
                    </ol>
                    <!-- Breadcrumb ends -->

                    <!-- Sales stats starts -->
                    <div class="ms-auto d-lg-flex d-none flex-row">
                        <div class="input-group">
                            <span class="input-group-text bg-primary-lighten">
                                <i class="ri-calendar-2-line text-primary"></i>
                            </span>
                            <input type="text" id="abc" class="form-control custom-daterange">
                        </div>
                    </div>
                    <!-- Sales stats ends -->

                </div>
                <!-- App Hero header ends -->

                <!-- App body starts -->
                <div class="app-body">

                    @yield('content')

                </div>
                <!-- App body ends -->

                <!-- App footer starts -->
                <div class="app-footer">
                    <span>© Candra 2024</span>
                </div>
                <!-- App footer ends -->

            </div>
            <!-- App container ends -->

        </div>
        <!-- Main container ends -->

    </div>
    <!-- Page wrapper ends -->

    <!-- *************
   ************ JavaScript Files *************
  ************* -->
    <!-- Required jQuery first, then Bootstrap Bundle JS -->
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/moment.min.js') }}"></script>
    @stack('scripts')
</body>

</html>
