@extends('layouts.app')

@section('title', 'Pembayaran Kasir')
@push('style')
    <!-- Scrollbar CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
    <style>
        .table td,
        .table th {
            white-space: normal !important;
            word-break: break-word;
            vertical-align: middle;
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            @if (session('success'))
                <div class="alert alert-success d-flex justify-content-between align-items-center">
                    {{ session('success') }}
                    @if (session('show_print_button'))
                        <a href="{{ route('kasir.cetakStrukTerakhir') }}" target="_blank" class="btn btn-sm btn-light">
                            <i class="ri-printer-line"></i> Cetak Struk
                        </a>
                    @endif
                </div>
            @endif
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Daftar Tagihan Pasien</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('kasir.index') }}" method="GET" class="mb-3">
                        <div class="d-flex flex-wrap gap-2">
                            <div class="flex-grow-1">
                                <input type="text" class="form-control" name="search"
                                    placeholder="Cari No. RM atau Nama Pasien..." value="{{ request('search') }}">
                            </div>
                            <div>
                                <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                            </div>
                            <div>
                                <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                            </div>
                            <div>
                                <button class="btn btn-primary" type="submit">Filter</button>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle m-0">
                            <thead>
                                <tr>
                                    <th>Rekam Medis</th>
                                    <th>Nama Pasien</th>
                                    <th>Jenis Kunjungan</th>
                                    <th>Kunjungan Terakhir</th>
                                    <th>Total Tagihan</th>
                                    <th>Status Bayar</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($patients as $patient)
                                    <tr>
                                        <td>{{ $patient->rekam_medis }}</td>
                                        <td>{{ $patient->name_pasien }}</td>
                                        <td>
                                            <span class="badge bg-info">{{ $patient->jenis_kunjungan }}</span>
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($patient->last_visit)->format('d M Y H:i') }}</td>
                                        <td class="text-end">
                                            {{ 'Rp. ' . number_format($patient->total_tagihan, 0, ',', '.') }}
                                        </td>
                                        <td>
                                            @if ($patient->unpaid_tindakan > 0)
                                                <span class="badge bg-danger">
                                                    Tindakan: {{ $patient->unpaid_tindakan }} Belum Lunas
                                                </span>
                                                <br>
                                            @endif
                                            @if ($patient->unpaid_resep > 0)
                                                <span class="badge bg-danger">
                                                    Resep: {{ $patient->unpaid_resep }} Belum Lunas
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('kasir.show', $patient->pasien_id) }}"
                                                class="btn btn-primary btn-sm">
                                                <i class="ri-wallet-3-line"></i> Bayar
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Tidak ada data transaksi yang perlu
                                            dibayar.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $patients->appends(request()->query())->links() }}
                    </div>
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
@endpush
