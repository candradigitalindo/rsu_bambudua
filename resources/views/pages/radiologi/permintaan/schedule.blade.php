@extends('layouts.app')

@section('title', 'Jadwalkan Pemeriksaan Radiologi')

@section('content')
<div class="row">
  <div class="col-12 col-lg-8">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Jadwalkan Pemeriksaan Radiologi</h5>
        <a href="{{ route('radiologi.requests.show', $req->id) }}" class="btn btn-light btn-sm">Kembali</a>
      </div>
      <div class="card-body">
        @if(session('error'))
          <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
          <div class="alert alert-danger">
            <ul class="m-0">
              @foreach ($errors->all() as $e)
                <li>{{ $e }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <div class="mb-3">
          <div class="text-muted small">Pasien</div>
          <div class="fw-semibold">{{ $req->pasien->rekam_medis ?? '-' }} — {{ $req->pasien->name ?? '-' }}</div>
        </div>
        <div class="mb-3">
          <div class="text-muted small">Pemeriksaan</div>
          <div class="fw-semibold">{{ $req->jenis->name ?? '-' }}</div>
        </div>

        <form method="POST" action="{{ route('radiologi.requests.schedule.store', $req->id) }}">
          @csrf
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Tanggal & Waktu Mulai</label>
              <input type="datetime-local" name="scheduled_start" class="form-control" value="{{ old('scheduled_start') }}" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Perkiraan Selesai (opsional)</label>
              <input type="datetime-local" name="scheduled_end" class="form-control" value="{{ old('scheduled_end') }}">
            </div>
            <div class="col-md-6">
              <label class="form-label">Modality</label>
              <select name="modality" class="form-select">
                <option value="">-- Pilih --</option>
                @foreach(['X-ray','USG','CT','MRI','Fluoroscopy'] as $m)
                  <option value="{{ $m }}" @selected(old('modality')===$m)>{{ $m }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Ruang</label>
              <input type="text" name="room" class="form-control" value="{{ old('room') }}" placeholder="Mis. XRay-1 / USG-2">
            </div>
            <div class="col-md-6">
              <label class="form-label">Radiografer (opsional)</label>
              <select name="radiographer_id" class="form-select">
                <option value="">-- Pilih Radiografer --</option>
                @foreach($radiographers as $r)
                  <option value="{{ $r->id }}" @selected(old('radiographer_id')==$r->id)>{{ $r->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Prioritas</label>
              <select name="priority" class="form-select" required>
                @foreach(['routine'=>'Routine','urgent'=>'Urgent','stat'=>'STAT'] as $k=>$v)
                  <option value="{{ $k }}" @selected(old('priority',$k)===$k)>{{ $v }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-12">
              <label class="form-label">Persiapan (Preparation)</label>
              <textarea name="preparation" class="form-control" rows="3" placeholder="Instruksi puasa, hidrasi, alergi, kontras, dll.">{{ old('preparation') }}</textarea>
            </div>
            <div class="col-12">
              <label class="form-label">Catatan</label>
              <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
            </div>
          </div>
          <div class="d-flex gap-2 mt-3">
            <a href="{{ route('radiologi.requests.show', $req->id) }}" class="btn btn-light">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan Jadwal</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
