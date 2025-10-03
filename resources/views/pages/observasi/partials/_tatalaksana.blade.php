<div class="row gx-3">
  <div class="col-xxl-6 col-sm-6">
    <div class="card mb-1">
      <div class="card-header">
        <h5 class="card-title">Resep Obat</h5>
        <hr class="mb-2">
      </div>
      <div class="card-body">
        <div class="mb-3">
          <label class="form-label" for="a2">Masa Resep (Hari)</label>
          <form method="GET" class="mb-3">
            <div class="input-group">
              <input type="text" name="masa_pemakaian_hari" class="form-control" placeholder="Jumlah Hari Resep" id="masa_pemakaian_hari">
              @if (auth()->user()->role != 3)
              <button class="btn btn-primary" type="submit" id="btn-buat-resep">
                <span id="text-buat-resep">Buat Resep</span>
                <span class="spinner-border spinner-border-sm d-none" id="spinner-buat-resep" role="status" aria-hidden="true"></span>
              </button>
              @endif
            </div>
          </form>
        </div>
        <hr>
        <div class="mb-3 d-none" id="resep">
          <label class="form-label" for="a2">Obat</label>
          <div class="input-group">
            <select name="product_apotek_id" id="product_apotek_id" class="form-control">
              <option value="">Pilih Obat</option>
            </select>
            <input type="hidden" name="product_apotek_id" id="product_apotek_id_hidden" value="{{ old('product_apotek_id') }}">
          </div>
          <div class="row gx-2 mt-3">
            <div class="col-md-6">
              <label class="form-label" for="qty_obat">Jumlah</label>
              <input type="number" class="form-control" id="qty_obat" name="qty_obat" value="{{ old('qty_obat', 1) }}">
              <p class="text-danger">{{ $errors->first('qty_obat') }}</p>
            </div>
            <div class="col-md-6">
              <label class="form-label" for="aturan_pakai_jumlah">Aturan Pakai</label>
              <div class="input-group">
                <input type="number" class="form-control" id="aturan_pakai_jumlah" value="1" min="1">
                <select class="form-select" id="aturan_pakai_frekuensi">
                  <option value="x Sehari">x Sehari</option>
                  <option value="x Seminggu">x Seminggu</option>
                  <option value="x Sebulan">x Sebulan</option>
                  <option value="x Setahun">x Setahun</option>
                  <option value="Jika Perlu">Jika Perlu</option>
                </select>
              </div>
            </div>
          </div>
          <div class="mt-2">
            <label class="form-label">Keterangan Tambahan</label>
            <select class="form-select" id="aturan_pakai_tambahan">
              <option value="">- Tidak ada -</option>
              <option value="Sebelum Makan">Sebelum Makan</option>
              <option value="Sesudah Makan">Sesudah Makan</option>
            </select>
          </div>
          <div class="mt-3">
            <label class="form-label">Waktu Pemberian</label>
            <div class="d-flex flex-wrap gap-3">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" value="Pagi" id="waktu_pagi">
                <label class="form-check-label" for="waktu_pagi">Pagi</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" value="Siang" id="waktu_siang">
                <label class="form-check-label" for="waktu_siang">Siang</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" value="Malam" id="waktu_malam">
                <label class="form-check-label" for="waktu_malam">Malam</label>
              </div>
            </div>
          </div>
          <div class="d-flex gap-2 justify-content-end mt-4">
            <button type="submit" class="btn btn-primary" @if (auth()->user()->role == 3) disabled @endif id="btn-tambah-obat">
              <span class="btn-txt" id="text-tambah-obat">Tambah Obat</span>
              <span class="spinner-border spinner-border-sm d-none" id="spinner-tambah-obat"></span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xxl-6 col-sm-12">
    <div class="card mb-3">
      <div class="card-header">
        <h5 class="card-title">List Obat Resep <span id="kode_resep"></span></h5>
        <hr class="mb-2">
      </div>
      <div class="card-body">
        <div class="table-outer">
          <div class="table-responsive">
            <table class="table truncate m-0">
              <thead>
                <tr>
                  <th class="text-center">Aksi</th>
                  <th>Nama Obat</th>
                  <th>Jumlah</th>
                  <th>Aturan Pakai</th>
                  <th>Harga</th>
                  <th>Subtotal</th>
                </tr>
              </thead>
              <tbody id="tbody-resep"></tbody>
              <tfoot>
                <tr>
                  <td colspan="5" class="text-end fw-bold">Total</td>
                  <td class="text-end fw-bold" id="total-resep">Rp. 0</td>
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
  const resepGetUrl = "{{ route('observasi.getResep', ':id') }}".replace(':id', ENCOUNTER_ID);
  const resepPostUrl = "{{ route('observasi.postResep', ':id') }}".replace(':id', ENCOUNTER_ID);
  const resepDetailPostUrl = "{{ route('observasi.postResepDetail', ':id') }}".replace(':id', ENCOUNTER_ID);
  const resepDetailDeleteUrl = function(id){ return "{{ route('observasi.deleteResepDetail', ':id') }}".replace(':id', id); };
  const produkAjaxUrl = "{{ route('observasi.getProdukApotek', $observasi) }}";

  function ensureFormat(){
    if (typeof window.formatRupiah !== 'function') {
      window.formatRupiah = function(angka, prefix){
        if (angka === null || angka === undefined) return (prefix || '') + '0';
        let integer_part = Math.floor(parseFloat(angka)).toString();
        let sisa = integer_part.length % 3;
        let rupiah = integer_part.substr(0, sisa);
        let ribuan = integer_part.substr(sisa).match(/\d{3}/gi);
        if (ribuan) { let separator = sisa ? '.' : ''; rupiah += separator + ribuan.join('.'); }
        return (prefix ? (rupiah ? prefix + ' ' + rupiah : prefix + ' 0') : rupiah);
      }
    }
  }

  function initProdukSelect(){
    if (!$.fn.select2) return;
    $('#product_apotek_id').select2({
      placeholder: 'Pilih Obat', allowClear: true, width: '100%',
      ajax: {
        url: produkAjaxUrl, dataType: 'json', delay: 250,
        data: function(params){ return { search: params.term }; },
        processResults: function(data){
          const rows = Array.isArray(data) ? data : (Array.isArray(data.data) ? data.data : []);
          return { results: rows.map(function(item){
            return { id: item.id, text: item.name + (item.harga ? ' - [' + formatRupiah(item.harga, 'Rp.') + ']' : '') };
          }) };
        }, cache: true
      }
    });
  }

  function loadResep(){
    ensureFormat();
    $.ajax({ url: resepGetUrl, type: 'GET', data: { _token: "{{ csrf_token() }}" } })
      .done(function(data){
        if (data && data.id) {
          $('#resep').removeClass('d-none');
          $('#kode_resep').text("[" + data.kode_resep + "] " + (data.masa_pemakaian_hari || '') + (data.masa_pemakaian_hari ? ' hari' : ''));
        } else {
          $('#resep').addClass('d-none');
          $('#kode_resep').text('');
        }
        const tbody = $('#tbody-resep');
        tbody.empty();
        let total = 0;
        if (data && data.details) {
          (data.details || []).forEach(function(item){
            tbody.append(`
              <tr>
                <td class="text-center">
                  <button class="btn btn-danger btn-sm btn-hapus-resep" data-id="${item.id}"><i class="bi bi-trash"></i> Hapus</button>
                </td>
                <td>${item.nama_obat}</td>
                <td>${item.qty}</td>
                <td>${item.aturan_pakai}</td>
                <td class="text-end">${formatRupiah(item.harga, 'Rp.')}</td>
                <td class="text-end">${formatRupiah(item.total_harga, 'Rp.')}</td>
              </tr>
            `);
            total += parseInt(item.total_harga || 0);
          });
        }
        $('#total-resep').text(formatRupiah(total, 'Rp.'));
      });
  }

  // Init when Tatalaksana tab is shown
  $(document).on('shown.bs.tab', 'a[data-bs-toggle="tab"]', function(e){
    const target = $(e.target).attr('href');
    if (target === '#tatalaksana') {
      initProdukSelect();
      loadResep();
    }
  });
  // Also init immediately if the tab is already active on load
  $(function(){ if ($('#tatalaksana').hasClass('show')) { initProdukSelect(); loadResep(); } });

  // Create resep
  $(document).on('click', '#btn-buat-resep', function(e){
    e.preventDefault();
    const hari = $('#masa_pemakaian_hari').val();
    if (!hari) { alert('Jumlah hari tidak boleh kosong'); return; }
    $('#spinner-buat-resep').removeClass('d-none');
    $('#text-buat-resep').addClass('d-none');
    $('#btn-buat-resep').prop('disabled', true);
    $.ajax({ url: resepPostUrl, type: 'POST', data: { _token: "{{ csrf_token() }}", masa_pemakaian_hari: hari } })
      .done(function(data){
        swal(data.message || 'Resep dibuat', { icon: (data.status == 200 ? 'success' : 'error') });
        loadResep();
      })
      .fail(function(xhr){
        const msg = (xhr.responseJSON && (xhr.responseJSON.message || xhr.responseJSON.error)) || 'Gagal membuat resep.';
        swal(msg, { icon: 'error' });
      })
      .always(function(){
        $('#spinner-buat-resep').addClass('d-none');
        $('#text-buat-resep').removeClass('d-none');
        $('#btn-buat-resep').prop('disabled', false);
      });
  });

  // Add resep detail
  $(document).on('click', '#btn-tambah-obat', function(e){
    e.preventDefault();
    const product_apotek_id = $('#product_apotek_id').val();
    const qty_obat = $('#qty_obat').val();
    const aturan_jumlah = $('#aturan_pakai_jumlah').val();
    const aturan_frekuensi = $('#aturan_pakai_frekuensi').val();
    const aturan_tambahan = $('#aturan_pakai_tambahan').val();
    let waktu_pemberian = [];
    if ($('#waktu_pagi').is(':checked')) waktu_pemberian.push('Pagi');
    if ($('#waktu_siang').is(':checked')) waktu_pemberian.push('Siang');
    if ($('#waktu_malam').is(':checked')) waktu_pemberian.push('Malam');
    if (!product_apotek_id) { alert('Obat tidak boleh kosong'); return; }
    if (!qty_obat || parseInt(qty_obat) <= 0) { alert('Jumlah tidak boleh kosong'); return; }
    if (!aturan_jumlah || parseInt(aturan_jumlah) <= 0) { alert('Aturan pakai harus diisi dengan benar.'); return; }
    let aturan_pakai = (aturan_frekuensi === 'Jika Perlu') ? 'Jika Perlu' : `${aturan_jumlah} ${aturan_frekuensi}`;
    if (aturan_tambahan) aturan_pakai += ` ${aturan_tambahan}`;
    if (waktu_pemberian.length > 0) aturan_pakai += ` (${waktu_pemberian.join(', ')})`;
    $('#spinner-tambah-obat').removeClass('d-none');
    $('#text-tambah-obat').addClass('d-none');
    $('#btn-tambah-obat').prop('disabled', true);
    $.ajax({ url: resepDetailPostUrl, type: 'POST', data: { _token: "{{ csrf_token() }}", product_apotek_id, qty_obat, aturan_pakai } })
      .done(function(data){
        swal(data.message || 'Item ditambahkan', { icon: (data.status == 200 ? 'success' : 'error') });
        loadResep();
      })
      .fail(function(xhr){
        const msg = (xhr.responseJSON && (xhr.responseJSON.message || xhr.responseJSON.error)) || 'Gagal menambah obat.';
        swal(msg, { icon: 'error' });
      })
      .always(function(){
        $('#spinner-tambah-obat').addClass('d-none');
        $('#text-tambah-obat').removeClass('d-none');
        $('#btn-tambah-obat').prop('disabled', false);
      });
  });

  // Delete resep detail
  $(document).on('click', '#tbody-resep .btn-hapus-resep', function(){
    const id = $(this).data('id');
    swal({ title: 'Apakah Anda yakin?', text: 'Data ini akan dihapus!', icon: 'warning', buttons: true, dangerMode: true })
      .then((willDelete)=>{
        if (!willDelete) return;
        $.ajax({ url: resepDetailDeleteUrl(id), type: 'DELETE', data: { _token: "{{ csrf_token() }}" } })
          .done(function(data){
            swal(data.message || 'Berhasil dihapus.', { icon: (data.status == true ? 'success' : 'error') });
            loadResep();
          }).fail(function(){ swal('Terjadi kesalahan saat menghapus data.', { icon: 'error' }); });
      });
  });
})();
</script>
@endpush
