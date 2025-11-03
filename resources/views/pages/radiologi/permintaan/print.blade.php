@extends('layouts.print')
@section('title', 'Hasil Pemeriksaan Radiologi')
@push('style')
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
        }

        .section-title {
            font-weight: bold;
            font-size: 14px;
            margin-top: 16px;
            margin-bottom: 8px;
            padding: 6px 8px;
            background-color: #f0f0f0;
            border-left: 4px solid #0d6efd;
        }

        .content-box {
            padding: 12px;
            border: 1px solid #ddd;
            margin-bottom: 16px;
            white-space: pre-wrap;
            line-height: 1.6;
        }

        .custom-fields-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            margin-bottom: 16px;
        }

        .custom-field-item {
            padding: 8px;
            border: 1px solid #e0e0e0;
            background-color: #f9f9f9;
        }

        .custom-field-label {
            font-size: 11px;
            color: #666;
            margin-bottom: 4px;
        }

        .custom-field-value {
            font-weight: 600;
            font-size: 13px;
        }



        @media print {
            @page {
                size: A4;
                margin: 1.5cm;
            }

            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }

            .header-table td {
                border: 1px solid #000 !important;
            }

            .header-section {
                background-color: #f0f0f0 !important;
            }

            .measurement-table-print {
                border: 2px solid #000 !important;
            }

            .measurement-table-print th {
                background-color: #4472c4 !important;
                color: white !important;
                border: 1px solid #000 !important;
            }

            .measurement-table-print td {
                border: 1px solid #000 !important;
            }

            .measurement-table-print .section-cell {
                background-color: #d9e2f3 !important;
            }

            .measurement-table-print .normal-cell {
                background-color: #fff2cc !important;
            }

            .section-title {
                background-color: #f0f0f0 !important;
                border-left: 4px solid #0d6efd !important;
            }

            .custom-field-item {
                background-color: #f9f9f9 !important;
            }
        }
    </style>
@endpush
@push('scripts')
    @if (request()->get('auto'))
        <script>
            window.addEventListener('load', function() {
                setTimeout(function() {
                    window.print();
                }, 100);
            });
        </script>
    @endif
@endpush
@section('content')
    <style>
        .header-table {
            width: 100%;
            border: 2px solid #000;
            border-collapse: collapse;
            margin-bottom: 16px;
        }

        .header-table td {
            padding: 8px;
            border: 1px solid #000;
            font-size: 11px;
            vertical-align: top;
        }

        .header-section {
            background-color: #f0f0f0;
            font-weight: 600;
            text-align: center;
            padding: 6px !important;
        }

        .label-cell {
            font-weight: 600;
            width: 140px;
        }

        .colon-cell {
            width: 10px;
            text-align: center;
        }
    </style>

    <table class="header-table">
        <tr>
            <td colspan="3" class="header-section">Dokter Pengirim / Refering Doctor</td>
            <td colspan="3" class="header-section">Identitas Pasien / Patient Identity</td>
        </tr>
        <tr>
            <td class="label-cell">Dokter Pengirim / Refering Doctor</td>
            <td class="colon-cell">:</td>
            <td>{{ optional($req->dokter)->name ?? '-' }}</td>
            <td class="label-cell">PASIEN A</td>
            <td class="colon-cell">:</td>
            <td><strong>{{ $req->pasien->name ?? '-' }}</strong></td>
        </tr>
        <tr>
            <td class="label-cell">Keluhan / Clinical Complaints</td>
            <td class="colon-cell">:</td>
            <td>-</td>
            <td class="label-cell"></td>
            <td class="colon-cell"></td>
            <td><strong>{{ $req->pasien->rekam_medis ?? '-' }}</strong></td>
        </tr>
        <tr>
            <td class="label-cell">Pengobatan / Medication</td>
            <td class="colon-cell">:</td>
            <td>-</td>
            <td class="label-cell"></td>
            <td class="colon-cell"></td>
            <td></td>
        </tr>
        <tr>
            <td class="label-cell">Diagnosa / Diagnosis</td>
            <td class="colon-cell">:</td>
            <td>-</td>
            <td class="label-cell"></td>
            <td class="colon-cell"></td>
            <td></td>
        </tr>
        <tr>
            <td class="label-cell">Tanggal / Date</td>
            <td class="colon-cell">:</td>
            <td>{{ optional($req->created_at)->format('d-m-Y') }}</td>
            <td class="label-cell"></td>
            <td class="colon-cell"></td>
            <td></td>
        </tr>
        <tr>
            <td class="label-cell">Perawat / Nurse</td>
            <td class="colon-cell">:</td>
            <td>{{ optional($latest->reporter ?? null)->name ?? 'dr. Owner' }}</td>
            <td class="label-cell"></td>
            <td class="colon-cell"></td>
            <td></td>
        </tr>
    </table>

    @if ($latest)
        {{-- ECHOCARDIOGRAPHY Measurement Table --}}
        @if (
            $req->jenis &&
                (stripos($req->jenis->name, 'ECHOCARDIOGRAPHY') !== false || stripos($req->jenis->name, 'ECHO') !== false))

            @php
                // Define measurement parameters
                $measurementParams = [
                    'Root diam',
                    'EDD',
                    'ESD',
                    'IVS Diastole',
                    'IVS Systole',
                    'PW Diastole',
                    'PW Systole',
                    'LA Dimension',
                    'LA/Ao ratio',
                    'RV Dimension',
                    'M.V.A',
                    'TAPSE',
                    'RA mayor',
                    'EF',
                    'FS',
                    'EPSS',
                ];

                // Get measurement data from payload
                $measurementData = [];
                $valveData = [];
                if (!empty($latest->payload) && is_array($latest->payload)) {
                    foreach ($latest->payload as $key => $value) {
                        if (in_array($key, $measurementParams)) {
                            $measurementData[$key] = $value;
                        } elseif (!empty(trim($value))) {
                            $valveData[$key] = $value;
                        }
                    }
                }
                $hasMeasurementData = !empty($measurementData);
                $hasValveData = !empty($valveData);
            @endphp

            @if ($hasMeasurementData)
                <style>
                    .measurement-table-print {
                        width: 100%;
                        border: 2px solid #000;
                        border-collapse: collapse;
                        margin-bottom: 16px;
                    }

                    .measurement-table-print th {
                        background-color: #4472c4;
                        color: white;
                        padding: 6px 4px;
                        border: 1px solid #000;
                        text-align: center;
                        font-size: 9px;
                        font-weight: 600;
                    }

                    .measurement-table-print td {
                        padding: 3px 5px;
                        border: 1px solid #000;
                        vertical-align: middle;
                        font-size: 9px;
                    }

                    .measurement-table-print .section-cell {
                        background-color: #d9e2f3;
                        font-weight: 600;
                        text-align: center;
                    }

                    .measurement-table-print .param-cell {
                        text-align: left;
                        padding-left: 6px;
                    }

                    .measurement-table-print .value-cell {
                        text-align: center;
                        font-weight: 600;
                    }

                    .measurement-table-print .normal-cell {
                        background-color: #fff2cc;
                        text-align: center;
                        font-size: 8px;
                    }
                </style>
                <table class="measurement-table-print">
                    <thead>
                        <tr>
                            <th colspan="6" style="background-color: #4472c4; padding: 8px;">Pengukuran / Measurement
                            </th>
                        </tr>
                        <tr>
                            <th style="width: 15%;">Parameter</th>
                            <th style="width: 8%;">Hasil</th>
                            <th style="width: 12%;">Normal<br>Range</th>
                            <th style="width: 15%;">Parameter</th>
                            <th style="width: 8%;">Hasil</th>
                            <th style="width: 12%;">Normal<br>Range</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="param-cell">Root Diam</td>
                            <td class="value-cell">{{ $measurementData['Root diam'] ?? '-' }}</td>
                            <td class="normal-cell">20-37 mm</td>
                            <td class="param-cell">EDD</td>
                            <td class="value-cell">{{ $measurementData['EDD'] ?? '-' }}</td>
                            <td class="normal-cell">35-52mm</td>
                        </tr>
                        <tr>
                            <td rowspan="2" class="section-cell">Aorta</td>
                            <td class="param-cell">Dimension</td>
                            <td class="value-cell">{{ $measurementData['LA Dimension'] ?? '-' }}</td>
                            <td class="normal-cell">15-40 mm</td>
                            <td class="param-cell">ESD</td>
                            <td class="value-cell">{{ $measurementData['ESD'] ?? '-' }}</td>
                            <td class="normal-cell">26-36mm</td>
                        </tr>
                        <tr>
                            <td rowspan="2" class="section-cell">LA/Ao<br>ratio</td>
                            <td class="param-cell">LA/Ao ratio</td>
                            <td class="value-cell">{{ $measurementData['LA/Ao ratio'] ?? '-' }}</td>
                            <td class="normal-cell">&lt; 1.33</td>
                            <td rowspan="6" class="section-cell">Ventrikel<br>Kiri<br>(Left<br>Ventricle)</td>
                            <td class="param-cell">IVS Diastole</td>
                            <td class="value-cell">{{ $measurementData['IVS Diastole'] ?? '-' }}</td>
                            <td class="normal-cell">7-11 mm</td>
                        </tr>
                        <tr>
                            <td rowspan="5" class="section-cell">Ventrikel<br>Kanan<br>(Right<br>Ventricle)</td>
                            <td class="param-cell">Dimension</td>
                            <td class="value-cell">{{ $measurementData['RV Dimension'] ?? '-' }}</td>
                            <td class="normal-cell">&lt; 43 mm</td>
                            <td class="param-cell">IVS Systole</td>
                            <td class="value-cell">{{ $measurementData['IVS Systole'] ?? '-' }}</td>
                            <td class="normal-cell"></td>
                        </tr>
                        <tr>
                            <td class="param-cell">M.V.A</td>
                            <td class="value-cell">{{ $measurementData['M.V.A'] ?? '-' }}</td>
                            <td class="normal-cell">&gt; 3 cm²</td>
                            <td class="param-cell">PW Diastole</td>
                            <td class="value-cell">{{ $measurementData['PW Diastole'] ?? '-' }}</td>
                            <td class="normal-cell">7-11 mm</td>
                        </tr>
                        <tr>
                            <td class="param-cell">TAPSE</td>
                            <td class="value-cell">{{ $measurementData['TAPSE'] ?? '-' }}</td>
                            <td class="normal-cell">≥ 16 mm</td>
                            <td class="param-cell">PW Systole</td>
                            <td class="value-cell">{{ $measurementData['PW Systole'] ?? '-' }}</td>
                            <td class="normal-cell"></td>
                        </tr>
                        <tr>
                            <td class="param-cell">RA mayor</td>
                            <td class="value-cell">{{ $measurementData['RA mayor'] ?? '-' }}</td>
                            <td class="normal-cell"></td>
                            <td class="param-cell">EF</td>
                            <td class="value-cell">{{ $measurementData['EF'] ?? '-' }}</td>
                            <td class="normal-cell">52-77%</td>
                        </tr>
                        <tr>
                            <td class="param-cell"></td>
                            <td class="value-cell"></td>
                            <td class="normal-cell"></td>
                            <td class="param-cell">FS</td>
                            <td class="value-cell">{{ $measurementData['FS'] ?? '-' }}</td>
                            <td class="normal-cell">&gt; 25%</td>
                        </tr>
                        <tr>
                            <td class="param-cell"></td>
                            <td class="value-cell"></td>
                            <td class="normal-cell"></td>
                            <td class="param-cell">EPSS</td>
                            <td class="value-cell">{{ $measurementData['EPSS'] ?? '-' }}</td>
                            <td class="normal-cell">&lt; 10 mm</td>
                        </tr>
                    </tbody>
                </table>
            @endif

            {{-- Valve Assessment / Penilaian Katup --}}
            @if ($hasValveData)
                <div style="margin-bottom: 12px;">
                    @foreach ($valveData as $field => $value)
                        <div style="font-size: 10px; margin-bottom: 4px; line-height: 1.4;">
                            <strong>{{ $field }}</strong> : {{ $value }}
                        </div>
                    @endforeach
                </div>
            @endif
        @elseif (!empty($latest->payload) && is_array($latest->payload) && count($latest->payload) > 0)
            {{-- Custom Fields for Non-ECHO Examinations --}}
            <div class="section-title">DATA PEMERIKSAAN</div>
            <div class="custom-fields-grid">
                @foreach ($latest->payload as $key => $value)
                    <div class="custom-field-item">
                        <div class="custom-field-label">{{ ucwords(str_replace('_', ' ', $key)) }}</div>
                        <div class="custom-field-value">{{ $value ?: '-' }}</div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Temuan (Findings) --}}
        <div style="margin-top: 12px; margin-bottom: 12px;">
            <div style="font-weight: 600; font-size: 10px; margin-bottom: 4px;">Temuan (Findings):</div>
            <div style="line-height: 1.5; font-size: 10px; white-space: pre-wrap;">{{ $latest->findings ?? '-' }}</div>
        </div>

        {{-- Attachments --}}
        @if (is_array($latest->files) && count($latest->files) > 0)
            <div style="margin-top: 12px; margin-bottom: 12px;">
                <div style="font-weight: 600; font-size: 10px; margin-bottom: 4px;">Lampiran:</div>
                <ul style="margin: 0; padding-left: 20px; font-size: 10px;">
                    @foreach ($latest->files as $file)
                        <li>{{ basename($file) }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    @else
        <div
            style="text-align: center; padding: 40px; background-color: #fff3cd; border: 1px solid #ffc107; font-size: 11px;">
            <strong>⚠️ HASIL PEMERIKSAAN BELUM TERSEDIA</strong>
            <p style="margin-top: 8px;">Pemeriksaan radiologi untuk pasien ini belum memiliki hasil.</p>
        </div>
    @endif
@endsection
