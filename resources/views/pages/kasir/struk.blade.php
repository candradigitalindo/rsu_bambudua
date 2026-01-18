<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Struk Pembayaran</title>
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    <style>
        :root {
            --fg: #000;
            --muted: #666;
            --border: #ddd;
        }

        body {
            color: var(--fg);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            margin: 0;
            background: #fff;
        }

        .container {
            max-width: 800px;
            margin: 24px auto;
            padding: 0 16px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .brand {
            font-weight: 600;
            font-size: 18px;
        }

        .muted {
            color: var(--muted);
            font-size: 12px;
        }

        hr {
            border: none;
            border-top: 1px solid var(--border);
            margin: 12px 0;
        }

        .row {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .col {
            flex: 1 1 300px;
        }

        .label {
            color: var(--muted);
            font-size: 12px;
        }

        .value {
            font-weight: 600;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 8px;
            border-bottom: 1px solid var(--border);
            text-align: left;
        }

        tfoot th {
            border-top: 2px solid var(--border);
        }

        .text-end {
            text-align: right;
        }

        .center {
            text-align: center;
        }

        .actions {
            margin-top: 16px;
            display: flex;
            gap: 8px;
            justify-content: center;
        }

        .btn {
            display: inline-block;
            padding: 8px 12px;
            border: 1px solid var(--border);
            background: #f5f5f5;
            color: #333;
            text-decoration: none;
            border-radius: 6px;
        }

        @media print {
            .actions {
                display: none;
            }

            body {
                margin: 0;
            }

            .container {
                margin: 0;
                padding: 0 12mm;
            }

            @page {
                size: A4;
                margin: 12mm;
            }
        }
    </style>
</head>

<body onload="window.print()">
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

        <div style="text-align: center; margin-bottom: 16px;">
            <div style="font-weight: 600; font-size: 16px;">STRUK PEMBAYARAN</div>
            <div class="muted">Waktu: {{ now()->format('d M Y H:i') }}</div>
        </div>
        <hr>
        <div class="row">
            <div class="col">
                <div class="label">Nama Pasien</div>
                <div class="value">{{ $pasien->name ?? '-' }}</div>
            </div>
            <div class="col">
                <div class="label">No. Rekam Medis</div>
                <div class="value">{{ $pasien->rekam_medis ?? '-' }}</div>
            </div>
        </div>
        <hr>
        <div style="font-weight: 600; margin-bottom: 8px;">DETAIL PEMBAYARAN</div>
        <table>
            <thead>
                <tr>
                    <th>Item / Layanan</th>
                    <th class="center">Qty</th>
                    <th class="text-end">Harga Satuan</th>
                    <th class="text-end">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @php $grandTotal = 0; @endphp
                @foreach ($encounters as $enc)
                    @php
                        $paidItems = $paid[$enc->id] ?? [];
                        $hasTindakan = isset($paidItems['tindakan']) && $paidItems['tindakan'] > 0;
                        $hasResep = isset($paidItems['resep']) && $paidItems['resep'] > 0;
                    @endphp

                    {{-- Header Encounter --}}
                    <tr style="background-color: #f8f9fa;">
                        <td colspan="4" style="font-weight: 600; border-top: 2px solid #000;">
                            Encounter: {{ $enc->no_encounter }} ({{ $enc->created_at->format('d M Y') }})
                        </td>
                    </tr>

                    {{-- Detail Tindakan --}}
                    @if ($hasTindakan)
                        @php
                            $paidTindakanItems = $paidItems['tindakan_items'] ?? [];
                        @endphp
                        @foreach ($enc->tindakan as $tindakan)
                            @php
                                // Cek apakah tindakan ini dibayar
                                $itemKey = 'tindakan-' . $enc->id . '-' . $tindakan->id;
                                $isPaid = empty($paidTindakanItems) || in_array($itemKey, $paidTindakanItems);

                                if (!$isPaid) {
                                    continue;
                                } // Skip jika tidak dibayar

                                $hargaSatuan =
                                    $tindakan->tindakan_harga ?? $tindakan->total_harga / ($tindakan->qty ?: 1);
                                $subtotal = $tindakan->total_harga ?? $hargaSatuan * $tindakan->qty;
                                $grandTotal += $subtotal;
                            @endphp
                            <tr>
                                <td>{{ $tindakan->tindakan_name }}</td>
                                <td class="center">{{ $tindakan->qty }}</td>
                                <td class="text-end">Rp {{ number_format($hargaSatuan, 0, ',', '.') }}</td>
                                <td class="text-end">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    @endif

                    {{-- Detail Lab --}}
                    @if ($hasTindakan)
                        @foreach ($enc->labRequests as $labReq)
                            @foreach ($labReq->items as $item)
                                @php
                                    // Cek apakah lab item ini dibayar
                                    $itemKey = 'lab-' . $enc->id . '-' . $item->id;
                                    $isPaid = empty($paidTindakanItems) || in_array($itemKey, $paidTindakanItems);

                                    if (!$isPaid) {
                                        continue;
                                    } // Skip jika tidak dibayar

                                    $hargaLab = $item->price ?? 0;
                                    $grandTotal += $hargaLab;
                                @endphp
                                <tr>
                                    <td>Lab: {{ $item->test_name }}</td>
                                    <td class="center">1</td>
                                    <td class="text-end">Rp {{ number_format($hargaLab, 0, ',', '.') }}</td>
                                    <td class="text-end">Rp {{ number_format($hargaLab, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        @endforeach
                    @endif

                    {{-- Detail Radiologi --}}
                    @if ($hasTindakan)
                        @foreach ($enc->radiologyRequests as $radReq)
                            @php
                                // Cek apakah radiologi item ini dibayar
                                $itemKey = 'radiologi-' . $enc->id . '-' . $radReq->id;
                                $isPaid = empty($paidTindakanItems) || in_array($itemKey, $paidTindakanItems);

                                if (!$isPaid) {
                                    continue;
                                } // Skip jika tidak dibayar

                                $hargaRad = $radReq->price ?? 0;
                                $grandTotal += $hargaRad;
                                $namaRadiologi = $radReq->jenis->name ?? 'Radiologi';
                            @endphp
                            <tr>
                                <td>Radiologi: {{ $namaRadiologi }}</td>
                                <td class="center">1</td>
                                <td class="text-end">Rp {{ number_format($hargaRad, 0, ',', '.') }}</td>
                                <td class="text-end">Rp {{ number_format($hargaRad, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    @endif

                    {{-- Detail Resep/Obat --}}
                    @if ($hasResep && $enc->resep)
                        @php
                            $paidResepItems = $paidItems['resep_items'] ?? [];
                        @endphp
                        @foreach ($enc->resep->details as $detail)
                            @php
                                // Cek apakah resep ini dibayar (format: resep-encounter_id-resep_id)
                                $itemKey = 'resep-' . $enc->id . '-' . $enc->resep->id;
                                $isPaid = empty($paidResepItems) || in_array($itemKey, $paidResepItems);

                                if (!$isPaid) {
                                    continue;
                                } // Skip jika tidak dibayar

                                $hargaObat = $detail->harga ?? 0;
                                $qty = $detail->qty ?? 1;
                                $subtotalObat = $hargaObat * $qty;
                                $grandTotal += $subtotalObat;
                            @endphp
                            <tr>
                                <td>Obat: {{ $detail->nama_obat }}</td>
                                <td class="center">{{ $qty }}</td>
                                <td class="text-end">Rp {{ number_format($hargaObat, 0, ',', '.') }}</td>
                                <td class="text-end">Rp {{ number_format($subtotalObat, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    @endif
                @endforeach
            </tbody>
            <tfoot>
                @php
                    // Hitung total fee dari semua encounter yang dibayar
                    $totalFee = 0;
                    foreach ($encounters as $enc) {
                        $paidItems = $paid[$enc->id] ?? [];
                        if (isset($paidItems['tindakan_fee'])) {
                            $totalFee += $paidItems['tindakan_fee'];
                        }
                        if (isset($paidItems['resep_fee'])) {
                            $totalFee += $paidItems['resep_fee'];
                        }
                    }
                    $finalGrandTotal = $grandTotal + $totalFee;
                @endphp

                <tr>
                    <th colspan="3" class="text-end">SUBTOTAL</th>
                    <th class="text-end">Rp {{ number_format($grandTotal, 0, ',', '.') }}</th>
                </tr>

                @if ($totalFee > 0)
                    <tr>
                        <th colspan="3" class="text-end">BIAYA ADMIN/FEE</th>
                        <th class="text-end">Rp {{ number_format($totalFee, 0, ',', '.') }}</th>
                    </tr>
                    <tr style="background-color: #f8f9fa;">
                        <th colspan="3" class="text-end">GRAND TOTAL</th>
                        <th class="text-end">Rp {{ number_format($finalGrandTotal, 0, ',', '.') }}</th>
                    </tr>
                @else
                    <tr>
                        <th colspan="3" class="text-end">TOTAL DIBAYAR</th>
                        <th class="text-end">Rp {{ number_format($grandTotal, 0, ',', '.') }}</th>
                    </tr>
                @endif
            </tfoot>
        </table>

        {{-- Metode Pembayaran --}}
        @php
            // Kumpulkan semua metode pembayaran dari encounters yang dibayar
            $paymentMethodsUsed = [];
            foreach ($encounters as $enc) {
                if ($enc->status_bayar_tindakan && $enc->metode_pembayaran_tindakan) {
                    $paymentMethodsUsed[] = $enc->metode_pembayaran_tindakan;
                }
                if ($enc->status_bayar_resep && $enc->metode_pembayaran_resep) {
                    $paymentMethodsUsed[] = $enc->metode_pembayaran_resep;
                }
            }
            $paymentMethodsUsed = array_unique($paymentMethodsUsed);
        @endphp

        @if (count($paymentMethodsUsed) > 0)
            <div style="margin-top: 16px; padding: 12px; background-color: #f8f9fa; border-radius: 4px;">
                <div style="font-weight: 600; margin-bottom: 8px;">METODE PEMBAYARAN</div>
                @foreach ($paymentMethodsUsed as $method)
                    <div style="margin-bottom: 4px;">{{ $method }}</div>
                @endforeach
            </div>
        @endif

        {{-- Tagihan Belum Terbayar --}}
        @if (isset($unpaidEncounters) && $unpaidEncounters->count() > 0)
            <hr style="margin-top: 24px;">
            <div style="font-weight: 600; margin-bottom: 8px; color: #d9534f;">DAFTAR TAGIHAN BELUM TERBAYAR</div>
            <table>
                <thead>
                    <tr>
                        <th>Item / Layanan</th>
                        <th class="center">Qty</th>
                        <th class="text-end">Harga Satuan</th>
                        <th class="text-end">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalUnpaid = 0; @endphp
                    @foreach ($unpaidEncounters as $enc)
                        {{-- Header Encounter --}}
                        <tr style="background-color: #fff3cd;">
                            <td colspan="4" style="font-weight: 600; border-top: 2px solid #d9534f;">
                                Encounter: {{ $enc->no_encounter }} ({{ $enc->created_at->format('d M Y') }})
                            </td>
                        </tr>

                        {{-- Tindakan belum bayar --}}
                        @if ($enc->total_bayar_tindakan > 0 && !$enc->status_bayar_tindakan)
                            @foreach ($enc->tindakan as $tindakan)
                                @php
                                    $hargaSatuan =
                                        $tindakan->tindakan_harga ?? $tindakan->total_harga / ($tindakan->qty ?: 1);
                                    $subtotal = $tindakan->total_harga ?? $hargaSatuan * $tindakan->qty;
                                    $totalUnpaid += $subtotal;
                                @endphp
                                <tr>
                                    <td>{{ $tindakan->tindakan_name }}</td>
                                    <td class="center">{{ $tindakan->qty }}</td>
                                    <td class="text-end">Rp {{ number_format($hargaSatuan, 0, ',', '.') }}</td>
                                    <td class="text-end">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach

                            @foreach ($enc->labRequests as $labReq)
                                @foreach ($labReq->items as $item)
                                    @php
                                        $hargaLab = $item->price ?? 0;
                                        $totalUnpaid += $hargaLab;
                                    @endphp
                                    <tr>
                                        <td>Lab: {{ $item->test_name }}</td>
                                        <td class="center">1</td>
                                        <td class="text-end">Rp {{ number_format($hargaLab, 0, ',', '.') }}</td>
                                        <td class="text-end">Rp {{ number_format($hargaLab, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            @endforeach

                            @foreach ($enc->radiologyRequests as $radReq)
                                @php
                                    $hargaRad = $radReq->price ?? 0;
                                    $totalUnpaid += $hargaRad;
                                    $namaRadiologi = $radReq->jenis->name ?? 'Radiologi';
                                @endphp
                                <tr>
                                    <td>Radiologi: {{ $namaRadiologi }}</td>
                                    <td class="center">1</td>
                                    <td class="text-end">Rp {{ number_format($hargaRad, 0, ',', '.') }}</td>
                                    <td class="text-end">Rp {{ number_format($hargaRad, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        @endif

                        {{-- Resep belum bayar --}}
                        @if ($enc->total_bayar_resep > 0 && !$enc->status_bayar_resep && $enc->resep)
                            @foreach ($enc->resep->details as $detail)
                                @php
                                    $hargaObat = $detail->harga ?? 0;
                                    $qty = $detail->qty ?? 1;
                                    $subtotalObat = $hargaObat * $qty;
                                    $totalUnpaid += $subtotalObat;
                                @endphp
                                <tr>
                                    <td>Obat: {{ $detail->nama_obat }}</td>
                                    <td class="center">{{ $qty }}</td>
                                    <td class="text-end">Rp {{ number_format($hargaObat, 0, ',', '.') }}</td>
                                    <td class="text-end">Rp {{ number_format($subtotalObat, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        @endif
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" class="text-end" style="color: #d9534f;">TOTAL BELUM TERBAYAR</th>
                        <th class="text-end" style="color: #d9534f;">Rp {{ number_format($totalUnpaid, 0, ',', '.') }}
                        </th>
                    </tr>
                </tfoot>
            </table>
        @endif

        <div class="center" style="margin-top: 16px;">
            <small class="muted">Terima kasih atas kunjungan Anda.</small>
        </div>

        {{-- QR Code Section --}}
        <div style="text-align: center; margin: 24px 0 16px 0; padding: 16px; background: #f8f9fa; border-radius: 8px;">
            <div id="qrcode" style="display: inline-block;"></div>
            <div class="muted" style="margin-top: 8px; font-size: 11px;">Scan QR Code untuk verifikasi transaksi</div>
        </div>

        <div class="actions">
            <a class="btn" href="{{ route('kasir.index') }}">Kembali ke Kasir</a>
            <a class="btn" href="#" onclick="window.print()">Cetak</a>
        </div>
    </div>

    <script>
        // Generate QR Code untuk verifikasi transaksi
        @php
            // Ambil ID encounter pertama atau buat kode unik
            $qrcodeValue = '';
            if (isset($encounters) && $encounters->count() > 0) {
                // Gunakan format: TRX-YYYYMMDD-ENCOUNTER_ID
                $firstEncounter = $encounters->first();
                $qrcodeValue = 'TRX-' . now()->format('Ymd') . '-' . substr($firstEncounter->id, 0, 8);
            } else {
                $qrcodeValue = 'TRX-' . now()->format('YmdHis');
            }
        @endphp

        // Generate QR Code
        var qrcode = new QRCode(document.getElementById("qrcode"), {
            text: "{{ $qrcodeValue }}",
            width: 150,
            height: 150,
            colorDark: "#000000",
            colorLight: "#f8f9fa",
            correctLevel: QRCode.CorrectLevel.H
        });
    </script>
</body>

</html>

```
