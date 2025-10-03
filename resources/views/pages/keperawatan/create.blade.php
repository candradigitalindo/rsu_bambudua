@extends('layouts.app')
@section('title','Catat Asuhan Keperawatan')
@push('style')
<link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush
@section('content')
<div class="row gx-3">
  <div class="col-12 col-lg-10">
    <div class="card mb-3">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Catat Asuhan Keperawatan</h5>
        <a href="{{ route('keperawatan.index') }}" class="btn btn-sm btn-secondary">Kembali</a>
      </div>
      <div class="card-body">
        <form action="{{ route('keperawatan.store') }}" method="POST">
          @csrf
          <div class="row g-2">
            <div class="col-md-6">
              <label class="form-label">Encounter</label>
              <select id="encounter_id" name="encounter_id" class="form-select @error('encounter_id') is-invalid @enderror" required></select>
              @error('encounter_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-3">
              <label class="form-label">Shift</label>
              <select name="shift" class="form-select">
                <option value="">-</option>
                @foreach(['Pagi','Siang','Malam'] as $s)
                  <option value="{{ $s }}" {{ old('shift')==$s?'selected':'' }}>{{ $s }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">Perawat (opsional)</label>
              <select name="nurse_id" class="form-select">
                <option value="">- Saya -</option>
                @foreach($nurses as $n)
                  <option value="{{ $n->id }}" {{ old('nurse_id')==$n->id?'selected':'' }}>{{ $n->name }}</option>
                @endforeach
              </select>
            </div>
          </div>

          <hr/>
          <h6>Tanda Vital</h6>
          <div class="row g-2">
            <div class="col-md-2"><label class="form-label">Sistolik</label><input type="number" name="systolic" class="form-control" value="{{ old('systolic') }}" placeholder="mmHg"></div>
            <div class="col-md-2"><label class="form-label">Diastolik</label><input type="number" name="diastolic" class="form-control" value="{{ old('diastolic') }}" placeholder="mmHg"></div>
            <div class="col-md-2"><label class="form-label">Heart Rate</label><input type="number" name="heart_rate" class="form-control" value="{{ old('heart_rate') }}" placeholder="/menit"></div>
            <div class="col-md-2"><label class="form-label">Resp Rate</label><input type="number" name="resp_rate" class="form-control" value="{{ old('resp_rate') }}" placeholder="/menit"></div>
            <div class="col-md-2"><label class="form-label">Suhu (°C)</label><input type="number" step="0.1" name="temperature" class="form-control" value="{{ old('temperature') }}" placeholder="°C"></div>
            <div class="col-md-2"><label class="form-label">SpO2 (%)</label><input type="number" name="spo2" class="form-control" value="{{ old('spo2') }}" placeholder="%"></div>
          </div>
          <div class="row g-2 mt-1">
            <div class="col-md-2"><label class="form-label">Skala Nyeri</label><input type="number" min="0" max="10" name="pain_scale" class="form-control" value="{{ old('pain_scale') }}" placeholder="0-10"></div>
          </div>

          <hr/>
          <div class="mb-3">
            <label class="form-label">Diagnosa Keperawatan</label>
            <textarea name="nursing_diagnosis" class="form-control">{{ old('nursing_diagnosis') }}</textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Intervensi</label>
            <textarea name="interventions" class="form-control">{{ old('interventions') }}</textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Evaluasi</label>
            <textarea name="evaluation_notes" class="form-control">{{ old('evaluation_notes') }}</textarea>
          </div>
          <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('keperawatan.index') }}" class="btn btn-secondary">Batal</a>
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
  $(function(){
    const $sel = $('#encounter_id');
    $sel.select2({
      placeholder:'Pilih Encounter',
      allowClear:true,
      width:'100%',
      ajax:{
        url:'{{ route('konsultasi.encounters.search') }}',
        dataType:'json', delay:250,
        data:params=>({ q: params.term }),
        processResults:data=>({ results:data.results })
      },
      minimumInputLength:1
    });
    const oldId = @json(old('encounter_id'));
    if (oldId) {
      $.get({ url:'{{ route('konsultasi.encounters.search') }}', data:{ id:oldId } }).done(function(resp){
        if (resp && resp.results && resp.results.length){
          const it = resp.results[0];
          const opt = new Option(it.text, it.id, true, true);
          $sel.append(opt).trigger('change');
        }
      });
    }
  });
</script>
@endpush
