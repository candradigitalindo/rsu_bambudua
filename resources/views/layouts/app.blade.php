<!DOCTYPE html>
<html lang="id">

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
| ************* -->
    <!-- Preload critical fonts -->
    <link rel="preload" href="{{ asset('fonts/remix/remixicon.css') }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="{{ asset('fonts/remix/remixicon.css') }}"></noscript>
    
    <!-- Critical CSS -->
    <link rel="stylesheet" href="{{ asset('css/main.min.css') }}">
    
    <!-- Custom Stack Styles -->
    @stack('style')
    
    <!-- Performance CSS -->
    <style>
        /* Loading skeleton */
        .skeleton {
            animation: skeleton-loading 1s linear infinite alternate;
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 37%, #f0f0f0 63%);
            background-size: 400% 100%;
        }
        
        @keyframes skeleton-loading {
            0% { background-position: 100% 0; }
            100% { background-position: -100% 0; }
        }
        
        /* Smooth transitions */
        .fade-in {
            animation: fadeIn 0.3s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        /* Optimize rendering */
        .main-container {
            contain: layout style;
        }
    </style>
    @stack('style')
</head>

<body>
    @stack('loading')
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
                            <img src="{{ asset('images/bdc.png') }}" class="logo" alt="Medicare Admin Template">
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
                    @if (auth()->user()->profile == null)
                        <img src="{{ asset('images/no Photo.png') }}" class="rounded-5" alt="Hospital Admin Templates">
                    @else
                        @if (auth()->user()->profile->foto == null)
                            <img src="{{ asset('images/no Photo.png') }}" class="rounded-5"
                                alt="Hospital Admin Templates">
                        @else
                            <img src="{{ route('home.profile.filename', auth()->user()->profile->foto) }}" class="rounded-5"
                                alt="Hospital Admin Templates">
                        @endif
                    @endif

                    <h6 class="mb-1 profile-name text-nowrap text-truncate text-primary"><span
                            class="badge bg-primary-subtle text-primary fs-6">{{ auth()->user()->name }}</span></h6>
                    <small class="profile-name text-nowrap text-truncate">

                    </small>
                </div>
                <!-- Sidebar profile ends -->

                <!-- Sidebar menu starts -->
                @include('components.sidebar')

                <!-- Sidebar menu ends -->

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
                                <img src="{{ asset('images/bdc.png') }}" class="logo" alt="logo">
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
                    <div class="header-actions">

                        <div class="d-lg-flex d-none gap-2">
                            <a href="{{ route('home.profile', auth()->user()->id) }}" class="btn btn-primary">
                                <i class="ri-account-pin-circle-line"></i> Profile
                            </a>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-danger">
                                    <i class="ri-logout-box-line"></i> Keluar Aplikasi
                                </button>
                            </form>
                        </div>
                    </div>
                    <!-- App header actions ends -->

                </div>
                <!-- App header ends -->

                <!-- App body starts -->
                <div class="app-body">

                    @yield('content')

                </div>
                <!-- App body ends -->

            </div>
            <!-- App container ends -->

        </div>
        <!-- Main container ends -->

    </div>
    @include('sweetalert::alert')
    <!-- Page wrapper ends -->

    <!-- *************
   ************ JavaScript Files *************
  ************* -->
    <!-- Critical JavaScript - Load First -->
    <script>
        // Performance monitoring
        if ('performance' in window) {
            window.addEventListener('load', function() {
                setTimeout(function() {
                    const perfData = performance.getEntriesByType('navigation')[0];
                    console.log('Page Load Time:', perfData.loadEventEnd - perfData.fetchStart + 'ms');
                }, 0);
            });
        }
        
        // Basic error handler
        window.addEventListener('error', function(e) {
            console.error('JavaScript Error:', e.error);
        });
    </script>
    
    <!-- Required jQuery first, then Bootstrap Bundle JS -->
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/moment.min.js') }}"></script>
    
    <!-- Page specific scripts -->
    @stack('scripts')
    
    <!-- Global Utils & Error Handler - Load Last -->
    @include('components.scripts.utils')
    @include('components.error-handler')
    
    <!-- Page Ready -->
    <script>
        // Set current user ID for error logging
        @auth
        window.currentUserId = {{ auth()->id() }};
        @endauth
        
        // Page loaded indicator
        document.addEventListener('DOMContentLoaded', function() {
            document.body.classList.add('fade-in');
            console.log('🚀 Bambudua SIMRS loaded successfully');
        });
    </script>
</body>

</html>
