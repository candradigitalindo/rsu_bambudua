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

        .bill-summary-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        .summary-item:last-child {
            border-bottom: none;
            padding-top: 15px;
            font-size: 1.3rem;
            font-weight: bold;
        }

        .payment-split-row {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 10px;
            border: 2px dashed #dee2e6;
        }

        .payment-split-row.active {
            border-color: #667eea;
            background: #f0f4ff;
        }

        .category-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-right: 8px;
        }

        .category-tindakan {
            background: #e3f2fd;
            color: #1976d2;
        }

        .category-resep {
            background: #f3e5f5;
            color: #7b1fa2;
        }

        .category-lab {
            background: #e8f5e9;
            color: #388e3c;
        }

        .category-radiologi {
            background: #fff3e0;
            color: #f57c00;
        }

        .total-display {
            font-size: 2rem;
            font-weight: bold;
            color: #28a745;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
        }

        .calculator-box {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .change-display {
            font-size: 1.5rem;
            padding: 15px;
            background: #d4edda;
            border-radius: 8px;
            border: 2px solid #28a745;
            text-align: center;
            margin-top: 15px;
        }

        .insufficient-display {
            background: #f8d7da;
            border-color: #dc3545;
            color: #721c24;
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">

                    <div>
                        <span class="badge bg-primary fs-6">{{ $pasien->name }}</span>
                        <span class="badge bg-primary fs-6">No. RM: {{ $pasien->rekam_medis }}</span>
                    </div>
                </div>
                <div class="card-body">
                    @if ($unpaidEncounters->isEmpty())
                        <div class="alert alert-info text-center">
                            <i class="ri-information-line fs-3"></i>
                            <p class="mb-0 mt-2">Tidak ada tagihan yang perlu dibayar untuk pasien ini.</p>
                        </div>
                    @else
                        <form action="{{ route('kasir.processPayment', $pasien->id) }}" method="POST" id="paymentForm">
                            @csrf

                            {{-- Hidden inputs untuk encounter IDs --}}
                            @foreach ($unpaidEncounters as $encounter)
                                <input type="hidden" name="encounter_ids[]" value="{{ $encounter->id }}">
                            @endforeach

                            {{-- Detail Tagihan per Encounter --}}
                            <h5 class="mb-3 mt-4"><i class="ri-file-text-line"></i> Detail Tagihan</h5>
                            @foreach ($unpaidEncounters as $encounter)
                                <div class="card mb-3 border">
                                    <div class="card-header bg-light">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong><i class="ri-hospital-line"></i> Encounter:
                                                    {{ $encounter->no_encounter }}</strong>
                                                <span class="ms-3 text-muted">
                                                    <i class="ri-calendar-line"></i>
                                                    {{ \Carbon\Carbon::parse($encounter->created_at)->format('d M Y H:i') }}
                                                </span>
                                            </div>
                                            <span class="badge bg-warning">Belum Lunas</span>
                                        </div>
                                    </div>
                                    <div class="card-body p-0">
                                        <table class="table table-sm table-hover m-0">
                                            <thead class="table-secondary">
                                                <tr>
                                                    <th style="width: 3%;" class="text-center">
                                                        <div
                                                            class="form-check d-flex justify-content-center align-items-center">
                                                            <input class="form-check-input me-1" type="checkbox"
                                                                id="checkAll-{{ $encounter->id }}"
                                                                data-encounter="{{ $encounter->id }}"
                                                                title="Pilih semua item">
                                                            <label class="form-check-label small text-nowrap"
                                                                for="checkAll-{{ $encounter->id }}"
                                                                style="cursor: pointer; font-size: 0.7rem;">
                                                                Semua
                                                            </label>
                                                        </div>
                                                    </th>
                                                    <th style="width: 47%;">Item / Layanan</th>
                                                    <th style="width: 10%;" class="text-center">Qty</th>
                                                    <th style="width: 20%;" class="text-end">Harga Satuan</th>
                                                    <th style="width: 20%;" class="text-end">Subtotal</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {{-- Tagihan Tindakan --}}
                                                @if ($encounter->total_bayar_tindakan > 0 && !$encounter->status_bayar_tindakan)
                                                    @php
                                                        $tindakanIndex = 0;
                                                    @endphp
                                                    @foreach ($encounter->tindakan as $tindakan)
                                                        @php
                                                            // Harga satuan dari tindakan_harga, atau hitung dari total_harga / qty
                                                            $hargaSatuan =
                                                                $tindakan->tindakan_harga ??
                                                                ($tindakan->qty > 0
                                                                    ? $tindakan->total_harga / $tindakan->qty
                                                                    : 0);
                                                            $subtotal =
                                                                $tindakan->total_harga ?? $hargaSatuan * $tindakan->qty;
                                                        @endphp
                                                        <tr>
                                                            <td class="align-middle text-center">
                                                                <div class="form-check d-flex justify-content-center">
                                                                    <input class="form-check-input payment-item"
                                                                        type="checkbox" name="items_to_pay[]"
                                                                        value="tindakan-{{ $encounter->id }}-{{ $tindakan->id }}"
                                                                        data-amount="{{ $subtotal }}"
                                                                        data-encounter="{{ $encounter->id }}"
                                                                        data-type="tindakan"
                                                                        id="tindakan-{{ $encounter->id }}-{{ $tindakan->id }}">
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <label
                                                                    for="tindakan-{{ $encounter->id }}-{{ $tindakan->id }}"
                                                                    class="mb-0 cursor-pointer">
                                                                    @if ($tindakanIndex === 0)
                                                                        <span
                                                                            class="category-badge category-tindakan">TINDAKAN</span>
                                                                        <br>
                                                                    @endif
                                                                    <i class="ri-stethoscope-line text-primary"></i>
                                                                    {{ $tindakan->tindakan_name }}
                                                                </label>
                                                            </td>
                                                            <td class="text-center align-middle">
                                                                <span class="badge bg-primary">{{ $tindakan->qty }}</span>
                                                            </td>
                                                            <td class="text-end align-middle">
                                                                <span class="text-muted">Rp
                                                                    {{ number_format($hargaSatuan, 0, ',', '.') }}</span>
                                                            </td>
                                                            <td class="text-end align-middle">
                                                                <strong class="text-primary">Rp
                                                                    {{ number_format($subtotal, 0, ',', '.') }}</strong>
                                                            </td>
                                                        </tr>
                                                        @php
                                                            $tindakanIndex++;
                                                        @endphp
                                                    @endforeach

                                                    {{-- Lab Items --}}
                                                    @php
                                                        $labIndex = 0;
                                                    @endphp
                                                    @foreach ($encounter->labRequests ?? [] as $labRequest)
                                                        @foreach ($labRequest->items ?? [] as $labItem)
                                                            @php
                                                                $labSubtotal = $labItem->price ?? 0;
                                                            @endphp
                                                            <tr>
                                                                <td class="align-middle text-center">
                                                                    <div class="form-check d-flex justify-content-center">
                                                                        <input class="form-check-input payment-item"
                                                                            type="checkbox" name="items_to_pay[]"
                                                                            value="lab-{{ $encounter->id }}-{{ $labItem->id }}"
                                                                            data-amount="{{ $labSubtotal }}"
                                                                            data-encounter="{{ $encounter->id }}"
                                                                            data-type="tindakan"
                                                                            id="lab-{{ $encounter->id }}-{{ $labItem->id }}">
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <label
                                                                        for="lab-{{ $encounter->id }}-{{ $labItem->id }}"
                                                                        class="mb-0 cursor-pointer">
                                                                        @if ($labIndex === 0)
                                                                            <span
                                                                                class="category-badge category-lab">LABORATORIUM</span>
                                                                            <br>
                                                                        @endif
                                                                        <i class="ri-test-tube-line text-success"></i>
                                                                        {{ $labItem->test_name }}
                                                                    </label>
                                                                </td>
                                                                <td class="text-center align-middle">
                                                                    <span class="badge bg-primary">1</span>
                                                                </td>
                                                                <td class="text-end align-middle">
                                                                    <span class="text-muted">Rp
                                                                        {{ number_format($labSubtotal, 0, ',', '.') }}</span>
                                                                </td>
                                                                <td class="text-end align-middle">
                                                                    <strong class="text-success">Rp
                                                                        {{ number_format($labSubtotal, 0, ',', '.') }}</strong>
                                                                </td>
                                                            </tr>
                                                            @php
                                                                $labIndex++;
                                                            @endphp
                                                        @endforeach
                                                    @endforeach

                                                    {{-- Radiology Items --}}
                                                    @php
                                                        $radIndex = 0;
                                                    @endphp
                                                    @foreach ($encounter->radiologyRequests ?? [] as $radRequest)
                                                        @php
                                                            $radSubtotal = $radRequest->price ?? 0;
                                                        @endphp
                                                        <tr>
                                                            <td class="align-middle text-center">
                                                                <div class="form-check d-flex justify-content-center">
                                                                    <input class="form-check-input payment-item"
                                                                        type="checkbox" name="items_to_pay[]"
                                                                        value="radiologi-{{ $encounter->id }}-{{ $radRequest->id }}"
                                                                        data-amount="{{ $radSubtotal }}"
                                                                        data-encounter="{{ $encounter->id }}"
                                                                        data-type="tindakan"
                                                                        id="radiologi-{{ $encounter->id }}-{{ $radRequest->id }}">
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <label
                                                                    for="radiologi-{{ $encounter->id }}-{{ $radRequest->id }}"
                                                                    class="mb-0 cursor-pointer">
                                                                    @if ($radIndex === 0)
                                                                        <span
                                                                            class="category-badge category-radiologi">RADIOLOGI</span>
                                                                        <br>
                                                                    @endif
                                                                    <i class="ri-image-line text-warning"></i>
                                                                    {{ optional($radRequest->jenis)->name ?? 'Pemeriksaan Radiologi' }}
                                                                </label>
                                                            </td>
                                                            <td class="text-center align-middle">
                                                                <span class="badge bg-primary">1</span>
                                                            </td>
                                                            <td class="text-end align-middle">
                                                                <span class="text-muted">Rp
                                                                    {{ number_format($radSubtotal, 0, ',', '.') }}</span>
                                                            </td>
                                                            <td class="text-end align-middle">
                                                                <strong class="text-warning">Rp
                                                                    {{ number_format($radSubtotal, 0, ',', '.') }}</strong>
                                                            </td>
                                                        </tr>
                                                        @php
                                                            $radIndex++;
                                                        @endphp
                                                    @endforeach

                                                    {{-- Total Tindakan --}}
                                                    <tr class="table-primary">
                                                        <td colspan="4" class="text-end"><strong>TOTAL TINDAKAN &
                                                                PENUNJANG</strong></td>
                                                        <td class="text-end">
                                                            <strong class="fs-6 text-primary">Rp
                                                                {{ number_format($encounter->total_bayar_tindakan, 0, ',', '.') }}</strong>
                                                        </td>
                                                    </tr>
                                                @endif

                                                {{-- Tagihan Resep --}}
                                                @if ($encounter->total_bayar_resep > 0 && !$encounter->status_bayar_resep)
                                                    @php
                                                        $resepIndex = 0;
                                                    @endphp
                                                    @foreach ($encounter->resep->details as $detail)
                                                        @php
                                                            $subtotalObat = ($detail->harga ?? 0) * $detail->qty;
                                                        @endphp
                                                        <tr>
                                                            <td class="align-middle text-center">
                                                                <div class="form-check d-flex justify-content-center">
                                                                    <input class="form-check-input payment-item"
                                                                        type="checkbox" name="items_to_pay[]"
                                                                        value="resep-{{ $encounter->id }}-{{ $detail->id }}"
                                                                        data-amount="{{ $subtotalObat }}"
                                                                        data-encounter="{{ $encounter->id }}"
                                                                        data-type="resep"
                                                                        id="resep-{{ $encounter->id }}-{{ $detail->id }}">
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <label
                                                                    for="resep-{{ $encounter->id }}-{{ $detail->id }}"
                                                                    class="mb-0 cursor-pointer">
                                                                    @if ($resepIndex === 0)
                                                                        <span class="category-badge category-resep">RESEP
                                                                            OBAT</span>
                                                                        <span
                                                                            class="badge bg-info">{{ $encounter->resep->kode_resep ?? '' }}</span>
                                                                        <br>
                                                                    @endif
                                                                    <i class="ri-capsule-line text-purple"></i>
                                                                    {{ $detail->nama_obat }}
                                                                </label>
                                                            </td>
                                                            <td class="text-center align-middle">
                                                                <span class="badge bg-primary">{{ $detail->qty }}</span>
                                                            </td>
                                                            <td class="text-end align-middle">
                                                                <span class="text-muted">Rp
                                                                    {{ number_format($detail->harga ?? 0, 0, ',', '.') }}</span>
                                                            </td>
                                                            <td class="text-end align-middle">
                                                                <strong style="color: #7b1fa2;">Rp
                                                                    {{ number_format($subtotalObat, 0, ',', '.') }}</strong>
                                                            </td>
                                                        </tr>
                                                        @php
                                                            $resepIndex++;
                                                        @endphp
                                                    @endforeach

                                                    {{-- Total Resep --}}
                                                    <tr class="table-light" style="background-color: #f3e5f5 !important;">
                                                        <td colspan="4" class="text-end"><strong>TOTAL
                                                                RESEP/OBAT</strong></td>
                                                        <td class="text-end">
                                                            <strong class="fs-6" style="color: #7b1fa2;">Rp
                                                                {{ number_format($encounter->total_bayar_resep, 0, ',', '.') }}</strong>
                                                        </td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endforeach

                            {{-- Riwayat Pembayaran --}}
                            @if (isset($paidEncounters) && $paidEncounters->isNotEmpty())
                                <hr class="my-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0"><i class="ri-history-line"></i> Riwayat Pembayaran</h5>
                                    <span class="badge bg-success">{{ $paidEncounters->count() }} Transaksi Lunas</span>
                                </div>
                                @foreach ($paidEncounters as $encounter)
                                    <div class="card mb-3 border border-success">
                                        <div class="card-header bg-success-subtle">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <strong><i class="ri-hospital-line"></i> Encounter:
                                                        {{ $encounter->no_encounter }}</strong>
                                                    <span class="ms-3 text-muted">
                                                        <i class="ri-calendar-check-line"></i>
                                                        {{ \Carbon\Carbon::parse($encounter->updated_at)->format('d M Y H:i') }}
                                                    </span>
                                                </div>
                                                <span class="badge bg-success"><i class="ri-checkbox-circle-line"></i>
                                                    LUNAS</span>
                                            </div>
                                        </div>
                                        <div class="card-body p-0">
                                            <table class="table table-sm m-0">
                                                <tbody>
                                                    @if ($encounter->total_bayar_tindakan > 0 && $encounter->status_bayar_tindakan)
                                                        <tr>
                                                            <td style="width:5%" class="align-middle">
                                                                <i class="ri-check-double-line text-success fs-5"></i>
                                                            </td>
                                                            <td>
                                                                <div class="d-flex justify-content-between">
                                                                    <div>
                                                                        <span
                                                                            class="category-badge category-tindakan">TINDAKAN
                                                                            MEDIS & PENUNJANG</span>
                                                                        <span class="badge bg-success ms-2">Lunas</span>
                                                                        @if ($encounter->metode_pembayaran_tindakan)
                                                                            <span
                                                                                class="badge bg-info">{{ $encounter->metode_pembayaran_tindakan }}</span>
                                                                        @endif
                                                                        <ul
                                                                            class="list-unstyled small text-muted mb-0 mt-2">
                                                                            @foreach ($encounter->tindakan as $tindakan)
                                                                                <li class="mb-1">
                                                                                    <i class="ri-arrow-right-s-line"></i>
                                                                                    {{ $tindakan->tindakan_name }}
                                                                                    <span class="badge bg-secondary">Qty:
                                                                                        {{ $tindakan->qty }}</span>
                                                                                </li>
                                                                            @endforeach
                                                                            @php
                                                                                $labItems = (
                                                                                    $encounter->labRequests ?? collect()
                                                                                )
                                                                                    ->flatMap(function ($lr) {
                                                                                        return $lr->items ?? collect();
                                                                                    })
                                                                                    ->pluck('test_name')
                                                                                    ->filter()
                                                                                    ->values()
                                                                                    ->all();
                                                                                $radItems = (
                                                                                    $encounter->radiologyRequests ??
                                                                                    collect()
                                                                                )
                                                                                    ->map(function ($rq) {
                                                                                        return optional(
                                                                                            $rq->jenis,
                                                                                        )->name;
                                                                                    })
                                                                                    ->filter()
                                                                                    ->values()
                                                                                    ->all();
                                                                            @endphp
                                                                            @if (!empty($labItems))
                                                                                <li class="mt-1"><span
                                                                                        class="category-badge category-lab">LAB</span>
                                                                                    {{ implode(', ', $labItems) }}</li>
                                                                            @endif
                                                                            @if (!empty($radItems))
                                                                                <li class="mt-1"><span
                                                                                        class="category-badge category-radiologi">RAD</span>
                                                                                    {{ implode(', ', $radItems) }}</li>
                                                                            @endif
                                                                        </ul>
                                                                    </div>
                                                                    <div class="text-end">
                                                                        <h6 class="text-success mb-0">
                                                                            Rp
                                                                            {{ number_format($encounter->total_bayar_tindakan, 0, ',', '.') }}
                                                                        </h6>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endif
                                                    @if ($encounter->total_bayar_resep > 0 && $encounter->status_bayar_resep)
                                                        <tr>
                                                            <td style="width:5%" class="align-middle">
                                                                <i class="ri-check-double-line text-success fs-5"></i>
                                                            </td>
                                                            <td>
                                                                <div class="d-flex justify-content-between">
                                                                    <div>
                                                                        <span class="category-badge category-resep">RESEP
                                                                            OBAT</span>
                                                                        <span class="badge bg-success ms-2">Lunas</span>
                                                                        <span
                                                                            class="badge bg-info">{{ $encounter->resep->kode_resep ?? '' }}</span>
                                                                        @if ($encounter->metode_pembayaran_resep)
                                                                            <span
                                                                                class="badge bg-info">{{ $encounter->metode_pembayaran_resep }}</span>
                                                                        @endif
                                                                        <ul
                                                                            class="list-unstyled small text-muted mb-0 mt-2">
                                                                            @foreach ($encounter->resep->details as $detail)
                                                                                <li class="mb-1">
                                                                                    <i class="ri-arrow-right-s-line"></i>
                                                                                    {{ $detail->nama_obat }}
                                                                                    <span class="badge bg-secondary">Qty:
                                                                                        {{ $detail->qty }}</span>
                                                                                </li>
                                                                            @endforeach
                                                                        </ul>
                                                                    </div>
                                                                    <div class="text-end">
                                                                        <h6 class="text-success mb-0">
                                                                            Rp
                                                                            {{ number_format($encounter->total_bayar_resep, 0, ',', '.') }}
                                                                        </h6>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endforeach
                            @endif

                            <hr class="my-4">

                            {{-- Form Pembayaran dengan Split Payment --}}
                            <div class="row">
                                <div class="col-md-7">
                                    <div class="calculator-box">
                                        <h5 class="mb-3"><i class="ri-calculator-line"></i> Pembayaran</h5>

                                        {{-- Split Payment Fields --}}
                                        <div id="splitPaymentContainer">
                                            {{-- Payment 1 (Default) --}}
                                            <div class="payment-split-row" data-split-index="0">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <strong>Metode Pembayaran 1</strong>
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-danger remove-split"
                                                        style="display:none;">
                                                        <i class="ri-delete-bin-line"></i>
                                                    </button>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6 mb-2">
                                                        <label class="form-label small">Metode</label>
                                                        <select class="form-select payment-method-select"
                                                            name="payment_methods[0][method]" required>
                                                            <option value="" disabled selected>Pilih Metode</option>
                                                            @forelse(($paymentMethods ?? []) as $pm)
                                                                <option value="{{ $pm->code }}">{{ $pm->name }}
                                                                </option>
                                                            @empty
                                                                <option value="" disabled>Belum ada metode pembayaran
                                                                </option>
                                                            @endforelse
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6 mb-2">
                                                        <label class="form-label small">Jumlah Bayar</label>
                                                        <input type="text" class="form-control payment-amount-input"
                                                            name="payment_methods[0][amount]" placeholder="0"
                                                            data-split-index="0">
                                                        <input type="hidden" class="payment-amount-hidden"
                                                            name="payment_methods[0][amount_raw]" value="0">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <button type="button" class="btn btn-sm btn-outline-primary mt-2"
                                            id="addSplitPayment">
                                            <i class="ri-add-line"></i> Tambah Metode Pembayaran
                                        </button>

                                        <div class="alert alert-info mt-3 small">
                                            <i class="ri-information-line"></i>
                                            <strong>Tips:</strong> Anda dapat membayar dengan beberapa metode sekaligus
                                            (split payment).
                                            Misalnya: 50% Cash, 50% Transfer Bank.
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-5">
                                    <div class="calculator-box">
                                        <h5 class="mb-3"><i class="ri-money-dollar-circle-line"></i> Ringkasan
                                            Pembayaran</h5>

                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Total Tagihan:</span>
                                            <strong id="billTotal">Rp 0</strong>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Total Dibayar:</span>
                                            <strong id="paidTotal" class="text-primary">Rp 0</strong>
                                        </div>
                                        <hr>
                                        <div class="d-flex justify-content-between mb-3">
                                            <strong>Kembalian / Kurang:</strong>
                                            <strong id="changeAmount" class="text-success">Rp 0</strong>
                                        </div>

                                        <div id="changeDisplay" class="change-display" style="display:none;">
                                            <div class="small text-muted mb-1">KEMBALIAN</div>
                                            <div id="changeValue" class="fw-bold">Rp 0</div>
                                        </div>

                                        <div id="insufficientDisplay" class="change-display insufficient-display"
                                            style="display:none;">
                                            <div class="small mb-1">PEMBAYARAN KURANG</div>
                                            <div id="insufficientValue" class="fw-bold">Rp 0</div>
                                        </div>

                                        <div class="d-grid gap-2 mt-3">
                                            <button type="submit" class="btn btn-success btn-lg" id="btnProcessPayment"
                                                disabled>
                                                <i class="ri-wallet-3-line"></i> Proses Pembayaran
                                            </button>
                                            <a href="{{ route('kasir.index') }}" class="btn btn-secondary">
                                                <i class="ri-arrow-left-line"></i> Kembali
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
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
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Custom JS files -->
    <script src="{{ asset('js/custom.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const paymentItems = document.querySelectorAll('.payment-item');
            const billTotalEl = document.getElementById('billTotal');
            const paidTotalEl = document.getElementById('paidTotal');
            const changeAmountEl = document.getElementById('changeAmount');
            const changeDisplayEl = document.getElementById('changeDisplay');
            const changeValueEl = document.getElementById('changeValue');
            const insufficientDisplayEl = document.getElementById('insufficientDisplay');
            const insufficientValueEl = document.getElementById('insufficientValue');
            const btnProcessPayment = document.getElementById('btnProcessPayment');
            const splitPaymentContainer = document.getElementById('splitPaymentContainer');
            const addSplitPaymentBtn = document.getElementById('addSplitPayment');

            let splitCounter = 1;
            let billTotal = 0;

            // Format number to Rupiah
            function formatRupiah(number) {
                return 'Rp ' + number.toLocaleString('id-ID');
            }

            // Parse Rupiah to number
            function parseRupiah(rupiah) {
                return parseInt(rupiah.replace(/[^0-9]/g, '')) || 0;
            }

            // Format input sebagai Rupiah saat user mengetik
            function formatInputRupiah(input) {
                let value = input.value.replace(/[^0-9]/g, '');
                let numValue = parseInt(value) || 0;
                input.value = formatRupiah(numValue).replace('Rp ', '');

                // Simpan nilai raw
                const index = input.dataset.splitIndex;
                const hiddenInput = input.closest('.payment-split-row').querySelector('.payment-amount-hidden');
                hiddenInput.value = numValue;

                return numValue;
            }

            // Calculate bill total from selected items
            function calculateBillTotal() {
                billTotal = 0;
                paymentItems.forEach(item => {
                    if (item.checked) {
                        billTotal += parseFloat(item.dataset.amount);
                    }
                });
                billTotalEl.textContent = formatRupiah(billTotal);
                return billTotal;
            }

            // Calculate total paid from all payment methods
            function calculatePaidTotal() {
                let paidTotal = 0;
                const allPaymentInputs = document.querySelectorAll('.payment-amount-hidden');
                allPaymentInputs.forEach(input => {
                    paidTotal += parseInt(input.value) || 0;
                });
                paidTotalEl.textContent = formatRupiah(paidTotal);
                return paidTotal;
            }

            // Update all calculations
            function updateCalculations() {
                const currentBillTotal = calculateBillTotal();
                const currentPaidTotal = calculatePaidTotal();
                const difference = currentPaidTotal - currentBillTotal;

                changeAmountEl.textContent = formatRupiah(Math.abs(difference));

                // Show appropriate display
                if (difference > 0) {
                    // Ada kembalian
                    changeDisplayEl.style.display = 'block';
                    insufficientDisplayEl.style.display = 'none';
                    changeValueEl.textContent = formatRupiah(difference);
                    changeAmountEl.className = 'text-success';
                } else if (difference < 0) {
                    // Pembayaran kurang
                    changeDisplayEl.style.display = 'none';
                    insufficientDisplayEl.style.display = 'block';
                    insufficientValueEl.textContent = formatRupiah(Math.abs(difference));
                    changeAmountEl.className = 'text-danger';
                } else {
                    // Pas
                    changeDisplayEl.style.display = 'none';
                    insufficientDisplayEl.style.display = 'none';
                    changeAmountEl.className = 'text-success';
                }

                // Validate form
                validateForm();
            }

            // Validate form before submission
            function validateForm() {
                const currentBillTotal = calculateBillTotal();
                const currentPaidTotal = calculatePaidTotal();

                // Cek apakah ada item yang dipilih
                const hasCheckedItems = Array.from(paymentItems).some(item => item.checked);

                // Cek apakah semua metode pembayaran yang ada sudah dipilih
                const allMethodSelects = document.querySelectorAll('.payment-method-select');
                const allMethodsSelected = Array.from(allMethodSelects).every(select => select.value !== '');

                // Cek apakah pembayaran sudah mencukupi
                const paymentSufficient = currentPaidTotal >= currentBillTotal;

                // Tombol aktif jika: ada item dipilih, semua metode terpilih, dan pembayaran cukup
                btnProcessPayment.disabled = !hasCheckedItems || !allMethodsSelected || !paymentSufficient ||
                    currentBillTotal === 0;
            }

            // Event listener untuk checkbox item tagihan
            paymentItems.forEach(item => {
                item.addEventListener('change', function() {
                    // Update calculations first
                    updateCalculations();

                    // Check if all items in this encounter are checked
                    const encounterId = this.dataset.encounter;
                    const checkAllBox = document.getElementById(`checkAll-${encounterId}`);
                    if (checkAllBox) {
                        const allItems = document.querySelectorAll(
                            `.payment-item[data-encounter="${encounterId}"]`);
                        const allChecked = Array.from(allItems).every(i => i.checked);
                        checkAllBox.checked = allChecked;
                    }
                });
            });

            // Event listener untuk "Check All" checkbox
            document.addEventListener('change', function(e) {
                if (e.target.id && e.target.id.startsWith('checkAll-')) {
                    const encounterId = e.target.dataset.encounter;
                    const isChecked = e.target.checked;
                    const items = document.querySelectorAll(
                        `.payment-item[data-encounter="${encounterId}"]`);

                    items.forEach(item => {
                        item.checked = isChecked;
                    });

                    updateCalculations();
                }
            });

            // Event listener untuk input jumlah pembayaran
            document.addEventListener('input', function(e) {
                if (e.target.classList.contains('payment-amount-input')) {
                    formatInputRupiah(e.target);
                    updateCalculations();
                }
            });

            // Event listener untuk select metode pembayaran
            document.addEventListener('change', function(e) {
                if (e.target.classList.contains('payment-method-select')) {
                    updateCalculations();
                    // Highlight active payment row
                    const row = e.target.closest('.payment-split-row');
                    if (e.target.value) {
                        row.classList.add('active');
                    } else {
                        row.classList.remove('active');
                    }
                }
            });

            // Add new split payment row
            addSplitPaymentBtn.addEventListener('click', function() {
                const newRow = `
                    <div class="payment-split-row" data-split-index="${splitCounter}">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <strong>Metode Pembayaran ${splitCounter + 1}</strong>
                            <button type="button" class="btn btn-sm btn-outline-danger remove-split">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label class="form-label small">Metode</label>
                                <select class="form-select payment-method-select" name="payment_methods[${splitCounter}][method]">
                                    <option value="" disabled selected>Pilih Metode</option>
                                    @foreach ($paymentMethods ?? [] as $pm)
                                        <option value="{{ $pm->code }}">{{ $pm->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label small">Jumlah Bayar</label>
                                <input type="text" class="form-control payment-amount-input"
                                       name="payment_methods[${splitCounter}][amount]"
                                       placeholder="0"
                                       data-split-index="${splitCounter}">
                                <input type="hidden" class="payment-amount-hidden" name="payment_methods[${splitCounter}][amount_raw]" value="0">
                            </div>
                        </div>
                    </div>
                `;

                splitPaymentContainer.insertAdjacentHTML('beforeend', newRow);
                splitCounter++;

                // Show remove button on all rows if more than 1
                updateRemoveButtons();
            });

            // Remove split payment row
            document.addEventListener('click', function(e) {
                if (e.target.closest('.remove-split')) {
                    const row = e.target.closest('.payment-split-row');
                    row.remove();
                    updateRemoveButtons();
                    updateCalculations();
                }
            });

            // Update visibility of remove buttons
            function updateRemoveButtons() {
                const allRows = document.querySelectorAll('.payment-split-row');
                const removeButtons = document.querySelectorAll('.remove-split');

                if (allRows.length > 1) {
                    removeButtons.forEach(btn => btn.style.display = 'inline-block');
                } else {
                    removeButtons.forEach(btn => btn.style.display = 'none');
                }
            }

            // Form submission validation
            document.getElementById('paymentForm').addEventListener('submit', function(e) {
                const currentBillTotal = calculateBillTotal();
                const currentPaidTotal = calculatePaidTotal();

                if (currentPaidTotal < currentBillTotal) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Pembayaran Kurang',
                        text: 'Jumlah pembayaran masih kurang ' + formatRupiah(currentBillTotal -
                            currentPaidTotal),
                        confirmButtonText: 'OK'
                    });
                    return false;
                }

                if (currentBillTotal === 0) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Pilih Item',
                        text: 'Pilih minimal satu item yang akan dibayar',
                        confirmButtonText: 'OK'
                    });
                    return false;
                }

                // Konfirmasi pembayaran
                if (currentPaidTotal > currentBillTotal) {
                    const kembalian = currentPaidTotal - currentBillTotal;
                    e.preventDefault();
                    Swal.fire({
                        title: 'Konfirmasi Pembayaran',
                        html: `
                            <div class="text-start">
                                <p><strong>Total Tagihan:</strong> ${formatRupiah(currentBillTotal)}</p>
                                <p><strong>Total Dibayar:</strong> ${formatRupiah(currentPaidTotal)}</p>
                                <p class="text-success"><strong>Kembalian:</strong> ${formatRupiah(kembalian)}</p>
                            </div>
                        `,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Proses',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            e.target.submit();
                        }
                    });
                }
            });

            // Initial calculation
            updateCalculations();
        });
    </script>
@endpush
