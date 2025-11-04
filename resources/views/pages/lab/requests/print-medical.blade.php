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
            margin-bottom: 10px;
            text-decoration: underline;
            text-align: center;
        }

        .responsible-doctor {
            text-align: center;
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
        }

        .responsible-doctor-title {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .responsible-doctor-name {
            font-size: 14px;
            font-weight: bold;
        }

        .patient-info {
            margin-bottom: 20px;
        }

        .info-row {
            display: flex;
            margin-bottom: 4px;
        }

        .info-label {
            width: 120px;
            font-weight: normal;
        }

        .info-colon {
            width: 20px;
        }

        .info-value {
            flex: 1;
            font-weight: normal;
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

    <!-- Responsible Doctor Section -->
    <div class="responsible-doctor">
        <div class="responsible-doctor-title">DOKTER PENANGGUNG JAWAB PELAYANAN</div>
        <div class="responsible-doctor-name">
            {{ $req->encounter->user->name ?? 'Dr. [Nama Dokter]' }}
        </div>
    </div>

    <!-- Patient Information -->
    <div class="patient-info">
        <div style="display: flex; gap: 40px;">
            <!-- Column 1 -->
            <div style="flex: 1;">
                <div class="info-row">
                    <div class="info-label">No. Rekam Medis</div>
                    <div class="info-colon">:</div>
                    <div class="info-value">{{ $req->encounter->rekam_medis ?? '-' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Nama Pasien</div>
                    <div class="info-colon">:</div>
                    <div class="info-value">{{ $req->encounter->name_pasien ?? '-' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Umur</div>
                    <div class="info-colon">:</div>
                    <div class="info-value">{{ $req->encounter->age ?? '-' }} tahun</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Jenis Kelamin</div>
                    <div class="info-colon">:</div>
                    <div class="info-value">{{ $req->encounter->gender ?? '-' }}</div>
                </div>
            </div>

            <!-- Column 2 -->
            <div style="flex: 1;">
                <div class="info-row">
                    <div class="info-label">Alamat</div>
                    <div class="info-colon">:</div>
                    <div class="info-value">{{ $req->encounter->address ?? '-' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Dokter Pengirim</div>
                    <div class="info-colon">:</div>
                    <div class="info-value">{{ $req->encounter->user->name ?? '-' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Tanggal Permintaan</div>
                    <div class="info-colon">:</div>
                    <div class="info-value">{{ $req->created_at->format('d/m/Y H:i') }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Tanggal Selesai</div>
                    <div class="info-colon">:</div>
                    <div class="info-value">{{ $req->completed_at?->format('d/m/Y H:i') ?? '-' }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Results Table -->
    <table class="results-table">
        <thead>
            <tr>
                <th class="examination-name">Pemeriksaan</th>
                <th class="result-value">Hasil</th>
                <th class="unit">Satuan</th>
                <th class="normal-range">Nilai Normal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($req->items as $item)
                @if ($item->jenisPemeriksaan && $item->jenisPemeriksaan->templateFields->isNotEmpty())
                    @foreach ($item->jenisPemeriksaan->templateFields as $field)
                        <!-- Group Header -->
                        <tr>
                            <td colspan="4" class="group-header">{{ $field->field_label }}</td>
                        </tr>
                        @if ($field->fieldItems && $field->fieldItems->isNotEmpty())
                            @foreach ($field->fieldItems->sortBy('order') as $fieldItem)
                                @php
                                    $resultValue = '';
                                    $isAbnormal = false;

                                    if (
                                        is_array($item->result_payload) &&
                                        isset($item->result_payload[$field->field_name])
                                    ) {
                                        $groupData = $item->result_payload[$field->field_name];
                                        if (is_array($groupData)) {
                                            // Convert examination name to lowercase with underscores to match the key format
                                            $examKey = strtolower(str_replace(' ', '_', $fieldItem->examination_name));
                                            if (isset($groupData[$examKey])) {
                                                $resultValue = $groupData[$examKey];
                                            }
                                        }
                                    }

                                    // Check if result is abnormal (basic check)
                                    if ($fieldItem->normal_range && $resultValue && is_numeric($resultValue)) {
                                        $normalRange = trim($fieldItem->normal_range);
                                        // Simple range check for format "min-max"
                                        if (
                                            preg_match(
                                                '/(\d+(?:\.\d+)?)\s*-\s*(\d+(?:\.\d+)?)/',
                                                $normalRange,
                                                $matches,
                                            )
                                        ) {
                                            $min = floatval($matches[1]);
                                            $max = floatval($matches[2]);
                                            $val = floatval($resultValue);
                                            $isAbnormal = $val < $min || $val > $max;
                                        }
                                    }
                                @endphp
                                <tr>
                                    <td class="examination-name">{{ $fieldItem->examination_name }}</td>
                                    <td class="result-value {{ $isAbnormal ? 'abnormal' : '' }}">
                                        {{ $resultValue ?: '-' }}
                                    </td>
                                    <td class="unit">{{ $fieldItem->unit ?? '-' }}</td>
                                    <td class="normal-range">{{ $fieldItem->normal_range ?? '-' }}</td>
                                </tr>
                            @endforeach
                        @endif
                    @endforeach
                @else
                    <!-- Simple result without template -->
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

    <div class="print-date">
        Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}
    </div>

    <script>
        // Auto print when page loads
        window.onload = function() {
            window.print();
        };
    </script>
</body>

</html>
