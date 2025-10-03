@extends('layouts.app')
@section('title', 'Kelola Konsultasi Spesialis')
@push('style')
<link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
@endpush
@section('content')
<div class="row gx-3">
  <div class="col-12 col-lg-8">
    <div class="card mb-3">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Kelola Konsultasi Spesialis</h5>
        <a href="{{ route('konsultasi.index') }}" class="btn btn-sm btn-secondary">Kembali</a>
      </div>
      <div class="card-body">
        <div class="mb-3">
          <small class="text-muted">Rekam Medis:</small>
          <div><strong>{{ $consultation->encounter->rekam_medis ?? '-' }}</strong> — {{ $consultation->encounter->name_pasien ?? '-' }}</div>
        </div>
        <form action="{{ route('konsultasi.update', $consultation->id) }}" method="POST">
          @csrf
          @method('PUT')
          <div class="mb-3">
            <label class="form-label">Spesialis</label>
            <select class="form-select @error('specialist_id') is-invalid @enderror" name="specialist_id" required>
              @foreach($spesialis as $s)
                <option value="{{ $s->id }}" {{ old('specialist_id', $consultation->specialist_id) == $s->id ? 'selected':'' }}>{{ $s->name }}</option>
              @endforeach
            </select>
            @error('specialist_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label">Dokter (opsional)</label>
            <select class="form-select @error('assigned_doctor_id') is-invalid @enderror" name="assigned_doctor_id">
              <option value="">- Tidak ditentukan -</option>
              @foreach($doctors as $d)
                <option value="{{ $d->id }}" {{ old('assigned_doctor_id', $consultation->assigned_doctor_id) == $d->id ? 'selected':'' }}>{{ $d->name }}</option>
              @endforeach
            </select>
            @error('assigned_doctor_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label">Alasan/Indikasi</label>
            <textarea class="form-control @error('reason') is-invalid @enderror" name="reason" rows="4" required>{{ old('reason', $consultation->reason) }}</textarea>
            @error('reason')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="row g-2">
            <div class="col-md-6">
              <label class="form-label">Jadwalkan</label>
              <input type="datetime-local" class="form-control @error('scheduled_at') is-invalid @enderror" name="scheduled_at" value="{{ old('scheduled_at', optional($consultation->scheduled_at)->format('Y-m-d\TH:i')) }}">
              @error('scheduled_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
              <label class="form-label">Status</label>
              <select class="form-select @error('status') is-invalid @enderror" name="status" required>
                @foreach(['requested','scheduled','completed','cancelled'] as $st)
                  <option value="{{ $st }}" {{ old('status', $consultation->status)===$st?'selected':'' }}>{{ ucfirst($st) }}</option>
                @endforeach
              </select>
              @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>
          <div class="mb-3 mt-2">
            <label class="form-label">Hasil Konsultasi (opsional)</label>
            <textarea class="form-control @error('result_notes') is-invalid @enderror" name="result_notes" rows="4">{{ old('result_notes', $consultation->result_notes) }}</textarea>
            @error('result_notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('konsultasi.index') }}" class="btn btn-secondary">Batal</a>
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
