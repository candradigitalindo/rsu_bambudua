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
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">Pengaturan Reminder Pasien</h5>
                    <a href="{{ route('reminder-settings.create') }}" class="btn btn-primary btn-sm">
                        <i class="ri-add-line"></i> Tambah Reminder
                    </a>
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
                                    <th width="15%" class="text-center">Hari Sebelum</th>
                                    <th width="30%">Template Pesan</th>
                                    <th width="10%" class="text-center">Status</th>
                                    <th width="15%" class="text-center">Aksi</th>
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
                                        <td class="text-center">{{ $reminder->days_before }} hari</td>
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
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('reminder-settings.edit', $reminder->id) }}"
                                                    class="btn btn-warning">
                                                    <i class="ri-edit-line"></i>
                                                </a>
                                                <button type="button" class="btn btn-danger btn-delete"
                                                    data-id="{{ $reminder->id }}">
                                                    <i class="ri-delete-bin-line"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">
                                            Belum ada pengaturan reminder.
                                            <a href="{{ route('reminder-settings.create') }}">Tambah sekarang</a>
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

            // Delete reminder
            $('.btn-delete').on('click', function() {
                const reminderId = $(this).data('id');

                Swal.fire({
                    title: 'Anda yakin?',
                    text: "Reminder akan dihapus permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const deleteUrl = '{{ route('reminder-settings.destroy', ':id') }}'
                            .replace(':id', reminderId);
                        const form = $('<form>', {
                            method: 'POST',
                            action: deleteUrl
                        });

                        form.append($('<input>', {
                            type: 'hidden',
                            name: '_token',
                            value: '{{ csrf_token() }}'
                        }));

                        form.append($('<input>', {
                            type: 'hidden',
                            name: '_method',
                            value: 'DELETE'
                        }));

                        $('body').append(form);
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush
