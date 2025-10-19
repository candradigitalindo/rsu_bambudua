@extends('layouts.app')
@section('title', 'Dashboard Apotek')
@push('style')
    <!-- Scrollbar CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
    <style>
        a.disabled {
            /* Make the disabled links grayish*/
            color: gray;
            /* And disable the pointer events */
            pointer-events: none;
        }
    </style>
@endpush
@section('content')
    <!-- Row starts -->
    <div class="row gx-3">
        <div class="col-sm-3 col-12">
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Total Obat</h5>
                    <h3>{{ $data['total_obat'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-sm-3 col-12">
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Obat Tersedia</h5>
                    <h3>{{ $data['obat_tersedia'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-sm-3 col-12">
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Obat Habis</h5>
                    <h3>{{ $data['obat_habis'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-sm-3 col-12">
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Obat Kadaluarsa</h5>
                    <h3>{{ $data['obat_kadaluarsa'] }}</h3>
                </div>
            </div>
        </div>
    </div>
    <!-- Row ends -->

    <!-- Row starts -->
    <div class="row gx-3">
        <div class="col-lg-8 col-12">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title">Grafik Pendapatan Obat (Resep & Rawat Inap) Tahun {{ date('Y') }}</h5>
                </div>
                <div class="card-body">
                    <canvas id="grafikNominalTransaksi" height="350"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-12">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title">Berita Terbaru</h5>
                </div>
                <div class="card-body" style="height: 400px; overflow-y: auto;">
                    @forelse ($beritaTerbaru as $berita)
                        <div class="d-flex align-items-start mb-4">
                            <div class="flex-grow-1">
                                <h6 class="m-0">{{ $berita->judul }}</h6>
                                <p class="small m-0 text-muted">
                                    {{ \Illuminate\Support\Str::limit(strip_tags($berita->konten), 70) }}
                                </p>
                                <p class="small m-0 text-muted">
                                    <i class="ri-time-line"></i>
                                    {{ $berita->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center">
                            <p>Belum ada berita yang dipublikasikan.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    <!-- Row ends -->

    <!-- Row starts -->
    <div class="row gx-3">
        <div class="col-12">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title">Data Transaksi Resep</h5>
                </div>
                <div class="card-body">
                    {{-- Filter dan tombol download --}}
                    <form method="GET" class="mb-3" id="filterForm">
                        <div class="d-flex flex-wrap gap-2">
                            <div>
                                <label for="start_date" class="form-label mb-0">Start Date</label>
                                <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}"
                                    class="form-control form-control-sm">
                            </div>
                            <div>
                                <label for="end_date" class="form-label mb-0">End Date</label>
                                <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}"
                                    class="form-control form-control-sm">
                            </div>
                            <div class="align-self-end">
                                <button type="submit" class="btn btn-sm btn-primary" id="btnFilter">
                                    <span id="spinnerFilter" class="spinner-border spinner-border-sm d-none" role="status"
                                        aria-hidden="true"></span>
                                    <span id="textFilter">Filter</span>
                                </button>
                            </div>
                            <div class="align-self-end">
                                <a href="{{ route('apotek.transaksi-resep.pdf', ['start_date' => request('start_date'), 'end_date' => request('end_date')]) }}"
                                    class="btn btn-sm btn-danger">
                                    <i class="ri-file-pdf-line"></i> Download PDF
                                </a>
                                <a href="{{ route('apotek.transaksi-resep.excel', ['start_date' => request('start_date'), 'end_date' => request('end_date')]) }}"
                                    class="btn btn-sm btn-success">
                                    <i class="ri-file-excel-2-line"></i> Download Excel
                                </a>
                            </div>
                        </div>
                    </form>

                    {{-- Tabel Data Transaksi Resep --}}
                    <div class="table-outer">
                        <div class="table-responsive">
                            <table class="table truncate m-0">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>No. Transaksi</th>
                                        <th>Tipe</th>
                                        <th>Pasien</th>
                                        <th class="text-center">Tanggal</th>
                                        <th class="text-end">Nominal</th>
                                        <th class="text-end">Diskon (Rp)</th>
                                        <th class="text-end">Total Bayar</th>
                                        <th class="text-center">Metode Pembayaran</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($data['encounter_terbayar'] as $item)
                                        <tr>
                                            <td>{{ $loop->iteration + $data['encounter_terbayar']->firstItem() - 1 }}
                                            </td>
                                            <td>{{ $item->kode_transaksi ?? '-' }}</td>
                                            <td><span
                                                    class="badge {{ $item->tipe == 'Resep Rawat Jalan' ? 'bg-primary' : 'bg-info' }}">{{ $item->tipe }}</span>
                                            </td>
                                            <td>{{ $item->name_pasien }}</td>
                                            <td class="text-center">
                                                {{ \Carbon\Carbon::parse($item->tanggal_transaksi)->format('d-m-Y') }}
                                            </td>
                                            <td class="text-end">
                                                {{ number_format($item->nominal, 0, ',', '.') }}</td>
                                            <td class="text-end">
                                                {{ number_format($item->diskon_rp ?? 0, 0, ',', '.') }}
                                            </td>
                                            <td class="text-end">
                                                {{ number_format($item->total_bayar, 0, ',', '.') }}
                                            </td>
                                            <td class="text-center">
                                                {{ $item->metode_pembayaran ?? '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center">Data tidak ada</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            <div class="mt-2">
                                {{ $data['encounter_terbayar']->withQueryString()->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Row ends -->
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
