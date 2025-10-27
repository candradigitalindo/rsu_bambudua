@extends('layouts.app')

@section('title', 'Pengaturan Reminder')

@push('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
    <style>
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
    <div class="row gx-3">
        <div class="col-xxl-12">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title">Pengaturan Reminder Pasien</h5>
                    <p class="text-muted small mb-0">Kelola template pesan reminder untuk pasien. Terdapat 2 jenis reminder:
                        Beli Obat dan Check Up.</p>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button-type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered m-0">
                            <thead>
                                <tr>
                                    <th width="5%" class="text-center">No</th>
                                    <th>Nama Reminder</th>
                                    <th width="15%" class="text-center">Tipe</th>
                                    <th width="20%" class="text-center">Waktu Reminder</th>
                                    <th width="25%">Template Pesan</th>
                                    <th width="10%" class="text-center">Status</th>
                                    <th width="10%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reminders as $reminder)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td>{{ $reminder->name }}</td>
                                        <td class="text-center">
                                            @if ($reminder->type === 'obat')
                                                <span class="badge bg-info">Beli Obat</span>
                                            @else
                                                <span class="badge bg-success">Check Up</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($reminder->type === 'obat')
                                                {{ $reminder->days_before }} hari setelah obat habis
                                            @else
                                                {{ $reminder->days_before }} hari dari kunjungan
                                            @endif
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-info btn-preview-template"
                                                data-template="{{ htmlspecialchars($reminder->message_template) }}"
                                                data-name="{{ $reminder->name }}">
                                                <i class="ri-eye-line"></i> Preview
                                            </button>
                                        </td>
                                        <td class="text-center">
                                            <div class="form-check form-switch d-flex justify-content-center">
                                                <input class="form-check-input toggle-status" type="checkbox"
                                                    data-id="{{ $reminder->id }}"
                                                    {{ $reminder->is_active ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('reminder-settings.edit', $reminder->id) }}"
                                                class="btn btn-warning btn-sm" title="Edit Template">
                                                <i class="ri-edit-line"></i> Edit
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">
                                            Belum ada pengaturan reminder. Jalankan seeder: <code>php artisan db:seed
                                                --class=ReminderSettingSeeder</code>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Preview Template -->
    <div class="modal fade" id="modalPreviewTemplate" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalPreviewTitle">Preview Template Pesan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="card bg-light">
                        <div class="card-body" style="white-space: pre-wrap; font-family: monospace; font-size: 13px;"
                            id="templateContent">
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
    <script src="{{ asset('vendor/overlay-scroll/jquery.overlayScrollbars.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('vendor/overlay-scroll/custom-scrollbar.js') }}"></script>
    <script src="{{ asset('js/custom.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Prevent body scroll when modal is open
            $('#modalPreviewTemplate').on('show.bs.modal', function() {
                $('body').css('overflow', 'hidden');
            }).on('hidden.bs.modal', function() {
                $('body').css('overflow', 'auto');
                // Remove any leftover backdrops
                $('.modal-backdrop').remove();
            });

            // Preview Template
            $('.btn-preview-template').on('click', function() {
                const template = $(this).data('template');
                const name = $(this).data('name');

                $('#modalPreviewTitle').text('Preview: ' + name);
                $('#templateContent').text(template);
                $('#modalPreviewTemplate').modal('show');
            });

            // Toggle status
            $('.toggle-status').on('change', function() {
                const reminderId = $(this).data('id');
                const checkbox = $(this);

                $.ajax({
                    url: '{{ route('reminder-settings.toggle-status', ':id') }}'.replace(':id',
                        reminderId),
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    },
                    error: function() {
                        checkbox.prop('checked', !checkbox.prop('checked'));
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: 'Terjadi kesalahan saat mengubah status.'
                        });
                    }
                });
            });
        });
    </script>
@endpush
