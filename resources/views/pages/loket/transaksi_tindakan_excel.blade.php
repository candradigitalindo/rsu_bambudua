<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Data Transaksi Tindakan</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        .kop {
            text-align: center;
            margin-bottom: 10px;
        }

        .kop .title {
            font-size: 18px;
            font-weight: bold;
        }

        .kop .alamat {
            font-size: 12px;
        }

        hr {
            border: none;
            border-top: 2px solid #000;
            margin: 8px 0 16px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 4px;
        }

        th {
            background: #eee;
        }
    </style>
</head>

<body>
    <div class="kop">
        <div class="title">BAMBU DUA CLINIC</div>
        <div class="alamat">
            Jl. Bambu II No.20, Durian, Kota Medan, Sumatera Utara<br>
            Telp: (061) 6610112 / 6622802 &nbsp;|&nbsp; WhatsApp: 0811-6311-378
        </div>
    </div>
    <hr>
    <h3 style="text-align:center; margin-bottom:0;">Data Transaksi Tindakan</h3>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>No. Encounter</th>
                <th>Pasien</th>
                <th>Tanggal</th>
                <th>Nominal</th>
                <th>Diskon (Rp)</th>
                <th>Diskon (%)</th>
                <th>Metode Pembayaran</th>
                <th>Nominal Bayar</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalBayar = 0;
                $totalDiskon = 0;
                $totalNominal = 0;
            @endphp
            @foreach($data as $i => $encounter)
            @php
                $totalBayar += $encounter->total_bayar_tindakan ?? 0;
            @endphp
            <tr>
                <td>{{ $i+1 }}</td>
                <td>{{ $encounter->no_encounter ?? '-' }}</td>
                <td>{{ $encounter->name_pasien }}</td>
                <td>{{ \Carbon\Carbon::parse($encounter->updated_at)->format('d-m-Y') }}</td>
                <td>{{ $encounter->total_tindakan ?? 0 }}</td>
                <td>{{ $encounter->diskon_tindakan ?? 0 }}</td>
                <td>{{ $encounter->diskon_persen_tindakan ?? 0 }}%</td>
                <td>{{ $encounter->metode_pembayaran_tindakan ?? '-' }}</td>
                <td>{{ $encounter->total_bayar_tindakan ?? 0 }}</td>
            </tr>
            @endforeach
            <tr>
                <th colspan="9" style="text-align:right">
                    Total Bayar: {{ $totalBayar }}
                </th>
            </tr>
        </tbody>
    </table>
</body>

</html>
