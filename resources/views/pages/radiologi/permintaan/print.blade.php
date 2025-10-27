@extends('layouts.print')
@section('title', 'Hasil Pemeriksaan Radiologi')
@push('style')
    <style>
        .report-title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin: 16px 0;
            text-decoration: underline;
        }

        .patient-info {
            margin-bottom: 20px;
        }

        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }

        .info-table td {
            padding: 4px 8px;
            border: none;
        }

        .info-table td:first-child {
            width: 180px;
            font-weight: 500;
        }

        .info-table td:nth-child(2) {
            width: 10px;
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

        .signature-area {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }

        .signature-box {
            text-align: center;
            min-width: 200px;
        }

        .signature-line {
            margin-top: 60px;
            border-top: 1px solid #000;
            padding-top: 4px;
        }

        .footer-note {
            margin-top: 30px;
            font-size: 11px;
            color: #666;
            text-align: center;
            border-top: 1px solid #ddd;
            padding-top: 8px;
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
    <div class="report-title">HASIL PEMERIKSAAN RADIOLOGI</div>

    <div class="patient-info">
        <table class="info-table">
            <tr>
                <td>No. Rekam Medis</td>
                <td>:</td>
                <td><strong>{{ $req->pasien->rekam_medis ?? '-' }}</strong></td>
            </tr>
            <tr>
                <td>Nama Pasien</td>
                <td>:</td>
                <td><strong>{{ $req->pasien->name ?? '-' }}</strong></td>
            </tr>
            <tr>
                <td>Tanggal Lahir / Umur</td>
                <td>:</td>
                <td>
                    @if ($req->pasien && $req->pasien->tanggal_lahir)
                        {{ \Carbon\Carbon::parse($req->pasien->tanggal_lahir)->format('d M Y') }}
                        ({{ \Carbon\Carbon::parse($req->pasien->tanggal_lahir)->age }} tahun)
                    @else
                        -
                    @endif
                </td>
            </tr>
            <tr>
                <td>Jenis Kelamin</td>
                <td>:</td>
                <td>{{ $req->pasien->jenis_kelamin ?? '-' }}</td>
            </tr>
            <tr>
                <td>Alamat</td>
                <td>:</td>
                <td>{{ $req->pasien->alamat ?? '-' }}</td>
            </tr>
            <tr>
                <td>Dokter Pengirim</td>
                <td>:</td>
                <td><strong>{{ optional($req->dokter)->name ?? '-' }}</strong></td>
            </tr>
            <tr>
                <td>Pemeriksaan</td>
                <td>:</td>
                <td><strong>{{ optional($req->jenis)->name ?? '-' }}</strong></td>
            </tr>
            <tr>
                <td>Tanggal Permintaan</td>
                <td>:</td>
                <td>{{ optional($req->created_at)->format('d M Y H:i') }} WIB</td>
            </tr>
            @if ($latest)
                <tr>
                    <td>Tanggal Pemeriksaan</td>
                    <td>:</td>
                    <td>{{ optional($latest->reported_at)->format('d M Y H:i') }} WIB</td>
                </tr>
            @endif
        </table>
    </div>

    @if ($latest)
        {{-- Custom Fields / Data Pemeriksaan --}}
        @if (!empty($latest->payload) && is_array($latest->payload) && count($latest->payload) > 0)
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

        {{-- Findings --}}
        <div class="section-title">TEMUAN (FINDINGS)</div>
        <div class="content-box">{{ $latest->findings ?? 'Tidak ada temuan yang dicatat.' }}</div>

        {{-- Impression --}}
        <div class="section-title">KESIMPULAN (IMPRESSION)</div>
        <div class="content-box">{{ $latest->impression ?? 'Tidak ada kesimpulan yang dicatat.' }}</div>

        {{-- Attachments --}}
        @if (is_array($latest->files) && count($latest->files) > 0)
            <div class="section-title">LAMPIRAN GAMBAR</div>
            <div class="content-box">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach ($latest->files as $file)
                        <li>{{ basename($file) }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Signature Area --}}
        <div class="signature-area">
            <div class="signature-box">
                <div>Mengetahui,</div>
                <div style="font-weight: 600;">Petugas Pendaftaran</div>
                <div class="signature-line">
                    {{ optional($latest->reporter)->name ?? '(................................)' }}
                </div>
            </div>
            <div class="signature-box">
                <div>&nbsp;</div>
                <div style="font-weight: 600;">Dokter Spesialis Radiologi</div>
                <div class="signature-line">
                    {{ optional($latest->radiologist)->name ?? '(................................)' }}
                </div>
            </div>
        </div>
    @else
        <div class="content-box"
            style="text-align: center; padding: 40px; background-color: #fff3cd; border: 1px solid #ffc107;">
            <strong>⚠️ HASIL PEMERIKSAAN BELUM TERSEDIA</strong>
            <p style="margin-top: 8px;">Pemeriksaan radiologi untuk pasien ini belum memiliki hasil.</p>
        </div>
    @endif

    <div class="footer-note">
        Dokumen ini dicetak secara elektronik dan sah tanpa tanda tangan basah.<br>
        Dicetak pada: {{ now()->format('d M Y H:i:s') }} WIB
    </div>
@endsection
