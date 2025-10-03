@extends('layouts.app')
@section('title', 'Cetak Konsultasi Spesialis')
@push('style')
<style>
  body { background: #fff; }
  .print-container { max-width: 800px; margin: 0 auto; padding: 16px; }
  @media print { .no-print { display: none !important; } }
</style>
@endpush
@section('content')
<div class="print-container">
  <div class="no-print" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
    <h5 class="m-0">Ringkasan Konsultasi Spesialis</h5>
    <div>
      <button class="btn btn-sm btn-primary" onclick="window.print()"><i class="ri-printer-line"></i> Cetak</button>
      <a href="{{ route('konsultasi.show', $consultation->id) }}" class="btn btn-sm btn-secondary">Kembali</a>
    </div>
  </div>
  <hr/>
  <div style="margin-bottom:8px;">
    <strong>Rekam Medis:</strong> {{ $consultation->encounter->rekam_medis ?? '-' }}<br/>
    <strong>Nama Pasien:</strong> {{ $consultation->encounter->name_pasien ?? '-' }}<br/>
    <strong>Tanggal Dibuat:</strong> {{ $consultation->created_at->format('d M Y H:i') }}
  </div>
  <div style="margin-bottom:8px;">
    <strong>Spesialis:</strong> {{ $consultation->specialist->name ?? '-' }}<br/>
    <strong>Dokter Ditugasi:</strong> {{ $consultation->assignedDoctor->name ?? '-' }}<br/>
    <strong>Status:</strong> {{ ucfirst($consultation->status) }}<br/>
    <strong>Dijadwalkan:</strong> {{ $consultation->scheduled_at ? $consultation->scheduled_at->format('d M Y H:i') : '-' }}
  </div>
  <div style="margin-bottom:8px;">
    <strong>Alasan/Indikasi:</strong>
    <div style="white-space:pre-wrap;border:1px solid #ddd;border-radius:6px;padding:8px;">{{ $consultation->reason }}</div>
  </div>
  <div>
    <strong>Hasil Konsultasi:</strong>
    <div style="white-space:pre-wrap;border:1px solid #ddd;border-radius:6px;padding:8px;">{{ $consultation->result_notes ?? '-' }}</div>
  </div>
</div>
@endsection
@push('scripts')
<script>
  // Optional: auto-print on load
  // window.onload = function() { window.print(); };
</script>
@endpush
