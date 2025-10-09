<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title','Cetak')</title>
    <style>
        html, body { background: #fff; color: #000; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, 'Noto Sans', 'Liberation Sans', sans-serif; line-height: 1.4; margin: 0; padding: 16px; }
        .container { max-width: 900px; margin: 0 auto; }
        @media print { .no-print { display: none !important; } body { padding: 0 16px; } }
        /* Letterhead */
        .letterhead { margin-bottom: 12px; }
        .lh-row { display: flex; align-items: center; gap: 12px; }
        .lh-left, .lh-right { width: 80px; }
        .lh-center { flex: 1; text-align: center; }
        .lh-logo { width: 72px; height: 72px; object-fit: contain; }
        .lh-name { font-size: 18px; font-weight: 700; }
        .lh-sub { font-size: 12px; color: #555; }
        .lh-divider { border: 0; border-top: 2px solid #000; margin-top: 8px; }
        /* Utility taken from app style (minimal subset) */
        .row { display: flex; flex-wrap: wrap; margin-right: -8px; margin-left: -8px; }
        .col-12 { flex: 0 0 100%; max-width: 100%; padding-left: 8px; padding-right: 8px; }
        .col-lg-10 { flex: 0 0 83.333333%; max-width: 83.333333%; padding-left: 8px; padding-right: 8px; }
        .d-flex { display: flex; }
        .justify-content-between { justify-content: space-between; }
        .align-items-center { align-items: center; }
        .mb-0 { margin-bottom: 0; }
        .mb-1 { margin-bottom: 4px; }
        .mb-2 { margin-bottom: 8px; }
        .mb-3 { margin-bottom: 12px; }
        .fw-semibold { font-weight: 600; }
        .small-text { font-size: 12px; color: #555; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { border-top: 1px solid #ddd; padding: 6px; vertical-align: top; }
        .table thead th { border-bottom: 2px solid #000; }
    </style>
    @stack('style')
</head>
<body>
    <div class="container">
        @include('components.print.header')
        @yield('content')
    </div>
    @stack('scripts')
</body>
</html>
