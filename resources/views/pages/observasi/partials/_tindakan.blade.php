<div class="row gx-3">
  <div class="col-xxl-6 col-sm-6">
    <div class="card mb-1">
      <div class="card-header">
        <h5 class="card-title">Tindakan Medis</h5>
        <hr class="mb-2">
      </div>
      <div class="card-body">
        <div class="mb-3">
          <label class="form-label" for="a2">Jenis Tindakan</label>
          <div class="input-group">
            <select name="jenis_tindakan" id="jenis_tindakan" class="form-control">
              <option value="">Pilih Jenis Tindakan</option>
            </select>
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label" for="a2">Jumlah</label>
          <div class="input-group">
            <input type="number" class="form-control" id="qty" name="qty" value="{{ old('qty', 1) }}">
            <p class="text-danger">{{ $errors->first('qty') }}</p>
          </div>
        </div>
        <div class="d-flex gap-2 justify-content-end mt-4">
          <button type="submit" class="btn btn-primary" id="btn-tindakan-medis">
            <span class="btn-txt" id="text-tindakan-medis">Simpan</span>
            <span class="spinner-border spinner-border-sm d-none" id="spinner-tindakan-medis"></span>
          </button>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xxl-6 col-sm-12">
    <div class="card mb-3">
      <div class="card-header">
        <h5 class="card-title">Data Tindakan Medis</h5>
        <hr class="mb-2">
      </div>
      <div class="card-body">
        <div class="table-outer">
          <div class="table-responsive">
            <table class="table truncate m-0">
              <thead>
                <tr>
                  <th class="text-center">Aksi</th>
                  <th>Nama Tindakan</th>
                  <th>Qty</th>
                  <th>Harga</th>
                  <th>Sub Total</th>
                </tr>
              </thead>
              <tbody id="tbody-tindakan"></tbody>
              <tfoot>
                <tr>
                  <td colspan="4" class="text-end fw-bold">Total</td>
                  <td class="text-end">
                    <span id="total-harga" class="fw-bold">0</span>
                  </td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
(function(){
  const ENCOUNTER_ID = @json($observasi);
  const tindakanListUrl = "{{ route('observasi.getTindakan', ':id') }}".replace(':id', ENCOUNTER_ID);
  const tindakanEncounterUrl = "{{ route('observasi.getTindakanEncounter', ':id') }}".replace(':id', ENCOUNTER_ID);
  const tindakanPostUrl = "{{ route('observasi.postTindakanEncounter', ':id') }}".replace(':id', ENCOUNTER_ID);
  const tindakanDeleteUrl = function(id){ return "{{ route('observasi.deleteTindakanEncounter', ':id') }}".replace(':id', id); };

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

  function loadTindakanSelect(){
    $.ajax({ url: tindakanListUrl, type: 'GET', data: { _token: "{{ csrf_token() }}" } })
      .done(function(data){
        const select = $('#jenis_tindakan');
        select.empty();
        select.append('<option value="">Pilih Jenis Tindakan</option>');
        (data || []).forEach(function(item){
          select.append(`<option value="${item.id}">${item.name}</option>`);
        });
      });
  }

  function loadTindakanEncounter(){
    ensureFormat();
    $.ajax({ url: tindakanEncounterUrl, type: 'GET', data: { _token: "{{ csrf_token() }}" } })
      .done(function(data){
        const tbody = $('#tbody-tindakan');
        tbody.empty();
        let total_harga = 0;
        (data || []).forEach(function(item){
          const itemTotal = parseFloat(item.total_harga) || (parseFloat(item.tindakan_harga) * parseInt(item.qty));
          total_harga += (itemTotal || 0);
          tbody.append(`
            <tr>
              <td class="text-center">
                <button class="btn btn-danger btn-sm btn-hapus-tindakan" data-id="${item.id}">
                  <i class="bi bi-trash"></i> Hapus
                </button>
              </td>
              <td>${item.tindakan_name}</td>
              <td>${item.qty}</td>
              <td class="text-end">${formatRupiah(item.tindakan_harga)}</td>
              <td class="text-end">${formatRupiah(itemTotal)}</td>
            </tr>
          `);
        });
        $('#total-harga').text(formatRupiah(total_harga));
      });
  }

  // Init select2 and loaders on tab click
  $(document).on('click', '#tab-tindakan-medis', function(){
    if ($.fn.select2) {
      $('#jenis_tindakan').select2({ placeholder: 'Pilih Bahan Tindakan', allowClear: true, width: '100%' });
    }
    loadTindakanSelect();
    loadTindakanEncounter();
  });

  // Save tindakan
  $(document).on('click', '#btn-tindakan-medis', function(){
    const jenis_tindakan = $('#jenis_tindakan').val();
    const qty = $('#qty').val();
    if (!jenis_tindakan) { alert('Jenis Tindakan tidak boleh kosong'); return; }
    if (!qty) { alert('Jumlah tidak boleh kosong'); return; }
    $.ajax({
      url: tindakanPostUrl,
      type: 'POST',
      data: { jenis_tindakan: jenis_tindakan, qty: qty, _token: "{{ csrf_token() }}" },
      beforeSend: function(){ $('#spinner-tindakan-medis').removeClass('d-none'); $('#text-tindakan-medis').addClass('d-none'); },
    }).done(function(data){
      if (data.status == 200) {
        swal(data.message, { icon: 'success' });
        $('#tab-tindakan-medis').trigger('click');
      } else {
        swal('Terjadi kesalahan saat menyimpan data.', { icon: 'error' });
      }
    }).always(function(){ $('#spinner-tindakan-medis').addClass('d-none'); $('#text-tindakan-medis').removeClass('d-none'); });
  });

  // Delete tindakan
  $(document).on('click', '#tbody-tindakan .btn-hapus-tindakan', function(){
    const id = $(this).data('id');
    swal({ title: 'Apakah Anda yakin?', text: 'Data ini akan dihapus!', icon: 'warning', buttons: true, dangerMode: true })
      .then((willDelete)=>{
        if (!willDelete) return;
        $.ajax({ url: tindakanDeleteUrl(id), type: 'DELETE', data: { _token: "{{ csrf_token() }}" } })
          .done(function(data){
            swal(data.message || 'Berhasil dihapus.', { icon: (data.status == true ? 'success' : 'error') });
            $('#tab-tindakan-medis').trigger('click');
          }).fail(function(){ swal('Terjadi kesalahan saat menghapus data.', { icon: 'error' }); });
      });
  });
})();
</script>
@endpush
