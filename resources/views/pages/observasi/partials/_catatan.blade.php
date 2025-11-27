<div class="row gx-3">
    <div class="col-xxl-6 col-sm-12">
        <div class="card mb-3 shadow-sm">
            <div class="card-header bg-gradient" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <h5 class="card-title text-white mb-0">
                    <i class="ri-surgical-mask-line me-2"></i>Ringkasan Tindakan Medis
                </h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info border-start border-4 border-info mb-3" role="alert">
                    <div class="d-flex align-items-start">
                        <i class="ri-information-line fs-5 me-2"></i>
                        <div>
                            <strong>Diskon Tindakan</strong>
                            <small class="d-block text-muted">Berikan diskon dalam persen untuk biaya tindakan
                                medis</small>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold" for="diskon_tindakan">
                        <i class="ri-percent-line text-primary me-1"></i>Diskon Tindakan (%)
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="ri-discount-percent-line"></i></span>
                        <input type="number" name="diskon_tindakan" class="form-control" placeholder="0"
                            id="diskon_tindakan" min="0" max="100">
                        <span class="input-group-text">%</span>
                        <button class="btn btn-primary px-4" type="submit" id="btn-buat-diskon-tindakan">
                            <span id="text-buat-diskon-tindakan"><i class="ri-check-line me-1"></i>Terapkan</span>
                            <span class="spinner-border spinner-border-sm d-none" id="spinner-buat-diskon-tindakan"
                                role="status" aria-hidden="true"></span>
                        </button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle m-0">
                        <thead
                            style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-bottom: 2px solid #667eea;">
                            <tr>
                                <th style="padding: 12px; font-weight: 600; color: #495057;">Nama Tindakan</th>
                                <th class="text-center"
                                    style="padding: 12px; font-weight: 600; color: #495057; width: 80px;">Qty</th>
                                <th class="text-end"
                                    style="padding: 12px; font-weight: 600; color: #495057; width: 120px;">Harga</th>
                                <th class="text-end"
                                    style="padding: 12px; font-weight: 600; color: #495057; width: 130px;">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="tbody-catatan-tindakan"></tbody>
                        <tfoot
                            style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-top: 2px solid #667eea;">
                            <tr>
                                <td colspan="3" class="text-end"
                                    style="padding: 12px; font-weight: 600; color: #495057;">
                                    <i class="ri-calculator-line me-1 text-primary"></i>Nominal Tindakan Medis
                                </td>
                                <td class="text-end" style="padding: 12px;">
                                    <span id="total-tindakan" class="fw-bold"
                                        style="color: #495057; font-size: 14px;">0</span>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-end"
                                    style="padding: 12px; font-weight: 600; color: #dc3545;">
                                    <i class="ri-discount-percent-line me-1"></i>Diskon Tindakan Medis
                                </td>
                                <td class="text-end" style="padding: 12px;">
                                    <span id="total-tindakan-diskon" class="fw-bold text-danger"
                                        style="font-size: 14px;">0</span>
                                </td>
                            </tr>
                            <tr style="background-color: #f1f3f5;">
                                <td colspan="3" class="text-end"
                                    style="padding: 12px; font-weight: 700; color: #212529;">
                                    <i class="ri-money-dollar-box-line me-1 text-success"></i>Subtotal Tindakan Medis
                                </td>
                                <td class="text-end" style="padding: 12px;">
                                    <span id="total-tindakan-harga" class="fw-bold text-success"
                                        style="font-size: 15px;">0</span>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-end"
                                    style="padding: 12px; font-weight: 600; color: #495057;">
                                    <i class="ri-stethoscope-line me-1 text-info"></i>Penunjang (Lab + Radiologi)
                                </td>
                                <td class="text-end" style="padding: 12px;">
                                    <span id="total-penunjang" class="fw-bold text-info"
                                        style="font-size: 14px;">0</span>
                                </td>
                            </tr>
                            <tr style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                <td colspan="3" class="text-end"
                                    style="padding: 16px; font-weight: 700; color: white; font-size: 16px;">
                                    <i class="ri-shopping-cart-2-line me-2"></i>TOTAL KESELURUHAN
                                </td>
                                <td class="text-end" style="padding: 16px;">
                                    <span id="total-tindakan-akhir" class="fw-bold"
                                        style="color: white; font-size: 18px;">0</span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xxl-6 col-sm-12">
        <div class="card mb-3 shadow-sm">
            <div class="card-header bg-gradient" style="background: linear-gradient(135deg, #0ba360 0%, #3cba92 100%);">
                <h5 class="card-title text-white mb-0">
                    <i class="ri-medicine-bottle-line me-2"></i>Ringkasan Resep Obat
                </h5>
            </div>
            <div class="card-body">
                <div class="alert alert-success border-start border-4 border-success mb-3" role="alert">
                    <div class="d-flex align-items-start">
                        <i class="ri-information-line fs-5 me-2"></i>
                        <div>
                            <strong>Diskon Resep</strong>
                            <small class="d-block text-muted">Berikan diskon dalam persen untuk biaya resep
                                obat</small>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold" for="diskon_resep">
                        <i class="ri-percent-line text-success me-1"></i>Diskon Resep (%)
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="ri-discount-percent-line"></i></span>
                        <input type="number" name="diskon_resep" class="form-control" placeholder="0"
                            id="diskon_resep" min="0" max="100">
                        <span class="input-group-text">%</span>
                        <button class="btn btn-success px-4" type="submit" id="btn-buat-diskon-resep">
                            <span id="text-buat-diskon-resep"><i class="ri-check-line me-1"></i>Terapkan</span>
                            <span class="spinner-border spinner-border-sm d-none" id="spinner-buat-diskon-resep"
                                role="status" aria-hidden="true"></span>
                        </button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle m-0">
                        <thead
                            style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-bottom: 2px solid #0ba360;">
                            <tr>
                                <th style="padding: 12px; font-weight: 600; color: #495057;">Nama Obat</th>
                                <th class="text-center"
                                    style="padding: 12px; font-weight: 600; color: #495057; width: 80px;">Jumlah</th>
                                <th style="padding: 12px; font-weight: 600; color: #495057; width: 150px;">Aturan Pakai
                                </th>
                                <th class="text-end"
                                    style="padding: 12px; font-weight: 600; color: #495057; width: 110px;">Harga</th>
                                <th class="text-end"
                                    style="padding: 12px; font-weight: 600; color: #495057; width: 120px;">Subtotal
                                </th>
                            </tr>
                        </thead>
                        <tbody id="tbody-catatan-resep"></tbody>
                        <tfoot
                            style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-top: 2px solid #0ba360;">
                            <tr>
                                <td colspan="4" class="text-end"
                                    style="padding: 12px; font-weight: 600; color: #495057;">
                                    <i class="ri-calculator-line me-1 text-success"></i>Nominal
                                </td>
                                <td class="text-end" style="padding: 12px;">
                                    <span id="total-resep-catatan" class="fw-bold"
                                        style="color: #495057; font-size: 14px;">Rp. 0</span>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4" class="text-end"
                                    style="padding: 12px; font-weight: 600; color: #dc3545;">
                                    <i class="ri-discount-percent-line me-1"></i>Diskon
                                </td>
                                <td class="text-end" style="padding: 12px;">
                                    <span id="total-resep-diskon" class="fw-bold text-danger"
                                        style="font-size: 14px;">Rp. 0</span>
                                </td>
                            </tr>
                            <tr style="background: linear-gradient(135deg, #0ba360 0%, #3cba92 100%);">
                                <td colspan="4" class="text-end"
                                    style="padding: 16px; font-weight: 700; color: white; font-size: 16px;">
                                    <i class="ri-shopping-cart-2-line me-2"></i>TOTAL RESEP
                                </td>
                                <td class="text-end" style="padding: 16px;">
                                    <span id="total-resep-harga" class="fw-bold"
                                        style="color: white; font-size: 18px;">Rp. 0</span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xxl-12 col-sm-12">
        <div class="card mb-3 shadow-sm">
            <div class="card-header bg-gradient"
                style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <h5 class="card-title text-white mb-0">
                    <i class="ri-file-text-line me-2"></i>Catatan & Penyelesaian Pemeriksaan
                </h5>
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
                                    total: item.total_harga
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
                                    total: item.total_harga
                                };
                            }));
                        }
                        allTindakan.forEach(function(item, index) {
                            tbodyTindakan.append(`
            <tr style="border-bottom: 1px solid #e9ecef;">
              <td style="padding: 14px 12px; vertical-align: middle;">
                <div class="d-flex align-items-center">
                  <div class="rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <i class="ri-stethoscope-line text-white" style="font-size: 14px;"></i>
                  </div>
                  <span style="color: #212529; font-weight: 500;">${item.nama}</span>
                </div>
              </td>
              <td class="text-center" style="padding: 14px 12px; vertical-align: middle;">
                <span class="badge" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 5px 12px; border-radius: 6px; font-weight: 600;">${item.qty}</span>
              </td>
              <td class="text-end" style="padding: 14px 12px; vertical-align: middle; color: #495057; font-size: 14px;">Rp ${formatRupiah(item.harga)}</td>
              <td class="text-end" style="padding: 14px 12px; vertical-align: middle;">
                <span class="fw-bold text-primary" style="font-size: 14px;">Rp ${formatRupiah(item.total)}</span>
              </td>
            </tr>
          `);
                        });
                        // Hitung footer berdasarkan Tindakan Medis saja (tanpa penunjang)
                        let medisNominal = 0;
                        if (data.tindakan && Array.isArray(data.tindakan)) {
                            data.tindakan.forEach(function(it) {
                                const qty = parseInt(it.qty) || 0;
                                const harga = parseFloat(it.tindakan_harga) || 0;
                                const total = parseFloat(it.total_harga);
                                medisNominal += (isNaN(total) ? (harga * qty) : total);
                            });
                        }
                        const persen = parseFloat(data.diskon_persen_tindakan || 0) || 0;
                        const diskonNominal = Math.round(medisNominal * (persen / 100.0));
                        const subtotalMedis = Math.max(0, medisNominal - diskonNominal);
                        $('#total-tindakan').text(formatRupiah(medisNominal));
                        $('#total-tindakan-diskon').text(formatRupiah(diskonNominal) + (diskonNominal ? ' (' +
                            persen + '%)' : ''));
                        $('#total-tindakan-harga').text(formatRupiah(subtotalMedis));
                        // Penunjang (Lab + Radiologi)
                        let penunjangNominal = 0;
                        if (data.pemeriksaan_penunjang && Array.isArray(data.pemeriksaan_penunjang)) {
                            data.pemeriksaan_penunjang.forEach(function(it) {
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
              <tr style="border-bottom: 1px solid #e9ecef;">
                <td style="padding: 14px 12px; vertical-align: middle;">
                  <div class="d-flex align-items-center">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; background: linear-gradient(135deg, #0ba360 0%, #3cba92 100%);">
                      <i class="ri-medicine-bottle-line text-white" style="font-size: 14px;"></i>
                    </div>
                    <span style="color: #212529; font-weight: 500;">${item.nama_obat}</span>
                  </div>
                </td>
                <td class="text-center" style="padding: 14px 12px; vertical-align: middle;">
                  <span class="badge" style="background: linear-gradient(135deg, #0ba360 0%, #3cba92 100%); color: white; padding: 5px 12px; border-radius: 6px; font-weight: 600;">${item.qty}</span>
                </td>
                <td style="padding: 14px 12px; vertical-align: middle;">
                  <div class="d-flex align-items-center">
                    <i class="ri-time-line text-info me-1" style="font-size: 14px;"></i>
                    <small style="color: #6c757d;">${item.aturan_pakai}</small>
                  </div>
                </td>
                <td class="text-end" style="padding: 14px 12px; vertical-align: middle; color: #495057; font-size: 14px;">Rp ${formatRupiah(item.harga)}</td>
                <td class="text-end" style="padding: 14px 12px; vertical-align: middle;">
                  <span class="fw-bold text-success" style="font-size: 14px;">Rp ${formatRupiah(item.total_harga)}</span>
                </td>
              </tr>
            `);
                            });
                        }
                        $('#total-resep-catatan').text(formatRupiah(data.total_resep || 0));
                        $('#total-resep-diskon').text(formatRupiah(data.diskon_resep || 0) + (data.diskon_resep ?
                            ' (' + (data.diskon_persen_resep || 0) + '%)' : ''));
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
                }
                loadEncounterSummary();
            });

            // Diskon Tindakan
            $(document).on('click', '#btn-buat-diskon-tindakan', function(e) {
                e.preventDefault();
                const diskon_tindakan = $('#diskon_tindakan').val();
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
                            diskon_tindakan
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
                            diskon_resep
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

            // Selesai Pemeriksaan (Simpan Catatan)
            $(document).on('click', '#btn-simpan-catatan', function(e) {
                e.preventDefault();
                const status_pulang = $('#status_pulang').val();
                const perawat_ids = $('#perawat_ids').val();
                const catatan = (window.quillCatatan && quillCatatan.root) ? quillCatatan.root.innerHTML : '';
                if (!status_pulang) {
                    alert('Status Pulang tidak boleh kosong');
                    return;
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
                            perawat_ids
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
