@extends('layouts.app')
@section('title', 'Data Reminder Pasien')
@push('style')
    <!-- Scrollbar CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
    <style>
        a.disabled {
            /* Make the disabled links grayish*/
            color: gray;
            /* And disable the pointer events */
            pointer-events: none;
        }
    </style>
@endpush
@section('content')
    <!-- Row starts -->
    <div class="row gx-3">
        <div class="col-xxl-12 col-sm-12">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title">Data Reminder Pasien</h5>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="table-outer">
                        <div class="table-responsive">
                            <table class="table truncate m-0">
                                <thead>
                                    <tr>
                                        <th>Nama Pasien</th>
                                        <th class="text-center">Tanggal Kunjungan</th>
                                        <th class="text-center">Masa Resep (Hari)</th>
                                        <th class="text-center">No HP</th>
                                        <th class="text-center" style="width: 20%;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($encounters as $encounter)
                                        @php
                                            $namaPasien = urlencode($encounter->name_pasien);

                                            $noHp = preg_replace('/[^0-9]/', '', $encounter->no_hp); // pastikan hanya angka
                                            if (substr($noHp, 0, 1) === '0') {
                                                $noHp = '62' . substr($noHp, 1); // konversi ke format internasional
                                            }

                                            $pesan =
                                                "*Bambu Dua Clinic* – *Pengingat Konsultasi*\n" .
                                                "Halo $encounter->name_pasien,\n" .
                                                "Kami mengingatkan bahwa obat Anda kemungkinan akan habis dalam beberapa hari ke depan.\n\n" .
                                                "Untuk menjaga kelangsungan pengobatan, kami sarankan Anda melakukan *kunjungan ulang sebelum obat habis*.\n\n" .
                                                "*Jadwal kontrol ulang:* Senin - Sabtu Pukul 17.00 - 21.00 WIB\n" .
                                                "*Lokasi:* Bambu Dua Clinic, Jl. Bambu II No.20\n" .
                                                "*Konfirmasi kedatangan:* 0811-6311-378\n\n" .
                                                "Kami siap membantu Anda menjaga kesehatan secara berkelanjutan.\n" .
                                                "Terima kasih\n" .
                                                "*Salam sehat*\n*Bambu Dua Clinic*";

                                            $pesanEncoded = urlencode($pesan);
                                            $waUrl = "https://wa.me/$noHp?text=$pesanEncoded";
                                        @endphp
                                        <tr>
                                            <td>{{ ucwords($encounter->name_pasien) }}</td>
                                            <td class="text-center">
                                                {{ \Carbon\Carbon::parse($encounter->created_at)->format('d-m-Y') }}
                                            </td>
                                            <td class="text-center">
                                                {{ $encounter->resep->masa_pemakaian_hari }}
                                            </td>
                                            <td class="text-center">
                                                {{ $encounter->no_hp ?: '-' }}
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-sm btn-info btn-detail-resep"
                                                        data-id="{{ $encounter->id }}"
                                                        data-nama="{{ $encounter->name_pasien }}">
                                                        <i class="ri-file-list-2-line"></i> Detail
                                                    </button>

                                                </div>

                                                <div class="btn-group">
                                                    <a href="{{ $waUrl }}" target="_blank"
                                                        class="btn btn-sm btn-success btn-bayar-tindakan"
                                                        data-id="{{ $encounter->id }}"
                                                        data-nama="{{ $encounter->name_pasien }}">
                                                        <i class="ri-whatsapp-line"></i> Kirim WhatsApp
                                                    </a>

                                                </div>
                                            </td>

                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">Data tidak ada</td>
                                        </tr>
                                    @endforelse

                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
    <!-- Row ends -->

    <!-- Modal Dinamis -->
    <div class="modal fade" id="modalResepDetail" tabindex="-1" aria-labelledby="modalResepDetailLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalResepDetailLabel">Detail Resep Pasien</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalResepDetailBody">
                    <div class="text-center text-muted">Memuat data...</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <!-- Overlay Scroll JS -->
    <script src="{{ asset('vendor/overlay-scroll/jquery.overlayScrollbars.min.js') }}"></script>
    <script src="{{ asset('vendor/overlay-scroll/custom-scrollbar.js') }}"></script>
    <!-- Custom JS files -->
    <script src="{{ asset('js/custom.js') }}"></script>
    <!-- SweetAlert2 JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).on('click', '.btn-detail-resep', function() {
            var id = $(this).data('id');
            var nama = $(this).data('nama');
            $('#modalResepDetailLabel').text('Detail Resep Pasien: ' + nama);
            $('#modalResepDetailBody').html('<div class="text-center text-muted">Memuat data...</div>');
            $('#modalResepDetail').modal('show');
            $.get("{{ url('apotek/resep-detail') }}/" + id, function(res) {
                $('#modalResepDetailBody').html(res);
            }).fail(function() {
                $('#modalResepDetailBody').html(
                    '<div class="text-danger text-center">Gagal memuat data.</div>');
            });
        });

    </script>
@endpush
