<div class="row gx-3">
    <div class="col-xxl-4 col-lg-5 col-sm-12">
        <div class="card mb-3 border-0 shadow-sm">
            <div class="card-header bg-white border-bottom-0 pb-0">
                <h6 class="card-title mb-0 fw-bold">
                    <i class="ri-stethoscope-line me-1 text-primary"></i>Form Tindakan
                </h6>
            </div>
            <div class="card-body pt-3">
                <form id="form-tindakan-medis">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted" for="jenis_tindakan">Jenis Tindakan</label>
                        <select name="jenis_tindakan" id="jenis_tindakan" class="form-select" required>
                            <option value="">-- Pilih Jenis Tindakan --</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted" for="qty">Jumlah</label>
                        <div class="input-group input-group-sm">
                            <button class="btn btn-outline-secondary" type="button" id="btn-qty-minus">
                                <i class="ri-subtract-line"></i>
                            </button>
                            <input type="number" class="form-control text-center fw-bold" id="qty" name="qty"
                                value="1" min="1" max="100" required>
                            <button class="btn btn-outline-secondary" type="button" id="btn-qty-plus">
                                <i class="ri-add-line"></i>
                            </button>
                        </div>
                    </div>

                    <div class="alert alert-info py-2 px-3 small" role="alert" id="info-harga" style="display: none;">
                        <i class="ri-price-tag-3-line me-1"></i>
                        <strong>Estimasi:</strong> <span id="estimasi-biaya">Rp 0</span>
                    </div>

                    <button type="submit" class="btn btn-primary w-100" id="btn-tindakan-medis">
                        <i class="ri-add-line me-1"></i>
                        <span class="btn-txt" id="text-tindakan-medis">Tambah Tindakan</span>
                        <span class="spinner-border spinner-border-sm d-none" id="spinner-tindakan-medis"></span>
                    </button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-xxl-8 col-lg-7 col-sm-12">
        <div class="card mb-3 border-0 shadow-sm">
            <div class="card-header bg-white d-flex align-items-center justify-content-between py-3">
                <h6 class="card-title mb-0 fw-bold">
                    <i class="ri-file-list-3-line me-1 text-primary"></i>Data Tindakan Medis
                </h6>
                <span class="badge bg-primary-subtle text-primary rounded-pill" id="tindakan-count"></span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="tbl-tindakan">
                        <thead>
                            <tr class="bg-light">
                                <th class="ps-3" style="min-width: 200px;">Tindakan</th>
                                <th class="text-center" style="width: 60px;">Qty</th>
                                <th class="text-end" style="width: 110px;">Harga</th>
                                <th class="text-end" style="width: 120px;">Sub Total</th>
                                <th style="width: 130px;">Dokter</th>
                                <th class="text-center pe-3" style="width: 60px;"></th>
                            </tr>
                        </thead>
                        <tbody id="tbody-tindakan"></tbody>
                        <tfoot id="tfoot-tindakan">
                            <tr>
                                <td colspan="3" class="text-end fw-semibold text-muted ps-3" style="font-size: .85rem;">
                                    Total Biaya
                                </td>
                                <td class="text-end fw-bold text-primary" style="font-size: .95rem;">
                                    Rp <span id="total-harga">0</span>
                                </td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div id="empty-state-tindakan" class="text-center py-4" style="display: none;">
                    <div class="text-muted">
                        <i class="ri-stethoscope-line fs-2 d-block mb-2 opacity-50"></i>
                        <span class="small">Belum ada tindakan medis</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('style')
<style>
    #tbl-tindakan thead th {
        font-size: .75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .5px;
        color: #fff;
        border-bottom: none;
        padding: .6rem .5rem;
    }
    #tbl-tindakan tbody tr {
        transition: background-color .15s ease;
    }
    #tbl-tindakan tbody tr:hover {
        background-color: #f8f9ff;
    }
    #tbl-tindakan tbody td {
        padding: .6rem .5rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f3f5;
    }
    #tfoot-tindakan td {
        padding: .6rem .5rem;
        border-top: 2px solid #e9ecef;
    }
</style>
@endpush

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
                        const countBadge = $('#tindakan-count');
                        const tfoot = $('#tfoot-tindakan');
                        tbody.empty();
                        let total_harga = 0;
                        const items = Array.isArray(data) ? data : (data ? [data] : []);
                        const validItems = items.filter(item => item && item.id && item.tindakan_name);

                        if (validItems.length === 0) {
                            emptyState.show();
                            tbody.closest('table').hide();
                            tfoot.hide();
                            countBadge.hide();
                            $('#total-harga').text('0');
                            return;
                        }

                        emptyState.hide();
                        tbody.closest('table').show();
                        tfoot.show();
                        countBadge.text(validItems.length + ' item').show();

                        validItems.forEach(function(item) {
                            const itemTotal = parseFloat(item.total_harga) || (parseFloat(item
                                .tindakan_harga) * parseInt(item.qty));
                            total_harga += (itemTotal || 0);
                            tbody.append(`
                                <tr>
                                    <td class="ps-3">
                                        <div class="fw-semibold text-dark" style="font-size: .85rem; line-height: 1.3;">${item.tindakan_name} ${item.paket_pasien_id ? '<span class="badge bg-success-subtle text-success" style="font-size: .6rem; vertical-align: middle;">Paket</span>' : ''}</div>
                                        ${item.tindakan_description ? `<div class="text-muted" style="font-size: .75rem;">${item.tindakan_description}</div>` : ''}
                                    </td>
                                    <td class="text-center fw-medium">${item.qty || 0}</td>
                                    <td class="text-end text-muted" style="font-size: .85rem;">${formatRupiah(item.tindakan_harga || 0)}</td>
                                    <td class="text-end fw-semibold" style="font-size: .85rem;">${formatRupiah(itemTotal)}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-1" style="font-size: .8rem;">
                                            <i class="ri-user-line text-muted"></i>
                                            <span class="text-truncate">${item.petugas_name || '-'}</span>
                                        </div>
                                    </td>
                                    <td class="text-center pe-3">
                                        <button class="btn btn-sm btn-soft-danger btn-hapus-tindakan" data-id="${item.id}" title="Hapus">
                                            <i class="ri-delete-bin-6-line"></i>
                                        </button>
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
