@extends('layouts.app')
@section('title','Edit Asuhan Keperawatan')
@push('style')
<link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
@endpush
@section('content')
<div class="row gx-3">
  <div class="col-12 col-lg-10">
    <div class="card mb-3">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Edit Asuhan Keperawatan</h5>
        <a href="{{ route('keperawatan.index') }}" class="btn btn-sm btn-secondary">Kembali</a>
      </div>
      <div class="card-body">
        <form action="{{ route('keperawatan.update', $record->id) }}" method="POST">
          @csrf
          @method('PUT')
          <div class="row g-2">
            <div class="col-md-3">
              <label class="form-label">Shift</label>
              <select name="shift" class="form-select">
                <option value="">-</option>
                @foreach(['Pagi','Siang','Malam'] as $s)
                  <option value="{{ $s }}" {{ old('shift',$record->shift)==$s?'selected':'' }}>{{ $s }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">Perawat (opsional)</label>
              <select name="nurse_id" class="form-select">
                <option value="">- Tidak diubah -</option>
                @foreach($nurses as $n)
                  <option value="{{ $n->id }}" {{ old('nurse_id',$record->nurse_id)==$n->id?'selected':'' }}>{{ $n->name }}</option>
                @endforeach
              </select>
            </div>
          </div>

          <hr/>
          <h6>Tanda Vital</h6>
          <div class="row g-2">
            <div class="col-md-2"><label class="form-label">Sistolik</label><input type="number" name="systolic" class="form-control" value="{{ old('systolic',$record->systolic) }}" placeholder="mmHg"></div>
            <div class="col-md-2"><label class="form-label">Diastolik</label><input type="number" name="diastolic" class="form-control" value="{{ old('diastolic',$record->diastolic) }}" placeholder="mmHg"></div>
            <div class="col-md-2"><label class="form-label">Heart Rate</label><input type="number" name="heart_rate" class="form-control" value="{{ old('heart_rate',$record->heart_rate) }}" placeholder="/menit"></div>
            <div class="col-md-2"><label class="form-label">Resp Rate</label><input type="number" name="resp_rate" class="form-control" value="{{ old('resp_rate',$record->resp_rate) }}" placeholder="/menit"></div>
            <div class="col-md-2"><label class="form-label">Suhu (°C)</label><input type="number" step="0.1" name="temperature" class="form-control" value="{{ old('temperature',$record->temperature) }}" placeholder="°C"></div>
            <div class="col-md-2"><label class="form-label">SpO2 (%)</label><input type="number" name="spo2" class="form-control" value="{{ old('spo2',$record->spo2) }}" placeholder="%"></div>
          </div>
          <div class="row g-2 mt-1">
            <div class="col-md-2"><label class="form-label">Skala Nyeri</label><input type="number" min="0" max="10" name="pain_scale" class="form-control" value="{{ old('pain_scale',$record->pain_scale) }}" placeholder="0-10"></div>
          </div>

          <hr/>
          <div class="mb-3">
            <label class="form-label">Diagnosa Keperawatan</label>
            <textarea name="nursing_diagnosis" class="form-control">{{ old('nursing_diagnosis',$record->nursing_diagnosis) }}</textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Intervensi</label>
            <textarea name="interventions" class="form-control">{{ old('interventions',$record->interventions) }}</textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Evaluasi</label>
            <textarea name="evaluation_notes" class="form-control">{{ old('evaluation_notes',$record->evaluation_notes) }}</textarea>
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
<script src="{{ asset('js/custom.js') }}"></script>
@endpush
