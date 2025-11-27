@extends('layouts.app')
@section('title')
    Arsip Dokumen RM
@endsection
@push('style')
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/dropzone/dropzone.min.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />
    <style>
        .gallery a {
            display: block;
            border: 1px solid #cdd6dc;
            border-radius: 8px;
            overflow: hidden;
            padding: 3px;
        }

        .gallery .file-card {
            border: 1px solid #cdd6dc;
            border-radius: 8px;
            padding: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .file-name {
            word-break: break-all;
        }
    </style>
@endpush
@section('content')
    <div class="row gx-3">
        <div class="col-lg-4 col-12">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title">Upload Dokumen</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Cari Pasien <span class="text-danger">*</span></label>
                        <select class="form-select" id="selectPasien" required>
                            <option value="">-- Pilih Pasien --</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keterangan (Opsional)</label>
                        <textarea class="form-control" id="description" rows="2" placeholder="Contoh: Hasil Lab 27 Nov 2025"></textarea>
                    </div>
                    <form action="{{ route('medical-records.arsip.upload') }}" class="dropzone" id="dropzoneArsip">
                        @csrf
                        <div class="dz-message">Tarik & lepas file disini atau klik untuk memilih</div>
                    </form>
                    <small class="text-muted d-block mt-2">Pastikan dokumen sesuai standar RS dan tidak memuat informasi
                        yang tidak relevan. Max 10MB.</small>
                </div>
            </div>
        </div>
        <div class="col-lg-8 col-12">
            <div class="card mb-3">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title m-0">Daftar Arsip</h5>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="btnReload"><i
                            class="ri-refresh-line"></i> Muat Ulang</button>
                </div>
                <div class="card-body">
                    <div id="listArsip" class="d-grid gap-2"></div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="{{ asset('vendor/overlay-scroll/jquery.overlayScrollbars.min.js') }}"></script>
    <script src="{{ asset('vendor/overlay-scroll/custom-scrollbar.js') }}"></script>
    <script src="{{ asset('vendor/dropzone/dropzone.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/custom.js') }}"></script>
    <script>
        Dropzone.autoDiscover = false;
        const dz = new Dropzone('#dropzoneArsip', {
            paramName: 'file',
            maxFilesize: 10,
            timeout: 0,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            autoProcessQueue: false,
            init: function() {
                const dropzone = this;
                dropzone.on('addedfile', function(file) {
                    const rekamMedis = $('#selectPasien').val();
                    if (!rekamMedis) {
                        alert('Pilih pasien terlebih dahulu!');
                        dropzone.removeFile(file);
                        return;
                    }

                    const formData = new FormData();
                    formData.append('file', file);
                    formData.append('rekam_medis', rekamMedis);
                    formData.append('description', $('#description').val());
                    formData.append('_token', '{{ csrf_token() }}');

                    $.ajax({
                        url: "{{ route('medical-records.arsip.upload') }}",
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(resp) {
                            dropzone.removeFile(file);
                            $('#description').val('');
                            loadArsip();
                            Swal.fire('Berhasil', 'File berhasil diupload', 'success');
                        },
                        error: function(xhr) {
                            dropzone.removeFile(file);
                            const msg = xhr.responseJSON?.message || 'Gagal upload file';
                            Swal.fire('Error', msg, 'error');
                        }
                    });
                });
            }
        });

        // Load pasien untuk select2
        $('#selectPasien').select2({
            placeholder: '-- Cari RM / Nama Pasien --',
            ajax: {
                url: "{{ route('pendaftaran.caripasien.json') }}",
                dataType: 'json',
                delay: 300,
                data: function(params) {
                    return {
                        term: params.term
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.map(p => ({
                            id: p.rekam_medis,
                            text: `${p.rekam_medis} - ${p.name}`
                        }))
                    };
                }
            },
            minimumInputLength: 2
        });

        function fmtBytes(bytes) {
            if (!bytes && bytes !== 0) return '-';
            const sizes = ['B', 'KB', 'MB', 'GB'];
            const i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
            return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
        }

        function row(item) {
            return `<div class="file-card">
                <div>
                    <div class="file-name">
                        <i class="ri-file-text-line"></i> ${item.file_name}
                        <span class="badge bg-secondary ms-2">${item.rekam_medis}</span>
                    </div>
                    <small class="text-muted">${item.pasien_name || ''}</small><br>
                    <small class="text-muted">${item.uploaded_at || ''} • ${fmtBytes(item.file_size)} • ${item.uploaded_by || ''}</small>
                    ${item.description ? `<br><small class="text-info">${item.description}</small>` : ''}
                </div>
                <div class="d-flex gap-2">
                    <a class="btn btn-sm btn-success" href="${item.url}" target="_blank"><i class="ri-download-2-line"></i></a>
                    <button class="btn btn-sm btn-danger btn-del" data-id="${item.id}"><i class="ri-delete-bin-6-line"></i></button>
                </div>
            </div>`;
        }

        function loadArsip() {
            $.get({
                    url: "{{ route('medical-records.arsip.list') }}"
                })
                .done(function(resp) {
                    const c = $('#listArsip');
                    c.empty();
                    if (resp.data && resp.data.length > 0) {
                        resp.data.forEach(f => c.append(row(f)));
                    } else {
                        c.html('<div class="text-center text-muted">Belum ada dokumen</div>');
                    }
                });
        }

        $(document).on('click', '#btnReload', loadArsip);

        $(document).on('click', '.btn-del', function() {
            const id = $(this).data('id');
            if (!confirm('Hapus file ini?')) return;
            $.ajax({
                    url: "{{ route('medical-records.arsip.delete', ':id') }}".replace(':id', id),
                    type: 'DELETE',
                    data: {
                        _token: "{{ csrf_token() }}"
                    }
                })
                .done(function() {
                    loadArsip();
                    Swal.fire('Berhasil', 'File berhasil dihapus', 'success');
                })
                .fail(function() {
                    Swal.fire('Error', 'Gagal menghapus file', 'error');
                });
        });

        $(function() {
            loadArsip();
        });
    </script>
@endpush
