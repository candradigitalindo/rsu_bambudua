<div class="alert alert-danger print-error-msg mt-2 mb-2" style="display:none" id="error-anamnesis">
    <ul></ul>
</div>
<div class="mb-3">
    <div class="card border-primary">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h6 class="mb-0">
                <i class="ri-history-line"></i> Ringkasan Kunjungan Terakhir
            </h6>
            <button class="btn btn-sm btn-light" type="button" data-bs-toggle="collapse"
                data-bs-target="#last-enc-summary-body">
                <i class="ri-eye-line"></i> <span id="toggle-text">Lihat</span>
            </button>
        </div>
        <div id="last-enc-summary-body" class="collapse">
            <div class="card-body">
                <div id="last-encounter-summary" class="text-muted">
                    <div class="text-center py-3">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Memuat...</span>
                        </div>
                        <p class="mt-2 mb-0">Memuat ringkasan kunjungan...</p>
                    </div>
                </div>
                <div class="d-flex justify-content-end mt-3" id="copy-tindakan-container">
                    <button class="btn btn-sm btn-success" type="button" id="btn-copy-tindakan"
                        onclick="copyLastEncounterData()" title="Copy data kunjungan terakhir ke encounter saat ini">
                        <i class="ri-file-copy-line"></i> Copy Tindakan
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row gx-3">
    <!-- Dokter yang menangani -->
    <div class="col-sm-12 col-12">
        <div class="mb-3">
            <label class="form-label" for="dokter_id">Dokter yang Menangani</label>
            <select name="dokter_ids[]" class="form-select" id="dokter_id" multiple
                @if (auth()->user()->role == 3) disabled @endif>
                @foreach ($dokters['dokters'] as $dokter)
                    <option value="{{ $dokter->id }}"
                        {{ in_array($dokter->id, $dokters['dokter_terpilih']) ? 'selected' : '' }}>
                        {{ $dokter->name }}
                    </option>
                @endforeach
            </select>
            <p class="text-danger">{{ $errors->first('dokter_ids') }}</p>
        </div>
    </div>

    <!-- Anamnesis fields -->
    <div class="col-sm-12 col-12">
        <div class="mb-3">
            <label class="form-label" for="keluhan_utama">Keluhan Utama</label>
            <div class="input-group">
                <textarea name="keluhan_utama" class="form-control" id="keluhan_utama" cols="10" rows="5">{{ old('keluhan_utama') }}</textarea>
            </div>
            <p class="text-danger">{{ $errors->first('keluhan_utama') }}</p>
        </div>
    </div>
    <div class="col-sm-6 col-12">
        <div class="mb-3">
            <label class="form-label" for="riwayat_penyakit">Riwayat Penyakit</label>
            <div class="input-group">
                <textarea name="riwayat_penyakit" class="form-control" id="riwayat_penyakit" cols="10" rows="5">{{ old('riwayat_penyakit') }}</textarea>
            </div>
            <p class="text-danger">{{ $errors->first('riwayat_penyakit') }}</p>
        </div>
    </div>
    <div class="col-sm-6 col-12">
        <div class="mb-3">
            <label class="form-label" for="riwayat_penyakit_keluarga">Riwayat Penyakit Keluarga</label>
            <div class="input-group">
                <textarea name="riwayat_penyakit_keluarga" class="form-control" id="riwayat_penyakit_keluarga" cols="10"
                    rows="5">{{ old('riwayat_penyakit_keluarga') }}</textarea>
            </div>
            <p class="text-danger">{{ $errors->first('riwayat_penyakit_keluarga') }}</p>
        </div>
    </div>
</div>
<hr class="my-4" />

@push('style')
    <style>
        #last-encounter-summary .card {
            transition: all 0.3s ease;
        }

        #last-encounter-summary .card:hover {
            box-shadow: 0 0.125rem 0.5rem rgba(0, 0, 0, 0.1);
        }

        #last-encounter-summary .card-header {
            font-weight: 600;
            padding: 0.75rem 1rem;
        }

        #last-encounter-summary .card-body {
            padding: 1rem;
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Make ENCOUNTER_ID global so it can be accessed by other functions
        const ENCOUNTER_ID = @json($observasi);

        (function() {
            const csrf = "{{ csrf_token() }}";
            const riwayatUrl = "{{ url('kunjungan/observasi') }}/" + ENCOUNTER_ID + "/riwayatPenyakit";
            const postAnamnesisUrl = "{{ url('kunjungan/observasi') }}/" + ENCOUNTER_ID + "/postAnemnesis";
            const getTtvUrl = "{{ url('kunjungan/observasi') }}/" + ENCOUNTER_ID + "/tandaVital";
            const postTtvUrl = "{{ url('kunjungan/observasi') }}/" + ENCOUNTER_ID + "/postTandaVital";
            const lastSummaryUrl = "{{ url('kunjungan/observasi') }}/" + ENCOUNTER_ID + "/lastEncounterSummary";

            // Toggle button text untuk ringkasan kunjungan terakhir
            $('#last-enc-summary-body').on('show.bs.collapse', function() {
                $('#toggle-text').text('Sembunyikan');
            }).on('hide.bs.collapse', function() {
                $('#toggle-text').text('Lihat');
            });

            function loadRiwayat() {
                $.get({
                    url: riwayatUrl
                }).done(function(data) {
                    if (data && data.riwayatPenyakit) {
                        $('#riwayat_penyakit').val(data.riwayatPenyakit.riwayat_penyakit);
                        $('#riwayat_penyakit_keluarga').val(data.riwayatPenyakit.riwayat_penyakit_keluarga);
                    }
                    if (data && data.anamnesis) {
                        $('#keluhan_utama').val(data.anamnesis.keluhan_utama || '');
                    }
                });
            }

            function loadTTV() {
                $.get({
                    url: getTtvUrl
                }).done(function(data) {
                    if (!data) return;
                    $('#nadi').val(data.nadi || '');
                    $('#pernapasan').val(data.pernapasan || '');
                    $('#sistolik').val(data.sistolik || '');
                    $('#diastolik').val(data.diastolik || '');
                    $('#suhu').val(data.suhu || '');
                    $('#kesadaran').val(data.kesadaran || '');
                    $('#tinggi_badan').val(data.tinggi_badan || '');
                    $('#berat_badan').val(data.berat_badan || '');
                });
            }

            function loadLastEncounter() {
                $.get({
                    url: lastSummaryUrl
                }).done(function(resp) {
                    const $c = $('#last-encounter-summary');
                    if (!resp || $.isEmptyObject(resp)) {
                        $c.html(`
                            <div class="alert alert-info mb-0">
                                <div class="text-center py-4">
                                    <i class="ri-folder-open-line fs-1 text-info mb-3"></i>
                                    <h5 class="mb-2">Data Kunjungan Terakhir Tidak Ada</h5>
                                    <p class="text-muted mb-0">Belum ada riwayat kunjungan sebelumnya untuk pasien ini.<br>Silakan isi data encounter baru secara manual.</p>
                                </div>
                            </div>
                        `);
                        // Disable copy button and update text if no data
                        $('#btn-copy-tindakan').prop('disabled', true)
                            .removeClass('btn-success').addClass('btn-secondary')
                            .attr('title', 'Tidak ada data kunjungan terakhir')
                            .html('<i class="ri-file-copy-line"></i> Tidak Ada Data');
                        lastEncounterData = null;
                        return;
                    }

                    // Store data globally for copy function
                    lastEncounterData = resp;
                    $('#btn-copy-tindakan').prop('disabled', false)
                        .removeClass('btn-secondary').addClass('btn-success')
                        .attr('title', 'Copy data kunjungan terakhir ke encounter saat ini')
                        .html('<i class="ri-file-copy-line"></i> Copy Tindakan');

                    const ucFirst = s => (s || '').charAt(0).toUpperCase() + (s || '').slice(1);

                    // Diagnosis (maks 5)
                    const diagItems = (resp.diagnosis || []).slice(0, 5).map(d => {
                        const code = d.diagnosis_code || '';
                        const desc = d.diagnosis_description || '';
                        const type = d.diagnosis_type ?
                            `<span class="badge bg-${d.diagnosis_type === 'Primer' ? 'primary' : 'secondary'} ms-1" style="font-size: 0.7rem;">${d.diagnosis_type}</span>` :
                            '';
                        return `
                            <div class="d-flex align-items-start gap-2 mb-2">
                                <i class="ri-stethoscope-line text-primary mt-1"></i>
                                <div class="flex-grow-1">
                                    <span class="fw-medium">${code}</span>
                                    <br><span class="text-muted small">${desc}</span>
                                    ${type}
                                </div>
                            </div>`;
                    }).join('');

                    // TTV dengan icon
                    const ttvItems = resp.ttv ? [{
                            icon: 'ri-pulse-line',
                            label: 'Nadi',
                            value: resp.ttv.nadi,
                            unit: 'x/mnt',
                            color: 'danger'
                        },
                        {
                            icon: 'ri-heart-pulse-line',
                            label: 'TD',
                            value: `${resp.ttv.sistolik||'-'}/${resp.ttv.diastolik||'-'}`,
                            unit: 'mmHg',
                            color: 'warning'
                        },
                        {
                            icon: 'ri-temp-hot-line',
                            label: 'Suhu',
                            value: resp.ttv.suhu,
                            unit: '°C',
                            color: 'info'
                        },
                        {
                            icon: 'ri-lungs-line',
                            label: 'Napas',
                            value: resp.ttv.pernapasan,
                            unit: 'x/mnt',
                            color: 'success'
                        },
                        {
                            icon: 'ri-eye-line',
                            label: 'Kesadaran',
                            value: resp.ttv.kesadaran || '-',
                            unit: '',
                            color: 'primary'
                        }
                    ].map(t => `
                        <div class="col-md-6 mb-2">
                            <div class="d-flex align-items-center gap-2">
                                <div class="p-2 rounded bg-${t.color}-subtle">
                                    <i class="${t.icon} text-${t.color} fs-5"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block">${t.label}</small>
                                    <span class="fw-semibold">${t.value || '-'} ${t.value && t.unit ? t.unit : ''}</span>
                                </div>
                            </div>
                        </div>
                    `).join('') : '<div class="text-muted">Data tidak tersedia</div>';

                    // Lab (maks 5) dengan button lihat hasil per item
                    const labItems = resp.lab && resp.lab.count ? (resp.lab.items || []).slice(0, 5).map(lab =>
                        `<div class="d-flex align-items-center justify-content-between gap-2 mb-2 pb-2 border-bottom">
                            <div class="d-flex align-items-center gap-2 flex-grow-1">
                                <i class="ri-flask-line text-success"></i>
                                <div>
                                    <span class="fw-medium">${lab.test_summary}</span>
                                    <div>
                                        <span class="badge bg-${lab.status==='completed'?'success':'warning'}-subtle text-${lab.status==='completed'?'success':'warning'} border" style="font-size: 0.7rem;">${ucFirst(lab.status)}</span>
                                    </div>
                                </div>
                            </div>
                            ${lab.status === 'completed' && lab.lab_request_id ?
                                `<button class="btn btn-sm btn-outline-primary" onclick="viewLabResults('${lab.lab_request_id}')" title="Lihat Hasil Lengkap">
                                                                                                                <i class="ri-file-list-3-line"></i>
                                                                                                            </button>` : ''}
                        </div>`).join('') : '<div class="text-muted small">Tidak ada pemeriksaan lab</div>';

                    // Radiologi dengan button lihat hasil per item
                    const radioItems = resp.radiologi && resp.radiologi.count ? (resp.radiologi.items || [])
                        .slice(0, 5).map(radio =>
                            `<div class="d-flex align-items-center justify-content-between gap-2 mb-2 pb-2 border-bottom">
                                <div class="d-flex align-items-center gap-2 flex-grow-1">
                                    <i class="ri-scan-line text-info"></i>
                                    <div>
                                        <span class="fw-medium">${radio.jenis_name}</span>
                                        <div>
                                            <span class="badge bg-${radio.status==='completed'?'success':'warning'}-subtle text-${radio.status==='completed'?'success':'warning'} border" style="font-size: 0.7rem;">${ucFirst(radio.status)}</span>
                                        </div>
                                    </div>
                                </div>
                                ${radio.status === 'completed' && radio.radiology_request_id ?
                                    `<button class="btn btn-sm btn-outline-info" onclick="viewRadioResults('${radio.radiology_request_id}')" title="Lihat Hasil Lengkap">
                                                                                                                <i class="ri-file-list-3-line"></i>
                                                                                                            </button>` : ''}
                            </div>`).join('') : '<div class="text-muted small">Tidak ada pemeriksaan radiologi</div>';

                    // Resep (maks 5) dengan aturan pakai
                    const resepItems = resp.resep && resp.resep.count ? (resp.resep.items || []).slice(0, 5)
                        .map(i =>
                            `<div class="mb-3 pb-2 border-bottom">
                                <div class="d-flex align-items-start justify-content-between mb-1">
                                    <div class="d-flex align-items-center gap-2 flex-grow-1">
                                        <i class="ri-capsule-fill text-danger fs-5"></i>
                                        <div>
                                            <div class="fw-semibold">${i.nama_obat}</div>
                                            ${i.aturan_pakai ? `<small class="text-muted"><i class="ri-time-line"></i> ${i.aturan_pakai}</small>` : ''}
                                        </div>
                                    </div>
                                    <span class="badge bg-danger-subtle text-danger border ms-2">${i.qty} ${i.satuan || 'pcs'}</span>
                                </div>
                            </div>`
                        ).join('') : '<div class="text-muted small">Tidak ada resep obat</div>';

                    $c.html(`
        <div class="row g-3">
          <!-- Informasi Kunjungan -->
          <div class="col-12">
            <div class="alert alert-info border-start border-4 border-info mb-0">
                <div class="d-flex align-items-center gap-2">
                    <i class="ri-calendar-event-line fs-4"></i>
                    <div>
                        <strong>Kunjungan Terakhir:</strong> ${resp.date || '-'}
                        ${resp.type ? `<span class="badge bg-primary ms-2">${resp.type}</span>` : ''}
                    </div>
                </div>
            </div>
          </div>

          <!-- Diagnosis -->
          <div class="col-md-6">
            <div class="card h-100 border">
                <div class="card-header bg-primary-subtle">
                    <h6 class="mb-0"><i class="ri-stethoscope-line"></i> Diagnosis</h6>
                </div>
                <div class="card-body">
                    ${diagItems || '<div class="text-muted">Belum ada diagnosis</div>'}
                    ${(resp.diagnosis || []).length > 5 ? '<small class="text-muted">+ ' + ((resp.diagnosis || []).length - 5) + ' diagnosis lainnya</small>' : ''}
                </div>
            </div>
          </div>

          <!-- Tanda Vital -->
          <div class="col-md-6">
            <div class="card h-100 border">
                <div class="card-header bg-success-subtle">
                    <h6 class="mb-0"><i class="ri-heart-pulse-line"></i> Tanda Vital</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        ${ttvItems}
                    </div>
                </div>
            </div>
          </div>

          <!-- Laboratorium -->
          <div class="col-md-6">
            <div class="card h-100 border">
                <div class="card-header bg-warning-subtle">
                    <h6 class="mb-0"><i class="ri-flask-line"></i> Pemeriksaan Lab</h6>
                </div>
                <div class="card-body">
                    ${labItems}
                    ${resp.lab && resp.lab.count > 5 ? '<small class="text-muted d-block mt-2">+ ' + (resp.lab.count - 5) + ' pemeriksaan lainnya</small>' : ''}
                </div>
            </div>
          </div>

          <!-- Radiologi -->
          <div class="col-md-6">
            <div class="card h-100 border">
                <div class="card-header bg-info-subtle">
                    <h6 class="mb-0"><i class="ri-scan-line"></i> Pemeriksaan Radiologi</h6>
                </div>
                <div class="card-body">
                    ${radioItems}
                    ${resp.radiologi && resp.radiologi.count > 5 ? '<small class="text-muted d-block mt-2">+ ' + (resp.radiologi.count - 5) + ' pemeriksaan lainnya</small>' : ''}
                </div>
            </div>
          </div>

          <!-- Resep Obat -->
          <div class="col-md-6">
            <div class="card h-100 border">
                <div class="card-header bg-danger-subtle">
                    <h6 class="mb-0"><i class="ri-capsule-line"></i> Resep Obat</h6>
                </div>
                <div class="card-body">
                    ${resepItems}
                    ${resp.resep && resp.resep.count > 5 ? '<small class="text-muted">+ ' + (resp.resep.count - 5) + ' obat lainnya</small>' : ''}
                </div>
            </div>
          </div>

          <!-- Keluhan Utama jika ada -->
          ${resp.keluhan ? `
                                                                                                              <div class="col-12">
                                                                                                                <div class="card border">
                                                                                                                    <div class="card-header bg-light">
                                                                                                                        <h6 class="mb-0"><i class="ri-chat-3-line"></i> Keluhan Utama</h6>
                                                                                                                    </div>
                                                                                                                    <div class="card-body">
                                                                                                                        <p class="mb-0">${resp.keluhan}</p>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                              </div>` : ''}
        </div>
      `);
                }).fail(function(xhr, status, error) {
                    console.error('Failed to load last encounter summary:', error, xhr.responseText);
                    $('#last-encounter-summary').html(`
                        <div class="alert alert-warning mb-0">
                            <i class="ri-alert-line"></i>
                            <strong>Gagal memuat ringkasan kunjungan terakhir.</strong>
                            <br><small class="text-muted">Mungkin pasien belum memiliki riwayat kunjungan sebelumnya atau terjadi kesalahan sistem.</small>
                        </div>
                    `);
                });
            }

            // Trigger load when tab is clicked
            $(document).on('click', '#tab-anamnesis', function() {
                loadRiwayat();
                loadTTV();
                loadLastEncounter();
            });
            // Initial load if tab active on page load
            $(function() {
                if ($('#anamnesis').hasClass('show')) {
                    loadRiwayat();
                    loadTTV();
                    loadLastEncounter();
                }
            });

            function validateAnamnesis() {
                const dokter_ids = $('#dokter_id').val();
                const keluhan_utama = $('#keluhan_utama').val();
                const riwayat_penyakit = $('#riwayat_penyakit').val();
                const riwayat_penyakit_keluarga = $('#riwayat_penyakit_keluarga').val();
                if (!dokter_ids || dokter_ids.length === 0) return 'Dokter tidak boleh kosong';
                if (!keluhan_utama) return 'Keluhan Utama tidak boleh kosong';
                // Validasi riwayat penyakit dan keluarga bisa opsional, sesuaikan jika perlu
                return null;
            }

            $(document).on('click', '#btn-save-anamnesis-ttv', function() {
                const $btn = $('#btn-save-anamnesis-ttv');
                $('#spinner-save-anamnesis-ttv').removeClass('d-none');
                $('#text-save-anamnesis-ttv').addClass('d-none');
                $btn.prop('disabled', true);

                const err = validateAnamnesis();
                if (err) {
                    swal(err, {
                        icon: 'error'
                    });
                    $('#spinner-save-anamnesis-ttv').addClass('d-none');
                    $('#text-save-anamnesis-ttv').removeClass('d-none');
                    $btn.prop('disabled', false);
                    return;
                }

                const anamPayload = {
                    dokter_ids: $('#dokter_id').val(),
                    keluhan_utama: $('#keluhan_utama').val(),
                    riwayat_penyakit: $('#riwayat_penyakit').val(),
                    riwayat_penyakit_keluarga: $('#riwayat_penyakit_keluarga').val(),
                    _token: csrf
                };
                const ttvPayload = {
                    nadi: $('#nadi').val(),
                    pernapasan: $('#pernapasan').val(),
                    sistolik: $('#sistolik').val(),
                    diastolik: $('#diastolik').val(),
                    suhu: $('#suhu').val(),
                    kesadaran: $('#kesadaran').val(),
                    tinggi_badan: $('#tinggi_badan').val(),
                    berat_badan: $('#berat_badan').val(),
                    _token: csrf
                };

                $.ajax({
                        url: postAnamnesisUrl,
                        type: 'POST',
                        data: anamPayload
                    })
                    .then(function(resp) {
                        if (!(resp && resp.status == 200)) {
                            return $.Deferred().reject(resp);
                        }
                        return $.ajax({
                            url: postTtvUrl,
                            type: 'POST',
                            data: ttvPayload
                        });
                    })
                    .done(function(ttvResp) {
                        if (ttvResp && ttvResp.status == 200) {
                            swal('Anamnesis & TTV tersimpan', {
                                icon: 'success'
                            });
                        } else {
                            swal('Anamnesis tersimpan, namun gagal menyimpan TTV.', {
                                icon: 'warning'
                            });
                        }
                    })
                    .fail(function(err) {
                        let msg = (err && err.responseJSON && (err.responseJSON.message || err.responseJSON
                            .error)) || 'Gagal menyimpan data.';
                        swal(msg, {
                            icon: 'error'
                        });
                    })
                    .always(function() {
                        $('#spinner-save-anamnesis-ttv').addClass('d-none');
                        $('#text-save-anamnesis-ttv').removeClass('d-none');
                        $btn.prop('disabled', false);
                    });
            });
        })();

        // Function to view lab results - open print page directly
        function viewLabResults(labRequestId) {
            // Open print medical page in new window (format untuk hasil medis)
            window.open(`/laboratorium/requests/${labRequestId}/print-medical`, '_blank');
        }

        // OLD Function (kept for reference, can be removed later)
        function viewLabResultsModal(labRequestId) {
            const modalEl = document.getElementById('modalLabResults');
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            const content = document.getElementById('labResultsContent');

            // Show loading spinner
            content.innerHTML = `
                <div class="text-center py-5">
                    <div class="spinner-border text-warning" role="status">
                        <span class="visually-hidden">Memuat...</span>
                    </div>
                    <p class="mt-2">Memuat hasil laboratorium...</p>
                </div>
            `;

            modal.show();

            // Fetch lab results
            $.ajax({
                url: `/kunjungan/lab/${labRequestId}/hasil`,
                method: 'GET',
                success: function(response) {
                    if (response.success && response.data) {
                        const data = response.data;
                        let html = `
                            <div class="lab-results-print" id="labResultsPrintArea">
                                <div class="text-center mb-4">
                                    <h5 class="mb-1">HASIL PEMERIKSAAN LABORATORIUM</h5>
                                    <p class="mb-0">${data.klinik_nama || 'Klinik Bambu Dua'}</p>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-6">
                                        <table class="table-sm">
                                            <tr><td class="fw-bold pe-3">No. RM</td><td>: ${data.pasien_no_rm || '-'}</td></tr>
                                            <tr><td class="fw-bold pe-3">Nama Pasien</td><td>: ${data.pasien_nama || '-'}</td></tr>
                                            <tr><td class="fw-bold pe-3">Tgl. Lahir</td><td>: ${data.pasien_tgl_lahir || '-'}</td></tr>
                                        </table>
                                    </div>
                                    <div class="col-6">
                                        <table class="table-sm">
                                            <tr><td class="fw-bold pe-3">No. Permintaan</td><td>: ${data.nomor_permintaan || '-'}</td></tr>
                                            <tr><td class="fw-bold pe-3">Tgl. Permintaan</td><td>: ${data.tanggal_permintaan || '-'}</td></tr>
                                            <tr><td class="fw-bold pe-3">Dokter</td><td>: ${data.dokter_nama || '-'}</td></tr>
                                        </table>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <h6 class="fw-bold">Hasil Pemeriksaan:</h6>
                                    <table class="table table-bordered" style="border-collapse: collapse;">
                                        <tbody>
                        `;

                        if (data.items && data.items.length > 0) {
                            data.items.forEach(function(item) {
                                let resultText = item.hasil || '-';

                                // Add reference range and unit if available
                                if (item.nilai_normal && item.nilai_normal !== '-') {
                                    resultText += ` (Normal: ${item.nilai_normal})`;
                                }
                                if (item.satuan && item.satuan !== '-' && item.satuan !== '') {
                                    resultText += ` ${item.satuan}`;
                                }

                                let resultClass = '';
                                if (item.is_abnormal) {
                                    resultClass = 'text-danger fw-bold';
                                }

                                html += `
                                    <tr>
                                        <td style="width: 40%; border: 1px solid #ccc; padding: 8px;">${item.nama_pemeriksaan || '-'}</td>
                                        <td style="border: 1px solid #ccc; padding: 8px;" class="${resultClass}">${resultText}</td>
                                    </tr>
                                `;
                            });
                        } else {
                            html += `
                                <tr>
                                    <td colspan="2" class="text-center" style="border: 1px solid #ccc; padding: 8px;">Belum ada hasil pemeriksaan</td>
                                </tr>
                            `;
                        }

                        html += `
                                        </tbody>
                                    </table>
                                </div>

                                ${data.catatan ? `
                                                                                                                    <div class="mt-3">
                                                                                                                        <h6>Catatan:</h6>
                                                                                                                        <p>${data.catatan}</p>
                                                                                                                    </div>
                                                                                                                    ` : ''}

                                <div class="row mt-4">
                                    <div class="col-6"></div>
                                    <div class="col-6 text-center">
                                        <p class="mb-1">Petugas Lab</p>
                                        <div style="height: 60px;"></div>
                                        <p class="mb-0">${data.petugas_lab || '(______________)'}</p>
                                    </div>
                                </div>
                            </div>
                        `;

                        content.innerHTML = html;
                    } else {
                        content.innerHTML = `
                            <div class="alert alert-warning">
                                <i class="ri-alert-line"></i> ${response.message || 'Data hasil lab tidak ditemukan'}
                            </div>
                        `;
                    }
                },
                error: function(xhr) {
                    let errorMsg = 'Gagal memuat hasil laboratorium';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    content.innerHTML = `
                        <div class="alert alert-danger">
                            <i class="ri-error-warning-line"></i> ${errorMsg}
                        </div>
                    `;
                }
            });
        }

        // Function to view radiologi results - open print page directly
        function viewRadioResults(radiologyRequestId) {
            // Open print page in new window with auto print
            window.open(`/radiologi/permintaan/${radiologyRequestId}/print?auto=1`, '_blank');
        }

        // OLD Function (kept for reference, can be removed later)
        function viewRadioResultsModal(radiologyRequestId) {
            const modalEl = document.getElementById('modalRadioResults');
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            const content = document.getElementById('radioResultsContent');

            // Show loading spinner
            content.innerHTML = `
                <div class="text-center py-5">
                    <div class="spinner-border text-info" role="status">
                        <span class="visually-hidden">Memuat...</span>
                    </div>
                    <p class="mt-2">Memuat hasil radiologi...</p>
                </div>
            `;

            modal.show();

            // Fetch radiologi results
            $.ajax({
                url: `/kunjungan/radiologi/${radiologyRequestId}/hasil`,
                method: 'GET',
                success: function(response) {
                    if (response.success && response.data) {
                        const data = response.data;
                        let html = `
                            <div class="radio-results-print" id="radioResultsPrintArea">
                                <div class="text-center mb-4">
                                    <h5 class="mb-1">HASIL PEMERIKSAAN RADIOLOGI</h5>
                                    <p class="mb-0">${data.klinik_nama || 'Klinik Bambu Dua'}</p>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-6">
                                        <table class="table-sm">
                                            <tr><td class="fw-bold pe-3">No. RM</td><td>: ${data.pasien_no_rm || '-'}</td></tr>
                                            <tr><td class="fw-bold pe-3">Nama Pasien</td><td>: ${data.pasien_nama || '-'}</td></tr>
                                            <tr><td class="fw-bold pe-3">Tgl. Lahir</td><td>: ${data.pasien_tgl_lahir || '-'}</td></tr>
                                        </table>
                                    </div>
                                    <div class="col-6">
                                        <table class="table-sm">
                                            <tr><td class="fw-bold pe-3">No. Permintaan</td><td>: ${data.nomor_permintaan || '-'}</td></tr>
                                            <tr><td class="fw-bold pe-3">Tgl. Permintaan</td><td>: ${data.tanggal_permintaan || '-'}</td></tr>
                                            <tr><td class="fw-bold pe-3">Dokter</td><td>: ${data.dokter_nama || '-'}</td></tr>
                                        </table>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <h6 class="fw-bold">Jenis Pemeriksaan:</h6>
                                    <ul class="mb-0">
                        `;

                        if (data.items && data.items.length > 0) {
                            data.items.forEach(function(item) {
                                html += `<li>${item.nama_pemeriksaan || '-'}</li>`;
                            });
                        } else {
                            html += `<li>-</li>`;
                        }

                        html += `
                                    </ul>
                                </div>

                                ${data.clinical_info ? `
                                                                                                                    <div class="mb-3">
                                                                                                                        <h6 class="fw-bold">Informasi Klinis:</h6>
                                                                                                                        <div style="border: 1px solid #ccc; min-height: 100px; border-radius: 4px; padding: 8px;">
                                                                                                                            ${data.clinical_info.replace(/\n/g, '<br>')}
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                    ` : ''}

                                ${data.technique ? `
                                                                                                                    <div class="mb-3">
                                                                                                                        <h6 class="fw-bold">Teknik Pemeriksaan:</h6>
                                                                                                                        <div style="border: 1px solid #ccc; min-height: 100px; border-radius: 4px; padding: 8px;">
                                                                                                                            ${data.technique.replace(/\n/g, '<br>')}
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                    ` : ''}

                                ${data.component_results && data.component_results.length > 0 ? `
                                                                                                                    <div class="mb-3">
                                                                                                                        <h6 class="fw-bold">Hasil Pemeriksaan:</h6>
                                                                                                                        <table class="table table-bordered" style="border-collapse: collapse;">
                                                                                                                            <tbody>
                                                                                                                                ${data.component_results.map(comp => `
                                            <tr>
                                                <td style="width: 40%; border: 1px solid #ccc; padding: 8px;">${comp.nama}</td>
                                                <td style="border: 1px solid #ccc; padding: 8px;">${comp.nilai}</td>
                                            </tr>
                                        `).join('')}
                                                                                                                            </tbody>
                                                                                                                        </table>
                                                                                                                    </div>
                                                                                                                ` : ''}

                                ${data.findings && data.findings !== '-' ? `
                                                                                                                    <div class="mb-3">
                                                                                                                        <h6 class="fw-bold">Hasil Pemeriksaan (Findings):</h6>
                                                                                                                        <div style="border: 1px solid #ccc; min-height: 100px; border-radius: 4px; padding: 8px;">
                                                                                                                            ${data.findings.replace(/\n/g, '<br>')}
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                    ` : ''}

                                ${data.impression && data.impression !== '-' ? `
                                                                                                                    <div class="mb-3">
                                                                                                                        <h6 class="fw-bold">Kesan (Impression):</h6>
                                                                                                                        <div style="border: 1px solid #ccc; min-height: 100px; border-radius: 4px; padding: 8px;">
                                                                                                                            ${data.impression.replace(/\n/g, '<br>')}
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                    ` : ''}

                                ${data.conclusion ? `
                                                                                                                    <div class="mb-3">
                                                                                                                        <h6 class="fw-bold">Kesimpulan:</h6>
                                                                                                                        <div style="border: 1px solid #ccc; min-height: 100px; border-radius: 4px; padding: 8px;">
                                                                                                                            ${data.conclusion.replace(/\n/g, '<br>')}
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                    ` : ''}

                                ${data.images && data.images.length > 0 ? `
                                                                                                                    <div class="mb-3">
                                                                                                                        <h6 class="fw-bold">Gambar:</h6>
                                                                                                                        <div class="row g-2">
                                                                                                                            ${data.images.map(img => `
                                            <div class="col-md-4">
                                                <img src="${img.url}" class="img-fluid rounded border" alt="Hasil Radiologi">
                                            </div>
                                        `).join('')}
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                    ` : ''}

                                <div class="row mt-4">
                                    <div class="col-6"></div>
                                    <div class="col-6 text-center">
                                        <p class="mb-1">Radiolog</p>
                                        <div style="height: 60px;"></div>
                                        <p class="mb-0">${data.radiolog || '(______________)'}</p>
                                    </div>
                                </div>
                            </div>
                        `;

                        content.innerHTML = html;
                    } else {
                        content.innerHTML = `
                            <div class="alert alert-warning">
                                <i class="ri-alert-line"></i> ${response.message || 'Data hasil radiologi tidak ditemukan'}
                            </div>
                        `;
                    }
                },
                error: function(xhr) {
                    let errorMsg = 'Gagal memuat hasil radiologi';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    content.innerHTML = `
                        <div class="alert alert-danger">
                            <i class="ri-error-warning-line"></i> ${errorMsg}
                        </div>
                    `;
                }
            });
        }

        // Function to print lab results
        function printLabResults() {
            const printContent = document.getElementById('labResultsPrintArea');
            if (!printContent) {
                swal('Tidak ada data untuk dicetak', {
                    icon: 'warning'
                });
                return;
            }

            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <html>
                <head>
                    <title>Hasil Pemeriksaan Laboratorium</title>
                    <style>
                        body { font-family: Arial, sans-serif; padding: 20px; }
                        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
                        table td, table th { padding: 8px; text-align: left; }
                        .table-bordered { border: 1px solid #dee2e6; }
                        .table-bordered th, .table-bordered td { border: 1px solid #dee2e6; }
                        .text-center { text-align: center; }
                        .fw-bold { font-weight: bold; }
                        .text-danger { color: #dc3545; }
                        .table-light { background-color: #f8f9fa; }
                        .pe-3 { padding-right: 1rem; }
                        h5, h6 { margin-bottom: 0.5rem; }
                        p { margin: 0.5rem 0; }
                    </style>
                </head>
                <body>
                    ${printContent.innerHTML}
                </body>
                </html>
            `);
            printWindow.document.close();
            printWindow.focus();
            setTimeout(() => {
                printWindow.print();
                printWindow.close();
            }, 250);
        }

        // Function to print radiologi results
        function printRadioResults() {
            const printContent = document.getElementById('radioResultsPrintArea');
            if (!printContent) {
                swal('Tidak ada data untuk dicetak', {
                    icon: 'warning'
                });
                return;
            }

            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <html>
                <head>
                    <title>Hasil Pemeriksaan Radiologi</title>
                    <style>
                        body { font-family: Arial, sans-serif; padding: 20px; }
                        table { margin: 10px 0; }
                        .text-center { text-align: center; }
                        .fw-bold { font-weight: bold; }
                        .pe-3 { padding-right: 1rem; }
                        .border { border: 1px solid #dee2e6; }
                        .rounded { border-radius: 0.25rem; }
                        .p-3 { padding: 1rem; }
                        .bg-light { background-color: #f8f9fa; }
                        h5, h6 { margin-bottom: 0.5rem; }
                        p { margin: 0.5rem 0; }
                        ul { margin: 0.5rem 0; padding-left: 1.5rem; }
                        img { max-width: 100%; height: auto; page-break-inside: avoid; }
                    </style>
                </head>
                <body>
                    ${printContent.innerHTML}
                </body>
                </html>
            `);
            printWindow.document.close();
            printWindow.focus();
            setTimeout(() => {
                printWindow.print();
                printWindow.close();
            }, 250);
        }

        // Function to copy last encounter data to current encounter
        let lastEncounterData = null; // Store last encounter data globally

        function copyLastEncounterData() {
            if (!lastEncounterData) {
                swal('Data kunjungan terakhir belum dimuat', {
                    icon: 'warning'
                });
                return;
            }

            // Confirm before copying
            swal({
                title: 'Copy Data Kunjungan Terakhir?',
                text: 'Ini akan mengisi Anamnesis dan Tanda Vital dengan data dari kunjungan terakhir (kecuali Pemeriksaan Penunjang dan Dokter). Data yang sudah diisi akan ditimpa.',
                icon: 'warning',
                buttons: {
                    cancel: 'Batal',
                    confirm: {
                        text: 'Ya, Copy Data',
                        value: true
                    }
                },
                dangerMode: true,
            }).then((willCopy) => {
                if (willCopy) {
                    performCopyData();
                }
            });
        }

        function performCopyData() {
            // Create progress modal
            const progressHtml = `
                <div style="text-align: center;">
                    <h4 style="margin-bottom: 20px;">Meng-copy Data Encounter</h4>
                    <div style="background: #f0f0f0; border-radius: 10px; height: 30px; position: relative; overflow: hidden; margin-bottom: 15px;">
                        <div id="progressBar" style="background: linear-gradient(90deg, #4CAF50, #45a049); height: 100%; width: 0%; transition: width 0.3s ease; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; border-radius: 10px;">
                            <span id="progressText">0%</span>
                        </div>
                    </div>
                    <p id="progressStatus" style="color: #666; font-size: 14px;">Memulai proses...</p>
                </div>
            `;

            // Show progress modal
            swal({
                content: {
                    element: "div",
                    attributes: {
                        innerHTML: progressHtml
                    }
                },
                buttons: false,
                closeOnClickOutside: false,
                closeOnEsc: false
            });

            // Simulate progress stages
            const stages = [{
                    percent: 20,
                    text: 'Meng-copy Anamnesis...'
                },
                {
                    percent: 40,
                    text: 'Meng-copy Tanda Vital...'
                },
                {
                    percent: 60,
                    text: 'Meng-copy Diagnosis...'
                },
                {
                    percent: 80,
                    text: 'Meng-copy Tindakan...'
                },
                {
                    percent: 95,
                    text: 'Meng-copy Resep...'
                }
            ];

            let currentStage = 0;

            function updateProgress() {
                if (currentStage < stages.length) {
                    const stage = stages[currentStage];
                    $('#progressBar').css('width', stage.percent + '%');
                    $('#progressText').text(stage.percent + '%');
                    $('#progressStatus').text(stage.text);
                    currentStage++;
                }
            }

            // Update progress every 300ms
            const progressInterval = setInterval(updateProgress, 300);

            // Call copy endpoint
            const copyUrl = "{{ route('observasi.copyLastEncounter', ':id') }}".replace(':id', ENCOUNTER_ID);

            $.ajax({
                url: copyUrl,
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {
                    clearInterval(progressInterval);

                    // Complete progress
                    $('#progressBar').css('width', '100%');
                    $('#progressText').text('100%');
                    $('#progressStatus').text('Selesai!');

                    setTimeout(() => {
                        if (response.success) {
                            swal('Berhasil!', response.message, 'success').then(() => {
                                // Reload page to show copied data
                                window.location.reload();
                            });
                        } else {
                            swal('Gagal!', response.message, 'error');
                        }
                    }, 500);
                },
                error: function(xhr) {
                    clearInterval(progressInterval);
                    let errorMsg = 'Gagal meng-copy data encounter';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    swal('Error', errorMsg, 'error');
                }
            });
        }

        // Make functions global so they can be called from onclick
        window.viewLabResults = viewLabResults;
        window.viewRadioResults = viewRadioResults;
        window.printLabResults = printLabResults;
        window.printRadioResults = printRadioResults;
        window.copyLastEncounterData = copyLastEncounterData;
    </script>
@endpush
<!-- Tanda Tanda Vital (TTV) -->
<div class="row gx-3">
    <div class="col-xxl-3 col-lg-4 col-sm-6">
        <div class="mb-3">
            <label class="form-label" for="nadi">Nadi</label>
            <div class="input-group">
                <input type="text" class="form-control" id="nadi" name="nadi" value="{{ old('nadi') }}">
            </div>
            <p class="text-danger">{{ $errors->first('nadi') }}</p>
        </div>
    </div>
    <div class="col-xxl-3 col-lg-4 col-sm-6">
        <div class="mb-3">
            <label class="form-label" for="pernapasan">Pernapasan</label>
            <div class="input-group">
                <input type="text" class="form-control" id="pernapasan" name="pernapasan"
                    value="{{ old('pernapasan') }}">
            </div>
            <p class="text-danger">{{ $errors->first('pernapasan') }}</p>
        </div>
    </div>
    <div class="col-xxl-3 col-lg-4 col-sm-6">
        <div class="mb-3">
            <label class="form-label" for="sistolik">TD Sistolik</label>
            <div class="input-group">
                <input type="text" class="form-control" id="sistolik" name="sistolik"
                    value="{{ old('sistolik') }}">
            </div>
            <p class="text-danger">{{ $errors->first('sistolik') }}</p>
        </div>
    </div>
    <div class="col-xxl-3 col-lg-4 col-sm-6">
        <div class="mb-3">
            <label class="form-label" for="diastolik">TD Diastolik</label>
            <div class="input-group">
                <input type="text" class="form-control" id="diastolik" name="diastolik"
                    value="{{ old('diastolik') }}">
            </div>
            <p class="text-danger">{{ $errors->first('diastolik') }}</p>
        </div>
    </div>
    <div class="col-xxl-3 col-lg-4 col-sm-6">
        <div class="mb-3">
            <label class="form-label" for="suhu">Suhu</label>
            <div class="input-group">
                <input type="text" class="form-control" id="suhu" name="suhu"
                    value="{{ old('suhu') }}">
            </div>
            <p class="text-danger">{{ $errors->first('suhu') }}</p>
        </div>
    </div>
    <div class="col-xxl-3 col-lg-4 col-sm-6">
        <div class="mb-3">
            <label class="form-label" for="kesadaran">Kesadaran</label>
            <div class="input-group">
                <select name="kesadaran" id="kesadaran" class="form-control">
                    <option value="">Pilih Kesadaran</option>
                    <option value="Compos Mentis" {{ old('kesadaran') == 'Compos Mentis' ? 'selected' : '' }}>Compos
                        Mentis</option>
                    <option value="Apatis" {{ old('kesadaran') == 'Apatis' ? 'selected' : '' }}>Apatis</option>
                    <option value="Somnolent" {{ old('kesadaran') == 'Somnolent' ? 'selected' : '' }}>Somnolent
                    </option>
                    <option value="Sopor" {{ old('kesadaran') == 'Sopor' ? 'selected' : '' }}>Sopor</option>
                    <option value="Coma" {{ old('kesadaran') == 'Coma' ? 'selected' : '' }}>Coma</option>
                </select>
            </div>
            <p class="text-danger">{{ $errors->first('kesadaran') }}</p>
        </div>
    </div>
    <div class="col-xxl-3 col-lg-4 col-sm-6">
        <div class="mb-3">
            <label class="form-label" for="tinggi_badan">Tinggi Badan</label>
            <div class="input-group">
                <input type="text" class="form-control" id="tinggi_badan" name="tinggi_badan"
                    value="{{ old('tinggi_badan') }}">
            </div>
            <p class="text-danger">{{ $errors->first('tinggi_badan') }}</p>
        </div>
    </div>
    <div class="col-xxl-3 col-lg-4 col-sm-6">
        <div class="mb-3">
            <label class="form-label" for="berat_badan">Berat Badan</label>
            <div class="input-group">
                <input type="text" class="form-control" id="berat_badan" name="berat_badan"
                    value="{{ old('berat_badan') }}">
            </div>
            <p class="text-danger">{{ $errors->first('berat_badan') }}</p>
        </div>
    </div>
</div>
<div class="d-flex gap-2 justify-content-end mt-4">
    <button type="button" class="btn btn-primary" id="btn-save-anamnesis-ttv">
        <span class="btn-txt" id="text-save-anamnesis-ttv">Simpan Anamnesis &amp; TTV</span>
        <span class="spinner-border spinner-border-sm d-none" id="spinner-save-anamnesis-ttv"></span>
    </button>
    <a href="{{ $redirectRoute ?? '#' }}" class="btn btn-secondary" id="btn-kembali-anamnesis-ttv">
        <span class="btn-txt" id="text-kembali-anamnesis-ttv">Kembali</span>
        <span class="spinner-border spinner-border-sm d-none" id="spinner-kembali-anamnesis-ttv"></span>
    </a>
</div>
