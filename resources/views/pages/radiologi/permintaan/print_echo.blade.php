<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Echocardiography</title>
    <style>
        @page {
            size: A4;
            margin: 10mm;
        }

        body {
            background-color: #fff;
            color: #000;
            font-family: 'Arial', sans-serif;
            font-size: 9pt;
            line-height: 1.2;
        }

        .container {
            width: 100%;
            max-width: 210mm;
            margin: 0 auto;
            padding: 5px;
        }

        .header-box {
            border: 2px solid #000;
            padding: 8px;
            margin-bottom: 8px;
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

        .info-section {
            border: 1px solid #000;
            padding: 0;
            margin-bottom: 8px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 6px 10px;
            border: none;
            font-size: 9pt;
        }

        .info-table td:nth-child(1),
        .info-table td:nth-child(4) {
            width: 200px;
            font-weight: bold;
            background-color: #f5f5f5;
        }

        .info-table td:nth-child(2),
        .info-table td:nth-child(5) {
            width: 15px;
            text-align: center;
            font-weight: bold;
            background-color: #f5f5f5;
        }

        .info-table td:nth-child(3),
        .info-table td:nth-child(6) {
            background-color: #fff;
        }

        .info-table tr {
            border-bottom: 1px solid #000;
        }

        .info-table tr:last-child {
            border-bottom: none;
        }

        .section-title {
            background-color: #ffffff;
            color: #000000;
            padding: 5px 8px;
            font-weight: bold;
            text-align: center;
            margin-top: 0px;
            margin-bottom: 5px;
            border: 1px solid #000;
        }

        .measurement-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            font-size: 9pt;
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

        .measurement-table td.center {
            text-align: center;
        }

        .findings-box {
            border: 1px solid #000;
            padding: 0;
            margin-bottom: 8px;
        }

        .findings-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #000;
        }

        .findings-table td {
            padding: 6px 8px;
            border: 1px solid #000;
            font-size: 9pt;
        }

        .findings-table td:nth-child(1),
        .findings-table td:nth-child(4) {
            width: 200px;
            font-weight: bold;
            background-color: #f8f9fa;
            vertical-align: top;
        }

        .findings-table td:nth-child(2),
        .findings-table td:nth-child(5) {
            width: 15px;
            text-align: center;
            font-weight: bold;
            background-color: #f8f9fa;
        }

        .findings-table td:nth-child(3),
        .findings-table td:nth-child(6) {
            background-color: #ffffff;
        }

        .summary-box {
            border: 1px solid #000;
            padding: 8px;
            margin-bottom: 10px;
        }

        .recommendation-section {
            margin-top: 8px;
        }

        .recommendation-title {
            background-color: #D9E2F3;
            border: 1px solid #000;
            padding: 5px 8px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 5px;
            font-size: 10pt;
        }

        .recommendation-content {
            border: 1px solid #000;
            padding: 8px;
            min-height: 60px;
            font-size: 9pt;
            line-height: 1.4;
        }

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
        }
    </style>
</head>

<body>
    <div class="container">
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
                        <div>{{ $req->pasien->name ?? '-' }},
                            @php
                                $umur = '-';
                                if ($req->pasien && !empty($req->pasien->tgl_lahir)) {
                                    try {
                                        $umur = \Carbon\Carbon::parse($req->pasien->tgl_lahir)->age;
                                    } catch (\Exception $e) {
                                        $umur = '-';
                                    }
                                }
                            @endphp
                            {{ $umur }} thn
                        </div>
                        <div>{{ $req->pasien->rekam_medis ?? '-' }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="info-section">
            <table class="info-table">
                <tr>
                    <td>Dokter Pengirim / Refering Doctor</td>
                    <td>:</td>
                    <td>{{ optional($req->dokter)->name ?? '-' }}</td>
                    <td>Diagnosa / Diagnosis</td>
                    <td>:</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td>Keluhan / Clinical Complaints</td>
                    <td>:</td>
                    <td>{{ $req->notes ?? '-' }}</td>
                    <td>Tanggal / Date</td>
                    <td>:</td>
                    <td>
                        @if ($latest)
                            {{ optional($latest->reported_at)->format('d-m-Y') ?? \Carbon\Carbon::parse($latest->created_at)->format('d-m-Y') }}@else{{ \Carbon\Carbon::parse($req->created_at)->format('d-m-Y') }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>Pengobatan / Medication</td>
                    <td>:</td>
                    <td>-</td>
                    <td>Perawat / Nurse</td>
                    <td>:</td>
                    <td>{{ optional($latest->reporter)->name ?? '-' }}</td>
                </tr>
            </table>
        </div>
        @if ($latest)
            <div class="section-title">Pengukuran / Measurement</div>
            @php $measurementData = $latest->payload ?? []; @endphp
            <table class="measurement-table">
                <thead>
                    <tr>
                        <th colspan="2">Parameter</th>
                        <th>Hasil</th>
                        <th style="background-color: #FFF2CC;">Normal Range</th>
                        <th colspan="2">Parameter</th>
                        <th>Hasil</th>
                        <th style="background-color: #FFF2CC;">Normal Range</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="label-col">Aorta</td>
                        <td class="label-col" style="text-align: left;">Root diam</td>
                        <td class="center">{{ $measurementData['Root diam'] ?? '-' }}</td>
                        <td class="center" style="background-color: #FFF2CC;">20-37 mm</td>
                        <td rowspan="9" class="label-col" style="vertical-align: top;">Ventrikel Kiri<br>(Left
                            Ventricle)</td>
                        <td class="label-col" style="text-align: left;">EDD</td>
                        <td class="center">{{ $measurementData['EDD'] ?? '-' }}</td>
                        <td class="center" style="background-color: #FFF2CC;">35-52 mm</td>
                    </tr>
                    <tr>
                        <td rowspan="2" class="label-col" style="vertical-align: top;">Atrium Kiri<br>(Left Atrium)
                        </td>
                        <td class="label-col" style="text-align: left;">Dimension</td>
                        <td class="center">{{ $measurementData['LA Dimension'] ?? '-' }}</td>
                        <td class="center" style="background-color: #FFF2CC;">15-40 mm</td>
                        <td class="label-col" style="text-align: left;">ESD</td>
                        <td class="center">{{ $measurementData['ESD'] ?? '-' }}</td>
                        <td class="center" style="background-color: #FFF2CC;">26-36 mm</td>
                    </tr>
                    <tr>
                        <td class="label-col" style="text-align: left;">LA/Ao ratio</td>
                        <td class="center">{{ $measurementData['LA/Ao ratio'] ?? '-' }}</td>
                        <td class="center" style="background-color: #FFF2CC;">&lt; 1.33</td>
                        <td class="label-col" style="text-align: left;">IVS Diastole</td>
                        <td class="center">{{ $measurementData['IVS Diastole'] ?? '-' }}</td>
                        <td class="center" style="background-color: #FFF2CC;">7-11 mm</td>
                    </tr>
                    <tr>
                        <td rowspan="5" class="label-col" style="vertical-align: top;">Ventrikel Kanan<br>(Right
                            Ventricle)</td>
                        <td class="label-col" style="text-align: left;">Dimension</td>
                        <td class="center">{{ $measurementData['RV Dimension'] ?? '-' }}</td>
                        <td class="center" style="background-color: #FFF2CC;">&lt; 43 mm</td>
                        <td class="label-col" style="text-align: left;">IVS Systole</td>
                        <td class="center">{{ $measurementData['IVS Systole'] ?? '-' }}</td>
                        <td class="center" style="background-color: #FFF2CC;"></td>
                    </tr>
                    <tr>
                        <td class="label-col" style="text-align: left;">M.V.A</td>
                        <td class="center">{{ $measurementData['M.V.A'] ?? '-' }}</td>
                        <td class="center" style="background-color: #FFF2CC;">&gt; 3 cm²</td>
                        <td class="label-col" style="text-align: left;">PW Diastole</td>
                        <td class="center">{{ $measurementData['PW Diastole'] ?? '-' }}</td>
                        <td class="center" style="background-color: #FFF2CC;">7-11 mm</td>
                    </tr>
                    <tr>
                        <td class="label-col" style="text-align: left;">TAPSE</td>
                        <td class="center">{{ $measurementData['TAPSE'] ?? '-' }}</td>
                        <td class="center" style="background-color: #FFF2CC;">≥ 16 mm</td>
                        <td class="label-col" style="text-align: left;">PW Systole</td>
                        <td class="center">{{ $measurementData['PW Systole'] ?? '-' }}</td>
                        <td class="center" style="background-color: #FFF2CC;"></td>
                    </tr>
                    <tr>
                        <td class="label-col" style="text-align: left;">RA mayor</td>
                        <td class="center">{{ $measurementData['RA mayor'] ?? '-' }}</td>
                        <td class="center" style="background-color: #FFF2CC;"></td>
                        <td class="label-col" style="text-align: left;">EF</td>
                        <td class="center">{{ $measurementData['EF'] ?? '-' }}</td>
                        <td class="center" style="background-color: #FFF2CC;">52-77 %</td>
                    </tr>
                    <tr>
                        <td class="label-col" style="text-align: left;">RA minor</td>
                        <td class="center">{{ $measurementData['RA minor'] ?? '-' }}</td>
                        <td class="center" style="background-color: #FFF2CC;"></td>
                        <td class="label-col" style="text-align: left;">FS</td>
                        <td class="center">{{ $measurementData['FS'] ?? '-' }}</td>
                        <td class="center" style="background-color: #FFF2CC;">&gt; 25 %</td>
                    </tr>
                    <tr>
                        <td colspan="4"></td>
                        <td class="label-col" style="text-align: left;">EPSS</td>
                        <td class="center">{{ $measurementData['EPSS'] ?? '-' }}</td>
                        <td class="center" style="background-color: #FFF2CC;">&lt; 10 mm</td>
                    </tr>
                </tbody>
            </table>
            {{-- TABEL 1: Detail Lengkap Gerakan Otot & Katup (2 Kolom) --}}
            <div class="findings-box">
                <table class="findings-table">
                    <tr>
                        <td>Gerakan Otot / Wall Motion</td>
                        <td>:</td>
                        <td>{{ $measurementData['Gerakan Otot / Wall Motion'] ?? 'Normokinetik' }}</td>
                        <td>Katup Aorta / Aortic Valve</td>
                        <td>:</td>
                        <td>{{ $measurementData['Katup Aorta / Aortic Valve'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Katup Mitral / Mitral Valve</td>
                        <td>:</td>
                        <td>{{ $measurementData['Katup Mitral / Mitral Valve'] ?? '-' }}</td>
                        <td>Katup Pulmonal / Pulmonal Valve</td>
                        <td>:</td>
                        <td>{{ $measurementData['Katup Pulmonal / Pulmonal Valve'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Katup Trikuspid / Tricuspid Valve</td>
                        <td>:</td>
                        <td colspan="4">{{ $measurementData['Katup Trikuspid / Tricuspid Valve'] ?? '-' }}</td>
                    </tr>
                </table>
            </div>

            {{-- TABEL 2: Ringkasan (2 Kolom) --}}
            <div class="findings-box">
                <table class="findings-table">
                    <tr>
                        <td>Wall motion</td>
                        <td>:</td>
                        <td>{{ $measurementData['Gerakan Otot / Wall Motion'] ?? 'Normokinetik' }}</td>
                        <td>Katup-Katup</td>
                        <td>:</td>
                        <td>
                            @php
                                $katupItems = [];
                                if (!empty($measurementData['Katup Mitral / Mitral Valve'])) {
                                    $katupItems[] = $measurementData['Katup Mitral / Mitral Valve'];
                                }
                                if (!empty($measurementData['Katup Trikuspid / Tricuspid Valve'])) {
                                    $katupItems[] = $measurementData['Katup Trikuspid / Tricuspid Valve'];
                                }
                                if (!empty($measurementData['Katup Aorta / Aortic Valve'])) {
                                    $katupItems[] = $measurementData['Katup Aorta / Aortic Valve'];
                                }
                                if (!empty($measurementData['Katup Pulmonal / Pulmonal Valve'])) {
                                    $katupItems[] = $measurementData['Katup Pulmonal / Pulmonal Valve'];
                                }
                                echo !empty($katupItems) ? implode(', ', $katupItems) : '-';
                            @endphp
                        </td>
                    </tr>
                    <tr>
                        <td>Fungsi sistolik LV</td>
                        <td>:</td>
                        <td>
                            @php
                                $fungsi = $measurementData['Fungsi Sistolik LV'] ?? null;
                                $ef = $measurementData['EF'] ?? null;
                                if ($ef && (empty($fungsi) || stripos($fungsi, 'ef') === false)) {
                                    $fungsi = trim(($fungsi ? $fungsi . ' ' : '') . '(EF: ' . $ef . ' %)');
                                }
                                echo $fungsi ?: '-';
                            @endphp
                        </td>
                        <td>Dimensi ruang jantung</td>
                        <td>:</td>
                        <td>{{ $measurementData['Dimensi Ruang Jantung'] ?? '-' }}</td>
                    </tr>
                </table>
            </div>

            <div class="recommendation-section">
                <div class="recommendation-title">Saran / Recommendation</div>
                <div class="recommendation-content">{{ $latest->impression ?? '-' }}</div>
            </div>
            <div style="margin-top: 15px; padding: 8px; border: 1px solid #000;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="width: 120px; font-weight: bold; padding: 5px; font-size: 9pt;">Cardiologist :</td>
                        <td style="padding: 5px; font-size: 9pt;">{{ optional($latest->radiologist)->name ?? '-' }}
                        </td>
                        <td style="width: 180px; font-weight: bold; padding: 5px; text-align: right; font-size: 9pt;">
                            Tanda Tangan/Signature :</td>
                        <td style="width: 150px; padding: 5px; border-bottom: 1px solid #000;"></td>
                    </tr>
                </table>
            </div>
        @else
            <div style="text-align: center; padding: 40px; background-color: #fff3cd; border: 1px solid #ffc107;">
                <strong>⚠️ HASIL PEMERIKSAAN BELUM TERSEDIA</strong>
                <p style="margin-top: 8px;">Pemeriksaan echocardiography untuk pasien ini belum memiliki hasil.</p>
            </div>
        @endif
        @if (!request()->get('auto'))
            <div class="text-center mt-4" style="margin-top: 20px;">
                <button class="btn btn-primary btn-print" onclick="window.print()"
                    style="padding: 10px 30px; font-size: 14pt;">Cetak</button>
            </div>
        @endif
    </div>
    @if (request()->get('auto'))
        <script>
            window.addEventListener('load', function() {
                setTimeout(function() {
                    window.print();
                }, 100);
            });
        </script>
    @endif
</body>

</html>
