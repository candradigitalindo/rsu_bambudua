@extends('layouts.app')
@section('title', 'Buat Permintaan Konsultasi Spesialis')
@push('style')
<link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush
@section('content')
<div class="row gx-3">
  <div class="col-12 col-lg-8">
    <div class="card mb-3">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Buat Permintaan Konsultasi Spesialis</h5>
        <a href="{{ route('konsultasi.index') }}" class="btn btn-sm btn-secondary">Kembali</a>
      </div>
      <div class="card-body">
        <form action="{{ route('konsultasi.store') }}" method="POST">
          @csrf
          <div class="mb-3">
            <label class="form-label">Encounter</label>
            <select id="encounter_id" class="form-select @error('encounter_id') is-invalid @enderror" name="encounter_id" required></select>
            @error('encounter_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label">Spesialis</label>
            <select class="form-select @error('specialist_id') is-invalid @enderror" name="specialist_id" required>
              <option value="" disabled selected>Pilih Spesialis</option>
              @foreach($spesialis as $s)
                <option value="{{ $s->id }}" {{ old('specialist_id')==$s->id?'selected':'' }}>{{ $s->name }}</option>
              @endforeach
            </select>
            @error('specialist_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label">Dokter (opsional)</label>
            <select class="form-select @error('assigned_doctor_id') is-invalid @enderror" name="assigned_doctor_id">
              <option value="">- Tidak ditentukan -</option>
              @foreach($doctors as $d)
                <option value="{{ $d->id }}" {{ old('assigned_doctor_id')==$d->id?'selected':'' }}>{{ $d->name }}</option>
              @endforeach
            </select>
            @error('assigned_doctor_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label">Alasan/Indikasi</label>
            <textarea class="form-control @error('reason') is-invalid @enderror" name="reason" rows="4" required>{{ old('reason') }}</textarea>
            @error('reason')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label">Jadwalkan (opsional)</label>
            <input type="datetime-local" class="form-control @error('scheduled_at') is-invalid @enderror" name="scheduled_at" value="{{ old('scheduled_at') }}">
            @error('scheduled_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ asset('js/custom.js') }}"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const $select = $('#encounter_id');
    $select.select2({
      placeholder: 'Pilih Encounter',
      allowClear: true,
      width: '100%',
      ajax: {
        url: '{{ route('konsultasi.encounters.search') }}',
        dataType: 'json',
        delay: 250,
        data: function (params) {
          return { q: params.term };
        },
        processResults: function (data) {
          return { results: data.results };
        }
      },
      minimumInputLength: 1
    });

    // Preselect old value after validation errors
    const oldId = @json(old('encounter_id'));
    if (oldId) {
      $.get({ url: '{{ route('konsultasi.encounters.search') }}', data: { id: oldId }})
        .done(function(resp){
          if (resp && resp.results && resp.results.length) {
            const item = resp.results[0];
            const option = new Option(item.text, item.id, true, true);
            $select.append(option).trigger('change');
          }
        });
    }
  });
</script>
@endpush
