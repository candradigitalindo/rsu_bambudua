@extends('layouts.app')
@section('title','Perawatan Alat Medis')
@push('style')
  <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
  <link rel="stylesheet" href="{{ asset('vendor/daterange/daterange.css') }}">
  <link rel="stylesheet" href="{{ asset('vendor/datatables/dataTables.bs5.css') }}">
@endpush
@section('content')
<div class="row gx-3">
  <div class="col-12">
    <div class="card mb-3">
      <div class="card-header d-flex justify-content-between align-items-center">
        <div>
          <h5 class="card-title mb-0">Perawatan</h5>
          <small class="text-muted">{{ $equipment->name ?? '-' }}</small>
        </div>
        <a href="{{ route('inventory.equipment.show', $equipment->id) }}" class="btn btn-sm btn-secondary">Kembali</a>
      </div>
      <div class="card-body">
        @if (session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
        <form method="POST" action="{{ route('inventory.equipment.perawatan.store', $equipment->id) }}" class="row g-2 mb-3 align-items-end" enctype="multipart/form-data">
          @csrf
          <div class="col-md-2">
            <label class="form-label">Tanggal</label>
            <input type="date" name="date" class="form-control" required>
          </div>
          <div class="col-md-3">
            <label class="form-label">Jenis</label>
            <select name="type" class="form-select" required>
              <option value="preventive">Preventive</option>
              <option value="corrective">Corrective</option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Pelaksana</label>
            <input name="performed_by" class="form-control" required>
          </div>
          <div class="col-md-2">
            <label class="form-label">Biaya (Rp)</label>
            <input type="number" step="0.01" min="0" name="cost" class="form-control">
          </div>
          <div class="col-md-6">
            <label class="form-label">Catatan</label>
            <input name="notes" class="form-control">
          </div>
          <div class="col-md-6">
            <label class="form-label">Lampiran (PDF/JPG/PNG, maks 10MB)</label>
            <input type="file" name="attachment" accept="application/pdf,image/*" class="form-control">
          </div>

          <div class="col-md-12">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" value="1" id="post_to_finance" name="post_to_finance">
              <label class="form-check-label" for="post_to_finance">Catat ke Pengeluaran Operasional Keuangan</label>
            </div>
          </div>
          <div class="col-md-4">
            <label class="form-label">Kategori Pengeluaran</label>
            <select name="finance_category_id" class="form-select">
              <option value="">Pilih Kategori</option>
              @foreach(($categories ?? []) as $cat)
                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label">Cost Center</label>
            <select name="finance_cost_center_id" class="form-select">
              <option value="">Pilih Cost Center</option>
              @foreach(($costCenters ?? []) as $cc)
                <option value="{{ $cc->id }}">{{ $cc->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Metode Pembayaran</label>
            <select name="payment_method_code" class="form-select">
              <option value="">Pilih Metode</option>
              @foreach(($methods ?? []) as $pm)
                <option value="{{ $pm->code }}">{{ $pm->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Referensi (Opsional)</label>
            <input type="text" name="payment_reference" class="form-control" placeholder="No. bukti / referensi">
          </div>
          <div class="col-md-2">
            <button class="btn btn-primary w-100" type="submit">Simpan</button>
          </div>
        </form>

        <div class="table-responsive">
          <table class="table m-0" id="logTable">
            <thead><tr><th>Tanggal</th><th>Jenis</th><th>Pelaksana</th><th>Catatan</th><th class="text-end">Biaya</th><th>Lampiran</th><th class="text-end">Aksi</th></tr></thead>
            <tbody>
              @forelse($logs as $log)
                <tr>
                  <td>{{ $log->date?->format('Y-m-d') }}</td>
                  <td class="text-capitalize">{{ $log->type }}</td>
                  <td>{{ $log->performed_by }}</td>
                  <td>{{ $log->notes }}</td>
                  <td class="text-end">{{ 'Rp ' . number_format($log->cost ?? 0, 0, ',', '.') }}</td>
                  <td>
                    @if($log->attachment_path)
                      <a class="btn btn-sm btn-outline-secondary" href="{{ route('inventory.equipment.maintenance.download', $log->id) }}">Unduh</a>
                    @else
                      <span class="text-muted">-</span>
                    @endif
                  </td>
                  <td class="text-end">
                    <a href="{{ route('inventory.equipment.maintenance.edit', $log->id) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                    <form action="{{ route('inventory.equipment.maintenance.destroy', $log->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus log perawatan?')">
                      @csrf @method('DELETE')
                      <button class="btn btn-sm btn-danger" type="submit">Hapus</button>
                    </form>
                  </td>
                </tr>
              @empty
                <tr><td colspan="6" class="text-center text-muted">Belum ada data.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
@push('scripts')
  <script src="{{ asset('vendor/overlay-scroll/jquery.overlayScrollbars.min.js') }}"></script>
  <script src="{{ asset('vendor/overlay-scroll/custom-scrollbar.js') }}"></script>
  <script src="{{ asset('vendor/daterange/daterange.js') }}"></script>
  <script src="{{ asset('vendor/daterange/custom-daterange.js') }}"></script>
  <script src="{{ asset('vendor/datatables/dataTables.min.js') }}"></script>
  <script src="{{ asset('vendor/datatables/dataTables.bootstrap.min.js') }}"></script>
  <script src="{{ asset('vendor/datatables/custom/custom-datatables.js') }}"></script>
  <script src="{{ asset('js/custom.js') }}"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function(){
      if (window.jQuery && jQuery.fn.DataTable) {
        jQuery('#logTable').DataTable({ paging: true, searching: true });
      }
    });
  </script>
@endpush
