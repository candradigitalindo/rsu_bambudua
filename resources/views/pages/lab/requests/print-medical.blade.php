<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Pemeriksaan Laboratorium - {{ $req->encounter->name_pasien ?? 'Pasien' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12px;
            line-height: 1.4;
            color: #000;
        }

        .header {
            display: flex;
            align-items: center;
            margin-bottom: 16px;
            border-bottom: 3px double #000;
            padding-bottom: 12px;
        }

        .header-logo {
            height: 70px;
            margin-right: 18px;
        }

        .header-content {
            flex: 1;
        }

        .hospital-name {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .hospital-address {
            font-size: 15px;
            margin-bottom: 3px;
        }

        .hospital-contact {
            font-size: 13px;
            margin-bottom: 3px;
        }

        .report-title {
            font-size: 16px;
            font-weight: bold;
            margin-top: 15px;
            margin-bottom: 15px;
            text-decoration: underline;
            text-align: center;
        }

        .patient-info {
            margin-bottom: 20px;
        }

        .info-row {
            display: flex;
            margin-bottom: 6px;
        }

        .info-label {
            width: 140px;
            font-weight: bold;
            font-size: 11px;
        }

        .info-colon {
            width: 15px;
        }

        .info-value {
            flex: 1;
            font-weight: normal;
            font-size: 11px;
        }

        .results-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .results-table th,
        .results-table td {
            border: 1px solid #000;
            padding: 6px 8px;
            vertical-align: top;
        }

        .results-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }

        .group-header {
            background-color: #e8e8e8;
            font-weight: bold;
            text-align: left !important;
            padding-left: 10px;
        }

        .examination-name {
            font-weight: normal;
            width: 35%;
            text-align: left;
        }

        .result-value {
            text-align: center !important;
            width: 20%;
        }

        .unit {
            text-align: center !important;
            width: 15%;
        }

        .normal-range {
            text-align: center !important;
            width: 30%;
        }

        .abnormal {
            font-weight: bold;
            color: #d32f2f;
        }

        .print-date {
            font-size: 10px;
            color: #666;
            margin-top: 20px;
        }

        @media print {
            body {
                font-size: 11px;
            }

            .no-print {
                display: none !important;
            }

            .page-break {
                page-break-before: always;
            }
        }

        @page {
            margin: 2cm;
            size: A4;
        }
    </style>
</head>

<body>
    <!-- Header Section -->
    <div class="header">
        <img src="{{ asset('images/bdc.png') }}" alt="Logo" class="header-logo">
        <div class="header-content">
            <div class="hospital-name">Bambu Dua Clinic</div>
            <div class="hospital-address">Jl. Bambu II No.20, Durian, Kota Medan, Sumatera Utara</div>
            <div class="hospital-contact">Telp: (061) 6610112 / 6622802 | WhatsApp : 0811 - 6311 - 378</div>
        </div>
    </div>

    <div class="report-title">HASIL PEMERIKSAAN LABORATORIUM</div>

    <!-- Patient Information -->
    <div class="patient-info">
        <table style="width: 100%; border: none;">
            <tr>
                <td style="width: 50%; vertical-align: top; border: none; padding-right: 20px;">
                    <div class="info-row">
                        <div class="info-label">NAMA PASIEN</div>
                        <div class="info-colon">:</div>
                        <div class="info-value">{{ $req->encounter->name_pasien ?? '-' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">TANGGAL LAHIR</div>
                        <div class="info-colon">:</div>
                        <div class="info-value">
                            {{ $req->encounter->tanggal_lahir ? \Carbon\Carbon::parse($req->encounter->tanggal_lahir)->format('d F Y') : '-' }}
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">J.KELAMIN</div>
                        <div class="info-colon">:</div>
                        <div class="info-value">{{ $req->encounter->gender ?? '-' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">ALAMAT</div>
                        <div class="info-colon">:</div>
                        <div class="info-value">{{ $req->encounter->address ?? '-' }}</div>
                    </div>
                </td>
                <td style="width: 50%; vertical-align: top; border: none; padding-left: 20px;">
                    <div class="info-row">
                        <div class="info-label">KODE LAB</div>
                        <div class="info-colon">:</div>
                        <div class="info-value">{{ $req->id ? substr($req->id, 0, 4) . '/prtk' : '-' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">TANGGAL</div>
                        <div class="info-colon">:</div>
                        <div class="info-value">
                            {{ $req->completed_at?->format('d F Y') ?? $req->created_at->format('d F Y') }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">PENGIRIM</div>
                        <div class="info-colon">:</div>
                        <div class="info-value">{{ $req->encounter->user->name ?? '-' }}</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Results Table -->
    <table class="results-table">
        <thead>
            <tr>
                <th class="examination-name">PEMERIKSAAN</th>
                <th class="result-value">HASIL</th>
                <th class="unit">SATUAN</th>
                <th class="normal-range">NILAI NORMAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($req->items as $item)
                @php
                    $hasPayload = is_array($item->result_payload) && count($item->result_payload) > 0;
                    $isGroupedPayload = false;
                    if ($hasPayload) {
                        foreach ($item->result_payload as $k => $v) {
                            if (is_array($v)) {
                                $isGroupedPayload = true;
                                break;
                            }
                        }
                    }
                @endphp

                @if ($isGroupedPayload)
                    {{-- Test name sebagai header --}}
                    <tr>
                        <td colspan="4" style="background-color: #e9ecef; font-weight: bold; padding: 8px;">
                            {{ $item->test_name }}
                        </td>
                    </tr>
                    @php
                        // Build template metadata map
                        $templateMeta = [];
                        if ($item->jenisPemeriksaan && $item->jenisPemeriksaan->templateFields) {
                            foreach ($item->jenisPemeriksaan->templateFields as $field) {
                                if ($field->field_type === 'group' && $field->fieldItems) {
                                    $groupMeta = [];
                                    foreach ($field->fieldItems as $fieldItem) {
                                        $groupMeta[$fieldItem->item_name] = [
                                            'label' => $fieldItem->examination_name ?? $fieldItem->item_label,
                                            'unit' => $fieldItem->unit,
                                            'normal_range' => $fieldItem->normal_range,
                                        ];
                                    }
                                    $templateMeta[$field->field_name] = $groupMeta;
                                }
                            }
                        }
                    @endphp
                    @foreach ($item->result_payload as $groupName => $groupItems)
                        @if (is_array($groupItems))
                            {{-- Sub-group header (hijau) --}}
                            <tr>
                                <td colspan="4" class="group-header"
                                    style="background-color: #198754; color: white; font-weight: bold; padding: 8px;">
                                    {{ ucwords(str_replace('_', ' ', $groupName)) }}
                                </td>
                            </tr>
                            {{-- Items dalam grup --}}
                            @foreach ($groupItems as $itemName => $itemValue)
                                @php
                                    $meta = $templateMeta[$groupName][$itemName] ?? [];
                                    $displayName = $meta['label'] ?? ucwords(str_replace('_', ' ', $itemName));
                                    $unit = $meta['unit'] ?? '-';
                                    $normalRange = $meta['normal_range'] ?? '-';

                                    // Check if result is abnormal
                                    $isAbnormal = false;
                                    if ($normalRange && $normalRange !== '-' && $itemValue && is_numeric($itemValue)) {
                                        if (
                                            preg_match(
                                                '/(\d+(?:\.\d+)?)\s*-\s*(\d+(?:\.\d+)?)/',
                                                $normalRange,
                                                $matches,
                                            )
                                        ) {
                                            $min = floatval($matches[1]);
                                            $max = floatval($matches[2]);
                                            $val = floatval($itemValue);
                                            $isAbnormal = $val < $min || $val > $max;
                                        }
                                    }
                                @endphp
                                <tr>
                                    <td class="examination-name">{{ $displayName }}</td>
                                    <td class="result-value {{ $isAbnormal ? 'abnormal' : '' }}">
                                        {{ $itemValue ?? '-' }}
                                    </td>
                                    <td class="unit">{{ $unit }}</td>
                                    <td class="normal-range">{{ $normalRange }}</td>
                                </tr>
                            @endforeach
                        @endif
                    @endforeach
                @else
                    {{-- Simple result without grouped payload --}}
                    <tr>
                        <td class="examination-name">{{ $item->test_name }}</td>
                        <td class="result-value">{{ $item->result_value ?? '-' }}</td>
                        <td class="unit">{{ $item->result_unit ?? '-' }}</td>
                        <td class="normal-range">{{ $item->result_reference ?? '-' }}</td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>

    @if ($req->notes)
        <div style="margin-top: 20px;">
            <strong>Catatan:</strong><br>
            {{ $req->notes }}
        </div>
    @endif

    <div
        style="margin-top: 30px; border-top: 2px solid #000; padding-top: 15px; display: flex; justify-content: space-between; align-items: center;">
        <div style="font-size: 11px;">
            <strong>Tanggal Cetak:</strong> {{ now()->format('d-m-Y(H:i') }} <strong>Wib</strong>)
        </div>
        <div style="text-align: right; font-size: 11px;">
            <strong>dto</strong>
        </div>
    </div>

    <script>
        // Auto print when page loads
        window.onload = function() {
            window.print();
        };
    </script>
</body>

</html>
