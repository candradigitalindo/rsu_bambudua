<div class="row gx-3">
    <div class="col-xxl-6 col-sm-12">
        <div class="card mb-3 border-0 shadow-sm">
            <div class="card-header bg-white d-flex align-items-center justify-content-between py-3">
                <h6 class="card-title mb-0 fw-bold">
                    <i class="ri-surgical-mask-line me-1 text-primary"></i>Ringkasan Tindakan Medis
                </h6>
            </div>
            <div class="card-body pt-2">
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted" for="diskon_tindakan">Diskon Tindakan</label>
                    <div class="input-group input-group-sm">
                        <select class="form-select" id="diskon_tindakan_type" style="max-width: 120px;">
                            <option value="percent" selected>Persen (%)</option>
                            <option value="nominal">Nominal (Rp)</option>
                        </select>
                        <span class="input-group-text" id="diskon_tindakan_prefix">%</span>
                        <input type="number" name="diskon_tindakan" class="form-control" placeholder="0"
                            id="diskon_tindakan" min="0">
                        <button class="btn btn-primary" type="submit" id="btn-buat-diskon-tindakan">
                            <span id="text-buat-diskon-tindakan"><i class="ri-check-line"></i></span>
                            <span class="spinner-border spinner-border-sm d-none" id="spinner-buat-diskon-tindakan"
                                role="status" aria-hidden="true"></span>
                        </button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 tbl-catatan">
                        <thead>
                            <tr class="bg-light">
                                <th class="ps-3" style="min-width: 180px;">Nama Tindakan</th>
                                <th class="text-center" style="width: 60px;">Qty</th>
                                <th class="text-end" style="width: 100px;">Harga</th>
                                <th class="text-end pe-3" style="width: 110px;">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="tbody-catatan-tindakan"></tbody>
                        <tfoot class="tfoot-catatan">
                            <tr>
                                <td colspan="3" class="text-end fw-semibold text-muted" style="font-size: .8rem;">
                                    Nominal Tindakan
                                </td>
                                <td class="text-end pe-3" style="font-size: .85rem;">
                                    <span id="total-tindakan" class="fw-semibold">0</span>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-end fw-semibold text-muted" style="font-size: .8rem;">
                                    <i class="ri-flask-line me-1 text-info"></i>Penunjang
                                </td>
                                <td class="text-end pe-3" style="font-size: .85rem;">
                                    <span id="total-penunjang" class="fw-semibold text-info">0</span>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-end fw-semibold text-danger" style="font-size: .8rem;">
                                    <i class="ri-discount-percent-line me-1"></i>Diskon
                                </td>
                                <td class="text-end pe-3" style="font-size: .85rem;">
                                    <span id="total-tindakan-diskon" class="fw-semibold text-danger">0</span>
                                </td>
                            </tr>
                            <tr class="bg-primary">
                                <td colspan="3" class="text-end fw-bold text-white ps-3" style="font-size: .9rem;">
                                    TOTAL
                                </td>
                                <td class="text-end pe-3">
                                    <span id="total-tindakan-akhir" class="fw-bold text-white" style="font-size: 1rem;">0</span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xxl-6 col-sm-12">
        <div class="card mb-3 border-0 shadow-sm">
            <div class="card-header bg-white d-flex align-items-center justify-content-between py-3">
                <h6 class="card-title mb-0 fw-bold">
                    <i class="ri-medicine-bottle-line me-1 text-success"></i>Ringkasan Resep Obat
                </h6>
            </div>
            <div class="card-body pt-2">
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted" for="diskon_resep">Diskon Resep</label>
                    <div class="input-group input-group-sm">
                        <select class="form-select" id="diskon_resep_type" style="max-width: 120px;">
                            <option value="percent" selected>Persen (%)</option>
                            <option value="nominal">Nominal (Rp)</option>
                        </select>
                        <span class="input-group-text" id="diskon_resep_prefix">%</span>
                        <input type="number" name="diskon_resep" class="form-control" placeholder="0"
                            id="diskon_resep" min="0">
                        <button class="btn btn-success" type="submit" id="btn-buat-diskon-resep">
                            <span id="text-buat-diskon-resep"><i class="ri-check-line"></i></span>
                            <span class="spinner-border spinner-border-sm d-none" id="spinner-buat-diskon-resep"
                                role="status" aria-hidden="true"></span>
                        </button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 tbl-catatan">
                        <thead>
                            <tr class="bg-light">
                                <th class="ps-3" style="min-width: 160px;">Nama Obat</th>
                                <th class="text-center" style="width: 60px;">Jml</th>
                                <th style="width: 140px;">Aturan Pakai</th>
                                <th class="text-end" style="width: 90px;">Harga</th>
                                <th class="text-end pe-3" style="width: 100px;">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="tbody-catatan-resep"></tbody>
                        <tfoot class="tfoot-catatan">
                            <tr>
                                <td colspan="4" class="text-end fw-semibold text-muted" style="font-size: .8rem;">
                                    Nominal
                                </td>
                                <td class="text-end pe-3" style="font-size: .85rem;">
                                    <span id="total-resep-catatan" class="fw-semibold">Rp. 0</span>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4" class="text-end fw-semibold text-danger" style="font-size: .8rem;">
                                    <i class="ri-discount-percent-line me-1"></i>Diskon
                                </td>
                                <td class="text-end pe-3" style="font-size: .85rem;">
                                    <span id="total-resep-diskon" class="fw-semibold text-danger">Rp. 0</span>
                                </td>
                            </tr>
                            <tr class="bg-success">
                                <td colspan="4" class="text-end fw-bold text-white ps-3" style="font-size: .9rem;">
                                    TOTAL RESEP
                                </td>
                                <td class="text-end pe-3">
                                    <span id="total-resep-harga" class="fw-bold text-white" style="font-size: 1rem;">Rp. 0</span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xxl-12 col-sm-12">
        <div class="card mb-3 border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="card-title mb-0 fw-bold">
                    <i class="ri-file-text-line me-1 text-danger"></i>Catatan & Penyelesaian Pemeriksaan
                </h6>
            </div>
            <div class="card-body p-4">
                <div class="alert alert-warning border-start border-4 border-warning mb-4" role="alert">
                    <div class="d-flex align-items-start">
                        <i class="ri-information-line fs-4 me-2"></i>
                        <div>
                            <strong class="d-block">Instruksi Penyelesaian</strong>
                            <small>Lengkapi catatan dokter, pilih perawat yang menangani, dan tentukan status pulang
                                pasien sebelum menyelesaikan pemeriksaan</small>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold" for="catatanEditor">
                        <i class="ri-edit-2-line text-primary me-1"></i>Catatan Dokter
                    </label>
                    <div class="border rounded" style="min-height: 150px;">
                        <div id="catatanEditor" class="quill-editor"></div>
                    </div>
                    <small class="form-text text-muted">
                        <i class="ri-information-line"></i> Tulis catatan penting mengenai kondisi dan penanganan
                        pasien
                    </small>
                </div>

                <div class="row gx-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="perawat_ids">
                            <i class="ri-nurse-line text-info me-1"></i>Perawat yang Menangani
                            <span class="text-danger">*</span>
                        </label>
                        <select class="form-select select2" id="perawat_ids" name="perawat_ids[]" multiple
                            style="width: 100%;">
                            @foreach ($perawats['perawats'] as $perawat)
                                <option value="{{ $perawat->id }}"
                                    {{ (is_array($perawats['perawat_terpilih']) && in_array($perawat->id, $perawats['perawat_terpilih'])) || collect(old('perawat_id'))->contains($perawat->id) ? 'selected' : '' }}>
                                    [{{ $perawat->id_petugas }}] - {{ $perawat->name }}
                                </option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">
                            <i class="ri-information-line"></i> Pilih satu atau lebih perawat yang terlibat dalam
                            penanganan
                        </small>
                        <p class="text-danger mb-0">{{ $errors->first('perawat_ids') }}</p>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="status_pulang">
                            <i class="ri-home-heart-line text-success me-1"></i>Status Pulang
                            <span class="text-danger">*</span>
                        </label>
                        <select name="status_pulang" id="status_pulang" class="form-select">
                            <option value="">-- Pilih Status Pulang --</option>
                            <option value="1" {{ old('status_pulang') == 1 ?: '' }}>✅ Kondisi Stabil</option>
                            <option value="2" {{ old('status_pulang') == 2 ?: '' }}>🔄 Pulang Kontrol Kembali
                            </option>
                            <option value="3" {{ old('status_pulang') == 3 ?: '' }}>🏥 Rujukan Rawat Inap</option>
                            <option value="4" {{ old('status_pulang') == 4 ?: '' }}>🚑 Rujukan RSU Lain</option>
                            <option value="5" {{ old('status_pulang') == 5 ?: '' }}>🕊️ Meninggal</option>
                        </select>
                        <small class="form-text text-muted">
                            <i class="ri-information-line"></i> Tentukan kondisi pasien saat pulang
                        </small>
                    </div>
                </div>

                <!-- Field Dokter Spesialis untuk Rujukan Rawat Inap -->
                <div class="row gx-3 mb-4 d-none" id="dokter-spesialis-section">
                    <div class="col-12">
                        <div class="alert alert-info border-0 shadow-sm">
                            <i class="ri-hospital-line me-2"></i>
                            <strong>Rujukan ke Rawat Inap</strong> - Pilih dokter spesialis yang akan menangani pasien
                        </div>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label fw-semibold" for="dokter_spesialis_id">
                            <i class="ri-user-star-line text-primary me-1"></i>Dokter Spesialis
                            <span class="text-danger">*</span>
                        </label>
                        <select class="form-select select2" id="dokter_spesialis_id" name="dokter_spesialis_id"
                            style="width: 100%;">
                            <option value="">-- Pilih Dokter Spesialis --</option>
                            @foreach ($dokters['dokters'] as $dokter)
                                @if ($dokter->role == 2)
                                    <option value="{{ $dokter->id }}">
                                        [{{ $dokter->id_petugas }}] {{ $dokter->name }}
                                        @if ($dokter->spesialis)
                                            - {{ $dokter->spesialis->name }}
                                        @endif
                                    </option>
                                @endif
                            @endforeach
                        </select>
                        <small class="form-text text-muted">
                            <i class="ri-information-line"></i> Dokter yang akan bertanggung jawab di rawat inap
                        </small>
                        <p class="text-danger mb-0" id="error-dokter-spesialis"></p>
                    </div>
                </div>

                <hr class="my-4">
                <div class="d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-outline-secondary">
                        <i class="ri-draft-line me-1"></i>Simpan Draft
                    </button>
                    <button type="button" class="btn btn-success btn-lg px-5" id="btn-simpan-catatan"
                        style="background: linear-gradient(135deg, #0ba360 0%, #3cba92 100%); border: none;">
                        <span id="text-simpan-catatan">
                            <i class="ri-check-double-line me-2"></i>Selesai Pemeriksaan
                        </span>
                        <span class="spinner-border spinner-border-sm d-none" id="spinner-simpan-catatan"
                            role="status" aria-hidden="true"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('style')
<style>
    .tbl-catatan thead th {
        font-size: .75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .5px;
        color: #fff;
        border-bottom: none;
        padding: .6rem .5rem;
    }
    .tbl-catatan tbody tr {
        transition: background-color .15s ease;
    }
    .tbl-catatan tbody tr:hover {
        background-color: #f8f9ff;
    }
    .tbl-catatan tbody td {
        padding: .5rem .5rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f3f5;
    }
    .tfoot-catatan td {
        padding: .5rem .5rem;
        border-top: 1px solid #e9ecef;
    }
</style>
@endpush

@push('scripts')
    <script>
        (function() {
            const ENCOUNTER_ID = @json($observasi);
            const getEncounterUrl = "{{ route('observasi.getEncounter', ':id') }}".replace(':id', ENCOUNTER_ID);
            const postDiskonTindakanUrl = "{{ route('observasi.postDiskonTindakan', ':id') }}".replace(':id',
                ENCOUNTER_ID);
            const postDiskonResepUrl = "{{ route('observasi.postDiskonResep', ':id') }}".replace(':id', ENCOUNTER_ID);
            const postCatatanUrl = "{{ route('observasi.postCatatanEncounter', ':id') }}".replace(':id', ENCOUNTER_ID);

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

            function loadEncounterSummary() {
                ensureFormat();
                $.ajax({
                        url: getEncounterUrl,
                        type: 'GET',
                        data: {
                            _token: "{{ csrf_token() }}"
                        }
                    })
                    .done(function(data) {
                        // Tindakan + Penunjang
                        const tbodyTindakan = $('#tbody-catatan-tindakan');
                        tbodyTindakan.empty();
                        let allTindakan = [];
                        if (data.tindakan && Array.isArray(data.tindakan) && data.tindakan.length > 0) {
                            allTindakan = allTindakan.concat(data.tindakan.map(function(item) {
                                return {
                                    nama: item.tindakan_name,
                                    qty: item.qty,
                                    harga: item.tindakan_harga,
                                    total: item.total_harga,
                                    is_paket: !!item.paket_pasien_id
                                };
                            }));
                        }
                        if (data.pemeriksaan_penunjang && Array.isArray(data.pemeriksaan_penunjang) && data
                            .pemeriksaan_penunjang.length > 0) {
                            allTindakan = allTindakan.concat(data.pemeriksaan_penunjang.map(function(item) {
                                return {
                                    nama: item.jenis_pemeriksaan,
                                    qty: item.qty,
                                    harga: item.harga,
                                    total: item.total_harga,
                                    is_paket: !!item.is_paket
                                };
                            }));
                        }
                        allTindakan.forEach(function(item, index) {
                            tbodyTindakan.append(`
                                <tr>
                                    <td class="ps-3">
                                        <div class="fw-semibold text-dark" style="font-size: .85rem; line-height: 1.3;">${item.nama} ${item.is_paket ? '<span class="badge bg-success-subtle text-success" style="font-size: .6rem; vertical-align: middle;">Paket</span>' : ''}</div>
                                    </td>
                                    <td class="text-center fw-medium">${item.qty}</td>
                                    <td class="text-end text-muted" style="font-size: .85rem;">${formatRupiah(item.harga)}</td>
                                    <td class="text-end pe-3 fw-semibold" style="font-size: .85rem;">${formatRupiah(item.total)}</td>
                                </tr>
                            `);
                        });
                        // Hitung footer berdasarkan Tindakan Medis saja (tanpa penunjang, exclude item paket)
                        let medisNominal = 0;
                        if (data.tindakan && Array.isArray(data.tindakan)) {
                            data.tindakan.forEach(function(it) {
                                if (it.paket_pasien_id) return; // Skip item paket
                                const qty = parseInt(it.qty) || 0;
                                const harga = parseFloat(it.tindakan_harga) || 0;
                                const total = parseFloat(it.total_harga);
                                medisNominal += (isNaN(total) ? (harga * qty) : total);
                            });
                        }
                        const persen = parseFloat(data.diskon_persen_tindakan || 0) || 0;
                        let diskonNominal = 0;
                        if (persen > 0) {
                            diskonNominal = Math.round(medisNominal * (persen / 100.0));
                        } else {
                            diskonNominal = parseFloat(data.diskon_tindakan || 0) || 0;
                        }
                        diskonNominal = Math.min(diskonNominal, medisNominal);
                        const subtotalMedis = Math.max(0, medisNominal - diskonNominal);
                        $('#total-tindakan').text(formatRupiah(medisNominal));
                        $('#total-tindakan-diskon').text(formatRupiah(diskonNominal) + (persen > 0 ? ' (' +
                            persen + '%)' : ''));
                        $('#total-tindakan-harga').text(formatRupiah(subtotalMedis));
                        // Penunjang (Lab + Radiologi, exclude item paket)
                        let penunjangNominal = 0;
                        if (data.pemeriksaan_penunjang && Array.isArray(data.pemeriksaan_penunjang)) {
                            data.pemeriksaan_penunjang.forEach(function(it) {
                                if (it.is_paket) return; // Skip item paket
                                const harga = parseFloat(it.harga) || 0;
                                const total = parseFloat(it.total_harga);
                                penunjangNominal += (isNaN(total) ? harga : total);
                            });
                        }
                        $('#total-penunjang').text(formatRupiah(penunjangNominal));
                        $('#total-tindakan-akhir').text(formatRupiah(subtotalMedis + penunjangNominal));

                        // Resep
                        const tbodyResep = $('#tbody-catatan-resep');
                        tbodyResep.empty();
                        if (data.resep && data.resep.details && Array.isArray(data.resep.details)) {
                            data.resep.details.forEach(function(item, index) {
                                tbodyResep.append(`
                                    <tr>
                                        <td class="ps-3">
                                            <div class="fw-semibold text-dark" style="font-size: .85rem; line-height: 1.3;">${item.nama_obat} ${item.paket_pasien_id ? '<span class="badge bg-success-subtle text-success" style="font-size: .6rem; vertical-align: middle;">Paket</span>' : ''}</div>
                                        </td>
                                        <td class="text-center fw-medium">${item.qty}</td>
                                        <td>
                                            <span class="text-muted" style="font-size: .8rem;">${item.aturan_pakai}</span>
                                        </td>
                                        <td class="text-end text-muted" style="font-size: .85rem;">${formatRupiah(item.harga)}</td>
                                        <td class="text-end pe-3 fw-semibold" style="font-size: .85rem;">${formatRupiah(item.total_harga)}</td>
                                    </tr>
                                `);
                            });
                        }
                        const totalResep = parseFloat(data.total_resep || 0) || 0;
                        const persenResep = parseFloat(data.diskon_persen_resep || 0) || 0;
                        let diskonResepNominal = 0;
                        if (persenResep > 0) {
                            diskonResepNominal = Math.round(totalResep * (persenResep / 100.0));
                        } else {
                            diskonResepNominal = parseFloat(data.diskon_resep || 0) || 0;
                        }
                        diskonResepNominal = Math.min(diskonResepNominal, totalResep);
                        $('#total-resep-catatan').text(formatRupiah(totalResep));
                        $('#total-resep-diskon').text(formatRupiah(diskonResepNominal) + (persenResep > 0 ?
                            ' (' + persenResep + '%)' : ''));
                        $('#total-resep-harga').text(formatRupiah(data.total_bayar_resep || 0));
                    });
            }

            $(document).on('click', '#tab-catatan', function() {
                if ($.fn.select2) {
                    $('#perawat_ids').select2({
                        placeholder: 'Pilih Perawat',
                        allowClear: true,
                        width: '100%'
                    });
                    $('#dokter_spesialis_id').select2({
                        placeholder: 'Pilih Dokter Spesialis',
                        allowClear: true,
                        width: '100%'
                    });
                }

                // Inisialisasi Quill Editor untuk catatan dokter
                if (typeof Quill !== 'undefined' && !window.quillCatatan) {
                    window.quillCatatan = new Quill('#catatanEditor', {
                        theme: 'snow',
                        modules: {
                            toolbar: [
                                ['bold', 'italic', 'underline'],
                                [{
                                    'list': 'ordered'
                                }, {
                                    'list': 'bullet'
                                }],
                                ['clean']
                            ]
                        },
                        placeholder: 'Tulis catatan dokter di sini...'
                    });
                }

                loadEncounterSummary();
            });

            $(document).on('change', '#diskon_tindakan_type', function() {
                const type = $(this).val() === 'nominal' ? 'nominal' : 'percent';
                $('#diskon_tindakan_prefix').text(type === 'nominal' ? 'Rp' : '%');
            });

            $(document).on('change', '#diskon_resep_type', function() {
                const type = $(this).val() === 'nominal' ? 'nominal' : 'percent';
                $('#diskon_resep_prefix').text(type === 'nominal' ? 'Rp' : '%');
            });

            // Diskon Tindakan
            $(document).on('click', '#btn-buat-diskon-tindakan', function(e) {
                e.preventDefault();
                const diskon_tindakan = $('#diskon_tindakan').val();
                const diskon_tindakan_type = $('#diskon_tindakan_type').val() || 'percent';
                if (!diskon_tindakan) {
                    alert('Diskon Tindakan tidak boleh kosong');
                    return;
                }
                $('#spinner-buat-diskon-tindakan').removeClass('d-none');
                $('#text-buat-diskon-tindakan').addClass('d-none');
                $('#btn-buat-diskon-tindakan').prop('disabled', true);
                $.ajax({
                        url: postDiskonTindakanUrl,
                        type: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            diskon_tindakan,
                            diskon_tindakan_type
                        }
                    })
                    .done(function(resp) {
                        swal(resp.message || 'Diskon tersimpan', {
                            icon: (resp.success ? 'success' : 'error')
                        });
                        loadEncounterSummary();
                    })
                    .always(function() {
                        $('#spinner-buat-diskon-tindakan').addClass('d-none');
                        $('#text-buat-diskon-tindakan').removeClass('d-none');
                        $('#btn-buat-diskon-tindakan').prop('disabled', false);
                    });
            });

            // Diskon Resep
            $(document).on('click', '#btn-buat-diskon-resep', function(e) {
                e.preventDefault();
                const diskon_resep = $('#diskon_resep').val();
                const diskon_resep_type = $('#diskon_resep_type').val() || 'percent';
                if (!diskon_resep) {
                    alert('Diskon Resep tidak boleh kosong');
                    return;
                }
                $('#spinner-buat-diskon-resep').removeClass('d-none');
                $('#text-buat-diskon-resep').addClass('d-none');
                $('#btn-buat-diskon-resep').prop('disabled', true);
                $.ajax({
                        url: postDiskonResepUrl,
                        type: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            diskon_resep,
                            diskon_resep_type
                        }
                    })
                    .done(function(resp) {
                        swal(resp.message || 'Diskon tersimpan', {
                            icon: (resp.success ? 'success' : 'error')
                        });
                        loadEncounterSummary();
                    })
                    .always(function() {
                        $('#spinner-buat-diskon-resep').addClass('d-none');
                        $('#text-buat-diskon-resep').removeClass('d-none');
                        $('#btn-buat-diskon-resep').prop('disabled', false);
                    });
            });

            // Toggle Dokter Spesialis Section
            $('#status_pulang').on('change', function() {
                const statusPulang = $(this).val();
                if (statusPulang == '3') { // Rujukan Rawat Inap
                    $('#dokter-spesialis-section').removeClass('d-none');
                    $('#dokter_spesialis_id').prop('required', true);
                } else {
                    $('#dokter-spesialis-section').addClass('d-none');
                    $('#dokter_spesialis_id').prop('required', false);
                    $('#dokter_spesialis_id').val('').trigger('change');
                    $('#error-dokter-spesialis').text('');
                }
            });

            // Selesai Pemeriksaan (Simpan Catatan)
            $(document).on('click', '#btn-simpan-catatan', function(e) {
                e.preventDefault();
                const status_pulang = $('#status_pulang').val();
                const perawat_ids = $('#perawat_ids').val();
                const dokter_spesialis_id = $('#dokter_spesialis_id').val();
                const catatan = (window.quillCatatan && quillCatatan.root) ? quillCatatan.root.innerHTML : '';

                if (!status_pulang) {
                    alert('Status Pulang tidak boleh kosong');
                    return;
                }

                // Validasi dokter spesialis jika rujukan rawat inap
                if (status_pulang == '3' && !dokter_spesialis_id) {
                    $('#error-dokter-spesialis').text(
                        'Dokter Spesialis harus dipilih untuk rujukan rawat inap');
                    $('#dokter_spesialis_id').focus();
                    return;
                } else {
                    $('#error-dokter-spesialis').text('');
                }

                $('#spinner-simpan-catatan').removeClass('d-none');
                $('#text-simpan-catatan').addClass('d-none');
                $('#btn-simpan-catatan').prop('disabled', true);
                $.ajax({
                        url: postCatatanUrl,
                        type: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            catatan,
                            status_pulang,
                            perawat_ids,
                            dokter_spesialis_id
                        }
                    })
                    .done(function(resp) {
                        swal(resp.message || 'Berhasil disimpan', {
                            icon: (resp.success ? 'success' : 'error')
                        });
                        if (resp.success && resp.url) {
                            window.location.href = resp.url;
                        }
                    })
                    .fail(function(xhr) {
                        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                            const errors = xhr.responseJSON.errors;
                            const errorMsg = Object.values(errors).map(function(msgArr) {
                                return msgArr.join('\n');
                            }).join('\n');
                            swal('Validasi Gagal', {
                                icon: 'error',
                                text: errorMsg
                            });
                        } else {
                            swal('Terjadi kesalahan saat menyimpan data.', {
                                icon: 'error'
                            });
                        }
                    })
                    .always(function() {
                        $('#spinner-simpan-catatan').addClass('d-none');
                        $('#text-simpan-catatan').removeClass('d-none');
                        $('#btn-simpan-catatan').prop('disabled', false);
                    });
            });
        })();
    </script>
@endpush
