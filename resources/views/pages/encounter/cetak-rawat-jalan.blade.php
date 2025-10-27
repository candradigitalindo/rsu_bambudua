<!-- resources/views/pages/encounter/print-hasil.blade.php -->
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Cetak Hasil Encounter</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 13px;
            margin: 0;
            padding: 20px;
        }

        .kop-surat {
            display: flex;
            align-items: center;
            margin-bottom: 16px;
            border-bottom: 3px double #000;
            padding-bottom: 12px;
        }

        .kop-surat img {
            height: 70px;
            margin-right: 18px;
        }

        .kop-surat .info {
            flex: 1;
        }

        .kop-surat .clinic-name {
            font-size: 20px;
            font-weight: bold;
        }

        .kop-surat .clinic-address {
            font-size: 15px;
        }

        .kop-surat .clinic-contact {
            font-size: 13px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h2 {
            margin: 8px 0;
            font-size: 18px;
        }

        .info-table {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 4px 8px;
            vertical-align: top;
        }

        .info-table td:first-child {
            width: 180px;
            font-weight: 600;
        }

        .info-table td:nth-child(2) {
            width: 10px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .table th,
        .table td {
            border: 1px solid #333;
            padding: 8px;
        }

        .table th {
            background: #eee;
            font-weight: 600;
        }

        .section-title {
            font-weight: bold;
            font-size: 14px;
            margin: 20px 0 10px 0;
            padding: 6px 10px;
            background: #f5f5f5;
            border-left: 4px solid #0066cc;
        }

        .text-center {
            text-align: center;
        }

        .text-end {
            text-align: right;
        }

        .vital-signs-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 15px;
        }

        .vital-item {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background: #fafafa;
        }

        .vital-label {
            font-size: 11px;
            color: #666;
            margin-bottom: 4px;
        }

        .vital-value {
            font-size: 16px;
            font-weight: 600;
            color: #333;
        }

        .content-box {
            border: 1px solid #ddd;
            padding: 12px;
            border-radius: 6px;
            background: #fafafa;
            min-height: 50px;
            margin-bottom: 15px;
        }

        .signature-section {
            page-break-inside: avoid;
            margin-top: 50px;
        }

        .footer-section {
            page-break-inside: avoid;
        }

        @media print {
            body {
                padding: 10mm;
            }

            @page {
                margin: 15mm;
            }

            .section-block {
                page-break-inside: avoid;
            }

            .signature-section {
                page-break-before: auto;
                page-break-inside: avoid;
                page-break-after: avoid;
            }

            .footer-section {
                page-break-inside: avoid;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <!-- Kop Surat -->
    <div
        style="display: flex; align-items: center; margin-bottom: 16px; border-bottom: 3px double #000; padding-bottom: 12px;">
        <img src="{{ asset('images/bdc.png') }}" alt="Logo" style="height:70px; margin-right:18px;">
        <div style="flex:1;">
            <div style="font-size:20px; font-weight:bold;">Bambu Dua Clinic</div>
            <div style="font-size:15px;">Jl. Bambu II No.20, Durian, Kota Medan, Sumatera Utara</div>
            <div style="font-size:13px;">Telp: (061) 6610112 / 6622802 | WhatsApp : 0811 - 6311 - 378</div>
        </div>
    </div>
    <!-- Akhir Kop Surat -->

    @php
        $judulEncounter = match ($encounter->type) {
            1 => 'REKAM MEDIS RAWAT JALAN',
            2 => 'REKAM MEDIS RAWAT INAP',
            3 => 'REKAM MEDIS IGD',
            default => 'REKAM MEDIS',
        };
        $pasien = $encounter->pasien;
    @endphp

    <div class="header">
        <h2>{{ $judulEncounter }}</h2>
        <div style="font-size: 12px; color: #666;">No. Encounter: {{ $encounter->no_encounter }} | Tanggal:
            {{ \Carbon\Carbon::parse($encounter->created_at)->format('d F Y H:i') }}</div>
    </div>

    {{-- DATA PASIEN --}}
    <div class="section-title">IDENTITAS PASIEN</div>
    <table class="info-table">
        <tr>
            <td>No. Rekam Medis</td>
            <td>:</td>
            <td><strong>{{ $encounter->rekam_medis }}</strong></td>
            <td>Jenis Kelamin</td>
            <td>:</td>
            <td>{{ $pasien ? ($pasien->jenis_kelamin == 1 ? 'Laki-laki' : 'Perempuan') : '-' }}</td>
        </tr>
        <tr>
            <td>Nama Pasien</td>
            <td>:</td>
            <td><strong>{{ $encounter->name_pasien }}</strong></td>
            <td>Tanggal Lahir / Umur</td>
            <td>:</td>
            <td>{{ $pasien && $pasien->tgl_lahir ? \Carbon\Carbon::parse($pasien->tgl_lahir)->format('d M Y') . ' (' . \Carbon\Carbon::parse($pasien->tgl_lahir)->age . ' tahun)' : '-' }}
            </td>
        </tr>
        <tr>
            <td>Jenis Jaminan</td>
            <td>:</td>
            <td>{{ $encounter->jenis_jaminan }}</td>
            <td>No. Telepon</td>
            <td>:</td>
            <td>{{ $pasien->no_hp ?? '-' }}</td>
        </tr>
        <tr>
            <td>Tujuan Kunjungan</td>
            <td>:</td>
            <td>{{ $encounter->tujuan_kunjungan }}</td>
            <td>Alamat</td>
            <td>:</td>
            <td>{{ $pasien->alamat ?? '-' }}</td>
        </tr>
        <tr>
            <td>Dokter Pemeriksa</td>
            <td>:</td>
            <td colspan="4">
                <strong>
                    @if ($encounter->practitioner instanceof \Illuminate\Support\Collection && $encounter->practitioner->count())
                        {{ $encounter->practitioner->pluck('name')->join(', ') }}
                    @else
                        {{ $encounter->practitioner->name ?? '-' }}
                    @endif
                </strong>
            </td>
        </tr>
    </table>

    {{-- ANAMNESIS --}}
    @php
        // Handle anamnesis data - could be object, array, or collection
        $anamnesis = null;
        if (is_object($encounter->anamnesis) && !($encounter->anamnesis instanceof \Illuminate\Support\Collection)) {
            // Single object (hasOne relationship)
            $anamnesis = $encounter->anamnesis;
        } elseif (
            $encounter->anamnesis instanceof \Illuminate\Support\Collection &&
            $encounter->anamnesis->count() > 0
        ) {
            // Collection (hasMany relationship)
            $anamnesis = $encounter->anamnesis->first();
        } elseif (is_array($encounter->anamnesis) && count($encounter->anamnesis) > 0) {
            // Array
            $anamnesis = (object) $encounter->anamnesis[0];
        }

        // Handle tanda vital data
        $tandaVital = null;
        if (is_object($encounter->tandaVital) && !($encounter->tandaVital instanceof \Illuminate\Support\Collection)) {
            $tandaVital = $encounter->tandaVital;
        } elseif (
            $encounter->tandaVital instanceof \Illuminate\Support\Collection &&
            $encounter->tandaVital->count() > 0
        ) {
            $tandaVital = $encounter->tandaVital->first();
        } elseif (
            isset($encounter->tandaVital) &&
            is_array($encounter->tandaVital) &&
            count($encounter->tandaVital) > 0
        ) {
            $tandaVital = (object) $encounter->tandaVital[0];
        }
    @endphp

    <div class="section-title">ANAMNESIS</div>
    @if ($anamnesis && $anamnesis->keluhan_utama)
        <table class="info-table">
            <tr>
                <td>Keluhan Utama</td>
                <td>:</td>
                <td>{{ $anamnesis->keluhan_utama ?? '-' }}</td>
            </tr>
        </table>
    @else
        <div class="content-box text-center" style="color: #999;">Belum ada data anamnesis</div>
    @endif

    {{-- TANDA VITAL --}}
    @if ($tandaVital && ($tandaVital->sistolik || $tandaVital->nadi || $tandaVital->pernapasan || $tandaVital->suhu))
        <div class="section-title">TANDA VITAL</div>
        <div class="vital-signs-grid">
            @if ($tandaVital->sistolik || $tandaVital->diastolik)
                <div class="vital-item">
                    <div class="vital-label">Tekanan Darah</div>
                    <div class="vital-value">
                        {{ $tandaVital->sistolik ?? '-' }}/{{ $tandaVital->diastolik ?? '-' }}
                        <small>mmHg</small>
                    </div>
                </div>
            @endif
            @if ($tandaVital->nadi)
                <div class="vital-item">
                    <div class="vital-label">Nadi</div>
                    <div class="vital-value">{{ $tandaVital->nadi }} <small>x/mnt</small></div>
                </div>
            @endif
            @if ($tandaVital->pernapasan)
                <div class="vital-item">
                    <div class="vital-label">Respirasi</div>
                    <div class="vital-value">{{ $tandaVital->pernapasan }} <small>x/mnt</small></div>
                </div>
            @endif
            @if ($tandaVital->suhu)
                <div class="vital-item">
                    <div class="vital-label">Suhu</div>
                    <div class="vital-value">{{ $tandaVital->suhu }} <small>°C</small></div>
                </div>
            @endif
            @if ($tandaVital->berat_badan)
                <div class="vital-item">
                    <div class="vital-label">Berat Badan</div>
                    <div class="vital-value">{{ $tandaVital->berat_badan }} <small>kg</small></div>
                </div>
            @endif
            @if ($tandaVital->tinggi_badan)
                <div class="vital-item">
                    <div class="vital-label">Tinggi Badan</div>
                    <div class="vital-value">{{ $tandaVital->tinggi_badan }} <small>cm</small></div>
                </div>
            @endif
            @if ($tandaVital->kesadaran)
                <div class="vital-item">
                    <div class="vital-label">Kesadaran</div>
                    <div class="vital-value">{{ $tandaVital->kesadaran }}</div>
                </div>
            @endif
        </div>
    @endif

    {{-- PEMERIKSAAN FISIK --}}
    @if ($anamnesis && isset($anamnesis->physical_examination) && $anamnesis->physical_examination)
        <div class="section-title">PEMERIKSAAN FISIK</div>
        <div class="content-box">
            {!! nl2br(e($anamnesis->physical_examination)) !!}
        </div>
    @endif

    {{-- DIAGNOSIS --}}
    <div class="section-title">DIAGNOSIS</div>
    @if ($encounter->diagnosis && count($encounter->diagnosis))
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 15%;">Kode ICD-10</th>
                    <th>Deskripsi</th>
                    <th style="width: 15%;">Jenis</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($encounter->diagnosis as $diag)
                    <tr>
                        <td>{{ $diag->diagnosis_code }}</td>
                        <td>{{ $diag->diagnosis_description }}</td>
                        <td>{{ ucfirst($diag->diagnosis_type) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="content-box text-center" style="color: #999;">Tidak ada diagnosis</div>
    @endif

    {{-- PEMERIKSAAN LABORATORIUM --}}
    @if ($encounter->labRequests && $encounter->labRequests->count())
        <div class="section-title">PEMERIKSAAN LABORATORIUM</div>
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 10%;">No</th>
                    <th>Jenis Pemeriksaan</th>
                    <th style="width: 18%;">Dokter Perujuk</th>
                    <th style="width: 12%;">Status</th>
                    <th style="width: 15%;">Tanggal</th>
                    <th style="width: 15%;">Harga</th>
                </tr>
            </thead>
            <tbody>
                @php $totalLab = 0; @endphp
                @foreach ($encounter->labRequests as $index => $labReq)
                    @if ($labReq->items && $labReq->items->count())
                        @foreach ($labReq->items as $itemIndex => $item)
                            @php $totalLab += $item->price ?? 0; @endphp
                            <tr>
                                @if ($itemIndex == 0)
                                    <td rowspan="{{ $labReq->items->count() }}" class="text-center">
                                        {{ $index + 1 }}</td>
                                @endif
                                <td>{{ $item->test_name }}</td>
                                @if ($itemIndex == 0)
                                    <td rowspan="{{ $labReq->items->count() }}">
                                        {{ $labReq->requester->name ?? '-' }}</td>
                                    <td rowspan="{{ $labReq->items->count() }}" class="text-center">
                                        <span
                                            style="padding: 2px 8px; border-radius: 3px; font-size: 11px; display: inline-block;
                                            {{ $labReq->status == 'completed' ? 'background: #d4edda; color: #155724;' : 'background: #fff3cd; color: #856404;' }}">
                                            {{ ucfirst($labReq->status) }}
                                        </span>
                                    </td>
                                    <td rowspan="{{ $labReq->items->count() }}">
                                        {{ $labReq->requested_at ? \Carbon\Carbon::parse($labReq->requested_at)->format('d/m/Y H:i') : '-' }}
                                    </td>
                                @endif
                                <td class="text-end">Rp {{ number_format($item->price ?? 0, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    @endif
                @endforeach
                @if ($totalLab > 0)
                    <tr style="background: #f5f5f5;">
                        <td colspan="5" class="text-end"><strong>Total Laboratorium:</strong></td>
                        <td class="text-end"><strong>Rp {{ number_format($totalLab, 0, ',', '.') }}</strong></td>
                    </tr>
                @endif
            </tbody>
        </table>
    @endif

    {{-- PEMERIKSAAN RADIOLOGI --}}
    @if ($encounter->radiologyRequests && $encounter->radiologyRequests->count())
        <div class="section-title">PEMERIKSAAN RADIOLOGI</div>
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 10%;">No</th>
                    <th>Jenis Pemeriksaan</th>
                    <th style="width: 18%;">Dokter Perujuk</th>
                    <th style="width: 12%;">Status</th>
                    <th style="width: 15%;">Tanggal</th>
                    <th style="width: 15%;">Harga</th>
                </tr>
            </thead>
            <tbody>
                @php $totalRadiologi = 0; @endphp
                @foreach ($encounter->radiologyRequests as $index => $radReq)
                    @php $totalRadiologi += $radReq->price ?? 0; @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $radReq->jenis->name ?? 'Pemeriksaan Radiologi' }}</td>
                        <td>{{ $radReq->dokter->name ?? '-' }}</td>
                        <td class="text-center">
                            <span
                                style="padding: 2px 8px; border-radius: 3px; font-size: 11px; display: inline-block;
                                {{ $radReq->status == 'completed' ? 'background: #d4edda; color: #155724;' : 'background: #fff3cd; color: #856404;' }}">
                                {{ ucfirst($radReq->status) }}
                            </span>
                        </td>
                        <td>{{ $radReq->created_at ? \Carbon\Carbon::parse($radReq->created_at)->format('d/m/Y H:i') : '-' }}
                        </td>
                        <td class="text-end">Rp {{ number_format($radReq->price ?? 0, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
                @if ($totalRadiologi > 0)
                    <tr style="background: #f5f5f5;">
                        <td colspan="5" class="text-end"><strong>Total Radiologi:</strong></td>
                        <td class="text-end"><strong>Rp {{ number_format($totalRadiologi, 0, ',', '.') }}</strong></td>
                    </tr>
                @endif
            </tbody>
        </table>
    @endif

    {{-- TINDAKAN/PROSEDUR --}}
    @if ($encounter->tindakan && count($encounter->tindakan))
        <div class="section-title">TINDAKAN / PROSEDUR MEDIS</div>
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 10%;">No</th>
                    <th>Nama Tindakan</th>
                    <th style="width: 10%;">Qty</th>
                    <th style="width: 20%;">Biaya Satuan</th>
                    <th style="width: 20%;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($encounter->tindakan as $index => $tindakan)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $tindakan->tindakan_name }}</td>
                        <td class="text-center">{{ $tindakan->qty }}</td>
                        <td class="text-end">Rp {{ number_format($tindakan->tindakan_harga ?? 0, 0, ',', '.') }}</td>
                        <td class="text-end"><strong>Rp
                                {{ number_format(($tindakan->tindakan_harga ?? 0) * $tindakan->qty, 0, ',', '.') }}</strong>
                        </td>
                    </tr>
                @endforeach
                <tr style="background: #f5f5f5;">
                    <td colspan="4" class="text-end"><strong>Total Tindakan:</strong></td>
                    <td class="text-end"><strong>Rp
                            {{ number_format($encounter->tindakan->sum(fn($t) => ($t->tindakan_harga ?? 0) * $t->qty), 0, ',', '.') }}</strong>
                    </td>
                </tr>
            </tbody>
        </table>
    @endif

    {{-- RESEP OBAT --}}
    @if ($encounter->resep && $encounter->resep->details && count($encounter->resep->details))
        <div class="section-title">RESEP OBAT</div>
        <div style="margin-bottom: 10px;">
            <strong>No. Resep:</strong> {{ $encounter->resep->kode_resep ?? '-' }} |
            <strong>Masa Pemakaian:</strong> {{ $encounter->resep->masa_pemakaian_hari ?? '-' }} Hari
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 10%;">No</th>
                    <th>Nama Obat</th>
                    <th style="width: 10%;">Qty</th>
                    <th style="width: 25%;">Aturan Pakai</th>
                    <th style="width: 20%;">Harga Satuan</th>
                    <th style="width: 20%;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($encounter->resep->details as $index => $detail)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $detail->nama_obat }}</td>
                        <td class="text-center">{{ $detail->qty }}</td>
                        <td>{{ $detail->aturan_pakai }}</td>
                        <td class="text-end">Rp {{ number_format($detail->harga ?? 0, 0, ',', '.') }}</td>
                        <td class="text-end"><strong>Rp
                                {{ number_format($detail->total_harga ?? 0, 0, ',', '.') }}</strong></td>
                    </tr>
                @endforeach
                <tr style="background: #f5f5f5;">
                    <td colspan="5" class="text-end"><strong>Total Resep:</strong></td>
                    <td class="text-end"><strong>Rp
                            {{ number_format($encounter->resep->details->sum('total_harga'), 0, ',', '.') }}</strong>
                    </td>
                </tr>
            </tbody>
        </table>
    @endif

    {{-- CATATAN DOKTER --}}
    @if ($encounter->catatan)
        <div class="section-title">CATATAN DOKTER</div>
        <div class="content-box">
            {!! $encounter->catatan !!}
        </div>
    @endif

    {{-- TOTAL BIAYA --}}
    @php
        // Hitung total semua komponen
        $totalTindakan = 0;
        if ($encounter->tindakan && count($encounter->tindakan)) {
            $totalTindakan = $encounter->tindakan->sum(fn($t) => ($t->tindakan_harga ?? 0) * $t->qty);
        }

        $totalResep = 0;
        if ($encounter->resep && $encounter->resep->details && count($encounter->resep->details)) {
            $totalResep = $encounter->resep->details->sum('total_harga');
        }

        $totalLaboratorium = 0;
        if ($encounter->labRequests && $encounter->labRequests->count()) {
            foreach ($encounter->labRequests as $labReq) {
                if ($labReq->items && $labReq->items->count()) {
                    $totalLaboratorium += $labReq->items->sum('price');
                }
            }
        }

        $totalRadiologi = 0;
        if ($encounter->radiologyRequests && $encounter->radiologyRequests->count()) {
            $totalRadiologi = $encounter->radiologyRequests->sum('price');
        }

        $grandTotal = $totalTindakan + $totalResep + $totalLaboratorium + $totalRadiologi;
    @endphp

    @if ($grandTotal > 0 || $encounter->total_tagihan)
        <div
            style="margin-top: 30px; padding: 15px; background: #f8f9fa; border: 2px solid #0066cc; border-radius: 8px;">
            <table style="width: 100%; border: none;">
                @if ($totalTindakan > 0)
                    <tr>
                        <td style="border: none; padding: 4px;">Tindakan</td>
                        <td style="border: none; padding: 4px; text-align: right;">
                            Rp {{ number_format($totalTindakan, 0, ',', '.') }}
                        </td>
                    </tr>
                @endif
                @if ($totalResep > 0)
                    <tr>
                        <td style="border: none; padding: 4px;">Resep Obat</td>
                        <td style="border: none; padding: 4px; text-align: right;">
                            Rp {{ number_format($totalResep, 0, ',', '.') }}
                        </td>
                    </tr>
                @endif
                @if ($totalLaboratorium > 0)
                    <tr>
                        <td style="border: none; padding: 4px;">Laboratorium</td>
                        <td style="border: none; padding: 4px; text-align: right;">
                            Rp {{ number_format($totalLaboratorium, 0, ',', '.') }}
                        </td>
                    </tr>
                @endif
                @if ($totalRadiologi > 0)
                    <tr>
                        <td style="border: none; padding: 4px;">Radiologi</td>
                        <td style="border: none; padding: 4px; text-align: right;">
                            Rp {{ number_format($totalRadiologi, 0, ',', '.') }}
                        </td>
                    </tr>
                @endif
                <tr style="border-top: 2px solid #0066cc;">
                    <td style="border: none; padding: 8px 4px 4px 4px; font-size: 16px;"><strong>TOTAL TAGIHAN</strong>
                    </td>
                    <td
                        style="border: none; padding: 8px 4px 4px 4px; text-align: right; font-size: 18px; color: #0066cc;">
                        <strong>Rp
                            {{ number_format($grandTotal > 0 ? $grandTotal : $encounter->total_tagihan ?? 0, 0, ',', '.') }}</strong>
                    </td>
                </tr>
            </table>
        </div>
    @endif

    {{-- TANDA TANGAN --}}
    <div class="signature-section">
        <table style="width: 100%; border: none;">
            <tr>
                <td style="width: 50%; border: none; padding: 0; vertical-align: top;">
                    <div style="text-align: center;">
                        <div style="height: 20px;">&nbsp;</div>
                        <div style="margin-bottom: 80px;">Pasien/Keluarga</div>
                        <div
                            style="border-top: 1px solid #333; display: inline-block; padding-top: 4px; min-width: 200px;">
                            {{ $encounter->name_pasien }}
                        </div>
                    </div>
                </td>
                <td style="width: 50%; border: none; padding: 0; vertical-align: top;">
                    <div style="text-align: center;">
                        <div style="height: 20px;">Medan,
                            {{ \Carbon\Carbon::parse($encounter->updated_at)->format('d F Y') }}</div>
                        <div style="margin-bottom: 80px;">Dokter Pemeriksa</div>
                        <div
                            style="border-top: 1px solid #333; display: inline-block; padding-top: 4px; min-width: 200px;">
                            @if ($encounter->practitioner instanceof \Illuminate\Support\Collection && $encounter->practitioner->count())
                                {{ $encounter->practitioner->first()->name ?? '-' }}
                            @else
                                {{ $encounter->practitioner->name ?? '-' }}
                            @endif
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- FOOTER --}}
    <div class="footer-section text-center"
        style="margin-top: 30px; padding-top: 15px; border-top: 1px solid #ddd; font-size: 11px; color: #666;">
        <p>Dokumen ini dicetak pada: {{ now()->format('d F Y H:i:s') }} WIB</p>
        <p style="margin-top: 8px;">Dokumen ini adalah salinan resmi rekam medis elektronik yang sah dan dapat
            digunakan sebagai bukti pelayanan kesehatan.</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
        window.onafterprint = function() {
            window.close();
        };
    </script>
</body>

</html>
