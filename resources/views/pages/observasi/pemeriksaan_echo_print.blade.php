<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Echocardiography</title>
    <link rel="stylesheet" href="{{ asset('css/main.min.css') }}">
    <style>
        @page {
            size: A4;
            margin: 15mm;
        }

        body {
            background-color: #fff;
            color: #000;
            font-family: 'Arial', sans-serif;
            font-size: 11pt;
            line-height: 1.4;
        }

        .container {
            width: 100%;
            max-width: 210mm;
            margin: 0 auto;
            padding: 10px;
        }

        /* Header dengan border */
        .header-box {
            border: 2px solid #000;
            padding: 8px;
            margin-bottom: 10px;
        }

        .header-top {
            display: table;
            width: 100%;
            margin-bottom: 5px;
        }

        .logo-cell {
            display: table-cell;
            width: 100px;
            vertical-align: middle;
            border-right: 2px solid #000;
            padding-right: 10px;
        }

        .logo-cell img {
            width: 80px;
            height: auto;
        }

        .title-cell {
            display: table-cell;
            vertical-align: middle;
            padding-left: 10px;
            text-align: center;
        }

        .title-cell h3 {
            margin: 0;
            font-size: 16pt;
            font-weight: bold;
            color: #4472C4;
        }

        .title-cell h4 {
            margin: 5px 0 0 0;
            font-size: 10pt;
            font-style: italic;
            color: #666;
        }

        .identity-cell {
            display: table-cell;
            width: 180px;
            vertical-align: middle;
            border-left: 2px solid #000;
            padding-left: 10px;
            font-size: 9pt;
        }

        .identity-cell strong {
            display: block;
            font-size: 8pt;
            color: #666;
        }

        /* Info Section */
        .info-section {
            border: 1px solid #000;
            padding: 0;
            margin-bottom: 10px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 6px 10px;
            border: none;
            font-size: 10pt;
        }

        .info-table td:first-child {
            width: 250px;
            font-weight: bold;
            background-color: #f5f5f5;
        }

        .info-table td:nth-child(2) {
            width: 20px;
            text-align: center;
            font-weight: bold;
        }

        .info-table td:nth-child(3) {
            background-color: #fff;
        }

        .info-table tr {
            border-bottom: 1px solid #e0e0e0;
        }

        .info-table tr:last-child {
            border-bottom: none;
        }

        /* Measurement Table */
        .section-title {
            background-color: #4472C4;
            color: white;
            padding: 5px 10px;
            font-weight: bold;
            text-align: center;
            margin-top: 10px;
            margin-bottom: 5px;
        }

        .measurement-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 10pt;
        }

        .measurement-table th,
        .measurement-table td {
            border: 1px solid #000;
            padding: 4px 6px;
        }

        .measurement-table th {
            background-color: #D9E2F3;
            font-weight: bold;
            text-align: center;
        }

        .measurement-table td.label-col {
            font-weight: bold;
            background-color: #F2F2F2;
        }

        .measurement-table td.sub-label {
            padding-left: 15px;
            font-style: italic;
        }

        .measurement-table td.center {
            text-align: center;
        }

        /* Findings Section */
        .findings-box {
            border: 1px solid #000;
            padding: 8px;
            margin-bottom: 10px;
        }

        .finding-row {
            margin-bottom: 5px;
            line-height: 1.6;
        }

        .finding-label {
            font-weight: bold;
            display: inline-block;
            min-width: 220px;
        }

        /* Summary Section */
        .summary-box {
            border: 1px solid #000;
            padding: 8px;
            margin-bottom: 10px;
        }

        .summary-item {
            margin-bottom: 4px;
            line-height: 1.5;
        }

        /* Recommendation Section */
        .recommendation-section {
            margin-top: 10px;
        }

        .recommendation-title {
            background-color: #D9E2F3;
            border: 1px solid #000;
            padding: 5px 10px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 5px;
        }

        .recommendation-content {
            border: 1px solid #000;
            padding: 8px;
            min-height: 60px;
        }

        /* Signature Section */
        .signature-row {
            display: table;
            width: 100%;
            margin-top: 30px;
            border: 1px solid #000;
            padding: 8px;
        }

        .signature-cell {
            display: table-cell;
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding: 10px;
        }

        .signature-cell:first-child {
            border-right: 1px solid #000;
        }

        .signature-space {
            height: 60px;
        }

        .signature-name {
            font-weight: bold;
            border-top: 1px solid #000;
            padding-top: 5px;
            display: inline-block;
            min-width: 200px;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
            }

            .container {
                max-width: 100%;
                padding: 0;
            }

            .btn-print {
                display: none;
            }

            .page-break {
                page-break-after: always;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header Box -->
        <div class="header-box">
            <div class="header-top">
                <div class="logo-cell">
                    <img src="{{ asset('images/bdc.png') }}" alt="Logo Bambu Dua Clinic">
                </div>
                <div class="title-cell">
                    <h3>BAMBU DUA CLINIC</h3>
                    <h4>LAPORAN ECHOCARDIOGRAPHY</h4>
                    <h4>(Echocardiography Report)</h4>
                </div>
                <div class="identity-cell">
                    <strong>Identitas Pasien / Patient Identity</strong>
                    <div style="margin-top: 5px;">
                        <div>{{ $encounter->name_pasien }},
                            @if ($pasien && $pasien->tgl_lahir)
                                {{ \Carbon\Carbon::parse($pasien->tgl_lahir)->age }} thn
                            @else
                                - thn
                            @endif
                        </div>
                        <div>{{ $encounter->rekam_medis }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Doctor & Patient Info -->
        <div class="info-section">
            <table class="info-table">
                <tr>
                    <td>Dokter Pengirim / Refering Doctor</td>
                    <td>:</td>
                    <td>{{ $encounter->practitioner->first()->name ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Keluhan / Clinical Complaints</td>
                    <td>:</td>
                    <td>{{ $encounter->keluhan_utama ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Pengobatan / Medication</td>
                    <td>:</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td>Diagnosa / Diagnosis</td>
                    <td>:</td>
                    <td>
                        @if ($encounter->diagnosis && $encounter->diagnosis->count() > 0)
                            {{ $encounter->diagnosis->pluck('diagnosis_description')->implode(', ') }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>Tanggal / Date</td>
                    <td>:</td>
                    <td>{{ \Carbon\Carbon::parse($pemeriksaan->created_at)->format('d-m-Y') }}</td>
                </tr>
                <tr>
                    <td>Perawat / Nurse</td>
                    <td>:</td>
                    <td>{{ $encounter->nurses->first()->name ?? '-' }}</td>
                </tr>
            </table>
        </div>

        <!-- Measurement Section -->
        <div class="section-title">Pengukuran / Measurement</div>

        @php
            // Decode hasil pemeriksaan JSON
            $hasil = json_decode($pemeriksaan->hasil_pemeriksaan, true) ?? [];

            // Organisir data berdasarkan kategori
            $measurements = [
                'aorta' => [],
                'atrium_kiri' => [],
                'ventrikel_kanan' => [],
                'ventrikel_kiri' => [],
                'gerakan_otot' => '',
                'katup_mitral' => '',
                'katup_trikuspid' => '',
                'katup_aorta' => '',
                'katup_pulmonal' => '',
            ];

            // Parsing hasil
            foreach ($hasil as $item) {
                $label = strtolower($item['label'] ?? '');
                $value = $item['value'] ?? '';

                if (str_contains($label, 'aorta') || str_contains($label, 'root')) {
                    $measurements['aorta'][] = $item;
                } elseif (
                    str_contains($label, 'atrium kiri') ||
                    str_contains($label, 'left atrium') ||
                    str_contains($label, 'dimension') ||
                    str_contains($label, 'la/ao') ||
                    str_contains($label, 'la ao')
                ) {
                    $measurements['atrium_kiri'][] = $item;
                } elseif (
                    str_contains($label, 'ventrikel kanan') ||
                    str_contains($label, 'right ventricle') ||
                    str_contains($label, 'tapse') ||
                    str_contains($label, 'ra mayor') ||
                    str_contains($label, 'ra minor') ||
                    str_contains($label, 'mva') ||
                    str_contains($label, 'm.v.a')
                ) {
                    $measurements['ventrikel_kanan'][] = $item;
                } elseif (
                    str_contains($label, 'ventrikel kiri') ||
                    str_contains($label, 'left ventricle') ||
                    str_contains($label, 'edd') ||
                    str_contains($label, 'esd') ||
                    str_contains($label, 'ivs') ||
                    str_contains($label, 'pw') ||
                    str_contains($label, 'ef') ||
                    str_contains($label, 'fs') ||
                    str_contains($label, 'epss')
                ) {
                    $measurements['ventrikel_kiri'][] = $item;
                } elseif (str_contains($label, 'gerakan otot') || str_contains($label, 'wall motion')) {
                    $measurements['gerakan_otot'] = $value;
                } elseif (
                    str_contains($label, 'katup mitral') ||
                    str_contains($label, 'mitral valve') ||
                    str_contains($label, 'mitral')
                ) {
                    $measurements['katup_mitral'] = $value;
                } elseif (
                    str_contains($label, 'katup trikuspid') ||
                    str_contains($label, 'tricuspid valve') ||
                    str_contains($label, 'trikuspid')
                ) {
                    $measurements['katup_trikuspid'] = $value;
                } elseif (
                    str_contains($label, 'katup aorta') ||
                    str_contains($label, 'aortic valve') ||
                    str_contains($label, 'aortic')
                ) {
                    $measurements['katup_aorta'] = $value;
                } elseif (
                    str_contains($label, 'katup pulmonal') ||
                    str_contains($label, 'pulmonal valve') ||
                    str_contains($label, 'pulmonal')
                ) {
                    $measurements['katup_pulmonal'] = $value;
                }
            }
        @endphp

        <table class="measurement-table">
            <thead>
                <tr>
                    <th colspan="2">Parameter</th>
                    <th>Hasil</th>
                    <th>Normal Range</th>
                    <th colspan="2">Parameter</th>
                    <th>Hasil</th>
                    <th>Normal Range</th>
                </tr>
            </thead>
            <tbody>
                <!-- Baris 1: Aorta & Ventrikel Kiri -->
                <tr>
                    <td class="label-col" rowspan="{{ max(count($measurements['aorta']), 1) }}">Aorta</td>
                    @php
                        $root_diam = collect($measurements['aorta'])->first(function ($item) {
                            return stripos($item['label'] ?? '', 'Root') !== false ||
                                stripos($item['label'] ?? '', 'Aorta') !== false;
                        });
                    @endphp
                    <td>Root diam</td>
                    <td class="center">{{ $root_diam['value'] ?? '-' }}</td>
                    <td class="center">20-37 mm</td>

                    <td class="label-col" rowspan="9">Ventrikel Kiri<br>(Left Ventricle)</td>
                    @php
                        $edd = collect($measurements['ventrikel_kiri'])->first(function ($item) {
                            return stripos($item['label'] ?? '', 'EDD') !== false;
                        });
                    @endphp
                    <td>EDD</td>
                    <td class="center">{{ $edd['value'] ?? '-' }}</td>
                    <td class="center">35-52mm</td>
                </tr>

                <!-- Baris 2: Atrium Kiri & Ventrikel Kiri (ESD) -->
                <tr>
                    <td class="label-col" rowspan="{{ max(count($measurements['atrium_kiri']), 2) }}">Atrium
                        Kiri<br>(Left Atrium)</td>
                    @php
                        $dimension = collect($measurements['atrium_kiri'])->first(function ($item) {
                            return stripos($item['label'] ?? '', 'Dimension') !== false &&
                                !str_contains(strtolower($item['label'] ?? ''), 'la/ao');
                        });
                    @endphp
                    <td>Dimension</td>
                    <td class="center">{{ $dimension['value'] ?? '-' }}</td>
                    <td class="center">15-40 mm</td>

                    @php
                        $esd = collect($measurements['ventrikel_kiri'])->first(function ($item) {
                            return stripos($item['label'] ?? '', 'ESD') !== false;
                        });
                    @endphp
                    <td>ESD</td>
                    <td class="center">{{ $esd['value'] ?? '-' }}</td>
                    <td class="center">26-36mm</td>
                </tr>

                <!-- Baris 3: LA/Ao ratio & IVS Diastole -->
                <tr>
                    @php
                        $la_ao = collect($measurements['atrium_kiri'])->first(function ($item) {
                            return stripos($item['label'] ?? '', 'LA/Ao') !== false ||
                                stripos($item['label'] ?? '', 'LA Ao') !== false;
                        });
                    @endphp
                    <td>LA/Ao ratio</td>
                    <td class="center">{{ $la_ao['value'] ?? '-' }}</td>
                    <td class="center">
                        < 1.33</td>

                            @php
                                $ivs_dias = collect($measurements['ventrikel_kiri'])->first(function ($item) {
                                    return stripos($item['label'] ?? '', 'IVS Diastole') !== false ||
                                        stripos($item['label'] ?? '', 'IVS Dias') !== false;
                                });
                            @endphp
                    <td>IVS Diastole</td>
                    <td class="center">{{ $ivs_dias['value'] ?? '-' }}</td>
                    <td class="center">7-11 mm</td>
                </tr>

                <!-- Baris 4: Ventrikel Kanan & IVS Systole -->
                <tr>
                    <td class="label-col" rowspan="5">Ventrikel Kanan<br>(Right Ventricle)</td>
                    @php
                        $rv_dimension = collect($measurements['ventrikel_kanan'])->first(function ($item) {
                            return stripos($item['label'] ?? '', 'Dimension') !== false;
                        });
                    @endphp
                    <td>Dimension</td>
                    <td class="center">{{ $rv_dimension['value'] ?? '-' }}</td>
                    <td class="center">
                        < 43 mm</td>

                            @php
                                $ivs_sys = collect($measurements['ventrikel_kiri'])->first(function ($item) {
                                    return stripos($item['label'] ?? '', 'IVS Systole') !== false ||
                                        stripos($item['label'] ?? '', 'IVS Sys') !== false;
                                });
                            @endphp
                    <td>IVS Systole</td>
                    <td class="center">{{ $ivs_sys['value'] ?? '-' }}</td>
                    <td class="center"></td>
                </tr>

                <!-- Baris 5: M.V.A & EF -->
                <tr>
                    @php
                        $mva = collect($measurements['ventrikel_kanan'])->first(function ($item) {
                            return stripos($item['label'] ?? '', 'MVA') !== false ||
                                stripos($item['label'] ?? '', 'M.V.A') !== false;
                        });
                    @endphp
                    <td>M.V.A</td>
                    <td class="center">{{ $mva['value'] ?? '-' }}</td>
                    <td class="center">> 3 cm²</td>

                    @php
                        $ef_item = collect($measurements['ventrikel_kiri'])->first(function ($item) {
                            return stripos($item['label'] ?? '', 'EF') !== false &&
                                !str_contains(strtolower($item['label'] ?? ''), 'ivs');
                        });
                    @endphp
                    <td>EF</td>
                    <td class="center">{{ $ef_item['value'] ?? '-' }}</td>
                    <td class="center">52-77 %</td>
                </tr>

                <!-- Baris 6: TAPSE & PW Diastole -->
                <tr>
                    @php
                        $tapse = collect($measurements['ventrikel_kanan'])->first(function ($item) {
                            return stripos($item['label'] ?? '', 'TAPSE') !== false;
                        });
                    @endphp
                    <td>TAPSE</td>
                    <td class="center">{{ $tapse['value'] ?? '-' }}</td>
                    <td class="center">≥ 16 mm</td>

                    @php
                        $pw_dias = collect($measurements['ventrikel_kiri'])->first(function ($item) {
                            return stripos($item['label'] ?? '', 'PW Diastole') !== false ||
                                stripos($item['label'] ?? '', 'PW Dias') !== false;
                        });
                    @endphp
                    <td>PW Diastole</td>
                    <td class="center">{{ $pw_dias['value'] ?? '-' }}</td>
                    <td class="center">7-11 mm</td>
                </tr>

                <!-- Baris 7: RA mayor & PW Systole -->
                <tr>
                    @php
                        $ra_mayor = collect($measurements['ventrikel_kanan'])->first(function ($item) {
                            return stripos($item['label'] ?? '', 'RA mayor') !== false;
                        });
                    @endphp
                    <td>RA mayor</td>
                    <td class="center">{{ $ra_mayor['value'] ?? '-' }}</td>
                    <td class="center"></td>

                    @php
                        $pw_sys = collect($measurements['ventrikel_kiri'])->first(function ($item) {
                            return stripos($item['label'] ?? '', 'PW Systole') !== false ||
                                stripos($item['label'] ?? '', 'PW Sys') !== false;
                        });
                    @endphp
                    <td>PW Systole</td>
                    <td class="center">{{ $pw_sys['value'] ?? '-' }}</td>
                    <td class="center"></td>
                </tr>

                <!-- Baris 8: RA minor & EPSS -->
                <tr>
                    @php
                        $ra_minor = collect($measurements['ventrikel_kanan'])->first(function ($item) {
                            return stripos($item['label'] ?? '', 'RA minor') !== false;
                        });
                    @endphp
                    <td>RA minor</td>
                    <td class="center">{{ $ra_minor['value'] ?? '-' }}</td>
                    <td class="center"></td>

                    @php
                        $epss = collect($measurements['ventrikel_kiri'])->first(function ($item) {
                            return stripos($item['label'] ?? '', 'EPSS') !== false;
                        });
                    @endphp
                    <td>EPSS</td>
                    <td class="center">{{ $epss['value'] ?? '-' }}</td>
                    <td class="center">
                        < 10 mm</td>
                </tr>

                <!-- Baris 9: Empty & FS -->
                <tr>
                    <td colspan="4"></td>
                    <td>FS</td>
                    <td class="center">
                        @php
                            $fs = collect($measurements['ventrikel_kiri'])->first(function ($item) {
                                return stripos($item['label'] ?? '', 'FS') !== false &&
                                    !str_contains(strtolower($item['label'] ?? ''), 'ivs');
                            });
                        @endphp
                        {{ $fs['value'] ?? '-' }}
                    </td>
                    <td class="center">> 25 %</td>
                </tr>
            </tbody>
        </table>

        <!-- Findings Section -->
        <div class="findings-box">
            <div class="finding-row">
                <span class="finding-label">Gerakan Otot / Wall Motion</span>
                : {{ $measurements['gerakan_otot'] ?: 'Normokinetik' }}
            </div>
            <div class="finding-row">
                <span class="finding-label">Katup Mitral / Mitral Valve</span>
                : {{ $measurements['katup_mitral'] ?: '-' }}
            </div>
            <div class="finding-row">
                <span class="finding-label">Katup Trikuspid / Tricuspid Valve</span>
                : {{ $measurements['katup_trikuspid'] ?: '-' }}
            </div>
            <div class="finding-row">
                <span class="finding-label">Katup Aorta / Aortic Valve</span>
                : {{ $measurements['katup_aorta'] ?: 'Baik' }}
            </div>
            <div class="finding-row">
                <span class="finding-label">Katup Pulmonal / Pulmonal Valve</span>
                : {{ $measurements['katup_pulmonal'] ?: '-' }}
            </div>
        </div>

        <!-- Summary Section -->
        <div class="summary-box">
            @php
                $ef =
                    collect($measurements['ventrikel_kiri'])->first(function ($item) {
                        return stripos($item['label'] ?? '', 'EF') !== false;
                    })['value'] ?? '-';

                $katup_summary = [];
                if ($measurements['katup_mitral']) {
                    $katup_summary[] = $measurements['katup_mitral'];
                }
                if ($measurements['katup_trikuspid']) {
                    $katup_summary[] = $measurements['katup_trikuspid'];
                }
                if ($measurements['katup_pulmonal']) {
                    $katup_summary[] = $measurements['katup_pulmonal'];
                }
            @endphp

            <div class="summary-item">
                <strong>Wall motion :</strong> {{ $measurements['gerakan_otot'] ?: 'Normokinetik' }}
            </div>
            <div class="summary-item">
                <strong>Fungsi sistolik LV</strong> {{ $ef != '-' ? 'Baik (EF: ' . $ef . ')' : '-' }}
            </div>
            <div class="summary-item">
                <strong>Katup-Katup :</strong>
                {{ count($katup_summary) > 0 ? implode(', ', $katup_summary) : 'Normal' }}
            </div>
            <div class="summary-item">
                <strong>Dimensi ruang jantung :</strong> LVH (+)
            </div>
            <div class="summary-item">
                <strong>PH (-), Efusi Perikard (-)</strong>
            </div>
        </div>

        <!-- Recommendation Section -->
        <div class="recommendation-section">
            <div class="recommendation-title">Saran / Recommendation</div>
            <div class="recommendation-content">
                {!! $pemeriksaan->recomendation ?: '-' !!}
            </div>
        </div>

        <!-- Signature Section -->
        <div class="signature-row">
            <div class="signature-cell">
                <div style="font-weight: bold; margin-bottom: 5px;">Cardiologist :</div>
                <div class="signature-space"></div>
                <div class="signature-name">
                    {{ $encounter->practitioner->first()->name ?? '______________________' }}
                </div>
            </div>
            <div class="signature-cell">
                <div style="font-weight: bold; margin-bottom: 5px;">Tanda Tangan/Signature :</div>
                <div class="signature-space"></div>
                <div class="signature-name" style="border-top: none;">
                    &nbsp;
                </div>
            </div>
        </div>

        @if (!isset($pdf))
            <div class="text-center mt-4" style="margin-top: 20px;">
                <button class="btn btn-primary btn-print" onclick="window.print()"
                    style="padding: 10px 30px; font-size: 14pt;">Cetak</button>
            </div>
        @endif
    </div>
</body>

</html>
