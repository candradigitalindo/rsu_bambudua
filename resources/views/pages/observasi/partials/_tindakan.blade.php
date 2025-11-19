<div class="row gx-3">
    <div class="col-xxl-6 col-sm-6">
        <div class="card mb-1 shadow-sm">
            <div class="card-header bg-gradient" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <h5 class="card-title text-white mb-0">
                    <i class="ri-stethoscope-line me-2"></i>Form Tindakan Medis
                </h5>
            </div>
            <div class="card-body p-4">
                <form id="form-tindakan-medis">
                    <div class="mb-4">
                        <label class="form-label fw-semibold" for="jenis_tindakan">
                            <i class="ri-medical-book-line text-primary me-1"></i>Jenis Tindakan
                            <span class="text-danger">*</span>
                        </label>
                        <select name="jenis_tindakan" id="jenis_tindakan" class="form-select" required>
                            <option value="">-- Pilih Jenis Tindakan --</option>
                        </select>
                        <div class="invalid-feedback">Silakan pilih jenis tindakan</div>
                        <small class="form-text text-muted">
                            <i class="ri-information-line"></i> Pilih tindakan medis yang akan dilakukan
                        </small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold" for="qty">
                            <i class="ri-add-circle-line text-success me-1"></i>Jumlah
                            <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <button class="btn btn-outline-secondary" type="button" id="btn-qty-minus">
                                <i class="ri-subtract-line"></i>
                            </button>
                            <input type="number" class="form-control text-center fw-bold" id="qty" name="qty"
                                value="1" min="1" max="100" required>
                            <button class="btn btn-outline-secondary" type="button" id="btn-qty-plus">
                                <i class="ri-add-line"></i>
                            </button>
                        </div>
                        <div class="invalid-feedback">Jumlah minimal 1</div>
                        <small class="form-text text-muted">
                            <i class="ri-information-line"></i> Jumlah tindakan yang akan dilakukan
                        </small>
                    </div>

                    <div class="alert alert-info alert-dismissible fade show" role="alert" id="info-harga"
                        style="display: none;">
                        <i class="ri-price-tag-3-line me-2"></i>
                        <strong>Estimasi Biaya:</strong> <span id="estimasi-biaya">Rp 0</span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>

                    <div class="d-flex gap-2 justify-content-end mt-4">
                        <button type="reset" class="btn btn-outline-secondary" id="btn-reset-tindakan">
                            <i class="ri-refresh-line me-1"></i>Reset
                        </button>
                        <button type="submit" class="btn btn-primary px-4" id="btn-tindakan-medis">
                            <span class="btn-txt" id="text-tindakan-medis">
                                <i class="ri-save-line me-1"></i>Simpan Tindakan
                            </span>
                            <span class="spinner-border spinner-border-sm d-none me-1"
                                id="spinner-tindakan-medis"></span>
                            <span class="d-none" id="text-loading-tindakan">Menyimpan...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-xxl-6 col-sm-12">
        <div class="card mb-3 shadow-sm">
            <div class="card-header bg-gradient" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <h5 class="card-title text-white mb-0">
                    <i class="ri-file-list-3-line me-2"></i>Data Tindakan Medis
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle m-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" width="80">Aksi</th>
                                <th>Nama Tindakan</th>
                                <th class="text-center" width="60">Qty</th>
                                <th class="text-end" width="120">Harga</th>
                                <th class="text-end" width="120">Sub Total</th>
                                <th width="150">Dokter</th>
                            </tr>
                        </thead>
                        <tbody id="tbody-tindakan"></tbody>
                        <tfoot class="table-light">
                            <tr class="fw-bold">
                                <td colspan="4" class="text-end">
                                    <i class="ri-calculator-line me-2"></i>Total Biaya
                                </td>
                                <td class="text-end text-primary fs-5">
                                    Rp <span id="total-harga">0</span>
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div id="empty-state-tindakan" class="text-center py-5" style="display: none;">
                    <i class="ri-inbox-line" style="font-size: 4rem; color: #e0e0e0;"></i>
                    <p class="text-muted mt-3 mb-0">Belum ada tindakan medis yang ditambahkan</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        (function() {
            const ENCOUNTER_ID = @json($observasi);
            const tindakanListUrl = "{{ route('observasi.getTindakan', ':id') }}".replace(':id', ENCOUNTER_ID);
            const tindakanEncounterUrl = "{{ route('observasi.getTindakanEncounter', ':id') }}".replace(':id',
                ENCOUNTER_ID);
            const tindakanPostUrl = "{{ route('observasi.postTindakanEncounter', ':id') }}".replace(':id',
                ENCOUNTER_ID);
            const tindakanDeleteUrl = function(id) {
                return "{{ route('observasi.deleteTindakanEncounter', ':id') }}".replace(':id', id);
            };

            function ensureFormat() {
                if (typeof window.formatRupiah !== 'function') {
                    window.formatRupiah = function(angka) {
                        if (angka === null || angka === undefined) return '0';
                        let integer_part = Math.floor(parseFloat(angka)).toString();
                        let sisa = integer_part.length % 3;
                        let rupiah = integer_part.substr(0, sisa);
                        let ribuan = integer_part.substr(sisa).match(/\d{3}/gi);
                        if (ribuan) {
                            let separator = sisa ? '.' : '';
                            rupiah += separator + ribuan.join('.');
                        }
                        return rupiah;
                    }
                }
            }

            let tindakanData = {};

            function loadTindakanSelect() {
                $.ajax({
                        url: tindakanListUrl,
                        type: 'GET',
                        data: {
                            _token: "{{ csrf_token() }}"
                        }
                    })
                    .done(function(data) {
                        const select = $('#jenis_tindakan');
                        select.empty();
                        select.append('<option value="">-- Pilih Jenis Tindakan --</option>');
                        // Ensure data is an array
                        const items = Array.isArray(data) ? data : (data ? [data] : []);
                        items.forEach(function(item) {
                            tindakanData[item.id] = item;
                            select.append(
                                `<option value="${item.id}" data-harga="${item.harga}">${item.name} - Rp ${formatRupiah(item.harga)}</option>`
                                );
                        });
                    });
            }

            // Update estimasi biaya
            function updateEstimasi() {
                const tindakanId = $('#jenis_tindakan').val();
                const qty = parseInt($('#qty').val()) || 0;

                if (tindakanId && tindakanData[tindakanId]) {
                    const harga = parseFloat(tindakanData[tindakanId].harga) || 0;
                    const total = harga * qty;
                    $('#estimasi-biaya').text('Rp ' + formatRupiah(total));
                    $('#info-harga').slideDown();
                } else {
                    $('#info-harga').slideUp();
                }
            }

            function loadTindakanEncounter() {
                ensureFormat();
                $.ajax({
                        url: tindakanEncounterUrl,
                        type: 'GET',
                        data: {
                            _token: "{{ csrf_token() }}"
                        }
                    })
                    .done(function(data) {
                        const tbody = $('#tbody-tindakan');
                        const emptyState = $('#empty-state-tindakan');
                        tbody.empty();
                        let total_harga = 0;
                        // Ensure data is an array
                        const items = Array.isArray(data) ? data : (data ? [data] : []);

                        // Filter out items without required fields
                        const validItems = items.filter(item => item && item.id && item.tindakan_name);

                        if (validItems.length === 0) {
                            emptyState.show();
                            tbody.closest('table').hide();
                            $('#total-harga').text('0');
                            return;
                        }

                        emptyState.hide();
                        tbody.closest('table').show();

                        validItems.forEach(function(item, index) {
                            const itemTotal = parseFloat(item.total_harga) || (parseFloat(item
                                .tindakan_harga) * parseInt(item.qty));
                            total_harga += (itemTotal || 0);
                            tbody.append(`
            <tr class="animate-fade-in" style="animation-delay: ${index * 0.05}s">
              <td class="text-center">
                <button class="btn btn-danger btn-sm btn-hapus-tindakan" data-id="${item.id}" title="Hapus tindakan">
                  <i class="ri-delete-bin-line"></i>
                </button>
              </td>
              <td>
                <div class="fw-semibold">${item.tindakan_name}</div>
                ${item.tindakan_description ? `<small class="text-muted">${item.tindakan_description}</small>` : ''}
              </td>
              <td class="text-center">
                <span class="badge bg-light text-dark">${item.qty || 0}</span>
              </td>
              <td class="text-end">Rp ${formatRupiah(item.tindakan_harga || 0)}</td>
              <td class="text-end fw-semibold text-primary">Rp ${formatRupiah(itemTotal)}</td>
              <td>
                <div class="d-flex align-items-center">
                  <i class="ri-user-line me-2 text-muted"></i>
                  <span class="text-truncate">${item.petugas_name || '-'}</span>
                </div>
              </td>
            </tr>
          `);
                        });
                        $('#total-harga').text(formatRupiah(total_harga));
                    });
            }

            // Init select2 and loaders on tab click
            $(document).on('click', '#tab-tindakan-medis', function() {
                if ($.fn.select2) {
                    $('#jenis_tindakan').select2({
                        placeholder: '-- Pilih Jenis Tindakan --',
                        allowClear: true,
                        width: '100%'
                    });
                }
                loadTindakanSelect();
                loadTindakanEncounter();
            });

            // Qty increment/decrement
            $(document).on('click', '#btn-qty-plus', function() {
                const input = $('#qty');
                const currentVal = parseInt(input.val()) || 1;
                input.val(currentVal + 1);
                updateEstimasi();
            });

            $(document).on('click', '#btn-qty-minus', function() {
                const input = $('#qty');
                const currentVal = parseInt(input.val()) || 1;
                if (currentVal > 1) {
                    input.val(currentVal - 1);
                    updateEstimasi();
                }
            });

            // Update estimasi saat tindakan atau qty berubah
            $(document).on('change', '#jenis_tindakan', updateEstimasi);
            $(document).on('input', '#qty', updateEstimasi);

            // Reset form
            $(document).on('click', '#btn-reset-tindakan', function() {
                $('#form-tindakan-medis')[0].reset();
                $('#jenis_tindakan').val(null).trigger('change');
                $('#info-harga').slideUp();
                $('#form-tindakan-medis').removeClass('was-validated');
            });

            // Save tindakan dengan validasi Bootstrap
            $(document).on('submit', '#form-tindakan-medis', function(e) {
                e.preventDefault();

                const form = this;
                if (!form.checkValidity()) {
                    e.stopPropagation();
                    $(form).addClass('was-validated');
                    return;
                }

                const jenis_tindakan = $('#jenis_tindakan').val();
                const qty = $('#qty').val();

                $.ajax({
                    url: tindakanPostUrl,
                    type: 'POST',
                    data: {
                        jenis_tindakan: jenis_tindakan,
                        qty: qty,
                        _token: "{{ csrf_token() }}"
                    },
                    beforeSend: function() {
                        $('#btn-tindakan-medis').prop('disabled', true);
                        $('#spinner-tindakan-medis').removeClass('d-none');
                        $('#text-tindakan-medis').addClass('d-none');
                        $('#text-loading-tindakan').removeClass('d-none');
                    },
                }).done(function(data) {
                    if (data.status == 200) {
                        swal({
                            title: 'Berhasil!',
                            text: data.message || 'Tindakan medis berhasil disimpan',
                            icon: 'success',
                            timer: 2000,
                            buttons: false
                        });
                        // Reset form
                        $('#form-tindakan-medis')[0].reset();
                        $('#jenis_tindakan').val(null).trigger('change');
                        $('#qty').val(1);
                        $('#info-harga').slideUp();
                        $(form).removeClass('was-validated');
                        // Reload data
                        loadTindakanEncounter();
                    } else {
                        swal({
                            title: 'Gagal!',
                            text: data.message || 'Terjadi kesalahan saat menyimpan data',
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
                    $('#btn-tindakan-medis').prop('disabled', false);
                    $('#spinner-tindakan-medis').addClass('d-none');
                    $('#text-tindakan-medis').removeClass('d-none');
                    $('#text-loading-tindakan').addClass('d-none');
                });
            });

            // Delete tindakan dengan konfirmasi yang lebih informatif
            $(document).on('click', '#tbody-tindakan .btn-hapus-tindakan', function() {
                const id = $(this).data('id');
                const row = $(this).closest('tr');
                const tindakanName = row.find('td:eq(1) .fw-semibold').text();

                swal({
                        title: 'Konfirmasi Hapus',
                        text: `Apakah Anda yakin ingin menghapus tindakan "${tindakanName}"?`,
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
                                url: tindakanDeleteUrl(id),
                                type: 'DELETE',
                                data: {
                                    _token: "{{ csrf_token() }}"
                                }
                            })
                            .done(function(data) {
                                swal({
                                    title: 'Berhasil!',
                                    text: data.message || 'Tindakan berhasil dihapus',
                                    icon: 'success',
                                    timer: 2000,
                                    buttons: false
                                });
                                loadTindakanEncounter();
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

    #jenis_tindakan.select2-container {
        width: 100% !important;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(102, 126, 234, 0.05);
        transition: all 0.2s ease;
    }

    .btn {
        transition: all 0.2s ease;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .alert-info {
        border-left: 4px solid #17a2b8;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }

    .badge {
        padding: 0.35em 0.65em;
    }
</style>
