@extends('layouts.app')
@section('title','Edit Log Perawatan/Kalibrasi')
@push('style')
  <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
@endpush
@section('content')
<div class="row gx-3">
  <div class="col-12">
    <div class="card mb-3">
      <div class="card-header d-flex justify-content-between align-items-center">
        <div>
          <h5 class="card-title mb-0">Edit Log {{ $log->type === 'calibration' ? 'Kalibrasi' : 'Perawatan' }}</h5>
          <small class="text-muted">{{ $equipment->name ?? '-' }}</small>
        </div>
        <a href="{{ $log->type === 'calibration' ? route('inventory.equipment.kalibrasi', $equipment->id) : route('inventory.equipment.perawatan', $equipment->id) }}" class="btn btn-sm btn-secondary">Kembali</a>
      </div>
      <div class="card-body">
        @if (session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
        <form method="POST" action="{{ route('inventory.equipment.maintenance.update', $log->id) }}" class="row g-2 align-items-end" enctype="multipart/form-data">
          @csrf
          @method('PUT')
          <div class="col-md-2">
            <label class="form-label">Tanggal</label>
            <input type="date" name="date" class="form-control" value="{{ $log->date?->format('Y-m-d') }}" required>
          </div>
          <div class="col-md-3">
            <label class="form-label">Jenis</label>
            @if($log->type === 'calibration')
              <input type="text" class="form-control" value="Kalibrasi" disabled>
            @else
              <select name="type" class="form-select" required>
                <option value="preventive" @selected($log->type==='preventive')>Preventive</option>
                <option value="corrective" @selected($log->type==='corrective')>Corrective</option>
              </select>
            @endif
          </div>
          <div class="col-md-3">
            <label class="form-label">Pelaksana</label>
            <input name="performed_by" class="form-control" value="{{ $log->performed_by }}" required>
          </div>
          <div class="col-md-2">
            <label class="form-label">Biaya (Rp)</label>
            <input type="number" step="0.01" min="0" name="cost" class="form-control" value="{{ $log->cost }}">
          </div>
          <div class="col-md-6">
            <label class="form-label">Catatan</label>
            <input name="notes" class="form-control" value="{{ $log->notes }}">
          </div>
          <div class="col-md-6">
            <label class="form-label">Lampiran (PDF/JPG/PNG, maks 10MB)</label>
            <input type="file" name="attachment" accept="application/pdf,image/*" class="form-control">
            @if($log->attachment_path)
              <small class="text-muted">Lampiran saat ini: {{ $log->attachment_name }} (<a href="{{ route('inventory.equipment.maintenance.download', $log->id) }}">Unduh</a>)</small>
            @endif
          </div>

          <div class="col-md-12">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" value="1" id="post_to_finance" name="post_to_finance">
              <label class="form-check-label" for="post_to_finance">Perbarui/Catat ke Pengeluaran Operasional Keuangan</label>
            </div>
          </div>
          <div class="col-md-4">
            <label class="form-label">Kategori Pengeluaran</label>
            <select name="finance_category_id" class="form-select">
              <option value="">Pilih Kategori</option>
              @foreach(($categories ?? []) as $cat)
                <option value="{{ $cat->id }}" @selected(($expense->expense_category_id ?? null) === $cat->id)>{{ $cat->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label">Cost Center</label>
            <select name="finance_cost_center_id" class="form-select">
              <option value="">Pilih Cost Center</option>
              @foreach(($costCenters ?? []) as $cc)
                <option value="{{ $cc->id }}" @selected(($expense->cost_center_id ?? null) === $cc->id)>{{ $cc->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Metode Pembayaran</label>
            <select name="payment_method_code" class="form-select">
              <option value="">Pilih Metode</option>
              @foreach(($methods ?? []) as $pm)
                <option value="{{ $pm->code }}" @selected(($expense->payment_method_code ?? null) === $pm->code)>{{ $pm->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Referensi (Opsional)</label>
            <input type="text" name="payment_reference" class="form-control" placeholder="No. bukti / referensi" value="{{ $expense->payment_reference ?? '' }}">
          </div>
          <div class="col-md-2">
            <button class="btn btn-primary w-100" type="submit">Simpan</button>
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
