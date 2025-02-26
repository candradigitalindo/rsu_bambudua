<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Minitor</title>

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
        <img src="{{ asset('images/bdc.png') }}" class="logo" width="200">
        <h2 class="mb-2">{{$data['lokasi']}}</h2>

        <h3 class="fw-bold mb-4 mt-4 h1">
            {{ $data['antrian'] }}
        </h3>

    </div>
    <!-- Error container ends -->
</div>

<script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('js/moment.min.js') }}"></script>
<!-- Custom JS files -->
<script src="{{ asset('js/custom.js') }}"></script>
<script src="https://js.pusher.com/8.0.1/pusher.min.js"></script>
<script>
    var pusher = new Pusher("mf2n4zlwdsh4ogrkobrv", {
        cluster: "",
        enabledTransports: ['ws'],
        forceTLS:false,
        wsHost: "127.0.0.1",
        wsPort: "8080"
    });
    var channel = pusher.subscribe("monitor.umum");
    channel.bind("app\\Events\\AntrianEvent", (data) => {
        console.log(data)
    });
</script>
</body>

</html>
