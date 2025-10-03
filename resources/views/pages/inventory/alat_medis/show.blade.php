@extends('layouts.app')
@section('title','Detail Alat Medis')
@push('style')
  <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
  <link rel="stylesheet" href="{{ asset('vendor/daterange/daterange.css') }}">
  <link rel="stylesheet" href="{{ asset('vendor/datatables/dataTables.bs5.css') }}">
@endpush
@section('content')
<div class="row gx-3">
  <div class="col-lg-6 col-12">
    <div class="card mb-3">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Informasi Alat</h5>
        <a href="{{ route('inventory.equipment.edit', $equipment['id']) }}" class="btn btn-sm btn-primary"><i class="ri-edit-2-line"></i> Edit</a>
      </div>
      <div class="card-body">
        <div class="row g-2">
          <div class="col-6"><small class="text-muted">Nama</small><div class="fw-semibold">{{ $equipment['name'] }}</div></div>
          <div class="col-6"><small class="text-muted">Kategori</small><div class="fw-semibold">{{ $equipment['category'] }}</div></div>
          <div class="col-6"><small class="text-muted">Serial</small><div class="fw-semibold">{{ $equipment['serial_number'] }}</div></div>
          <div class="col-6"><small class="text-muted">Asset Tag</small><div class="fw-semibold">{{ $equipment['asset_tag'] }}</div></div>
          <div class="col-6"><small class="text-muted">Lokasi</small><div class="fw-semibold">{{ $equipment['location'] }}</div></div>
          <div class="col-6"><small class="text-muted">Vendor</small><div class="fw-semibold">{{ $equipment['vendor'] }}</div></div>
          <div class="col-6"><small class="text-muted">Status</small><div><span class="badge bg-secondary text-uppercase">{{ $equipment['status'] }}</span></div></div>
          <div class="col-6"><small class="text-muted">Pembelian</small><div class="fw-semibold">{{ $equipment['purchase_date'] }}</div></div>
          <div class="col-6"><small class="text-muted">Garansi</small><div class="fw-semibold">{{ $equipment['warranty_expiry'] }}</div></div>
          <div class="col-6"><small class="text-muted">Kalibrasi Terakhir</small><div class="fw-semibold">{{ $equipment['last_calibration_date'] }}</div></div>
          <div class="col-6"><small class="text-muted">Kalibrasi Berikutnya</small><div class="fw-semibold">{{ $equipment['next_calibration_due'] }}</div></div>
          <div class="col-12"><small class="text-muted">Catatan</small><div class="fw-semibold">{{ $equipment['notes'] }}</div></div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-6 col-12">
    <div class="card mb-3">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Perawatan & Kalibrasi</h5>
        <div class="d-flex gap-2">
          <a href="{{ route('inventory.equipment.perawatan', $equipment['id']) }}" class="btn btn-sm btn-outline-primary"><i class="ri-tools-line"></i> Perawatan</a>
          <a href="{{ route('inventory.equipment.kalibrasi', $equipment['id']) }}" class="btn btn-sm btn-outline-secondary"><i class="ri-wrench-line"></i> Kalibrasi</a>
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table m-0">
            <thead>
              <tr>
                <th>Tanggal</th>
                <th>Jenis</th>
                <th>Pelaksana</th>
                <th class="text-end">Biaya</th>
                <th>Lampiran</th>
              </tr>
            </thead>
            <tbody>
              @forelse($maintenance as $log)
                <tr>
                  <td>{{ $log['date'] }}</td>
                  <td class="text-capitalize">{{ $log['type'] }}</td>
                  <td>{{ $log['performed_by'] }}</td>
                  <td class="text-end">{{ 'Rp ' . number_format($log['cost'] ?? 0, 0, ',', '.') }}</td>
                  <td>
                    @if(!empty($log['attachment_path']))
                      <a class="btn btn-sm btn-outline-secondary" href="{{ route('inventory.equipment.maintenance.download', $log['id']) }}">Unduh</a>
                    @else
                      <span class="text-muted">-</span>
                    @endif
                  </td>
                </tr>
              @empty
                <tr><td colspan="4" class="text-center text-muted">Belum ada log perawatan.</td></tr>
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
@endpush
