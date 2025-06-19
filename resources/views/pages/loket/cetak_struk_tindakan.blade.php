<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Struk Tindakan</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            margin: 0;
            padding: 0;
            width: 58mm;
        }

        .struk {
            width: 58mm;
            padding: 8px;
        }

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 2px 0;
        }

        hr {
            border: none;
            border-top: 1px dashed #000;
            margin: 6px 0;
        }

        @media print {

            body,
            .struk {
                width: 58mm;
            }
        }
    </style>
</head>

<body onload="window.print()">
    <div class="struk">
        <div class="text-center">
            <strong style="font-size:18px;">Bambu Dua Clinic</strong><br><br>
            Jl. Bambu II No.20, Durian, Kota Medan, Sumatera Utara<br>
            Telp: (061) 6610112 / 6622802 | WhatsApp : 0811 - 6311 - 378
        </div>
        <hr>
        <table>
            <tr>
                <td>No. Encounter</td>
                <td class="text-right">{{ $encounter->no_encounter ?? '-' }}</td>
            </tr>
            <tr>
                <td>Pasien</td>
                <td class="text-right">{{ $encounter->name_pasien ?? '-' }}</td>
            </tr>
            <tr>
                <td>Tanggal</td>
                <td class="text-right">{{ \Carbon\Carbon::parse($encounter->updated_at)->format('d-m-Y H:i') }}</td>
            </tr>
        </table>
        <hr>
        <table>
            <thead>
                <tr>
                    <th class="text-left">Tindakan</th>
                    <th class="text-center">Harga</th>
                    <th class="text-center">Qty</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>

                @foreach ($encounter->tindakan as $detail)
                    <tr>
                        <td>{{ $detail->tindakan_name }}</td>
                        <td class="text-center">{{ formatPrice($detail->tindakan_harga) }}</td>
                        <td class="text-center">{{ $detail->qty }}</td>
                        <td class="text-right">{{ formatPrice($detail->total_harga) }}</td>
                    </tr>
                @endforeach

            </tbody>
        </table>
        <hr>
        <table>
            <tr>
                <td>Nominal</td>
                <td class="text-right">{{ formatPrice($encounter->total_bayar_tindakan ?? 0) }}</td>
            </tr>
            <tr>
                <td>Diskon</td>
                <td class="text-right">{{ formatPrice($encounter->diskon_tindakan ?? 0) }}</td>
            </tr>
            <tr>
                <td>Total Bayar</td>
                <td class="text-right">{{ formatPrice($encounter->total_bayar_tindakan ?? 0) }}</td>
            </tr>
            <tr>
                <td>Metode</td>
                <td class="text-right">{{ $encounter->metode_pembayaran_tindakan ?? '-' }}</td>
            </tr>
        </table>
        <hr>
        <div class="text-center">
            Terima kasih<br>
            {{ now()->format('d-m-Y H:i') }}
        </div>
    </div>
    <script>
        window.onafterprint = function() {
            window.close();
        };
    </script>
</body>

</html>
