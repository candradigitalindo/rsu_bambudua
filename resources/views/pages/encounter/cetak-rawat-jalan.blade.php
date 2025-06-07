<!-- resources/views/pages/encounter/print-hasil.blade.php -->
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Cetak Hasil Encounter</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .table th,
        .table td {
            border: 1px solid #333;
            padding: 8px;
        }

        .table th {
            background: #eee;
        }

        .section-title {
            font-weight: bold;
            margin-top: 20px;
        }

        .text-center {
            text-align: center;
        }

        .text-end {
            text-align: end;
        }

        @media print {
            body {
                counter-reset: pageTotal;
            }

            @page {
                margin-bottom: 30mm;
            }

            table {
                page-break-inside: avoid;
            }

            tfoot {
                display: table-row-group;
            }

            .section-block {
                page-break-inside: avoid;
                break-inside: avoid;
            }

            .page-footer {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                text-align: center;
                font-size: 12px;
            }

            .page-number:after {
                content: "Halaman " counter(page);
            }
        }
    </style>
</head>

<body>
    <!-- Kop Surat -->
    <div
        style="display: flex; align-items: center; margin-bottom: 16px; border-bottom:2px solid #333; padding-bottom:12px;">
        <img src="{{ asset('images/bdc.PNG') }}" alt="Logo" style="height:70px; margin-right:18px;">
        <div style="flex:1;">
            <div style="font-size:20px; font-weight:bold;">Bambu Dua Clinic</div>
            <div style="font-size:15px;">Jl. Bambu II No.20, Durian, Kota Medan, Sumatera Utara</div>
            <div style="font-size:13px;">Telp: (061) 6610112 / 6622802 | WhatsApp : 0811 - 6311 - 378</div>
        </div>
    </div>
    <!-- Akhir Kop Surat -->

    <div class="header">
        <h2>Hasil Pemeriksaan Rawat Jalan</h2>


    </div>
    <div>
        <table>
            <tr>
                <td>No. Kunjungan</td>
                <td>:</td>
                <td>{{ $encounter->no_encounter }}</td>
            </tr>
            <tr>
                <td>Nama Pasien</td>
                <td>:</td>
                <td>{{ $encounter->name_pasien }}</td>
            </tr>
            <tr>
                <td>Tanggal</td>
                <td>:</td>
                <td>{{ \Carbon\Carbon::parse($encounter->updated_at)->format('d-m-Y H:i:s') }}</td>
            </tr>
        </table>
    </div>

    <div>
        <div class="section-title">Identitas Pasien</div>
        <table>
            <tr>
                <td>No. RM</td>
                <td>{{ $encounter->rekam_medis }}</td>
            </tr>
            <tr>
                <td>Jenis Jaminan</td>
                <td>{{ $encounter->jenis_jaminan }}</td>
            </tr>
            <tr>
                <td>Tujuan</td>
                <td>{{ $encounter->tujuan_kunjungan }}</td>
            </tr>
        </table>
    </div>

    <div>
        <div class="section-title">Pemeriksaan Penunjang</div>
        <table class="table">
            <thead>
                <tr>
                    <th>Jenis Pemeriksaan</th>
                    <th>Hasil</th>
                </tr>
            </thead>
            <tbody>
                @if ($encounter->pemeriksaanPenunjang && count($encounter->pemeriksaanPenunjang))
                    @foreach ($encounter->pemeriksaanPenunjang as $penunjang)
                        <tr>
                            <td>{{ $penunjang->jenis_pemeriksaan }}</td>
                            <td>{!! $penunjang->hasil_pemeriksaan !!}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="2" class="text-center">-</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <div>
        <div class="section-title">Diagnosis</div>
        <table class="table">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Deskripsi</th>
                    <th>Jenis</th>
                </tr>
            </thead>
            <tbody>
                @if ($encounter->diagnosis && count($encounter->diagnosis))
                    @foreach ($encounter->diagnosis as $diag)
                        <tr>
                            <td>{{ $diag->diagnosis_code }}</td>
                            <td>{{ $diag->diagnosis_description }}</td>
                            <td>{{ $diag->diagnosis_type }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="3" class="text-center">-</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <div class="section-block">
        <div class="section-title">Tindakan/Prosedur</div>
        <table class="table">
            <thead>
                <tr>
                    <th>Nama Tindakan</th>
                    <th>Qty</th>

                </tr>
            </thead>
            <tbody>
                @foreach ($encounter->tindakan as $tindakan)
                    <tr>
                        <td>{{ $tindakan->tindakan_name }}</td>
                        <td class="text-center">{{ $tindakan->qty }}</td>

                    </tr>
                @endforeach
            </tbody>

        </table>
    </div>

    <div class="section-block">
        <div class="section-title">Resep Obat [{{ $encounter->resep->kode_resep ?? '-' }}] |
            {{ $encounter->resep->masa_pemakaian_hari ?? '-' }} Hari</div>
        <table class="table">
            <thead>
                <tr>
                    <th>Nama Obat</th>
                    <th>Qty</th>
                    <th>Aturan Pakai</th>

                </tr>
            </thead>
            <tbody>
                @if ($encounter->resep && $encounter->resep->details)
                    @foreach ($encounter->resep->details as $detail)
                        <tr>
                            <td>{{ $detail->nama_obat }}</td>
                            <td class="text-center">{{ $detail->qty }}</td>
                            <td>{{ $detail->aturan_pakai }}</td>

                        </tr>
                    @endforeach
                @endif
            </tbody>

        </table>
    </div>

    <div>
        <div class="section-title">Catatan Dokter</div>
        <div style="border:1px solid #333; min-height:60px; padding:12px; border-radius:6px; background:#fafafa;">
            {!! $encounter->catatan ?? '-' !!}
        </div>
    </div>

    <div class="section-block" style="margin-top:40px;">
        <div class="text-end">
            <p>Dokter Pemeriksa,</p>
            <br><br><br><br>
            <p><strong>
                @if ($encounter->practitioner instanceof \Illuminate\Support\Collection)
                    {{ $encounter->practitioner->pluck('name')->join(', ') ?: '-' }}
                @else
                    {{ $encounter->practitioner->name ?? '-' }}
                @endif
            </strong></p>
        </div>
    </div>
    <div class="text-center" style="margin-top: 20px;">
        <p>Dicetak pada: {{ now()->format('d-m-Y H:i:s') }}</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
        window.onafterprint = function() {
            window.close();
        };
        // Untuk beberapa browser, tutup juga jika user tekan ESC saat print dialog
        window.addEventListener('keydown', function(e) {
            if (e.key === "Escape") {
                window.close();
            }
        });
    </script>
</body>

</html>
