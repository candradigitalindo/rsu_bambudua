<div class="alert alert-danger print-error-msg mt-2 mb-2" style="display:none" id="error-anamnesis">
    <ul></ul>
</div>
<div class="mb-3">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Ringkasan Kunjungan Terakhir</h6>
            <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse"
                data-bs-target="#last-enc-summary-body">Lihat/Sembunyikan</button>
        </div>
        <div id="last-enc-summary-body" class="collapse">
            <div class="card-body">
                <div id="last-encounter-summary" class="text-muted">Memuat ringkasan...</div>
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

@push('scripts')
    <script>
        (function() {
            const ENCOUNTER_ID = @json($observasi);
            const csrf = "{{ csrf_token() }}";
            const riwayatUrl = "{{ route('observasi.riwayatPenyakit', ':id') }}".replace(':id', ENCOUNTER_ID);
            const postAnamnesisUrl = "{{ route('observasi.postAnemnesis', ':id') }}".replace(':id', ENCOUNTER_ID);
            const getTtvUrl = "{{ route('observasi.tandaVital', ':id') }}".replace(':id', ENCOUNTER_ID);
            const postTtvUrl = "{{ route('observasi.postTandaVital', ':id') }}".replace(':id', ENCOUNTER_ID);
            const lastSummaryUrl = "{{ route('observasi.lastEncounterSummary', ':id') }}".replace(':id', ENCOUNTER_ID);

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
                        $c.text('Tidak ada kunjungan sebelumnya.');
                        return;
                    }

                    const ucFirst = s => (s || '').charAt(0).toUpperCase() + (s || '').slice(1);
                    const renderList = items => (items && items.length) ?
                        '<ul class="list-unstyled mb-0">' + items.map(li =>
                            `<li class="d-flex align-items-start gap-2">${li}</li>`).join('') + '</ul>' :
                        '<div class="text-muted">-</div>';

                    // Diagnosis (maks 3)
                    const diagItems = (resp.diagnosis || []).slice(0, 3).map(d => {
                        const code = d.diagnosis_code || '';
                        const desc = d.diagnosis_description || '';
                        const type = d.diagnosis_type ?
                            `<span class=\"badge bg-secondary rounded-pill\">${d.diagnosis_type}</span>` :
                            '';
                        return `<span class=\"ri-stethoscope-line text-primary\"></span><span>${code ? code + ' - ' : ''}${desc} ${type}</span>`;
                    });

                    // TTV string
                    const ttv = resp.ttv ? `
        <div class="d-flex flex-wrap gap-2">
          <span class="badge bg-primary-subtle text-primary border">Nadi: ${resp.ttv.nadi||'-'}</span>
          <span class="badge bg-primary-subtle text-primary border">TD: ${resp.ttv.sistolik||'-'}/${resp.ttv.diastolik||'-'}</span>
          <span class="badge bg-primary-subtle text-primary border">Suhu: ${resp.ttv.suhu||'-'}</span>
        </div>` : '<div class="text-muted">-</div>';

                    // Lab (maks 3 + status)
                    const labStatus = resp.lab && resp.lab.status ?
                        `<span class=\"badge ${resp.lab.status==='completed'?'bg-success':'bg-secondary'}\">${ucFirst(resp.lab.status)}</span>` :
                        '';
                    const labItems = resp.lab && resp.lab.count ? (resp.lab.items || []).slice(0, 3).map(i =>
                        `<span class=\"ri-flask-line text-success\"></span><span>${i.test_name}</span>`) :
                    [];

                    // Resep (maks 3)
                    const resepItems = resp.resep && resp.resep.count ? (resp.resep.items || []).slice(0, 3)
                        .map(i =>
                            `<span class=\"ri-capsule-fill text-danger\"></span><span>${i.nama_obat} <span class=\"badge bg-light text-dark border\">${i.qty}</span></span>`
                        ) : [];

                    $c.html(`
        <div class="row g-3">
          <div class="col-md-3">
            <small class="text-muted">Tanggal Kunjungan</small>
            <div class="fw-semibold">${resp.date || '-'}</div>
          </div>
          <div class="col-md-5">
            <small class="text-muted">Diagnosis</small>
            <div>${renderList(diagItems)}</div>
          </div>
          <div class="col-md-4">
            <small class="text-muted">Tanda Vital</small>
            <div>${ttv}</div>
          </div>
          <div class="col-md-6">
            <div class="d-flex align-items-center justify-content-between">
              <small class="text-muted">Laboratorium (terakhir)</small>
              ${labStatus}
            </div>
            <div>${renderList(labItems)}</div>
          </div>
          <div class="col-md-6">
            <small class="text-muted">Resep (terakhir)</small>
            <div>${renderList(resepItems)}</div>
          </div>
        </div>
      `);
                }).fail(function() {
                    $('#last-encounter-summary').text('Gagal memuat ringkasan.');
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
