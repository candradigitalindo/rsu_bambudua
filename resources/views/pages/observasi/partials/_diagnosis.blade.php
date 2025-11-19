<div class="row gx-3">
    <div class="col-xxl-6 col-sm-6">
        <div class="card mb-1 shadow-sm">
            <div class="card-header bg-gradient" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                <h5 class="card-title text-white mb-0">
                    <i class="ri-health-book-line me-2"></i>Form Diagnosis Medis
                </h5>
            </div>
            <div class="card-body p-4">
                <form id="form-diagnosis-medis">
                    <div class="mb-4">
                        <label class="form-label fw-semibold" for="icd10_id">
                            <i class="ri-file-list-2-line text-success me-1"></i>Diagnosis (ICD10)
                            <span class="text-danger">*</span>
                        </label>
                        <select name="icd10_id" id="icd10_id" class="form-select" required>
                            <option value="">-- Cari kode atau nama diagnosis --</option>
                        </select>
                        <div class="invalid-feedback">Silakan pilih diagnosis ICD10</div>
                        <small class="form-text text-muted">
                            <i class="ri-information-line"></i> Ketik untuk mencari diagnosis berdasarkan kode ICD10
                            atau nama penyakit
                        </small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold" for="diagnosis_type">
                            <i class="ri-node-tree text-info me-1"></i>Tipe Diagnosis
                            <span class="text-danger">*</span>
                        </label>
                        <select name="diagnosis_type" id="diagnosis_type" class="form-select" required>
                            <option value="">-- Pilih Tipe Diagnosis --</option>
                            <option value="Primer">🎯 Primer (Diagnosis Utama)</option>
                            <option value="Sekunder">📋 Sekunder (Diagnosis Tambahan)</option>
                        </select>
                        <div class="invalid-feedback">Silakan pilih tipe diagnosis</div>
                        <small class="form-text text-muted">
                            <i class="ri-information-line"></i> Primer: Diagnosis utama pasien | Sekunder: Diagnosis
                            penyerta
                        </small>
                    </div>

                    <div class="alert alert-success border-start border-4 border-success d-none" role="alert"
                        id="info-diagnosis">
                        <div class="d-flex align-items-start">
                            <i class="ri-checkbox-circle-line fs-4 me-2"></i>
                            <div>
                                <strong class="d-block">Diagnosis dipilih:</strong>
                                <span id="selected-diagnosis-preview" class="text-muted small"></span>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 justify-content-end mt-4">
                        <button type="reset" class="btn btn-outline-secondary" id="btn-reset-diagnosis">
                            <i class="ri-refresh-line me-1"></i>Reset
                        </button>
                        <button type="submit" class="btn btn-success px-4" id="btn-diagnosis-medis">
                            <span class="btn-txt" id="text-diagnosis-medis">
                                <i class="ri-save-line me-1"></i>Simpan Diagnosis
                            </span>
                            <span class="spinner-border spinner-border-sm d-none me-1"
                                id="spinner-diagnosis-medis"></span>
                            <span class="d-none" id="text-loading-diagnosis">Menyimpan...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-xxl-6 col-sm-12">
        <div class="card mb-3 shadow-sm">
            <div class="card-header bg-gradient" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                <h5 class="card-title text-white mb-0">
                    <i class="ri-folder-chart-line me-2"></i>Data Diagnosis Medis
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle m-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" width="80">Aksi</th>
                                <th width="100">Kode ICD10</th>
                                <th>Diagnosa</th>
                                <th class="text-center" width="120">Type</th>
                                <th width="150">Dokter</th>
                            </tr>
                        </thead>
                        <tbody id="tbody-diagnosis"></tbody>
                    </table>
                </div>
                <div id="empty-state-diagnosis" class="text-center py-5" style="display: none;">
                    <i class="ri-file-search-line" style="font-size: 4rem; color: #e0e0e0;"></i>
                    <p class="text-muted mt-3 mb-0">Belum ada diagnosis yang ditambahkan</p>
                    <p class="text-muted small">Tambahkan diagnosis untuk melengkapi rekam medis</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        (function() {
            const ENCOUNTER_ID = @json($observasi);
            const listUrl = "{{ route('observasi.getDiagnosis', ':id') }}".replace(':id', ENCOUNTER_ID);
            const postUrl = "{{ route('observasi.postDiagnosis', ':id') }}".replace(':id', ENCOUNTER_ID);
            const deleteUrl = function(id) {
                return "{{ route('observasi.deleteDiagnosis', ':id') }}".replace(':id', id);
            };
            const icdAjaxUrl = "{{ route('observasi.getIcd10', $observasi) }}";

            function initIcdSelect() {
                if (!$.fn.select2) return;
                $('#icd10_id').select2({
                    placeholder: '🔍 Ketik untuk mencari diagnosis...',
                    allowClear: true,
                    width: '100%',
                    minimumInputLength: 2,
                    ajax: {
                        url: icdAjaxUrl,
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                search: params.term
                            };
                        },
                        processResults: function(data) {
                            // Support array or object with data property
                            const rows = Array.isArray(data) ? data : (Array.isArray(data.data) ? data
                                .data : []);
                            return {
                                results: rows.map(function(item) {
                                    if (item.code && item.description) {
                                        return {
                                            id: item.code,
                                            text: item.code + ' - ' + item.description,
                                            description: item.description
                                        };
                                    }
                                    // Fallback
                                    return {
                                        id: item.id || item.code || '',
                                        text: (item.description || item.text || ''),
                                        description: item.description || item.text || ''
                                    };
                                })
                            };
                        },
                        cache: true
                    },
                    language: {
                        inputTooShort: function() {
                            return "Masukkan minimal 2 karakter untuk mencari";
                        },
                        searching: function() {
                            return "Mencari diagnosis...";
                        },
                        noResults: function() {
                            return "Diagnosis tidak ditemukan";
                        }
                    }
                });
            }

            // Show preview when diagnosis selected
            $(document).on('select2:select', '#icd10_id', function(e) {
                const data = e.params.data;
                $('#selected-diagnosis-preview').text(data.text);
                $('#info-diagnosis').removeClass('d-none').hide().slideDown();
            });

            $(document).on('select2:clear', '#icd10_id', function() {
                $('#info-diagnosis').slideUp();
            });

            function loadDiagnosis() {
                $.ajax({
                        url: listUrl,
                        type: 'GET',
                        data: {
                            _token: "{{ csrf_token() }}"
                        }
                    })
                    .done(function(data) {
                        const tbody = $('#tbody-diagnosis');
                        const emptyState = $('#empty-state-diagnosis');
                        tbody.empty();
                        // Ensure data is an array
                        const items = Array.isArray(data) ? data : (data ? [data] : []);

                        if (items.length === 0) {
                            emptyState.show();
                            tbody.closest('table').hide();
                            return;
                        }

                        emptyState.hide();
                        tbody.closest('table').show();

                        items.forEach(function(item, index) {
                            const typeIcon = item.diagnosis_type === 'Primer' ? '🎯' : '📋';
                            const typeBadgeClass = item.diagnosis_type === 'Primer' ? 'bg-primary' :
                                'bg-info';

                            tbody.append(`
            <tr class="animate-fade-in" style="animation-delay: ${index * 0.05}s">
              <td class="text-center">
                <button class="btn btn-danger btn-sm btn-hapus-diagnosis" data-id="${item.id}" title="Hapus diagnosis">
                  <i class="ri-delete-bin-line"></i>
                </button>
              </td>
              <td>
                <span class="badge bg-light text-dark border">${item.diagnosis_code}</span>
              </td>
              <td>
                <div class="fw-semibold">${item.diagnosis_description}</div>
              </td>
              <td class="text-center">
                <span class="badge ${typeBadgeClass}">${typeIcon} ${item.diagnosis_type}</span>
              </td>
              <td>
                <div class="d-flex align-items-center">
                  <i class="ri-user-line me-2 text-muted"></i>
                  <span class="text-truncate">${item.petugas_name || '-'}</span>
                </div>
              </td>
            </tr>
          `);
                        });
                    });
            }

            // On tab click, reset and load
            $(document).on('click', '#tab-diagnosis', function() {
                initIcdSelect();
                $('#icd10_id').val(null).trigger('change');
                $('#diagnosis_type').val('');
                $('#info-diagnosis').slideUp();
                $('#form-diagnosis-medis').removeClass('was-validated');
                loadDiagnosis();
            });

            // Reset form
            $(document).on('click', '#btn-reset-diagnosis', function() {
                $('#form-diagnosis-medis')[0].reset();
                $('#icd10_id').val(null).trigger('change');
                $('#info-diagnosis').slideUp();
                $('#form-diagnosis-medis').removeClass('was-validated');
            });

            // Save diagnosis dengan validasi Bootstrap
            $(document).on('submit', '#form-diagnosis-medis', function(e) {
                e.preventDefault();

                const form = this;
                if (!form.checkValidity()) {
                    e.stopPropagation();
                    $(form).addClass('was-validated');
                    return;
                }

                const icd10_id = $('#icd10_id').val();
                const diagnosis_type = $('#diagnosis_type').val();

                $.ajax({
                    url: postUrl,
                    type: 'POST',
                    data: {
                        icd10_id: icd10_id,
                        diagnosis_type: diagnosis_type,
                        _token: "{{ csrf_token() }}"
                    },
                    beforeSend: function() {
                        $('#btn-diagnosis-medis').prop('disabled', true);
                        $('#spinner-diagnosis-medis').removeClass('d-none');
                        $('#text-diagnosis-medis').addClass('d-none');
                        $('#text-loading-diagnosis').removeClass('d-none');
                    }
                }).done(function(resp) {
                    if (resp.status == 200) {
                        swal({
                            title: 'Berhasil!',
                            text: resp.message || 'Diagnosis berhasil disimpan',
                            icon: 'success',
                            timer: 2000,
                            buttons: false
                        });
                        // Reset form
                        $('#form-diagnosis-medis')[0].reset();
                        $('#icd10_id').val(null).trigger('change');
                        $('#diagnosis_type').val('');
                        $('#info-diagnosis').slideUp();
                        $(form).removeClass('was-validated');
                        // Reload data
                        loadDiagnosis();
                    } else {
                        swal({
                            title: 'Gagal!',
                            text: resp.message || 'Terjadi kesalahan saat menyimpan data',
                            icon: 'error'
                        });
                    }
                }).fail(function(xhr) {
                    swal({
                        title: 'Error!',
                        text: 'Terjadi kesalahan pada server',
                        icon: 'error'
                    });
                }).always(function() {
                    $('#btn-diagnosis-medis').prop('disabled', false);
                    $('#spinner-diagnosis-medis').addClass('d-none');
                    $('#text-diagnosis-medis').removeClass('d-none');
                    $('#text-loading-diagnosis').addClass('d-none');
                });
            });

            // Delete diagnosis dengan konfirmasi yang lebih informatif
            $(document).on('click', '#tbody-diagnosis .btn-hapus-diagnosis', function() {
                const id = $(this).data('id');
                const row = $(this).closest('tr');
                const diagnosisCode = row.find('td:eq(1) .badge').text();
                const diagnosisDesc = row.find('td:eq(2) .fw-semibold').text();

                swal({
                        title: 'Konfirmasi Hapus',
                        text: `Apakah Anda yakin ingin menghapus diagnosis "${diagnosisCode} - ${diagnosisDesc}"?`,
                        icon: 'warning',
                        buttons: {
                            cancel: {
                                text: 'Batal',
                                value: false,
                                visible: true,
                                className: 'btn-secondary',
                                closeModal: true,
                            },
                            confirm: {
                                text: 'Ya, Hapus',
                                value: true,
                                visible: true,
                                className: 'btn-danger',
                                closeModal: false
                            }
                        },
                        dangerMode: true
                    })
                    .then((willDelete) => {
                        if (!willDelete) return;

                        swal({
                            title: 'Menghapus...',
                            text: 'Mohon tunggu',
                            icon: 'info',
                            buttons: false,
                            closeOnClickOutside: false,
                            closeOnEsc: false
                        });

                        $.ajax({
                                url: deleteUrl(id),
                                type: 'DELETE',
                                data: {
                                    _token: "{{ csrf_token() }}"
                                }
                            })
                            .done(function(data) {
                                swal({
                                    title: 'Berhasil!',
                                    text: data.message || 'Diagnosis berhasil dihapus',
                                    icon: 'success',
                                    timer: 2000,
                                    buttons: false
                                });
                                loadDiagnosis();
                            }).fail(function(xhr) {
                                swal({
                                    title: 'Gagal!',
                                    text: 'Terjadi kesalahan saat menghapus data',
                                    icon: 'error'
                                });
                            });
                    });
            });
        })();
    </script>
@endpush

<style>
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fade-in {
        animation: fadeIn 0.3s ease-in-out forwards;
        opacity: 0;
    }

    .card-header.bg-gradient {
        border: none;
    }

    #icd10_id.select2-container {
        width: 100% !important;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(17, 153, 142, 0.05);
        transition: all 0.2s ease;
    }

    .btn {
        transition: all 0.2s ease;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .alert-success {
        background-color: rgba(56, 239, 125, 0.1);
        border-color: rgba(56, 239, 125, 0.3);
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #11998e;
        box-shadow: 0 0 0 0.2rem rgba(17, 153, 142, 0.25);
    }

    .badge {
        padding: 0.35em 0.65em;
        font-weight: 500;
    }

    .select2-container--default .select2-selection--single {
        border-radius: 0.375rem;
        border-color: #dee2e6;
        height: calc(2.5rem + 2px);
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 2.5rem;
    }

    .select2-container--default .select2-results__option--highlighted {
        background-color: #11998e !important;
    }
</style>
