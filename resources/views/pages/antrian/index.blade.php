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
            <div class="table-outer">
                <div class="table-responsive">
                    <table class="table truncate m-0">
                        <thead>
                            <tr>
                                <th>Lokasi Loket</th>
                                <th>Halaman Antrian</th>
                                <th>Halaman Minitor</th>

                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($lokasis as $l)
                            <tr>
                                <td>{{ $l->lokasi_loket }}</td>
                                <td class="text-center">
                                    <a href="{{route('antrian.show', $l->id)}}"
                                        class="btn btn-primary btn-sm" id="antrian-{{ $l->id }}">
                                        <i class="ri-printer-line"></i>
                                        <span class="btn-text" id="text-{{ $l->id }}">Buka Halaman </span>
                                        <span class="spinner-border spinner-border-sm d-none"
                                            id="spiner-{{ $l->id }}"></span>
                                    </a>
                                    <script src="{{ asset('js/jquery.min.js') }}"></script>
                                    <script>
                                        $(document).ready(function() {
                                            $("#antrian-{{ $l->id }}").click(function() {
                                                $("#spiner-{{ $l->id }}").removeClass("d-none");
                                                $("#edit-{{ $l->id }}").addClass("disabled", true);
                                                $("#text-{{ $l->id }}").text("Mohon Tunggu ...");
                                            });
                                        });
                                    </script>

                                </td>
                                <td>
                                    <a href="{{ route('antrian.monitor', $l->id) }}"
                                       class="btn btn-primary btn-sm" id="monitor-{{ $l->id }}">
                                        <i class="ri-computer-line"></i>
                                        <span class="btn-text" id="monitor-{{ $l->id }}">Buka Halaman </span>
                                        <span class="spinner-border spinner-border-sm d-none"
                                              id="monitor-{{ $l->id }}"></span>
                                    </a>
                                    <script src="{{ asset('js/jquery.min.js') }}"></script>
                                    <script>
                                        $(document).ready(function() {
                                            $("#monitor-{{ $l->id }}").click(function() {
                                                $("#monitor-{{ $l->id }}").removeClass("d-none");
                                                $("#monitor-{{ $l->id }}").addClass("disabled", true);
                                                $("#monitor-{{ $l->id }}").text("Mohon Tunggu ...");
                                            });
                                        });
                                    </script>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
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
