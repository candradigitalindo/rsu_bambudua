@extends('layouts.app')
@section('title', 'Dashboard Apotek')
@push('style')
    <!-- Scrollbar CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
    <style>
        .stat-card {
            border-radius: 12px;
            border: none;
            background: white;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            border-left: 5px solid;
            transition: all 0.3s ease;
            position: relative;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: 100px;
            opacity: 0.05;
            border-radius: 50%;
            transform: translate(30%, -30%);
        }

        .stat-card:hover {
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
            transform: translateY(-3px);
        }

        .stat-card .card-body {
            padding: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            z-index: 1;
        }

        .stat-content {
            flex: 1;
        }

        .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 800;
            line-height: 1;
            margin-bottom: 0.5rem;
            color: #111827;
            letter-spacing: -0.5px;
        }

        .stat-label {
            font-size: 0.875rem;
            font-weight: 600;
            color: #4b5563;
            margin-bottom: 0.35rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-target {
            font-size: 0.75rem;
            color: #9ca3af;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
        }

        .stat-badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 6px;
            font-size: 0.7rem;
            font-weight: 600;
            margin-top: 0.25rem;
        }

        .border-primary {
            border-left-color: #667eea !important;
        }

        .border-primary::before {
            background: #667eea;
        }

        .border-success {
            border-left-color: #10b981 !important;
        }

        .border-success::before {
            background: #10b981;
        }

        .border-danger {
            border-left-color: #ef4444 !important;
        }

        .border-danger::before {
            background: #ef4444;
        }

        .border-warning {
            border-left-color: #f59e0b !important;
        }

        .border-warning::before {
            background: #f59e0b;
        }

        .icon-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .icon-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }

        .icon-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
        }

        .icon-warning {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
        }

        .badge-primary {
            background: #ede9fe;
            color: #667eea;
        }

        .badge-success {
            background: #d1fae5;
            color: #059669;
        }

        .badge-danger {
            background: #fee2e2;
            color: #dc2626;
        }

        .badge-warning {
            background: #fef3c7;
            color: #d97706;
        }

        .chart-card {
            border-radius: 12px;
            border: none;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        }

        .news-item {
            padding: 1rem;
            border-radius: 8px;
            transition: all 0.2s;
            cursor: pointer;
            border: 1px solid #f0f0f0;
        }

        .news-item:hover {
            background: #f8f9fa;
            transform: translateX(5px);
        }

        .badge-modern {
            padding: 0.35rem 0.65rem;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.75rem;
        }

        .table-modern {
            border-radius: 8px;
            overflow: hidden;
        }

        .table-modern thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .table-modern thead th {
            border: none;
            font-weight: 600;
            padding: 1rem;
            font-size: 0.875rem;
        }

        .table-modern tbody tr {
            transition: all 0.2s;
        }

        .table-modern tbody tr:hover {
            background: #f8f9fa;
        }

        .filter-card {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }
    </style>
@endpush
@section('content')
    <!-- Statistics List -->
    <div class="row gx-3 mb-3">
        <div class="col-lg-3 col-md-6 col-12 mb-3">
            <div class="card stat-card border-primary">
                <div class="card-body">
                    <div class="stat-content">
                        <div class="stat-label">Total Produk Obat</div>
                        <div class="stat-value">{{ number_format($data['total_obat']) }}</div>
                        <div class="stat-target">
                            <i class="ri-information-line"></i> Semua jenis produk
                        </div>
                        @php
                            $persentaseTersedia =
                                $data['total_obat'] > 0
                                    ? round(($data['obat_tersedia'] / $data['total_obat']) * 100, 1)
                                    : 0;
                        @endphp
                        <span class="stat-badge badge-primary">
                            <i class="ri-check-line"></i> {{ $persentaseTersedia }}% Tersedia
                        </span>
                    </div>
                    <div class="stat-icon icon-primary">
                        <i class="ri-medicine-bottle-line"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-12 mb-3">
            <div class="card stat-card border-success">
                <div class="card-body">
                    <div class="stat-content">
                        <div class="stat-label">Obat Tersedia</div>
                        <div class="stat-value">{{ number_format($data['obat_tersedia']) }}</div>
                        <div class="stat-target">
                            <i class="ri-check-double-line"></i> Stok ready untuk digunakan
                        </div>
                        <span class="stat-badge badge-success">
                            <i class="ri-shield-check-line"></i> Stok Aman
                        </span>
                    </div>
                    <div class="stat-icon icon-success">
                        <i class="ri-checkbox-circle-line"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-12 mb-3">
            <a href="{{ route('products.index', ['filter' => 'habis']) }}" class="text-decoration-none">
                <div class="card stat-card border-danger">
                    <div class="card-body">
                        <div class="stat-content">
                            <div class="stat-label">Obat Habis</div>
                            <div class="stat-value">{{ number_format($data['obat_habis']) }}</div>
                            <div class="stat-target">
                                <i class="ri-alert-line"></i> Perlu restock segera
                            </div>
                            <span class="stat-badge badge-danger">
                                <i class="ri-arrow-right-circle-line"></i> Lihat Detail
                            </span>
                        </div>
                        <div class="stat-icon icon-danger">
                            <i class="ri-alert-line"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-lg-3 col-md-6 col-12 mb-3">
            <a href="{{ route('products.index', ['filter' => 'kadaluarsa']) }}" class="text-decoration-none">
                <div class="card stat-card border-warning">
                    <div class="card-body">
                        <div class="stat-content">
                            <div class="stat-label">Obat Kadaluarsa</div>
                            <div class="stat-value">{{ number_format($data['obat_kadaluarsa']) }}</div>
                            <div class="stat-target">
                                <i class="ri-time-line"></i> Expired atau akan expired
                            </div>
                            <span class="stat-badge badge-warning">
                                <i class="ri-arrow-right-circle-line"></i> Lihat Detail
                            </span>
                        </div>
                        <div class="stat-icon icon-warning">
                            <i class="ri-time-line"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Chart & News Section -->
    <div class="row gx-3">
        <div class="col-lg-8 col-12 mb-3">
            <div class="card chart-card">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1 fw-bold">
                                <i class="ri-bar-chart-2-line text-primary"></i>
                                Grafik Pendapatan Apotek
                            </h5>
                            <small class="text-muted">Pendapatan dari Resep & Rawat Inap Tahun {{ date('Y') }}</small>
                        </div>
                        <span class="badge badge-modern"
                            style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            {{ date('Y') }}
                        </span>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <canvas id="grafikNominalTransaksi" height="350"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-12 mb-3">
            <div class="card chart-card">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="ri-newspaper-line text-info"></i>
                        Berita & Pengumuman
                    </h5>
                </div>
                <div class="card-body" style="height: 400px; overflow-y: auto;">
                    @forelse ($beritaTerbaru as $berita)
                        <div class="news-item mb-3">
                            <h6 class="mb-2 fw-bold">{{ $berita->judul }}</h6>
                            <p class="small text-muted mb-2">
                                {{ \Illuminate\Support\Str::limit(strip_tags($berita->konten), 80) }}
                            </p>
                            <div class="d-flex align-items-center justify-content-between">
                                <small class="text-muted">
                                    <i class="ri-time-line"></i>
                                    {{ $berita->created_at->diffForHumans() }}
                                </small>
                                <span class="badge bg-info badge-modern">Baru</span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <i class="ri-inbox-line" style="font-size: 3rem; opacity: 0.3;"></i>
                            <p class="text-muted mt-2">Belum ada berita</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Transaction Table Section -->
    <div class="row gx-3">
        <div class="col-12">
            <div class="card chart-card">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1 fw-bold">
                                <i class="ri-file-list-3-line text-success"></i>
                                Riwayat Transaksi Resep
                            </h5>
                            <small class="text-muted">Data transaksi pembayaran resep obat</small>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    {{-- Filter Section --}}
                    <form method="GET" class="filter-card" id="filterForm">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="start_date" class="form-label fw-semibold small">
                                    <i class="ri-calendar-line"></i> Tanggal Mulai
                                </label>
                                <input type="date" name="start_date" id="start_date"
                                    value="{{ request('start_date') }}" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label for="end_date" class="form-label fw-semibold small">
                                    <i class="ri-calendar-check-line"></i> Tanggal Akhir
                                </label>
                                <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}"
                                    class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Aksi</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary" id="btnFilter">
                                        <span id="spinnerFilter" class="spinner-border spinner-border-sm d-none"
                                            role="status"></span>
                                        <i class="ri-filter-3-line"></i>
                                        <span id="textFilter">Terapkan Filter</span>
                                    </button>
                                    <a href="{{ route('apotek.transaksi-resep.pdf', ['start_date' => request('start_date'), 'end_date' => request('end_date')]) }}"
                                        class="btn btn-danger" title="Download PDF">
                                        <i class="ri-file-pdf-line"></i> PDF
                                    </a>
                                    <a href="{{ route('apotek.transaksi-resep.excel', ['start_date' => request('start_date'), 'end_date' => request('end_date')]) }}"
                                        class="btn btn-success" title="Download Excel">
                                        <i class="ri-file-excel-2-line"></i> Excel
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>

                    {{-- Transaction Table --}}
                    <div class="table-responsive">
                        <table class="table table-modern table-hover align-middle">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">No</th>
                                    <th>No. Transaksi</th>
                                    <th>Tipe</th>
                                    <th>Pasien</th>
                                    <th class="text-center">Tanggal</th>
                                    <th class="text-end">Nominal</th>
                                    <th class="text-end">Diskon</th>
                                    <th class="text-end">Total Bayar</th>
                                    <th class="text-center">Metode</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($data['encounter_terbayar'] as $item)
                                    <tr>
                                        <td class="fw-semibold">
                                            {{ $loop->iteration + $data['encounter_terbayar']->firstItem() - 1 }}</td>
                                        <td>
                                            <span
                                                class="badge bg-primary badge-modern">{{ $item->kode_transaksi ?? '-' }}</span>
                                        </td>
                                        <td>
                                            <span
                                                class="badge badge-modern {{ $item->tipe == 'Resep Rawat Jalan' ? 'bg-primary' : 'bg-info' }}">
                                                {{ $item->tipe }}
                                            </span>
                                        </td>
                                        <td class="fw-semibold">{{ $item->name_pasien }}</td>
                                        <td class="text-center">
                                            <small>{{ \Carbon\Carbon::parse($item->tanggal_transaksi)->format('d/m/Y') }}</small>
                                        </td>
                                        <td class="text-end fw-semibold">Rp
                                            {{ number_format($item->nominal, 0, ',', '.') }}</td>
                                        <td class="text-end text-danger">
                                            @if ($item->diskon_rp > 0)
                                                -Rp {{ number_format($item->diskon_rp, 0, ',', '.') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="text-end fw-bold text-success">Rp
                                            {{ number_format($item->total_bayar, 0, ',', '.') }}</td>
                                        <td class="text-center">
                                            <span
                                                class="badge bg-success badge-modern">{{ $item->metode_pembayaran ?? 'Cash' }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-5">
                                            <i class="ri-file-list-line" style="font-size: 3rem; opacity: 0.3;"></i>
                                            <p class="text-muted mt-2">Belum ada data transaksi</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if ($data['encounter_terbayar']->hasPages())
                        <div class="mt-3 d-flex justify-content-center">
                            {{ $data['encounter_terbayar']->withQueryString()->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <!-- Overlay Scroll JS -->
    <script src="{{ asset('vendor/overlay-scroll/jquery.overlayScrollbars.min.js') }}"></script>
    <script src="{{ asset('vendor/overlay-scroll/custom-scrollbar.js') }}"></script>
    <!-- Custom JS files -->
    <script src="{{ asset('js/custom.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Grafik Nominal Transaksi
        const ctxNominal = document.getElementById('grafikNominalTransaksi').getContext('2d');
        const rawData = @json($data['nominal_transaksi_per_bulan']);
        const isOwner = {{ auth()->user()->role == 1 ? 'true' : 'false' }};

        const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        const monthCount = isOwner ? 12 : 3;
        const labels = [];
        const chartData = [];

        for (let i = monthCount - 1; i >= 0; i--) {
            const date = new Date();
            date.setMonth(date.getMonth() - i);
            const year = date.getFullYear();
            const month = date.getMonth() + 1;
            labels.push(monthNames[month - 1]);
            chartData.push(rawData[`${year}-${month}`] || 0);
        }

        new Chart(ctxNominal, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total Pendapatan Obat (Rp)',
                    data: chartData,
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1,
                    maxBarThickness: 50
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });

        document.getElementById('filterForm').addEventListener('submit', function() {
            document.getElementById('spinnerFilter').classList.remove('d-none');
            document.getElementById('textFilter').textContent = 'Loading...';
            document.getElementById('btnFilter').setAttribute('disabled', true);
        });
    </script>
@endpush
