@extends('layouts.app')
@section('title', 'Cetak Asuhan Keperawatan')
@push('style')
    <style>
        .print-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 16px
        }

        @media print {
            .no-print {
                display: none !important
            }
        }
    </style>
@endpush
@section('content')
    <div class="print-container">
        <x-print.header />
        <div class="no-print d-flex justify-content-between align-items-center mb-2">
            <h5 class="m-0">Ringkasan Asuhan Keperawatan</h5>
            <div>
                <button class="btn btn-sm btn-primary" onclick="window.print()"><i class="ri-printer-line"></i> Cetak</button>
                <a href="{{ route('keperawatan.show', $record->id) }}" class="btn btn-sm btn-secondary">Kembali</a>
            </div>
        </div>
        <hr />
        <div class="mb-2"><strong>Tanggal:</strong> {{ $record->created_at->format('d M Y H:i') }}</div>
        <div class="mb-2"><strong>RM:</strong> {{ $record->encounter->rekam_medis ?? '-' }} — <strong>Pasien:</strong>
            {{ $record->encounter->name_pasien ?? '-' }}</div>
        <div class="mb-2"><strong>Perawat:</strong> {{ $record->nurse->name ?? '-' }} — <strong>Shift:</strong>
            {{ $record->shift ?? '-' }}</div>
        <div class="mb-2"><strong>Tanda Vital:</strong> SBP {{ $record->systolic ?? '-' }}, DBP
            {{ $record->diastolic ?? '-' }}, HR {{ $record->heart_rate ?? '-' }}, RR {{ $record->resp_rate ?? '-' }}, Temp
            {{ $record->temperature ?? '-' }}, SpO2 {{ $record->spo2 ?? '-' }}, Nyeri {{ $record->pain_scale ?? '-' }}
        </div>
        <div class="mb-2"><strong>Diagnosa:</strong><br>{!! nl2br(e($record->nursing_diagnosis ?? '-')) !!}</div>
        <div class="mb-2"><strong>Intervensi:</strong><br>{!! nl2br(e($record->interventions ?? '-')) !!}</div>
        <div class="mb-2"><strong>Evaluasi:</strong><br>{!! nl2br(e($record->evaluation_notes ?? '-')) !!}</div>
    </div>
@endsection
@push('scripts')
    <script>
        // window.onload = ()=>window.print();
    </script>
@endpush
