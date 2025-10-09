@extends('layouts.app')

@section('title', 'Detail Pembayaran')

@push('style')
    <!-- Scrollbar CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        .table td,
        .table th {
            white-space: normal !important;
            word-break: break-word;
            vertical-align: middle;
        }

        .card-header-pills {
            font-size: 0.9rem;
        }

        .form-check-input {
            transform: scale(1.2);
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Detail Tagihan Pasien: {{ $pasien->name }}</h5>
                    <span class="badge bg-primary">No. RM: {{ $pasien->rekam_medis }}</span>
                </div>
                <div class="card-body">
                    <form action="{{ route('kasir.processPayment', $pasien->id) }}" method="POST" id="paymentForm">
                        @csrf
                        @if ($unpaidEncounters->isEmpty())
                            <div class="alert alert-info text-center">
                                Tidak ada tagihan yang perlu dibayar untuk pasien ini.
                            </div>
                        @else
                            {{-- Belum Dibayar --}}
                            @foreach ($unpaidEncounters as $encounter)
                                <div class="card mb-3 border">
                                    <div class="card-header bg-light">
                                        <strong>No. Encounter: {{ $encounter->no_encounter }}</strong>
                                        <span class="ms-3">
                                            Tanggal:
                                            {{ \Carbon\Carbon::parse($encounter->created_at)->format('d M Y H:i') }}
                                        </span>
                                    </div>
                                    <div class="card-body p-0">
                                        <table class="table table-sm m-0">
                                            <tbody>
                                                {{-- Tagihan Tindakan --}}
                                                @if ($encounter->total_bayar_tindakan > 0 && !$encounter->status_bayar_tindakan)
                                                    <tr>
                                                        <td style="width: 5%;">
                                                            <div class="form-check">
                                                                <input class="form-check-input payment-item" type="checkbox"
                                                                    name="items_to_pay[]"
                                                                    value="tindakan-{{ $encounter->id }}"
                                                                    data-amount="{{ $encounter->total_bayar_tindakan }}"
                                                                    id="tindakan-{{ $encounter->id }}">
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <label for="tindakan-{{ $encounter->id }}">
                                                                <strong>Tagihan Tindakan</strong>
                                                                <ul class="list-unstyled small text-muted mb-0">
                                                                    @foreach ($encounter->tindakan as $tindakan)
                                                                        <li>- {{ $tindakan->tindakan_name }} (Qty: {{ $tindakan->qty }})</li>
                                                                    @endforeach
                                                                    @php
                                                                        $labItems = ($encounter->labRequests ?? collect())->flatMap(function($lr){ return $lr->items ?? collect(); })->pluck('test_name')->filter()->values()->all();
                                                                        $radItems = ($encounter->radiologyRequests ?? collect())->map(function($rq){ return optional($rq->jenis)->name; })->filter()->values()->all();
                                                                    @endphp
                                                                    @if (!empty($labItems))
                                                                        <li>- Pemeriksaan Lab: {{ implode(', ', $labItems) }}</li>
                                                                    @endif
                                                                    @if (!empty($radItems))
                                                                        <li>- Radiologi: {{ implode(', ', $radItems) }}</li>
                                                                    @endif
                                                                </ul>
                                                            </label>
                                                        </td>
                                                        <td class="text-end" style="width: 20%;">
                                                            {{ 'Rp ' . number_format($encounter->total_bayar_tindakan, 0, ',', '.') }}
                                                        </td>
                                                    </tr>
                                                @endif

                                                {{-- Tagihan Resep --}}
                                                @if ($encounter->total_bayar_resep > 0 && !$encounter->status_bayar_resep)
                                                    <tr>
                                                        <td style="width: 5%;">
                                                            <div class="form-check">
                                                                <input class="form-check-input payment-item" type="checkbox"
                                                                    name="items_to_pay[]"
                                                                    value="resep-{{ $encounter->id }}"
                                                                    data-amount="{{ $encounter->total_bayar_resep }}"
                                                                    id="resep-{{ $encounter->id }}">
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <label for="resep-{{ $encounter->id }}">
                                                                <strong>Tagihan Resep
                                                                    ({{ $encounter->resep->kode_resep ?? '' }})
                                                                </strong>
                                                                <ul class="list-unstyled small text-muted mb-0">
                                                                    @foreach ($encounter->resep->details as $detail)
                                                                        <li>- {{ $detail->nama_obat }} (Qty:
                                                                            {{ $detail->qty }})</li>
                                                                    @endforeach
                                                                </ul>
                                                            </label>
                                                        </td>
                                                        <td class="text-end" style="width: 20%;">
                                                            {{ 'Rp ' . number_format($encounter->total_bayar_resep, 0, ',', '.') }}
                                                        </td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endforeach

                            {{-- Sudah Dibayar (Riwayat) --}}
                            @if (isset($paidEncounters) && $paidEncounters->isNotEmpty())
                                <div class="alert alert-success">Riwayat Tagihan yang sudah dibayar</div>
                                @foreach ($paidEncounters as $encounter)
                                    <div class="card mb-3 border border-success">
                                        <div class="card-header bg-success-subtle">
                                            <strong>No. Encounter: {{ $encounter->no_encounter }}</strong>
                                            <span class="ms-3">Tanggal: {{ \Carbon\Carbon::parse($encounter->updated_at)->format('d M Y H:i') }}</span>
                                        </div>
                                        <div class="card-body p-0">
                                            <table class="table table-sm m-0">
                                                <tbody>
                                                    @if ($encounter->total_bayar_tindakan > 0 && $encounter->status_bayar_tindakan)
                                                        <tr>
                                                            <td style="width:5%"><i class="ri-check-double-line text-success" title="Lunas"></i></td>
                                                            <td>
                                                                <strong>Tagihan Tindakan</strong>
                                                                <span class="badge bg-success ms-2">Lunas</span>
                                                                <ul class="list-unstyled small text-muted mb-0">
                                                                    @foreach ($encounter->tindakan as $tindakan)
                                                                        <li>- {{ $tindakan->tindakan_name }} (Qty: {{ $tindakan->qty }})</li>
                                                                    @endforeach
                                                                    @php
                                                                        $labItems = ($encounter->labRequests ?? collect())->flatMap(function($lr){ return $lr->items ?? collect(); })->pluck('test_name')->filter()->values()->all();
                                                                        $radItems = ($encounter->radiologyRequests ?? collect())->map(function($rq){ return optional($rq->jenis)->name; })->filter()->values()->all();
                                                                    @endphp
                                                                    @if (!empty($labItems))
                                                                        <li>- Pemeriksaan Lab: {{ implode(', ', $labItems) }}</li>
                                                                    @endif
                                                                    @if (!empty($radItems))
                                                                        <li>- Radiologi: {{ implode(', ', $radItems) }}</li>
                                                                    @endif
                                                                </ul>
                                                            </td>
                                                            <td class="text-end" style="width:20%">
                                                                {{ 'Rp ' . number_format($encounter->total_bayar_tindakan, 0, ',', '.') }}
                                                            </td>
                                                        </tr>
                                                    @endif
                                                    @if ($encounter->total_bayar_resep > 0 && $encounter->status_bayar_resep)
                                                        <tr>
                                                            <td style="width:5%"><i class="ri-check-double-line text-success" title="Lunas"></i></td>
                                                            <td>
                                                                <strong>Tagihan Resep ({{ $encounter->resep->kode_resep ?? '' }})</strong>
                                                                <span class="badge bg-success ms-2">Lunas</span>
                                                                <ul class="list-unstyled small text-muted mb-0">
                                                                    @foreach ($encounter->resep->details as $detail)
                                                                        <li>- {{ $detail->nama_obat }} (Qty: {{ $detail->qty }})</li>
                                                                    @endforeach
                                                                </ul>
                                                            </td>
                                                            <td class="text-end" style="width:20%">
                                                                {{ 'Rp ' . number_format($encounter->total_bayar_resep, 0, ',', '.') }}
                                                            </td>
                                                        </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endforeach
                            @endif

                            <hr>

                            <div class="row justify-content-end">
                                <div class="col-md-5">
                                    <div class="mb-3">
                                        <label for="payment_method" class="form-label">Metode Pembayaran</label>
                                        <select class="form-select" id="payment_method" name="payment_method" required>
                                            <option value="" disabled selected>Pilih Metode</option>
                                            @forelse(($paymentMethods ?? []) as $pm)
                                                <option value="{{ $pm->code }}">{{ $pm->name }}</option>
                                            @empty
                                                <option value="" disabled>Belum ada metode pembayaran. Tambahkan di Master Data.</option>
                                            @endforelse
                                        </select>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="mb-0">Total Pembayaran:</h5>
                                        <h4 class="mb-0 text-success" id="totalPayment">Rp 0</h4>
                                    </div>
                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-primary btn-lg" id="btnProcessPayment"
                                            disabled>
                                            <i class="ri-wallet-3-line"></i> Proses Pembayaran
                                        </button>
                                        <a href="{{ route('kasir.index') }}" class="btn btn-secondary">Kembali</a>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Overlay Scroll JS -->
    <script src="{{ asset('vendor/overlay-scroll/jquery.overlayScrollbars.min.js') }}"></script>
    <script src="{{ asset('vendor/overlay-scroll/custom-scrollbar.js') }}"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Custom JS files -->
    <script src="{{ asset('js/custom.js') }}"></script>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const paymentItems = document.querySelectorAll('.payment-item');
            const paymentMethodSelect = document.getElementById('payment_method');
            const totalPaymentEl = document.getElementById('totalPayment');
            const btnProcessPayment = document.getElementById('btnProcessPayment');

            function updateTotal() {
                let total = 0;
                let itemsChecked = 0;
                const paymentMethodSelected = paymentMethodSelect.value !== '';

                paymentItems.forEach(item => {
                    if (item.checked) {
                        total += parseFloat(item.dataset.amount);
                        itemsChecked++;
                    }
                });

                totalPaymentEl.textContent = 'Rp ' + total.toLocaleString('id-ID');
                // Tombol aktif jika ada item yang dipilih DAN metode pembayaran sudah dipilih
                btnProcessPayment.disabled = itemsChecked === 0 || !paymentMethodSelected;
            }

            paymentMethodSelect.addEventListener('change', updateTotal);

            paymentItems.forEach(item => {
                item.addEventListener('change', updateTotal);
            });

            // Initial call in case some items are pre-checked (e.g., on back button)
            updateTotal();
        });
    </script>
@endpush
