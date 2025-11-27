<div class="row gx-3">
    <div class="col-xxl-6 col-sm-6">
        <div class="card mb-1 shadow-sm">
            <div class="card-header bg-gradient" style="background: linear-gradient(135deg, #0ba360 0%, #3cba92 100%);">
                <h5 class="card-title text-white mb-0">
                    <i class="ri-capsule-line me-2"></i>Form Resep Obat
                </h5>
            </div>
            <div class="card-body p-4">
                <form method="GET" id="form-buat-resep">
                    <div class="alert alert-info border-start border-4 border-info mb-4" role="alert">
                        <div class="d-flex align-items-start">
                            <i class="ri-information-line fs-4 me-2"></i>
                            <div>
                                <strong class="d-block">Petunjuk Penggunaan:</strong>
                                <small>Tentukan masa berlaku resep terlebih dahulu sebelum menambahkan obat</small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold" for="masa_pemakaian_hari">
                            <i class="ri-calendar-line text-success me-1"></i>Masa Resep (Hari)
                            <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="ri-timer-line"></i></span>
                            <input type="number" name="masa_pemakaian_hari" class="form-control"
                                placeholder="Contoh: 7, 14, 30" id="masa_pemakaian_hari" min="1" max="365"
                                required>
                            @if (auth()->user()->role != 3)
                                <button class="btn btn-success px-4" type="submit" id="btn-buat-resep">
                                    <span id="text-buat-resep"><i class="ri-add-circle-line me-1"></i>Buat Resep</span>
                                    <span class="spinner-border spinner-border-sm d-none" id="spinner-buat-resep"
                                        role="status" aria-hidden="true"></span>
                                </button>
                            @endif
                        </div>
                        <small class="form-text text-muted">
                            <i class="ri-information-line"></i> Durasi berlaku resep dalam satuan hari
                        </small>
                    </div>
                </form>
                <hr class="my-4">
                <div class="d-none" id="resep">
                    <form id="form-tambah-obat">
                        <div class="mb-4">
                            <label class="form-label fw-semibold" for="product_apotek_id">
                                <i class="ri-medicine-bottle-line text-success me-1"></i>Pilih Obat
                                <span class="text-danger">*</span>
                            </label>
                            <select name="product_apotek_id" id="product_apotek_id" class="form-select" required>
                                <option value="">-- Cari nama obat --</option>
                            </select>
                            <small class="form-text text-muted">
                                <i class="ri-information-line"></i> Ketik untuk mencari obat dari database apotek
                            </small>
                        </div>

                        <div class="row gx-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="qty_obat">
                                    <i class="ri-add-box-line text-success me-1"></i>Jumlah <span id="label-satuan-obat"
                                        class="text-primary">Obat</span>
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <button class="btn btn-outline-secondary" type="button" id="btn-qty-obat-minus">
                                        <i class="ri-subtract-line"></i>
                                    </button>
                                    <input type="number" class="form-control text-center fw-bold" id="qty_obat"
                                        name="qty_obat" value="1" min="1" required>
                                    <button class="btn btn-outline-secondary" type="button" id="btn-qty-obat-plus">
                                        <i class="ri-add-line"></i>
                                    </button>
                                </div>
                                <small class="form-text text-muted" id="hint-satuan-obat">Pilih obat terlebih dahulu
                                    untuk melihat satuan</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="aturan_pakai_jumlah">
                                    <i class="ri-time-line text-info me-1"></i>Frekuensi Pakai
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="aturan_pakai_jumlah" value="1"
                                        min="1" required>
                                    <select class="form-select" id="aturan_pakai_frekuensi">
                                        <option value="x Sehari">x Sehari</option>
                                        <option value="x Seminggu">x Seminggu</option>
                                        <option value="x Sebulan">x Sebulan</option>
                                        <option value="x Setahun">x Setahun</option>
                                        <option value="Jika Perlu">Jika Perlu</option>
                                    </select>
                                </div>
                                <small class="form-text text-muted">Berapa kali penggunaan</small>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                <i class="ri-restaurant-line text-warning me-1"></i>Keterangan Tambahan
                            </label>
                            <select class="form-select" id="aturan_pakai_tambahan">
                                <option value="">- Tidak ada -</option>
                                <option value="Sebelum Makan">🍽️ Sebelum Makan</option>
                                <option value="Sesudah Makan">🍴 Sesudah Makan</option>
                            </select>
                            <small class="form-text text-muted">Waktu konsumsi terhadap makanan</small>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                <i class="ri-sun-line text-warning me-1"></i>Waktu Pemberian
                            </label>
                            <div class="d-flex flex-wrap gap-3">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" value="Pagi" id="waktu_pagi">
                                    <label class="form-check-label" for="waktu_pagi">
                                        <i class="ri-sun-line text-warning"></i> Pagi
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" value="Siang" id="waktu_siang">
                                    <label class="form-check-label" for="waktu_siang">
                                        <i class="ri-sun-cloudy-line text-info"></i> Siang
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" value="Malam" id="waktu_malam">
                                    <label class="form-check-label" for="waktu_malam">
                                        <i class="ri-moon-line text-primary"></i> Malam
                                    </label>
                                </div>
                            </div>
                            <small class="form-text text-muted">Pilih waktu pemberian obat</small>
                        </div>

                        <div class="alert alert-warning border-start border-4 border-warning d-none" role="alert"
                            id="preview-aturan-pakai">
                            <div class="d-flex align-items-start">
                                <i class="ri-file-list-line fs-4 me-2"></i>
                                <div>
                                    <strong class="d-block">Preview Aturan Pakai:</strong>
                                    <span id="preview-aturan-text" class="text-muted small"></span>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2 justify-content-end mt-4">
                            <button type="reset" class="btn btn-outline-secondary" id="btn-reset-obat">
                                <i class="ri-refresh-line me-1"></i>Reset
                            </button>
                            <button type="submit" class="btn btn-success px-4"
                                @if (auth()->user()->role == 3) disabled @endif id="btn-tambah-obat">
                                <span class="btn-txt" id="text-tambah-obat">
                                    <i class="ri-add-line me-1"></i>Tambah ke Resep
                                </span>
                                <span class="spinner-border spinner-border-sm d-none me-1"
                                    id="spinner-tambah-obat"></span>
                                <span class="d-none" id="text-loading-obat">Menambahkan...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xxl-6 col-sm-12">
        <div class="card mb-3 shadow-sm">
            <div class="card-header bg-gradient"
                style="background: linear-gradient(135deg, #0ba360 0%, #3cba92 100%);">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title text-white mb-0">
                        <i class="ri-file-list-3-line me-2"></i>Daftar Obat Resep
                    </h5>
                    <span class="badge bg-white text-dark" id="kode_resep"></span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle m-0">
                        <thead style="background: linear-gradient(135deg, #0ba360 0%, #3cba92 100%);">
                            <tr>
                                <th class="text-center"
                                    style="padding: 14px 8px; font-weight: 600; color: white; width: 70px; vertical-align: middle;">
                                    Aksi</th>
                                <th class="text-center"
                                    style="padding: 14px 12px; font-weight: 600; color: white; vertical-align: middle;">
                                    Nama Obat</th>
                                <th class="text-center"
                                    style="padding: 14px 12px; font-weight: 600; color: white; width: 100px; vertical-align: middle;">
                                    Jumlah</th>
                                <th class="text-center"
                                    style="padding: 14px 12px; font-weight: 600; color: white; width: 180px; vertical-align: middle;">
                                    Aturan
                                    Pakai</th>
                                <th class="text-center"
                                    style="padding: 14px 12px; font-weight: 600; color: white; width: 110px; vertical-align: middle;">
                                    Harga</th>
                                <th class="text-center"
                                    style="padding: 14px 12px; font-weight: 600; color: white; width: 120px; vertical-align: middle;">
                                    Subtotal
                                </th>
                            </tr>
                        </thead>
                        <tbody id="tbody-resep"></tbody>
                        <tfoot
                            style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-top: 2px solid #0ba360;">
                            <tr class="fw-bold">
                                <td colspan="5" class="text-end"
                                    style="padding: 16px 12px; font-size: 15px; color: #212529;">
                                    <i class="ri-money-dollar-circle-line me-2 text-success"></i>Total Biaya Resep
                                </td>
                                <td class="text-end text-success" id="total-resep"
                                    style="padding: 16px 12px; font-size: 18px; font-weight: 700;">Rp 0</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div id="empty-state-resep" class="text-center py-5" style="display: none;">
                    <i class="ri-medicine-bottle-line" style="font-size: 4rem; color: #e0e0e0;"></i>
                    <p class="text-muted mt-3 mb-0">Belum ada obat yang ditambahkan</p>
                    <p class="text-muted small">Buat resep dan tambahkan obat untuk pasien</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        (function() {
            const ENCOUNTER_ID = @json($observasi);
            const resepGetUrl = "{{ route('observasi.getResep', ':id') }}".replace(':id', ENCOUNTER_ID);
            const resepPostUrl = "{{ route('observasi.postResep', ':id') }}".replace(':id', ENCOUNTER_ID);
            const resepDetailPostUrl = "{{ route('observasi.postResepDetail', ':id') }}".replace(':id', ENCOUNTER_ID);
            const resepDetailDeleteUrl = function(id) {
                return "{{ route('observasi.deleteResepDetail', ':id') }}".replace(':id', id);
            };
            const produkAjaxUrl = "{{ route('observasi.getProdukApotek', $observasi) }}";

            function ensureFormat() {
                if (typeof window.formatRupiah !== 'function') {
                    window.formatRupiah = function(angka, prefix) {
                        if (angka === null || angka === undefined) return (prefix || '') + '0';
                        let integer_part = Math.floor(parseFloat(angka)).toString();
                        let sisa = integer_part.length % 3;
                        let rupiah = integer_part.substr(0, sisa);
                        let ribuan = integer_part.substr(sisa).match(/\d{3}/gi);
                        if (ribuan) {
                            let separator = sisa ? '.' : '';
                            rupiah += separator + ribuan.join('.');
                        }
                        return (prefix ? (rupiah ? prefix + ' ' + rupiah : prefix + ' 0') : rupiah);
                    }
                }
            }

            function initProdukSelect() {
                if (!$.fn.select2) return;
                $('#product_apotek_id').select2({
                    placeholder: '🔍 Ketik untuk mencari obat...',
                    allowClear: true,
                    width: '100%',
                    minimumInputLength: 2,
                    ajax: {
                        url: produkAjaxUrl,
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                search: params.term
                            };
                        },
                        processResults: function(data) {
                            const rows = Array.isArray(data) ? data : (Array.isArray(data.data) ? data
                                .data : []);
                            return {
                                results: rows.map(function(item) {
                                    return {
                                        id: item.id,
                                        text: item.name + (item.satuan ? ' [' + item.satuan + ']' :
                                            '') + (item.harga ? ' - Rp ' + formatRupiah(item
                                            .harga) : ''),
                                        harga: item.harga,
                                        satuan: item.satuan
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
                            return "Mencari obat...";
                        },
                        noResults: function() {
                            return "Obat tidak ditemukan";
                        }
                    }
                });
            }

            // Update preview aturan pakai
            function updatePreviewAturanPakai() {
                const jumlah = $('#aturan_pakai_jumlah').val();
                const frekuensi = $('#aturan_pakai_frekuensi').val();
                const tambahan = $('#aturan_pakai_tambahan').val();
                let waktu = [];
                if ($('#waktu_pagi').is(':checked')) waktu.push('Pagi');
                if ($('#waktu_siang').is(':checked')) waktu.push('Siang');
                if ($('#waktu_malam').is(':checked')) waktu.push('Malam');

                if (jumlah && frekuensi) {
                    let preview = (frekuensi === 'Jika Perlu') ? 'Jika Perlu' : `${jumlah} ${frekuensi}`;
                    if (tambahan) preview += ` ${tambahan}`;
                    if (waktu.length > 0) preview += ` (${waktu.join(', ')})`;
                    $('#preview-aturan-text').text(preview);
                    $('#preview-aturan-pakai').removeClass('d-none').hide().slideDown();
                } else {
                    $('#preview-aturan-pakai').slideUp();
                }
            }

            function loadResep() {
                ensureFormat();
                $.ajax({
                        url: resepGetUrl,
                        type: 'GET',
                        data: {
                            _token: "{{ csrf_token() }}"
                        }
                    })
                    .done(function(data) {
                        const emptyState = $('#empty-state-resep');
                        const tbody = $('#tbody-resep');

                        if (data && data.id) {
                            $('#resep').removeClass('d-none').hide().slideDown();
                            $('#kode_resep').html(
                                `<i class="ri-file-text-line me-1"></i>${data.kode_resep} <span class="badge bg-info ms-2">${data.masa_pemakaian_hari || 0} hari</span>`
                            );
                            // Set masa_pemakaian_hari di input form jika belum ada
                            if ($('#masa_pemakaian_hari').val() === '' || $('#masa_pemakaian_hari').val() === '0') {
                                $('#masa_pemakaian_hari').val(data.masa_pemakaian_hari || 0);
                            }
                        } else {
                            $('#resep').slideUp();
                            $('#kode_resep').html('<span class="badge bg-secondary">Belum ada resep</span>');
                        }

                        tbody.empty();
                        let total = 0;

                        if (data && data.details && data.details.length > 0) {
                            emptyState.hide();
                            tbody.closest('table').show();

                            data.details.forEach(function(item, index) {
                                const satuanText = item.satuan ?
                                    ` <small class="text-muted">${item.satuan}</small>` : '';
                                tbody.append(`
              <tr class="animate-fade-in" style="animation-delay: ${index * 0.05}s; border-bottom: 1px solid #e9ecef;">
                <td class="text-center" style="padding: 14px 8px; vertical-align: middle;">
                  <button class="btn btn-danger btn-sm btn-hapus-resep" data-id="${item.id}" title="Hapus obat" style="padding: 6px 10px; border-radius: 6px;">
                    <i class="ri-delete-bin-line"></i>
                  </button>
                </td>
                <td style="padding: 14px 12px; vertical-align: middle;">
                  <div class="d-flex align-items-center">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 36px; height: 36px; background: linear-gradient(135deg, #0ba360 0%, #3cba92 100%);">
                      <i class="ri-medicine-bottle-line text-white"></i>
                    </div>
                    <div>
                      <div class="fw-semibold" style="color: #212529; font-size: 14px;">${item.nama_obat}</div>
                    </div>
                  </div>
                </td>
                <td class="text-center" style="padding: 14px 12px; vertical-align: middle;">
                  <div class="d-inline-flex flex-column align-items-center">
                    <span class="badge" style="background: linear-gradient(135deg, #0ba360 0%, #3cba92 100%); color: white; font-size: 15px; padding: 6px 14px; border-radius: 8px; font-weight: 600;">${item.qty}</span>
                    ${item.satuan ? `<small class="text-muted mt-1" style="font-size: 11px;">${item.satuan}</small>` : ''}
                  </div>
                </td>
                <td style="padding: 14px 12px; vertical-align: middle;">
                  <div class="d-flex align-items-center">
                    <i class="ri-time-line text-info me-2" style="font-size: 16px;"></i>
                    <span style="color: #6c757d; font-size: 13px;">${item.aturan_pakai || '-'}</span>
                  </div>
                </td>
                <td class="text-end" style="padding: 14px 12px; vertical-align: middle;">
                  <span style="color: #495057; font-size: 14px;">Rp ${formatRupiah(item.harga)}</span>
                </td>
                <td class="text-end" style="padding: 14px 12px; vertical-align: middle;">
                  <span class="fw-bold text-success" style="font-size: 15px;">Rp ${formatRupiah(item.total_harga)}</span>
                </td>
              </tr>
            `);
                                total += parseInt(item.total_harga || 0);
                            });
                            $('#total-resep').text('Rp ' + formatRupiah(total));
                        } else {
                            emptyState.show();
                            tbody.closest('table').hide();
                            $('#total-resep').text('Rp 0');
                        }
                    });
            }

            // Init when Tatalaksana tab is shown
            $(document).on('shown.bs.tab', 'a[data-bs-toggle="tab"]', function(e) {
                const target = $(e.target).attr('href');
                if (target === '#tatalaksana') {
                    initProdukSelect();
                    loadResep();
                }
            });
            // Also init immediately if the tab is already active on load
            $(function() {
                if ($('#tatalaksana').hasClass('show')) {
                    initProdukSelect();
                    loadResep();
                }
            });

            // Qty obat increment/decrement
            $(document).on('click', '#btn-qty-obat-plus', function() {
                const input = $('#qty_obat');
                const currentVal = parseInt(input.val()) || 1;
                input.val(currentVal + 1);
            });

            $(document).on('click', '#btn-qty-obat-minus', function() {
                const input = $('#qty_obat');
                const currentVal = parseInt(input.val()) || 1;
                if (currentVal > 1) input.val(currentVal - 1);
            });

            // Update label satuan saat obat dipilih
            $(document).on('select2:select', '#product_apotek_id', function(e) {
                const data = e.params.data;
                if (data.satuan) {
                    $('#label-satuan-obat').text(data.satuan);
                    $('#hint-satuan-obat').html(
                        `<i class="ri-information-line"></i> Jumlah dalam satuan <strong>${data.satuan}</strong>`
                    );
                } else {
                    $('#label-satuan-obat').text('Obat');
                    $('#hint-satuan-obat').text('Jumlah satuan obat');
                }
            });

            // Reset label satuan saat obat dihapus
            $(document).on('select2:clear', '#product_apotek_id', function() {
                $('#label-satuan-obat').text('Obat');
                $('#hint-satuan-obat').text('Pilih obat terlebih dahulu untuk melihat satuan');
            });

            // Update preview saat ada perubahan
            $(document).on('change input', '#aturan_pakai_jumlah, #aturan_pakai_frekuensi, #aturan_pakai_tambahan',
                updatePreviewAturanPakai);
            $(document).on('change', '#waktu_pagi, #waktu_siang, #waktu_malam', updatePreviewAturanPakai);

            // Reset form obat
            $(document).on('click', '#btn-reset-obat', function() {
                $('#form-tambah-obat')[0].reset();
                $('#product_apotek_id').val(null).trigger('change');
                $('#qty_obat').val(1);
                $('#aturan_pakai_jumlah').val(1);
                $('#preview-aturan-pakai').slideUp();
                $('#form-tambah-obat').removeClass('was-validated');
            });

            // Create resep dengan validasi
            $(document).on('submit', '#form-buat-resep', function(e) {
                e.preventDefault();

                const form = this;
                if (!form.checkValidity()) {
                    e.stopPropagation();
                    $(form).addClass('was-validated');
                    return;
                }

                const hari = $('#masa_pemakaian_hari').val();

                $('#spinner-buat-resep').removeClass('d-none');
                $('#text-buat-resep').addClass('d-none');
                $('#btn-buat-resep').prop('disabled', true);

                $.ajax({
                        url: resepPostUrl,
                        type: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            masa_pemakaian_hari: hari
                        }
                    })
                    .done(function(data) {
                        swal({
                            title: 'Berhasil!',
                            text: data.message || 'Resep berhasil dibuat',
                            icon: 'success',
                            timer: 2000,
                            buttons: false
                        });
                        $('#masa_pemakaian_hari').val('');
                        $(form).removeClass('was-validated');
                        loadResep();
                    })
                    .fail(function(xhr) {
                        const msg = (xhr.responseJSON && (xhr.responseJSON.message || xhr.responseJSON
                            .error)) || 'Gagal membuat resep.';
                        swal({
                            title: 'Gagal!',
                            text: msg,
                            icon: 'error'
                        });
                    })
                    .always(function() {
                        $('#spinner-buat-resep').addClass('d-none');
                        $('#text-buat-resep').removeClass('d-none');
                        $('#btn-buat-resep').prop('disabled', false);
                    });
            });

            // Add resep detail dengan validasi Bootstrap
            $(document).on('submit', '#form-tambah-obat', function(e) {
                e.preventDefault();

                const form = this;
                if (!form.checkValidity()) {
                    e.stopPropagation();
                    $(form).addClass('was-validated');
                    return;
                }

                const product_apotek_id = $('#product_apotek_id').val();
                const qty_obat = $('#qty_obat').val();
                const aturan_jumlah = $('#aturan_pakai_jumlah').val();
                const aturan_frekuensi = $('#aturan_pakai_frekuensi').val();
                const aturan_tambahan = $('#aturan_pakai_tambahan').val();

                let waktu_pemberian = [];
                if ($('#waktu_pagi').is(':checked')) waktu_pemberian.push('Pagi');
                if ($('#waktu_siang').is(':checked')) waktu_pemberian.push('Siang');
                if ($('#waktu_malam').is(':checked')) waktu_pemberian.push('Malam');

                let aturan_pakai = (aturan_frekuensi === 'Jika Perlu') ? 'Jika Perlu' :
                    `${aturan_jumlah} ${aturan_frekuensi}`;
                if (aturan_tambahan) aturan_pakai += ` ${aturan_tambahan}`;
                if (waktu_pemberian.length > 0) aturan_pakai += ` (${waktu_pemberian.join(', ')})`;

                $('#spinner-tambah-obat').removeClass('d-none');
                $('#text-tambah-obat').addClass('d-none');
                $('#text-loading-obat').removeClass('d-none');
                $('#btn-tambah-obat').prop('disabled', true);

                $.ajax({
                        url: resepDetailPostUrl,
                        type: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            product_apotek_id,
                            qty_obat,
                            aturan_pakai
                        }
                    })
                    .done(function(data) {
                        swal({
                            title: 'Berhasil!',
                            text: data.message || 'Obat berhasil ditambahkan ke resep',
                            icon: 'success',
                            timer: 2000,
                            buttons: false
                        });
                        // Reset form
                        $('#form-tambah-obat')[0].reset();
                        $('#product_apotek_id').val(null).trigger('change');
                        $('#qty_obat').val(1);
                        $('#aturan_pakai_jumlah').val(1);
                        $('#preview-aturan-pakai').slideUp();
                        $(form).removeClass('was-validated');
                        loadResep();
                    })
                    .fail(function(xhr) {
                        const msg = (xhr.responseJSON && (xhr.responseJSON.message || xhr.responseJSON
                            .error)) || 'Gagal menambah obat.';
                        swal({
                            title: 'Gagal!',
                            text: msg,
                            icon: 'error'
                        });
                    })
                    .always(function() {
                        $('#spinner-tambah-obat').addClass('d-none');
                        $('#text-tambah-obat').removeClass('d-none');
                        $('#text-loading-obat').addClass('d-none');
                        $('#btn-tambah-obat').prop('disabled', false);
                    });
            });

            // Delete resep detail dengan konfirmasi informatif
            $(document).on('click', '#tbody-resep .btn-hapus-resep', function() {
                const id = $(this).data('id');
                const row = $(this).closest('tr');
                const obatName = row.find('td:eq(1) .fw-semibold').text().trim();

                swal({
                        title: 'Konfirmasi Hapus',
                        text: `Apakah Anda yakin ingin menghapus "${obatName}" dari resep?`,
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
                                url: resepDetailDeleteUrl(id),
                                type: 'DELETE',
                                data: {
                                    _token: "{{ csrf_token() }}"
                                }
                            })
                            .done(function(data) {
                                swal({
                                    title: 'Berhasil!',
                                    text: data.message || 'Obat berhasil dihapus dari resep',
                                    icon: 'success',
                                    timer: 2000,
                                    buttons: false
                                });
                                loadResep();
                            })
                            .fail(function() {
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

    #product_apotek_id.select2-container {
        width: 100% !important;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(11, 163, 96, 0.05);
        transition: all 0.2s ease;
    }

    .btn {
        transition: all 0.2s ease;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .alert-info,
    .alert-warning {
        border-radius: 0.5rem;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #0ba360;
        box-shadow: 0 0 0 0.2rem rgba(11, 163, 96, 0.25);
    }

    .badge {
        padding: 0.35em 0.65em;
        font-weight: 500;
    }

    .form-check-input:checked {
        background-color: #0ba360;
        border-color: #0ba360;
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
        background-color: #0ba360 !important;
    }

    .input-group-text {
        background-color: #f8f9fa;
    }
</style>
