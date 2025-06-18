@extends('layouts.app')
@section('title', 'Dashboard Apotek')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Dashboard Apotek</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body">
                                        <h5 class="card-title">Total Obat</h5>
                                        <p class="card-text">{{ $data['total_obat'] }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body">
                                        <h5 class="card-title">Obat Tersedia</h5>
                                        <p class="card-text">{{ $data['obat_tersedia'] }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body">
                                        <h5 class="card-title">Obat Habis</h5>
                                        <p class="card-text">{{ $data['obat_habis'] }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-danger text-white">
                                    <div class="card-body">
                                        <h5 class="card-title">Obat Kadaluarsa</h5>
                                        <p class="card-text">{{ $data['obat_kadaluarsa'] }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row mt-4">
                            <div class="col-12">
                                <h5 class="card-title">Grafik Transaksi Resep Tahun {{ date('Y') }}</h5>
                                <div style="width: 100%; min-width: 300px;">
                                    <canvas id="grafikNominalTransaksi" height="350"></canvas>
                                </div>
                            </div>
                        </div>
                        <hr>
                        {{-- Filter dan tombol download --}}
                        <div class="row mt-4">
                            <div class="col-12">
                                <h5 class="card-title">Data Transaksi Resep</h5>
                                <form method="GET" class="mb-3" id="filterForm">
                                    <div class="d-flex flex-wrap gap-2">
                                        <div>
                                            <label for="start_date" class="form-label mb-0">Start Date</label>
                                            <input type="date" name="start_date" id="start_date"
                                                value="{{ request('start_date') }}" class="form-control form-control-sm">
                                        </div>
                                        <div>
                                            <label for="end_date" class="form-label mb-0">End Date</label>
                                            <input type="date" name="end_date" id="end_date"
                                                value="{{ request('end_date') }}" class="form-control form-control-sm">
                                        </div>
                                        <div class="align-self-end">
                                            <button type="submit" class="btn btn-sm btn-primary" id="btnFilter">
                                                <span id="spinnerFilter" class="spinner-border spinner-border-sm d-none"
                                                    role="status" aria-hidden="true"></span>
                                                <span id="textFilter">Filter</span>
                                            </button>
                                        </div>
                                        <div class="align-self-end">
                                            <a href="{{ route('apotek.transaksi-resep.pdf', ['start_date' => request('start_date'), 'end_date' => request('end_date')]) }}" class="btn btn-sm btn-danger">
                                                <i class="ri-file-pdf-line"></i> Download PDF
                                            </a>
                                            <a href="{{ route('apotek.transaksi-resep.excel', ['start_date' => request('start_date'), 'end_date' => request('end_date')]) }}" class="btn btn-sm btn-success">
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
                                                    <th>No. Resep</th>
                                                    <th>Pasien</th>
                                                    <th class="text-center">Tanggal</th>
                                                    <th class="text-end">Nominal</th>
                                                    <th class="text-end">Diskon (Rp)</th>
                                                    <th class="text-end">Diskon (%)</th>
                                                    <th class="text-end">Total Bayar</th>
                                                    <th class="text-center">Metode Pembayaran</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($data['encounter_terbayar'] as $i => $encounter)
                                                    <tr>
                                                        <td>{{ $i+1 }}</td>
                                                        <td>{{ $encounter->resep->kode_resep ?? '-' }}</td>
                                                        <td>{{ $encounter->name_pasien }}</td>
                                                        <td class="text-center">{{ \Carbon\Carbon::parse($encounter->created_at)->format('d-m-Y') }}
                                                        </td>
                                                        <td class="text-end">{{ number_format($encounter->total_resep,0,',','.') }}</td>
                                                        <td class="text-end">{{ number_format($encounter->diskon_resep ?? 0,0,',','.') }}</td>
                                                        <td class="text-end">{{ $encounter->diskon_persen_resep ?? 0 }}%</td>
                                                        <td class="text-end">{{ number_format($encounter->total_bayar_resep,0,',','.') }}</td>
                                                        <td class="text-center">{{ $encounter->metode_pembayaran_resep ?? '-' }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="8" class="text-center">Data tidak ada</td>
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
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Grafik Nominal Transaksi
        const ctxNominal = document.getElementById('grafikNominalTransaksi').getContext('2d');
        const dataNominal = @json(array_values($data['nominal_transaksi_per_bulan']));
        const bulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        new Chart(ctxNominal, {
            type: 'bar',
            data: {
                labels: bulan,
                datasets: [{
                    label: 'Nominal Transaksi (Rp)',
                    data: dataNominal,
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
