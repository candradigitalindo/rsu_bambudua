<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Antrian</title>

    <!-- Meta -->
    <meta name="description" content="Marketplace for Bootstrap Admin Dashboards">
    <meta property="og:title" content="Admin Templates - Dashboard Templates">
    <meta property="og:description" content="Marketplace for Bootstrap Admin Dashboards">
    <meta property="og:type" content="Website">
    <link rel="shortcut icon" href="{{ asset('images/logo.png') }}">

    <!-- *************
   ************ CSS Files *************
  ************* -->
    <link rel="stylesheet" href="{{ asset('fonts/remix/remixicon.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main.min.css') }}">

</head>

<body class="error-bg">
<div class="container">
    <!-- Error container starts -->
    <div class="error-container">
        <img src="{{ asset('images/bdc.png') }}" class="logo" width="500">
        <h2 class="mb-2">{{$lokasi->lokasi_loket}}</h2>
        <h3 class="fw-light mb-4">
            Silahkan Tekan Tombol Ambil Antrian dibawah ini.
        </h3>
        <a href="{{ route('antrian.store', $lokasi->id) }}" target="_blank" class="btn btn-danger btn-lg py-2 rounded-5"><i class="ri-printer-line lh-1 me-1"></i>
            Ambil Antrian
        </a>
    </div>
    <!-- Error container ends -->
</div>

<script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('js/moment.min.js') }}"></script>
<!-- Custom JS files -->
<script src="{{ asset('js/custom.js') }}"></script>
</body>

</html>
