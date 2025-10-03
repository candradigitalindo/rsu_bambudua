@extends('layouts.app')
@section('title','Hasil & Status Permintaan Lab')
@push('style')
<link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
@endpush
@section('content')
<div class="row gx-3">
  <div class="col-12 col-lg-10">
    <div class="card mb-3">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Hasil & Status Permintaan Lab</h5>
        <a href="{{ route('lab.requests.show', $req->id) }}" class="btn btn-sm btn-secondary">Kembali</a>
      </div>
      <div class="card-body">
        <form action="{{ route('lab.requests.update', $req->id) }}" method="POST">
          @csrf
          @method('PUT')
          <div class="mb-3">
            <small class="text-muted">RM</small>
            <div><strong>{{ $req->encounter->rekam_medis ?? '-' }}</strong> — {{ $req->encounter->name_pasien ?? '-' }}</div>
          </div>
          <div class="table-responsive">
            <table class="table table-sm align-middle">
              <thead><tr><th style="width:20%">Pemeriksaan</th><th>Hasil</th></tr></thead>
              <tbody>
                @foreach($req->items as $it)
                <tr>
                  <td>
                    <div class="fw-semibold">{{ $it->test_name }}</div>
                    <div class="small text-muted">{{ $it->jenisPemeriksaan?->name }}</div>
                  </td>
                  <td>
                    <input type="hidden" name="items[{{ $loop->index }}][id]" value="{{ $it->id }}" />
                    <div class="dynamic-fields" data-index="{{ $loop->index }}" data-test-id="{{ $it->test_id }}" data-payload='@json($it->result_payload)'>
                      @if(!$it->test_id)
                        <div class="row g-2">
                          <div class="col-md-4"><input type="text" name="items[{{ $loop->index }}][result_value]" class="form-control" placeholder="Nilai" value="{{ old('items.'.$loop->index.'.result_value', $it->result_value) }}"></div>
                          <div class="col-md-3"><input type="text" name="items[{{ $loop->index }}][result_unit]" class="form-control" placeholder="Satuan" value="{{ old('items.'.$loop->index.'.result_unit', $it->result_unit) }}"></div>
                          <div class="col-md-5"><input type="text" name="items[{{ $loop->index }}][result_reference]" class="form-control" placeholder="Rujukan" value="{{ old('items.'.$loop->index.'.result_reference', $it->result_reference) }}"></div>
                        </div>
                      @endif
                    </div>
                    <div class="mt-2">
                      <input type="text" name="items[{{ $loop->index }}][result_notes]" class="form-control" placeholder="Catatan" value="{{ old('items.'.$loop->index.'.result_notes', $it->result_notes) }}">
                    </div>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          <div class="row g-2 mt-2">
            <div class="col-md-4">
              <label class="form-label">Status</label>
              <select name="status" class="form-select" required>
                @foreach(['requested','collected','processing','completed','cancelled'] as $st)
                  <option value="{{ $st }}" {{ old('status', $req->status)===$st?'selected':'' }}>{{ ucfirst($st) }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-8">
              <label class="form-label">Catatan</label>
              <input type="text" name="notes" class="form-control" value="{{ old('notes', $req->notes) }}">
            </div>
          </div>
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
<script src="{{ asset('js/custom.js') }}"></script>
@endpush
@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function(){
    document.querySelectorAll('.dynamic-fields').forEach(function(container){
      const testId = container.dataset.testId;
      const idx = container.dataset.index;
      if (!testId) return;
      fetch('{{ route('observasi.getTemplateFields', ['id' => 'ID_PLACEHOLDER']) }}'.replace('ID_PLACEHOLDER', testId))
        .then(r=>r.json())
        .then(fields=>{
          const payload = (()=>{ try { return JSON.parse(container.dataset.payload || '{}') } catch(e){ return {} }})();
          const wrapper = document.createElement('div');
          wrapper.className = 'row g-2';
          (fields || []).sort((a,b)=> (a.order||0)-(b.order||0)).forEach(function(f){
            const col = document.createElement('div');
            col.className = 'col-md-4';
            const label = document.createElement('label');
            label.className = 'form-label small';
            label.textContent = f.field_label;
            let input;
            if (f.field_type === 'textarea') {
              input = document.createElement('textarea');
              input.className = 'form-control';
              input.rows = 2;
            } else {
              input = document.createElement('input');
              input.type = f.field_type === 'number' ? 'number' : 'text';
              input.className = 'form-control';
            }
            input.name = `items[${idx}][payload][${f.field_name}]`;
            input.placeholder = f.placeholder || '';
            const cur = payload ? payload[f.field_name] : '';
            if (cur !== undefined && cur !== null) input.value = cur;
            col.appendChild(label);
            col.appendChild(input);
            wrapper.appendChild(col);
          });
          container.prepend(wrapper);
        })
        .catch(()=>{});
    });
  });
</script>
@endpush
