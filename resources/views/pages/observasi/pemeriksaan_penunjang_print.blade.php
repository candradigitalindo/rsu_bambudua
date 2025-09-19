<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Hasil Pemeriksaan Penunjang</title>
    <link rel="stylesheet" href="{{ asset('css/main.min.css') }}">
    <style>
        body {
            background-color: #fff;
            color: #000;
            font-family: 'Arial', sans-serif;
        }

        .container {
            width: 100%;
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ccc;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }

        .header h4 {
            margin: 0;
            font-weight: bold;
        }

        .patient-info {
            margin-bottom: 20px;
        }

        .patient-info table {
            width: 100%;
            border-collapse: collapse;
        }

        .patient-info td {
            padding: 5px;
            font-size: 14px;
        }

        .result-content {
            margin-top: 20px;
        }

        .result-content h5 {
            font-weight: bold;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }

        .ql-editor {
            font-size: 14px;
            line-height: 1.6;
        }

        @media print {
            body {
                margin: 0;
                padding: 10px;
            }

            .container {
                border: none;
                box-shadow: none;
                margin: 0;
                max-width: 100%;
            }

            .btn-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="container">
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
        <div class="header">
            <h4>HASIL PEMERIKSAAN PENUNJANG</h4>
            <h5>{{ $pemeriksaan->jenis_pemeriksaan }}</h5>
        </div>

        <div class="patient-info">
            <table>
                <tr>
                    <td width="150"><strong>No. Rekam Medis</strong></td>
                    <td width="10">:</td>
                    <td>{{ $encounter->rekam_medis }}</td>
                    <td width="150"><strong>Tanggal Lahir</strong></td>
                    <td width="10">:</td>
                    <td>{{ $pasien ? \Carbon\Carbon::parse($pasien->tgl_lahir)->format('d F Y') : 'N/A' }}</td>
                </tr>
                <tr>
                    <td><strong>Nama Pasien</strong></td>
                    <td>:</td>
                    <td>{{ $encounter->name_pasien }}</td>
                    <td><strong>Jenis Kelamin</strong></td>
                    <td>:</td>
                    <td>{{ $pasien ? ($pasien->gender == 'L' ? 'Laki-laki' : 'Perempuan') : 'N/A' }}</td>
                </tr>
                <tr>
                    <td><strong>Tanggal Pemeriksaan</strong></td>
                    <td>:</td>
                    <td colspan="4">{{ \Carbon\Carbon::parse($pemeriksaan->created_at)->format('d F Y H:i') }}</td>
                </tr>
            </table>
        </div>

        <div class="result-content">
            <h5>Hasil Pemeriksaan:</h5>
            <div class="ql-snow">
                <div class="ql-editor">
                    {!! $pemeriksaan->hasil_pemeriksaan !!}
                </div>
            </div>
        </div>

        <div class="text-center mt-4">
            <button class="btn btn-primary btn-print" onclick="window.print()">Cetak</button>
        </div>
    </div>
</body>

</html>
