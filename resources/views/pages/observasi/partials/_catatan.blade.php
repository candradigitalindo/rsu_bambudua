<div class="row gx-3">
  <div class="col-xxl-6 col-sm-12">
    <div class="card mb-1">
      <div class="card-header">
        <h5 class="card-title">Tindakan</h5>
        <hr class="mb-1">
      </div>
      <div class="card-body">
        <div class="mb-1">
          <label class="form-label" for="a2">Diskon Tindakan</label>
          <div class="input-group">
            <input type="number" name="diskon_tindakan" class="form-control" placeholder="Diskon Tindakan" id="diskon_tindakan">
            @if (auth()->user()->role != 3)
              <div class="input-group-text">%</div>
              <button class="btn btn-primary" type="submit" id="btn-buat-diskon-tindakan">
                <span id="text-buat-diskon-tindakan">Buat Diskon</span>
                <span class="spinner-border spinner-border-sm d-none" id="spinner-buat-diskon-tindakan" role="status" aria-hidden="true"></span>
              </button>
            @endif
          </div>
        </div>
        <div class="table-outer">
          <div class="table-responsive">
            <table class="table truncate m-0">
              <thead>
                <tr>
                  <th>Nama Tindakan</th>
                  <th>Qty</th>
                  <th>Harga</th>
                  <th>Sub Total</th>
                </tr>
              </thead>
              <tbody id="tbody-catatan-tindakan"></tbody>
              <tfoot>
                <tr>
                  <td colspan="3" class="text-end fw-bold">Nominal</td>
                  <td class="text-end"><span id="total-tindakan" class="fw-bold">0</span></td>
                </tr>
                <tr>
                  <td colspan="3" class="text-end fw-bold">Diskon</td>
                  <td class="text-end"><span id="total-tindakan-diskon" class="fw-bold">0</span></td>
                </tr>
                <tr>
                  <td colspan="3" class="text-end fw-bold">Total</td>
                  <td class="text-end"><span id="total-tindakan-harga" class="fw-bold">0</span></td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xxl-6 col-sm-12">
    <div class="card mb-3">
      <div class="card-header">
        <h5 class="card-title">Resep</h5>
        <hr class="mb-1">
      </div>
      <div class="card-body">
        <div class="mb-1">
          <label class="form-label" for="a2">Diskon Resep</label>
          <div class="input-group">
            <input type="number" name="diskon_resep" class="form-control" placeholder="Diskon Resep" id="diskon_resep">
            @if (auth()->user()->role != 3)
              <div class="input-group-text">%</div>
              <button class="btn btn-primary" type="submit" id="btn-buat-diskon-resep">
                <span id="text-buat-diskon-resep">Buat Diskon</span>
                <span class="spinner-border spinner-border-sm d-none" id="spinner-buat-diskon-resep" role="status" aria-hidden="true"></span>
              </button>
            @endif
          </div>
        </div>
        <div class="table-outer">
          <div class="table-responsive">
            <table class="table truncate m-0">
              <thead>
                <tr>
                  <th>Nama Obat</th>
                  <th>Jumlah</th>
                  <th>Aturan Pakai</th>
                  <th>Harga</th>
                  <th>Subtotal</th>
                </tr>
              </thead>
              <tbody id="tbody-catatan-resep"></tbody>
              <tfoot>
                <tr>
                  <td colspan="4" class="text-end fw-bold">Nominal</td>
                  <td class="text-end fw-bold" id="total-resep-catatan">Rp. 0</td>
                </tr>
                <tr>
                  <td colspan="4" class="text-end fw-bold">Diskon</td>
                  <td class="text-end fw-bold" id="total-resep-diskon">Rp. 0</td>
                </tr>
                <tr>
                  <td colspan="4" class="text-end fw-bold">Total</td>
                  <td class="text-end fw-bold" id="total-resep-harga">Rp. 0</td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xxl-12 col-sm-12">
    <div class="card mb-3">
      <div class="card-header">
        <h5 class="card-title">Catatan</h5>
        <hr class="mb-1">
      </div>
      <div class="card-body">
        <div class="mb-3">
          <label class="form-label" for="a2">Catatan Dokter</label>
          <div class="col-sm-12">
            <div id="catatanEditor" class="quill-editor"></div>
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label" for="perawat_ids">Perawat yang Menangani <span class="text-danger">*</span></label>
          <div class="col-sm-12">
            <select class="form-select select2" id="perawat_ids" name="perawat_ids[]" multiple style="width: 100%;">
              @foreach ($perawats['perawats'] as $perawat)
                <option value="{{ $perawat->id }}" {{ (is_array($perawats['perawat_terpilih']) && in_array($perawat->id, $perawats['perawat_terpilih'])) || collect(old('perawat_id'))->contains($perawat->id) ? 'selected' : '' }}>
                  [{{ $perawat->id_petugas }}] - {{ $perawat->name }}
                </option>
              @endforeach
            </select>
            <p class="text-danger">{{ $errors->first('perawat_ids') }}</p>
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label" for="a2">Status Pulang</label>
          <div class="col-sm-12">
            <select name="status_pulang" id="status_pulang" class="form-control">
              <option value="">Pilih Status Pulang</option>
              <option value="1" {{ old('status_pulang') == 1 ?: '' }}>Kondisi Stabil</option>
              <option value="2" {{ old('status_pulang') == 2 ?: '' }}>Pulang Kontrol Kembali</option>
              <option value="3" {{ old('status_pulang') == 3 ?: '' }}>Rujukan Rawat Inap</option>
              <option value="4" {{ old('status_pulang') == 4 ?: '' }}>Rujukan RSU Lain</option>
              <option value="5" {{ old('status_pulang') == 5 ?: '' }}>Meninggal</option>
            </select>
          </div>
        </div>
        @if (auth()->user()->role != 3)
          <div class="mb-3 d-flex justify-content-end">
            <button type="button" class="btn btn-primary" id="btn-simpan-catatan">
              <span id="text-simpan-catatan">Selesai Pemeriksaan</span>
              <span class="spinner-border spinner-border-sm d-none" id="spinner-simpan-catatan" role="status" aria-hidden="true"></span>
            </button>
          </div>
        @endif
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
(function(){
  const ENCOUNTER_ID = @json($observasi);
  const getEncounterUrl = "{{ route('observasi.getEncounter', ':id') }}".replace(':id', ENCOUNTER_ID);
  const postDiskonTindakanUrl = "{{ route('observasi.postDiskonTindakan', ':id') }}".replace(':id', ENCOUNTER_ID);
  const postDiskonResepUrl = "{{ route('observasi.postDiskonResep', ':id') }}".replace(':id', ENCOUNTER_ID);
  const postCatatanUrl = "{{ route('observasi.postCatatanEncounter', ':id') }}".replace(':id', ENCOUNTER_ID);

  function ensureFormat(){
    if (typeof window.formatRupiah !== 'function') {
      window.formatRupiah = function(angka){
        if (angka === null || angka === undefined) return '0';
        let integer_part = Math.floor(parseFloat(angka)).toString();
        let sisa = integer_part.length % 3;
        let rupiah = integer_part.substr(0, sisa);
        let ribuan = integer_part.substr(sisa).match(/\d{3}/gi);
        if (ribuan) { let separator = sisa ? '.' : ''; rupiah += separator + ribuan.join('.'); }
        return rupiah;
      }
    }
  }

  function loadEncounterSummary(){
    ensureFormat();
    $.ajax({ url: getEncounterUrl, type: 'GET', data: { _token: "{{ csrf_token() }}" } })
      .done(function(data){
        // Tindakan + Penunjang
        const tbodyTindakan = $('#tbody-catatan-tindakan');
        tbodyTindakan.empty();
        let allTindakan = [];
        if (data.tindakan && Array.isArray(data.tindakan) && data.tindakan.length > 0) {
          allTindakan = allTindakan.concat(data.tindakan.map(function(item){ return { nama: item.tindakan_name, qty: item.qty, harga: item.tindakan_harga, total: item.total_harga }; }));
        }
        if (data.pemeriksaan_penunjang && Array.isArray(data.pemeriksaan_penunjang) && data.pemeriksaan_penunjang.length > 0) {
          allTindakan = allTindakan.concat(data.pemeriksaan_penunjang.map(function(item){ return { nama: item.jenis_pemeriksaan, qty: item.qty, harga: item.harga, total: item.total_harga }; }));
        }
        allTindakan.forEach(function(item){
          tbodyTindakan.append(`
            <tr>
              <td>${item.nama}</td>
              <td>${item.qty}</td>
              <td class="text-end">${formatRupiah(item.harga)}</td>
              <td class="text-end">${formatRupiah(item.total)}</td>
            </tr>
          `);
        });
        $('#total-tindakan').text(formatRupiah(data.total_tindakan || 0));
        $('#total-tindakan-diskon').text(formatRupiah(data.diskon_tindakan || 0) + (data.diskon_tindakan ? ' (' + (data.diskon_persen_tindakan || 0) + '%)' : ''));
        $('#total-tindakan-harga').text(formatRupiah(data.total_bayar_tindakan || 0));

        // Resep
        const tbodyResep = $('#tbody-catatan-resep');
        tbodyResep.empty();
        if (data.resep && data.resep.details && Array.isArray(data.resep.details)){
          data.resep.details.forEach(function(item){
            tbodyResep.append(`
              <tr>
                <td>${item.nama_obat}</td>
                <td>${item.qty}</td>
                <td>${item.aturan_pakai}</td>
                <td class="text-end">${formatRupiah(item.harga)}</td>
                <td class="text-end">${formatRupiah(item.total_harga)}</td>
              </tr>
            `);
          });
        }
        $('#total-resep-catatan').text(formatRupiah(data.total_resep || 0));
        $('#total-resep-diskon').text(formatRupiah(data.diskon_resep || 0) + (data.diskon_resep ? ' (' + (data.diskon_persen_resep || 0) + '%)' : ''));
        $('#total-resep-harga').text(formatRupiah(data.total_bayar_resep || 0));
      });
  }

  $(document).on('click', '#tab-catatan', function(){
    if ($.fn.select2) {
      $('#perawat_ids').select2({ placeholder: 'Pilih Perawat', allowClear: true, width: '100%' });
    }
    loadEncounterSummary();
  });

  // Diskon Tindakan
  $(document).on('click', '#btn-buat-diskon-tindakan', function(e){
    e.preventDefault();
    const diskon_tindakan = $('#diskon_tindakan').val();
    if (!diskon_tindakan) { alert('Diskon Tindakan tidak boleh kosong'); return; }
    $('#spinner-buat-diskon-tindakan').removeClass('d-none');
    $('#text-buat-diskon-tindakan').addClass('d-none');
    $('#btn-buat-diskon-tindakan').prop('disabled', true);
    $.ajax({ url: postDiskonTindakanUrl, type: 'POST', data: { _token: "{{ csrf_token() }}", diskon_tindakan } })
      .done(function(resp){
        swal(resp.message || 'Diskon tersimpan', { icon: (resp.success ? 'success' : 'error') });
        loadEncounterSummary();
      })
      .always(function(){
        $('#spinner-buat-diskon-tindakan').addClass('d-none');
        $('#text-buat-diskon-tindakan').removeClass('d-none');
        $('#btn-buat-diskon-tindakan').prop('disabled', false);
      });
  });

  // Diskon Resep
  $(document).on('click', '#btn-buat-diskon-resep', function(e){
    e.preventDefault();
    const diskon_resep = $('#diskon_resep').val();
    if (!diskon_resep) { alert('Diskon Resep tidak boleh kosong'); return; }
    $('#spinner-buat-diskon-resep').removeClass('d-none');
    $('#text-buat-diskon-resep').addClass('d-none');
    $('#btn-buat-diskon-resep').prop('disabled', true);
    $.ajax({ url: postDiskonResepUrl, type: 'POST', data: { _token: "{{ csrf_token() }}", diskon_resep } })
      .done(function(resp){
        swal(resp.message || 'Diskon tersimpan', { icon: (resp.success ? 'success' : 'error') });
        loadEncounterSummary();
      })
      .always(function(){
        $('#spinner-buat-diskon-resep').addClass('d-none');
        $('#text-buat-diskon-resep').removeClass('d-none');
        $('#btn-buat-diskon-resep').prop('disabled', false);
      });
  });

  // Selesai Pemeriksaan (Simpan Catatan)
  $(document).on('click', '#btn-simpan-catatan', function(e){
    e.preventDefault();
    const status_pulang = $('#status_pulang').val();
    const perawat_ids = $('#perawat_ids').val();
    const catatan = (window.quillCatatan && quillCatatan.root) ? quillCatatan.root.innerHTML : '';
    if (!status_pulang) { alert('Status Pulang tidak boleh kosong'); return; }
    $('#spinner-simpan-catatan').removeClass('d-none');
    $('#text-simpan-catatan').addClass('d-none');
    $('#btn-simpan-catatan').prop('disabled', true);
    $.ajax({ url: postCatatanUrl, type: 'POST', data: { _token: "{{ csrf_token() }}", catatan, status_pulang, perawat_ids } })
      .done(function(resp){
        swal(resp.message || 'Berhasil disimpan', { icon: (resp.success ? 'success' : 'error') });
        if (resp.success && resp.url) { window.location.href = resp.url; }
      })
      .fail(function(xhr){
        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors){
          const errors = xhr.responseJSON.errors;
          const errorMsg = Object.values(errors).map(function(msgArr){ return msgArr.join('\n'); }).join('\n');
          swal('Validasi Gagal', { icon: 'error', text: errorMsg });
        } else {
          swal('Terjadi kesalahan saat menyimpan data.', { icon: 'error' });
        }
      })
      .always(function(){
        $('#spinner-simpan-catatan').addClass('d-none');
        $('#text-simpan-catatan').removeClass('d-none');
        $('#btn-simpan-catatan').prop('disabled', false);
      });
  });
})();
</script>
@endpush
