@extends('layouts.app')
@section('title','Buat Permintaan Lab')
@push('style')
<link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush
@section('content')
<div class="row gx-3">
  <div class="col-12 col-lg-10">
    <div class="card mb-3">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Buat Permintaan Laboratorium</h5>
        <a href="{{ route('lab.requests.index') }}" class="btn btn-sm btn-secondary">Kembali</a>
      </div>
      <div class="card-body">
        <form action="{{ route('lab.requests.store') }}" method="POST" id="labCreateForm">
          @csrf
          <div class="mb-3">
            <label class="form-label">Encounter</label>
            <select id="encounter_id" name="encounter_id" class="form-select @error('encounter_id') is-invalid @enderror" required></select>
            @error('encounter_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label">Catatan (opsional)</label>
            <textarea name="notes" class="form-control">{{ old('notes') }}</textarea>
          </div>
          <hr/>
          <h6>Item Pemeriksaan</h6>
          <div id="testsContainer"></div>
          <button type="button" class="btn btn-sm btn-outline-primary" id="btnAddTest">+ Tambah Pemeriksaan</button>
          <div class="d-flex justify-content-end gap-2 mt-3">
            <button type="submit" class="btn btn-primary">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
@push('scripts')
<script src="{{ asset('vendor/overlay-scroll/jquery.overlayScrollbars.min.js') }}"></script>
<script src="{{ asset('vendor/overlay-scroll/custom-scrollbar.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ asset('js/custom.js') }}"></script>
<script>
  function addTestRow(name='', price=''){
    const idx = document.querySelectorAll('.test-row').length;
    const row = document.createElement('div');
    row.className = 'row g-2 test-row align-items-end mb-2';
    row.innerHTML = `
      <div class="col-md-7">
        <label class="form-label">Nama Pemeriksaan</label>
        <select name="tests[${idx}][id]" class="form-select test-name" required></select>
        <input type="hidden" name="tests[${idx}][name]" class="test-name-hidden" />
      </div>
      <div class="col-md-3">
        <label class="form-label">Harga (Rp)</label>
        <input type="number" min="0" name="tests[${idx}][price]" class="form-control" value="${price}" readonly />
      </div>
      <div class="col-md-2">
        <button type="button" class="btn btn-outline-danger btnRemoveTest">Hapus</button>
      </div>`;
    document.getElementById('testsContainer').appendChild(row);
    const $sel = $(row).find('.test-name');
    $sel.select2({
      placeholder:'Pilih pemeriksaan (dari master)',
      allowClear:true,
      width:'100%',
      tags: false,
      ajax:{ url:'{{ route('lab.tests.search') }}', dataType:'json', delay:250,
        data: params=>({ q: params.term }),
        processResults: data=>({ results:data.results })
      }
    });
    if (name) {
      const opt = new Option(name, name, true, true);
      $sel.append(opt).trigger('change');
    }
    // Update hidden name and price when selecting a test
    $sel.on('select2:select', function(evt){
      const data = evt.params.data || {};
      $(row).find('.test-name-hidden').val(data.text || '');
      const priceInput = $(row).find('input[name$="[price]"]');
      if (priceInput.length) priceInput.val(data.price != null ? data.price : '');
    });
    $sel.on('select2:clear', function(){
      $(row).find('.test-name-hidden').val('');
      const priceInput = $(row).find('input[name$="[price]"]');
      if (priceInput.length) priceInput.val('');
    });
    // Initialize hidden name
    $(row).find('.test-name-hidden').val($sel.find('option:selected').text());
    $(row).on('click','.btnRemoveTest', ()=> row.remove());
  }

  document.addEventListener('DOMContentLoaded', function(){
    // Encounter select2
    const $enc = $('#encounter_id');
    $enc.select2({ placeholder:'Pilih Encounter', allowClear:true, width:'100%', minimumInputLength:1,
      ajax:{ url:'{{ route('lab.encounters.search') }}', dataType:'json', delay:250,
        data: params=>({ q: params.term }),
        processResults: data=>({ results:data.results })
      }
    });
    const oldEnc = @json(old('encounter_id'));
    const qsEnc = @json(request('encounter_id'));
    function preselectEncounter(encId){
      if (!encId) return;
      $.get({ url:'{{ route('lab.encounters.search') }}', data:{ id: encId }}).done(function(resp){
        if (resp && resp.results && resp.results.length){
          const it = resp.results[0];
          const opt = new Option(it.text, it.id, true, true);
          $enc.append(opt).trigger('change');
        }
      });
    }
    if (oldEnc) { preselectEncounter(oldEnc); }
    else if (qsEnc) { preselectEncounter(qsEnc); }
    // Tests
    document.getElementById('btnAddTest').addEventListener('click', ()=> addTestRow());
    // Add one default row
    addTestRow();
  });
</script>
@endpush
