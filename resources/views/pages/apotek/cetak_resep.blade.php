<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk Resep</title>
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
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 2px 0; }
        hr { border: none; border-top: 1px dashed #000; margin: 6px 0; }
        @media print {
            body, .struk { width: 58mm; }
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
                <td>No. Resep</td>
                <td class="text-right">{{ $encounter->resep->kode_resep ?? '-' }}</td>
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
                    <th class="text-left">Obat</th>
                    <th class="text-center">Aturan Pakai</th>
                    <th class="text-right">Qty</th>
                </tr>
            </thead>
            <tbody>
                @if($encounter->resep && $encounter->resep->details)
                    @foreach($encounter->resep->details as $detail)
                        <tr>
                            <td>{{ $detail->nama_obat }}</td>
                            <td class="text-center">{{ $detail->aturan_pakai }}</td>
                            <td class="text-right">{{ $detail->qty }}</td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
        <hr>
        <table>
            <tr>
                <td>Nominal</td>
                <td class="text-right">{{ formatPrice($encounter->total_bayar_resep ?? 0) }}</td>
            </tr>
            <tr>
                <td>Diskon</td>
                <td class="text-right">{{ formatPrice($encounter->diskon_resep ?? 0) }}</td>
            </tr>
            <tr>
                <td>Total Bayar</td>
                <td class="text-right">{{ formatPrice($encounter->total_bayar_resep ?? 0) }}</td>
            </tr>
            <tr>
                <td>Metode</td>
                <td class="text-right">{{ $encounter->metode_pembayaran_resep ?? '-' }}</td>
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
