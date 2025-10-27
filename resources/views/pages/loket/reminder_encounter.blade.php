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

        /* Fix modal backdrop issue */
        .modal {
            background: rgba(0, 0, 0, 0.5);
        }

        .modal-backdrop {
            display: none !important;
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
                                        <th class="text-center">Jenis Pasien</th>
                                        <th class="text-center">Tanggal Kunjungan</th>
                                        <th class="text-center">Masa Resep</th>
                                        <th class="text-center">Jenis Reminder</th>
                                        <th class="text-center">Waktu Reminder</th>
                                        <th class="text-center">No HP</th>
                                        <th class="text-center" style="width: 20%;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($encounters as $encounter)
                                        <tr>
                                            <td>{{ ucwords($encounter->name_pasien) }}</td>
                                            <td class="text-center">
                                                @php
                                                    $conditionLabels = [
                                                        1 => ['label' => 'Rawat Jalan', 'color' => 'primary'],
                                                        2 => ['label' => 'Rawat Inap', 'color' => 'warning'],
                                                        3 => ['label' => 'IGD', 'color' => 'danger'],
                                                    ];
                                                    $condition = $conditionLabels[$encounter->condition] ?? [
                                                        'label' => '-',
                                                        'color' => 'secondary',
                                                    ];
                                                @endphp
                                                <span class="badge bg-{{ $condition['color'] }}">
                                                    {{ $condition['label'] }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                {{ \Carbon\Carbon::parse($encounter->created_at)->format('d-m-Y') }}
                                            </td>
                                            <td class="text-center">
                                                @if (isset($encounter->resep))
                                                    {{ $encounter->resep->masa_pemakaian_hari }} hari
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ($encounter->reminder_setting)
                                                    <span class="badge bg-info">
                                                        {{ $encounter->reminder_setting->name }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ($encounter->reminder_type === 'obat')
                                                    <span class="badge bg-danger">
                                                        {{ $encounter->days_after_empty }} hari setelah habis
                                                    </span>
                                                @elseif ($encounter->reminder_type === 'checkup')
                                                    <span class="badge bg-success">
                                                        {{ $encounter->days_after_visit }} hari dari kunjungan
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                {{ $encounter->no_hp ?: '-' }}
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group mb-1">
                                                    <button type="button" class="btn btn-sm btn-info btn-detail-resep"
                                                        data-id="{{ $encounter->id }}"
                                                        data-nama="{{ $encounter->name_pasien }}">
                                                        <i class="ri-file-list-2-line"></i> Detail Resep
                                                    </button>
                                                </div>
                                                <div class="btn-group mb-1">
                                                    @if ($encounter->reminder_message)
                                                        <button type="button"
                                                            class="btn btn-sm btn-primary btn-lihat-pesan"
                                                            data-message="{{ $encounter->reminder_message }}"
                                                            data-nama="{{ $encounter->name_pasien }}">
                                                            <i class="ri-message-2-line"></i> Lihat Pesan
                                                        </button>
                                                    @endif
                                                </div>
                                                <div class="btn-group mb-1">
                                                    @if ($encounter->whatsapp_url)
                                                        <a href="{{ $encounter->whatsapp_url }}" target="_blank"
                                                            class="btn btn-sm btn-success">
                                                            <i class="ri-whatsapp-line"></i> Kirim WhatsApp
                                                        </a>
                                                    @else
                                                        <button class="btn btn-sm btn-secondary" disabled>
                                                            <i class="ri-whatsapp-line"></i> No HP Tidak Ada
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>

                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">Data tidak ada</td>
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

    <!-- Modal Detail Resep -->
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

    <!-- Modal Lihat Pesan Reminder -->
    <div class="modal fade" id="modalLihatPesan" tabindex="-1" aria-labelledby="modalLihatPesanLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLihatPesanLabel">Pesan Reminder</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <strong>Pasien:</strong> <span id="pesanNamaPasien"></span>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <p class="mb-0" id="pesanReminderText" style="white-space: pre-wrap;"></p>
                        </div>
                    </div>
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
        // Prevent body scroll when modal is open
        $('.modal').on('show.bs.modal', function() {
            $('body').css('overflow', 'hidden');
        }).on('hidden.bs.modal', function() {
            $('body').css('overflow', 'auto');
            // Remove any leftover backdrops
            $('.modal-backdrop').remove();
        });

        // Detail Resep
        $(document).on('click', '.btn-detail-resep', function() {
            var id = $(this).data('id');
            var nama = $(this).data('nama');
            $('#modalResepDetailLabel').text('Detail Resep Pasien: ' + nama);
            $('#modalResepDetailBody').html(
                '<div class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>'
            );
            $('#modalResepDetail').modal('show');

            $.get("{{ url('apotek/resep-detail') }}/" + id, function(res) {
                $('#modalResepDetailBody').html(res);
            }).fail(function(xhr) {
                console.error('Error loading resep detail:', xhr);
                $('#modalResepDetailBody').html(
                    '<div class="alert alert-danger">Gagal memuat data. Silakan coba lagi.</div>'
                );
            });
        });

        // Lihat Pesan Reminder
        $(document).on('click', '.btn-lihat-pesan', function() {
            var message = $(this).data('message');
            var nama = $(this).data('nama');
            $('#pesanNamaPasien').text(nama);
            $('#pesanReminderText').text(message);
            $('#modalLihatPesan').modal('show');
        });
    </script>
@endpush
