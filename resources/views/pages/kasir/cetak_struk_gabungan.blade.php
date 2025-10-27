<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Struk Pembayaran</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 10pt;
            color: #000;
        }

        .container {
            width: 280px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .header h3 {
            margin: 0;
            font-size: 14pt;
        }

        .header p {
            margin: 0;
            font-size: 8pt;
        }

        .info,
        .items,
        .total {
            margin-bottom: 10px;
        }

        .info table,
        .items table,
        .total table {
            width: 100%;
            border-collapse: collapse;
        }

        .items th,
        .items td,
        .total td {
            padding: 2px 0;
        }

        .items th {
            text-align: left;
            border-bottom: 1px dashed #000;
        }

        .text-right {
            text-align: right;
        }

        .line {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }

        .footer {
            text-align: center;
            font-size: 9pt;
            margin-top: 20px;
        }
    </style>
</head>

<body onload="window.print()">
    <div class="container">
        <div class="header">
            <h3>Bambu Dua Clinic</h3>
            <p>Jl. Bambu II No.20, Durian, Kota Medan, Sumatera Utara</p>
            <p>Telp: (061) 6610112 / 6622802 | WA: 0811-6311-378</p>
        </div>

        <div class="info">
            <table>
                <tr>
                    <td>No. Struk:</td>
                    <td class="text-right">{{ 'INV-' . time() }}</td>
                </tr>
                <tr>
                    <td>Tanggal:</td>
                    <td class="text-right">{{ now()->format('d/m/Y H:i') }}</td>
                </tr>
                <tr>
                    <td>Pasien:</td>
                    <td class="text-right">{{ $encounters->first()->name_pasien ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td>No. RM:</td>
                    <td class="text-right">{{ $encounters->first()->rekam_medis ?? 'N/A' }}</td>
                </tr>
            </table>
        </div>

        <div class="line"></div>

        <div class="items">
            @php $grandTotal = 0; @endphp
            @foreach ($encounters as $encounter)
                @if (isset($paidItemsInfo[$encounter->id]['tindakan']))
                    <p><strong>Tindakan ({{ $encounter->no_encounter }})</strong>: <span
                            style="float:right;">{{ number_format($paidItemsInfo[$encounter->id]['tindakan'], 0, ',', '.') }}</span>
                    </p>
                    @php $grandTotal += $paidItemsInfo[$encounter->id]['tindakan']; @endphp
                @endif
                @if (isset($paidItemsInfo[$encounter->id]['resep']))
                    <p><strong>Resep ({{ $encounter->no_encounter }})</strong>: <span
                            style="float:right;">{{ number_format($paidItemsInfo[$encounter->id]['resep'], 0, ',', '.') }}</span>
                    </p>
                    @php $grandTotal += $paidItemsInfo[$encounter->id]['resep']; @endphp
                @endif
            @endforeach
        </div>

        <div class="line"></div>

        <div class="total">
            <table>
                <tr>
                    <td><strong>GRAND TOTAL</strong></td>
                    <td class="text-right"><strong>{{ number_format($grandTotal, 0, ',', '.') }}</strong></td>
                </tr>
                <tr>
                    <td>Metode Bayar</td>
                    <td class="text-right">
                        {{ $encounters->first()->metode_pembayaran_tindakan ?? $encounters->first()->metode_pembayaran_resep }}
                    </td>
                </tr>
            </table>
        </div>

        <div class="line"></div>

        <div class="footer">
            <p>Terima kasih atas kunjungan Anda.</p>
            <p>Semoga lekas sembuh.</p>
        </div>
    </div>
</body>

</html>
