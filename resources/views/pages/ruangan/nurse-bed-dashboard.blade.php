@extends('layouts.app')
@section('title')
    Dashboard Perawat Rawat Inap
@endsection

@push('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/animate.css@4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.0/dist/sweetalert2.min.css">
    <!-- Scrollbar CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">

    <!-- Uploader CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/dropzone/dropzone.min.css') }}">
    <style>
        /* Main Layout */
        .dashboard-container {
            background: #f8f9fa;
            min-height: 100vh;
            padding: 20px;
        }

        /* Select2 Medication Result Styling */
        .select2-result-medication {
            padding: 8px 5px;
        }

        .select2-result-medication .medication-name {
            font-weight: 500;
            color: #333;
        }

        .select2-result-medication .medication-info {
            margin-top: 4px;
        }

        .select2-result-medication.disabled-medication {
            opacity: 0.6;
            background-color: #f8f9fa;
            cursor: not-allowed !important;
        }

        .select2-result-medication.disabled-medication .medication-name {
            color: #6c757d;
            text-decoration: line-through;
        }

        .select2-results__option[aria-disabled=true] {
            pointer-events: none;
            cursor: not-allowed !important;
        }

        .select2-results__option[aria-disabled=true]:hover {
            background-color: #f8f9fa !important;
        }

        .select2-container--bootstrap-5 .select2-dropdown {
            border-color: #dee2e6;
        }

        /* Stats Cards */
        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border: none;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
        }

        .stat-icon.pending {
            background: linear-gradient(135deg, #FF6B6B 0%, #EE5A6F 100%);
            color: white;
        }

        .stat-icon.occupied {
            background: linear-gradient(135deg, #4FACFE 0%, #00F2FE 100%);
            color: white;
        }

        .stat-icon.available {
            background: linear-gradient(135deg, #43E97B 0%, #38F9D7 100%);
            color: white;
        }

        /* Pending Admissions Section */
        .section-header {
            background: white;
            border-radius: 12px;
            padding: 16px 24px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .section-title {
            font-size: 20px;
            font-weight: 700;
            color: #2d3748;
            margin: 0;
        }

        .section-subtitle {
            font-size: 14px;
            color: #718096;
            margin: 0;
        }

        /* Patient Cards */
        .patient-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 16px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
            border-left: 4px solid #e2e8f0;
        }

        .patient-card:hover {
            transform: translateX(5px);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        }

        .patient-card.from-igd {
            border-left-color: #f56565;
            background: linear-gradient(90deg, rgba(245, 101, 101, 0.03) 0%, white 100%);
        }

        .patient-card.from-registration {
            border-left-color: #ed8936;
            background: linear-gradient(90deg, rgba(237, 137, 54, 0.03) 0%, white 100%);
        }

        .source-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-igd {
            background: #fed7d7;
            color: #c53030;
        }

        .badge-registration {
            background: #feebc8;
            color: #c05621;
        }

        /* Room Cards */
        .room-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid #e2e8f0;
        }

        .room-card:hover {
            border-color: #22c55e;
            transform: scale(1.02);
            box-shadow: 0 8px 25px rgba(34, 197, 94, 0.15);
        }

        .room-card.available {
            border-color: #48bb78;
        }

        .room-card.occupied {
            border-color: #fc8181;
        }

        .room-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .room-number {
            font-size: 24px;
            font-weight: 700;
            color: #2d3748;
        }

        .room-capacity {
            background: #edf2f7;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            color: #4a5568;
        }

        /* Buttons */
        .btn-assign {
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
            border: none;
            border-radius: 8px;
            padding: 10px 24px;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-assign:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(34, 197, 94, 0.4);
            color: white;
        }

        .btn-select-room {
            background: white;
            border: 2px solid #22c55e;
            border-radius: 8px;
            padding: 8px 20px;
            color: #22c55e;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-select-room:hover {
            background: #22c55e;
            color: white;
        }

        /* Loading & Empty States */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 12px;
        }

        .empty-state-icon {
            font-size: 64px;
            color: #cbd5e0;
            margin-bottom: 16px;
        }

        .empty-state-title {
            font-size: 18px;
            font-weight: 600;
            color: #4a5568;
            margin-bottom: 8px;
        }

        .empty-state-text {
            font-size: 14px;
            color: #a0aec0;
        }

        /* Pulse Animation */
        .pulse {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.6;
            }
        }

        /* Refresh Button */
        .btn-refresh {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
            border: none;
            color: white;
            font-size: 24px;
            box-shadow: 0 4px 16px rgba(34, 197, 94, 0.4);
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .btn-refresh:hover {
            transform: scale(1.1) rotate(180deg);
        }

        /* Prescription Orders Table Styling */
        .prescription-row {
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .prescription-row:hover {
            background-color: rgba(25, 135, 84, 0.05) !important;
            transform: translateX(3px);
        }

        .prescription-row:hover .btn {
            transform: scale(1.05);
        }

        .table-success th {
            background: linear-gradient(135deg, #198754 0%, #20c997 100%) !important;
            color: white !important;
            font-weight: 600;
            border: none;
            text-transform: uppercase;
            font-size: 0.85em;
            letter-spacing: 0.5px;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(25, 135, 84, 0.08);
        }

        .prescription-row td {
            vertical-align: middle;
            padding: 12px 8px;
            border-color: rgba(25, 135, 84, 0.1);
        }

        .badge {
            font-weight: 500;
        }

        /* Smooth animations for buttons */
        .btn-sm {
            transition: all 0.2s ease;
            font-size: 0.75em;
            padding: 0.375rem 0.5rem;
        }

        .btn-sm:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }

        /* All modals have backdrop disabled */

        /* Enhanced Feature Styling */
        .feature-card {
            border-radius: 12px;
            border: none;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
        }

        .medication-item {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 8px;
            border-left: 4px solid #28a745;
        }

        .medication-item.pending {
            border-left-color: #ffc107;
        }

        .medication-item.overdue {
            border-left-color: #dc3545;
            background: #fff5f5;
        }

        .nursing-note-card {
            border-radius: 10px;
            border: none;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 12px;
        }

        .priority-critical {
            border-left: 4px solid #dc3545;
        }

        .priority-high {
            border-left: 4px solid #ffc107;
        }

        .priority-normal {
            border-left: 4px solid #28a745;
        }

        .handover-table th {
            background: #fff3cd !important;
            border: none;
            font-weight: 600;
        }

        .handover-note {
            resize: vertical;
            min-height: 60px;
        }

        .alert-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
            animation: slideInRight 0.3s ease-out;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(300px);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .btn-feature {
            border-radius: 8px;
            font-weight: 500;
            padding: 8px 16px;
            transition: all 0.3s ease;
        }

        .btn-feature:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .modal {
            z-index: 1040 !important;
        }

        .modal.show {
            z-index: 1040 !important;
        }

        .modal-dialog {
            z-index: 1041 !important;
            position: relative !important;
            width: auto !important;
            max-width: 90% !important;
        }

        .modal-content {
            z-index: 1042 !important;
            position: relative !important;
            margin: auto !important;
            border-radius: 16px;
            border: none;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.25);
            background: white !important;
        }

        /* Higher z-index for stacked modals */
        #modalVitalSigns,
        #modalVitalSignsHistory {
            z-index: 1050 !important;
        }

        #modalVitalSigns.show,
        #modalVitalSignsHistory.show {
            z-index: 1050 !important;
        }

        #modalVitalSigns .modal-dialog,
        #modalVitalSignsHistory .modal-dialog {
            z-index: 1051 !important;
        }

        #modalVitalSigns .modal-content,
        #modalVitalSignsHistory .modal-content {
            z-index: 1052 !important;
            background: white !important;
        }

        .modal.show {
            display: block !important;
            overflow-x: hidden !important;
            overflow-y: auto !important;
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 100vw !important;
            height: 100vh !important;
            padding: 0 !important;
        }

        .modal.show .modal-dialog {
            transform: none !important;
            margin: 50px auto !important;
            display: block !important;
        }

        .modal-dialog-centered {
            display: flex !important;
            align-items: center !important;
            min-height: calc(100vh - 100px) !important;
            margin: 50px auto !important;
        }

        .modal-dialog-scrollable .modal-body {
            max-height: 60vh !important;
            overflow-y: auto !important;
        }

        #modalRoomPatients .modal-body {
            max-height: 70vh !important;
            overflow-y: auto !important;
        }



        .modal-header {
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
            color: white;
            border-radius: 16px 16px 0 0;
            padding: 16px 24px;
        }

        .modal-header .btn-close {
            filter: brightness(0) invert(1);
        }

        .room-selection-item {
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 16px;
            transition: all 0.3s ease;
            cursor: pointer;
            background: white;
        }

        .room-selection-item:hover {
            border-color: #22c55e;
            background: #f0fdf4;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(34, 197, 94, 0.2);
        }

        .room-selection-item.disabled {
            opacity: 0.5;
            cursor: not-allowed;
            background: #f9fafb;
        }

        .room-selection-item.disabled:hover {
            transform: none;
            border-color: #e5e7eb;
            background: #f9fafb;
        }
        }

        /* Info Pills */
        .info-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            background: #edf2f7;
            border-radius: 20px;
            font-size: 13px;
            color: #4a5568;
            margin-right: 8px;
            margin-bottom: 8px;
        }

        .info-pill i {
            font-size: 14px;
        }

        /* Time Badge */
        .time-badge {
            display: inline-block;
            padding: 4px 10px;
            background: #e6fffa;
            color: #234e52;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        /* Priority Indicator */
        .priority-high {
            position: absolute;
            top: -8px;
            right: -8px;
            width: 24px;
            height: 24px;
            background: #f56565;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 12px;
            font-weight: bold;
            animation: pulse 2s infinite;
        }
    </style>
@endpush

@section('content')
    <div class="dashboard-container">
        <div class="container-fluid">

            {{-- Header Section --}}
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="fw-bold mb-1" style="color: #16a34a;">Dashboard Perawat Rawat Inap</h2>
                            <p class="mb-0" style="color: #4b5563;">
                                <i class="ri-time-line me-1"></i>
                                <span id="current-time">{{ now()->format('d F Y, H:i') }}</span>
                            </p>
                        </div>
                        <div class="d-flex gap-2 flex-wrap">
                            <button class="btn btn-primary btn-sm" onclick="openAllPatientsVitalSigns()">
                                <i class="ri-heart-pulse-line me-1"></i>
                                Vital Signs
                            </button>
                            @if (auth()->user()->role == 2 || auth()->user()->role == 1)
                                <button class="btn btn-success btn-sm" onclick="openPrescriptionOrders()">
                                    <i class="ri-prescription-line me-1"></i>
                                    Prescription Orders
                                </button>
                            @endif
                            <button class="btn btn-success btn-sm" onclick="openMedicationSchedule()">
                                <i class="ri-medicine-bottle-line me-1"></i>
                                Jadwal Obat
                            </button>
                            <button class="btn btn-info btn-sm" onclick="openNursingNotes()">
                                <i class="ri-file-text-line me-1"></i>
                                Catatan Perawat
                            </button>
                            <button class="btn btn-warning btn-sm" onclick="openHandover()">
                                <i class="ri-exchange-line me-1"></i>
                                Handover
                            </button>
                            <span class="badge bg-secondary text-white px-3 py-2 d-flex align-items-center">
                                <i class="ri-user-line me-1"></i>
                                {{ Auth::user()->name }}
                            </span>
                        </div>
                    </div>
                </div>
            </div> {{-- Stats Cards --}}
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="stat-card">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon pending">
                                <i class="ri-user-add-line"></i>
                            </div>
                            <div class="ms-3 flex-grow-1">
                                <div class="text-muted small fw-600">Menunggu Ruangan</div>
                                <div class="h2 fw-bold mb-0" id="pending-count">{{ $pendingList->count() }}</div>
                                <div class="small text-danger">
                                    <i class="ri-alert-line"></i>
                                    Perlu Penempatan
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon occupied">
                                <i class="ri-hotel-bed-line"></i>
                            </div>
                            <div class="ms-3 flex-grow-1">
                                <div class="text-muted small fw-600">Bed Terisi</div>
                                <div class="h2 fw-bold mb-0">{{ $summary['occupied_beds'] ?? 0 }}</div>
                                <div class="small text-info">
                                    dari {{ $summary['total_beds'] ?? 0 }} bed
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon available">
                                <i class="ri-checkbox-circle-line"></i>
                            </div>
                            <div class="ms-3 flex-grow-1">
                                <div class="text-muted small fw-600">Bed Tersedia</div>
                                <div class="h2 fw-bold mb-0">{{ $summary['available_beds'] ?? 0 }}</div>
                                <div class="small text-success">
                                    {{ number_format(100 - ($summary['occupancy_rate'] ?? 0), 1) }}% kosong
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Main Content --}}
            <div class="row">
                {{-- Left Column: Pending Admissions --}}
                <div class="col-lg-5 mb-4">
                    <div class="section-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="section-title">
                                    <i class="ri-user-add-line text-danger me-2"></i>
                                    Pasien Menunggu Ruangan
                                </h5>
                                <p class="section-subtitle">Pasien yang perlu ditempatkan di ruangan rawat inap</p>
                            </div>
                            <span class="badge bg-danger pulse" style="font-size: 16px; padding: 8px 16px;">
                                {{ $pendingList->count() }}
                            </span>
                        </div>
                    </div>

                    <div id="pending-admissions-list">
                        <div id="pending-list-container">
                            @forelse($pendingList as $pending)
                                <div class="patient-card from-{{ $pending['priority_class'] === 'danger' ? 'igd' : 'registration' }} position-relative"
                                    data-admission-id="{{ $pending['id'] }}"
                                    data-patient-name="{{ $pending['patient_name'] }}"
                                    data-medical-record="{{ $pending['medical_record'] }}">

                                    @if ($pending['priority_class'] === 'danger')
                                        <div class="priority-high">!</div>
                                    @endif

                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                                                <span
                                                    class="source-badge badge-{{ $pending['priority_class'] === 'danger' ? 'igd' : 'registration' }}">
                                                    {{ $pending['source_type'] }}
                                                </span>
                                                <span class="time-badge">
                                                    <i class="ri-time-line"></i>
                                                    {{ $pending['waiting_time'] }}
                                                </span>
                                                @if ($pending['kerabat_type'] === 'Kerabat Owner')
                                                    <span class="badge bg-warning text-dark" style="font-size: 10px;">
                                                        <i class="ri-vip-crown-line"></i> Kerabat Owner
                                                    </span>
                                                @elseif($pending['kerabat_type'] === 'Kerabat Dokter')
                                                    <span class="badge bg-primary text-white" style="font-size: 10px;">
                                                        <i class="ri-user-heart-line"></i> Kerabat Dokter
                                                    </span>
                                                @elseif($pending['kerabat_type'] === 'Kerabat Karyawan')
                                                    <span class="badge bg-success text-white" style="font-size: 10px;">
                                                        <i class="ri-user-smile-line"></i> Kerabat Karyawan
                                                    </span>
                                                @else
                                                    <span class="badge bg-primary text-white" style="font-size: 10px;">
                                                        <i class="ri-user-line"></i> Reguler
                                                    </span>
                                                @endif
                                            </div>
                                            <h6 class="fw-bold mb-1">{{ $pending['patient_name'] }}</h6>
                                            <div class="small text-muted">RM: {{ $pending['medical_record'] }}</div>
                                        </div>
                                    </div>

                                    <div class="patient-info-table mb-2"
                                        style="background: #f8f9fa; border-radius: 8px; padding: 10px; font-size: 11px;">
                                        <div class="row g-1">
                                            <div class="col-6 d-flex align-items-center py-1">
                                                <i class="ri-user-line text-muted me-2"></i>
                                                <span class="text-muted" style="width: 50px;">Umur</span>
                                                <span class="fw-semibold">: {{ $pending['age'] }} thn
                                                    ({{ $pending['birth_date'] !== 'N/A' ? \Carbon\Carbon::createFromFormat('d/m/Y', $pending['birth_date'])->format('d/m/y') : 'N/A' }})
                                                </span>
                                            </div>
                                            <div class="col-6 d-flex align-items-center py-1">
                                                <i
                                                    class="ri-{{ $pending['gender'] === 'Laki-laki' ? 'men' : 'women' }}-line text-muted me-2"></i>
                                                <span class="text-muted" style="width: 50px;">Gender</span>
                                                <span class="fw-semibold">: {{ $pending['gender'] }}</span>
                                            </div>
                                            <div class="col-12 d-flex align-items-center py-1 border-top pt-2 mt-1">
                                                <i class="ri-stethoscope-line text-muted me-2"></i>
                                                <span class="text-muted" style="width: 50px;">Dokter</span>
                                                <span class="fw-semibold" title="{{ $pending['doctor_name'] }}">:
                                                    {{ $pending['doctor_name'] }}</span>
                                            </div>
                                            <div class="col-12 d-flex align-items-center py-1">
                                                <i class="ri-shield-check-line text-muted me-2"></i>
                                                <span class="text-muted" style="width: 50px;">Jaminan</span>
                                                <span class="fw-semibold">: {{ $pending['jenis_jaminan'] }}</span>
                                            </div>
                                            @if ($pending['phone'] && $pending['phone'] !== 'N/A')
                                                <div class="col-12 d-flex align-items-center py-1">
                                                    <i class="ri-phone-line text-muted me-2"></i>
                                                    <span class="text-muted" style="width: 50px;">Telepon</span>
                                                    <span class="fw-semibold">: {{ $pending['phone'] }}</span>
                                                </div>
                                            @endif
                                            @if ($pending['address'] && $pending['address'] !== 'N/A')
                                                <div class="col-12 d-flex align-items-start py-1">
                                                    <i class="ri-map-pin-line text-muted me-2 mt-1"></i>
                                                    <span class="text-muted" style="width: 50px;">Alamat</span>
                                                    <span class="fw-semibold" style="flex: 1;"
                                                        title="{{ $pending['address'] }}">:
                                                        {{ $pending['address'] }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    @if ($pending['admission_reason'] && $pending['admission_reason'] !== 'N/A')
                                        <div class="alert alert-light border-0 mb-3 py-2">
                                            <small class="text-muted">
                                                <i class="ri-file-text-line me-1"></i>
                                                <strong>Alasan:</strong> {{ $pending['admission_reason'] }}
                                            </small>
                                        </div>
                                    @endif

                                    <button class="btn btn-assign w-100"
                                        onclick="showRoomSelection('{{ $pending['id'] }}', '{{ $pending['patient_name'] }}')">
                                        <i class="ri-door-open-line me-2"></i>
                                        Tempatkan di Ruangan
                                    </button>
                                </div>
                            @empty
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <i class="ri-checkbox-circle-line"></i>
                                    </div>
                                    <div class="empty-state-title">Tidak Ada Pasien Menunggu</div>
                                    <div class="empty-state-text">Semua pasien rawat inap sudah ditempatkan di ruangan
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Right Column: Room Availability --}}
                <div class="col-lg-7 mb-4">
                    <div class="section-header">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h5 class="section-title">
                                    <i class="ri-hotel-line text-primary me-2"></i>
                                    Ketersediaan Ruangan
                                </h5>
                                <p class="section-subtitle mb-0">Klik ruangan untuk menempatkan pasien</p>
                            </div>
                        </div>
                    </div>

                    {{-- Room Availability by Category --}}
                    <div id="rooms-availability-container">
                        @foreach ($availability as $category)
                            @foreach ($category['classes'] as $className => $classData)
                                <div class="card mb-3 border-0 shadow-sm">
                                    <div
                                        class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-2">
                                        <div>
                                            <span class="badge bg-primary me-2">{{ $category['category_name'] }}</span>
                                            <span class="badge bg-info">Kelas {{ $className }}</span>
                                        </div>
                                        <div class="text-muted small">
                                            <i class="ri-door-line"></i> {{ count($classData['rooms']) }} Ruangan
                                            | <i class="ri-checkbox-circle-line text-success"></i>
                                            {{ $classData['available_beds'] }} Tersedia
                                        </div>
                                    </div>
                                    <div class="card-body p-2">
                                        <div class="table-responsive">
                                            <table class="table table-sm table-hover mb-0" style="font-size: 12px;">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th width="20%" class="text-center">No. Ruangan</th>
                                                        <th width="25%" class="text-center">Kapasitas</th>
                                                        <th width="35%">Tingkat Hunian</th>
                                                        <th width="20%" class="text-center">Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($classData['rooms'] as $room)
                                                        <tr class="align-middle cursor-pointer"
                                                            onclick="handleRoomClick('{{ $room['id'] }}', '{{ $room['room_number'] }}', '{{ $category['category_name'] }}', '{{ $className }}', {{ $room['occupied'] }}, {{ $room['available'] }})"
                                                            style="cursor: pointer;">
                                                            <td class="text-center">
                                                                <strong
                                                                    class="text-primary">{{ $room['room_number'] }}</strong>
                                                            </td>
                                                            <td class="text-center">
                                                                <span class="badge bg-light text-dark border">
                                                                    <i class="ri-user-line"></i>
                                                                    {{ $room['occupied'] }}/{{ $room['capacity'] }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <div class="d-flex align-items-center gap-2">
                                                                    <div class="progress flex-grow-1"
                                                                        style="height: 8px;">
                                                                        <div class="progress-bar {{ $room['available'] > 0 ? 'bg-success' : 'bg-danger' }}"
                                                                            style="width: {{ $room['capacity'] > 0 ? ($room['occupied'] / $room['capacity']) * 100 : 0 }}%">
                                                                        </div>
                                                                    </div>
                                                                    <small class="text-muted" style="min-width: 60px;">
                                                                        {{ $room['capacity'] > 0 ? round(($room['occupied'] / $room['capacity']) * 100) : 0 }}%
                                                                    </small>
                                                                </div>
                                                            </td>
                                                            <td class="text-center">
                                                                @if ($room['available'] > 0)
                                                                    <span
                                                                        class="badge bg-success-subtle text-success border border-success">
                                                                        <i class="ri-check-line"></i>
                                                                        {{ $room['available'] }}
                                                                        Kosong
                                                                    </span>
                                                                @else
                                                                    <span
                                                                        class="badge bg-danger-subtle text-danger border border-danger">
                                                                        <i class="ri-close-line"></i> Penuh
                                                                    </span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Refresh Button --}}
    <button class="btn btn-refresh" onclick="refreshDashboard()" id="btn-refresh">
        <i class="ri-refresh-line"></i>
    </button>

    {{-- Modal: Select Room --}}
    <div class="modal fade" id="modalSelectRoom" tabindex="-1" data-bs-backdrop="false">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title mb-1">
                            <i class="ri-door-open-line me-2"></i>
                            Pilih Ruangan
                        </h5>
                        <small class="opacity-75">Pasien: <span id="modal-patient-name"
                                class="fw-semibold"></span></small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="max-height: 60vh; overflow-y: auto;">
                    <input type="hidden" id="selected-admission-id">

                    <div id="modal-rooms-list">
                        @foreach ($availability as $category)
                            @foreach ($category['classes'] as $className => $classData)
                                @php
                                    $rooms = is_array($classData['rooms'])
                                        ? $classData['rooms']
                                        : $classData['rooms']->toArray();
                                    $availableRooms = array_filter($rooms, fn($r) => $r['available'] > 0);
                                @endphp
                                @if (count($availableRooms) > 0)
                                    <div class="mb-4">
                                        <div class="d-flex align-items-center gap-2 mb-3">
                                            <span class="badge bg-primary">{{ $category['category_name'] }}</span>
                                            <span class="badge bg-info">Kelas {{ $className }}</span>
                                            <small class="text-muted ms-auto">
                                                <i class="ri-door-line"></i> {{ count($availableRooms) }} Ruangan Tersedia
                                            </small>
                                        </div>
                                        <div class="row g-2">
                                            @foreach ($classData['rooms'] as $room)
                                                @if ($room['available'] > 0)
                                                    <div class="col-md-6">
                                                        <div class="room-selection-item"
                                                            onclick="selectRoom('{{ $room['id'] }}', '{{ $room['room_number'] }}', '{{ $category['category_name'] }}', '{{ $className }}')">
                                                            <div
                                                                class="d-flex justify-content-between align-items-start mb-2">
                                                                <div>
                                                                    <h6 class="mb-0 text-primary fw-bold">
                                                                        {{ $room['room_number'] }}</h6>
                                                                    <small
                                                                        class="text-muted">{{ $category['category_name'] }}
                                                                        - Kelas {{ $className }}</small>
                                                                </div>
                                                                <span class="badge bg-success">
                                                                    <i class="ri-check-line"></i> {{ $room['available'] }}
                                                                    Kosong
                                                                </span>
                                                            </div>
                                                            <div class="d-flex align-items-center gap-2">
                                                                <i class="ri-user-line text-muted"></i>
                                                                <div class="progress flex-grow-1" style="height: 6px;">
                                                                    <div class="progress-bar bg-success"
                                                                        style="width: {{ $room['capacity'] > 0 ? ($room['occupied'] / $room['capacity']) * 100 : 0 }}%">
                                                                    </div>
                                                                </div>
                                                                <small
                                                                    class="text-muted">{{ $room['occupied'] }}/{{ $room['capacity'] }}</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <small class="text-muted"><i class="ri-information-line"></i> Klik ruangan untuk melanjutkan</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal: Confirm Assignment --}}
    <div class="modal fade" id="modalConfirmAssignment" tabindex="-1" data-bs-backdrop="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="ri-checkbox-circle-line me-2"></i>
                        Konfirmasi Penempatan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-success border-0 mb-3">
                        <i class="ri-information-line me-2"></i>
                        Pastikan data penempatan sudah benar
                    </div>

                    <input type="hidden" id="confirm-admission-id">
                    <input type="hidden" id="confirm-room-id">

                    <div class="mb-3">
                        <label class="form-label fw-bold">Pasien</label>
                        <div class="form-control-plaintext" id="confirm-patient-name"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Ruangan</label>
                        <div class="form-control-plaintext" id="confirm-room-number"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Nomor Bed (Opsional)</label>
                        <input type="text" class="form-control" id="bed-number" placeholder="Contoh: 1A, 2B, dll">
                        <small class="form-text text-muted">Kosongkan jika tidak ada nomor bed spesifik</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" onclick="confirmAssignment()">
                        <i class="ri-check-line me-1"></i>
                        Konfirmasi Penempatan
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal: Room Patients Info --}}
    <div class="modal fade" id="modalRoomPatients" tabindex="-1" data-bs-backdrop="false">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title mb-1">
                            <i class="ri-hospital-line me-2"></i>
                            Pasien di Ruangan <span id="room-info-number"></span>
                        </h5>
                        <small class="opacity-75">
                            <span id="room-info-category"></span> - <span id="room-info-class"></span> |
                            Kapasitas: <span id="room-info-capacity"></span>
                        </small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="room-patients-container">
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Input Vital Signs -->
    <div class="modal fade" id="modalVitalSigns" tabindex="-1" data-bs-backdrop="false">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <div>
                        <h5 class="modal-title mb-1">
                            <i class="ri-heart-pulse-line me-2"></i>
                            Input Vital Signs
                        </h5>
                        <small class="opacity-75">
                            <span id="vital-patient-name"></span> - Bed <span id="vital-bed-number"></span>
                        </small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="formVitalSigns" onsubmit="saveVitalSigns(event)">
                    <input type="hidden" id="vital-admission-id" name="admission_id">
                    <div class="modal-body">
                        <div class="row g-3">
                            <!-- Blood Pressure -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="ri-heart-line text-danger me-1"></i>
                                    Tekanan Darah (mmHg)
                                </label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="blood_pressure_systolic"
                                        name="blood_pressure_systolic" placeholder="Sistolik" min="0"
                                        max="300" required>
                                    <span class="input-group-text">/</span>
                                    <input type="number" class="form-control" id="blood_pressure_diastolic"
                                        name="blood_pressure_diastolic" placeholder="Diastolik" min="0"
                                        max="200" required>
                                </div>
                                <small class="text-muted">Normal: 90-140 / 60-90 mmHg</small>
                            </div>

                            <!-- Heart Rate -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="ri-pulse-line text-danger me-1"></i>
                                    Nadi (x/menit)
                                </label>
                                <input type="number" class="form-control" id="heart_rate" name="heart_rate"
                                    placeholder="Detak per menit" min="0" max="300" required>
                                <small class="text-muted">Normal: 60-100 x/menit</small>
                            </div>

                            <!-- Temperature -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="ri-temp-hot-line text-warning me-1"></i>
                                    Suhu Tubuh (°C)
                                </label>
                                <input type="number" class="form-control" id="temperature" name="temperature"
                                    placeholder="Celcius" step="0.1" min="30" max="45" required>
                                <small class="text-muted">Normal: 36.0-37.5 °C</small>
                            </div>

                            <!-- Respiratory Rate -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="ri-lungs-line text-info me-1"></i>
                                    Pernapasan (x/menit)
                                </label>
                                <input type="number" class="form-control" id="respiratory_rate" name="respiratory_rate"
                                    placeholder="Nafas per menit" min="0" max="100" required>
                                <small class="text-muted">Normal: 12-20 x/menit</small>
                            </div>

                            <!-- Oxygen Saturation -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="ri-lungs-line text-primary me-1"></i>
                                    Saturasi Oksigen (%)
                                </label>
                                <input type="number" class="form-control" id="oxygen_saturation"
                                    name="oxygen_saturation" placeholder="SpO2" min="0" max="100" required>
                                <small class="text-muted">Normal: >95%</small>
                            </div>

                            <!-- Consciousness Level -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="ri-brain-line text-success me-1"></i>
                                    Kesadaran
                                </label>
                                <select class="form-select" id="consciousness_level" name="consciousness_level" required>
                                    <option value="">-- Pilih Kesadaran --</option>
                                    <option value="Compos Mentis">Compos Mentis</option>
                                    <option value="Apatis">Apatis</option>
                                    <option value="Somnolent">Somnolent</option>
                                    <option value="Sopor">Sopor</option>
                                    <option value="Coma">Coma</option>
                                </select>
                            </div>

                            <!-- Notes -->
                            <div class="col-12">
                                <label class="form-label fw-semibold">
                                    <i class="ri-file-text-line me-1"></i>
                                    Catatan (Opsional)
                                </label>
                                <textarea class="form-control" id="notes" name="notes" rows="3"
                                    placeholder="Tambahan catatan atau observasi..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-save-line me-1"></i>Simpan Vital Signs
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Vital Signs History -->
    <div class="modal fade" id="modalVitalSignsHistory" tabindex="-1" data-bs-backdrop="false">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <div>
                        <h5 class="modal-title mb-1">
                            <i class="ri-history-line me-2"></i>
                            Riwayat Vital Signs
                        </h5>
                        <small class="opacity-75">
                            <span id="history-patient-name"></span>
                        </small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="vital-signs-history-container">
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal All Patients Vital Signs -->
    <div class="modal fade" id="modalAllPatientsVitalSigns" tabindex="-1" data-bs-backdrop="false">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <div>
                        <h5 class="modal-title mb-1">
                            <i class="ri-heart-pulse-line me-2"></i>
                            Input Vital Signs - Semua Pasien Rawat Inap
                        </h5>
                        <small class="opacity-75">
                            Pilih pasien untuk input vital signs
                        </small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Search Bar -->
                    <div class="mb-3">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="ri-search-line"></i>
                            </span>
                            <input type="text" class="form-control" id="searchAllPatients"
                                placeholder="Cari berdasarkan nama pasien atau nomor rekam medis..."
                                onkeyup="searchAllPatients()">
                        </div>
                    </div>

                    <!-- Patients Table -->
                    <div id="all-patients-container">
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div id="pagination-container" class="mt-3"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Medication Schedule -->
    <div class="modal fade" id="modalMedicationSchedule" tabindex="-1" data-bs-backdrop="false">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <div>
                        <h5 class="modal-title mb-1">
                            <i class="ri-medicine-bottle-line me-2"></i>Jadwal Pemberian Obat
                        </h5>
                        <small class="opacity-75">Berdasarkan resep dokter yang telah diverifikasi farmasi</small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <div class="input-group">
                            <span class="input-group-text"><i class="ri-search-line"></i></span>
                            <input type="text" class="form-control" id="searchMedication"
                                placeholder="Cari pasien atau obat..." onkeyup="searchMedication()">
                        </div>
                    </div>

                    <div id="medication-schedule-container">
                        <div class="text-center py-4">
                            <div class="spinner-border text-success" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-info" onclick="refreshMedicationSchedule()">
                        <i class="ri-refresh-line me-1"></i>Refresh Jadwal
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Add Medication Record -->
    <div class="modal fade" id="modalAddMedication" tabindex="-1" data-bs-backdrop="false">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        <i class="ri-medicine-bottle-line me-2"></i>Konfirmasi Pemberian Obat
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="formAddMedication" onsubmit="saveMedicationRecord(event)">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Pasien</label>
                                <select class="form-select" id="medication-patient-select" name="admission_id" required>
                                    <option value="">Pilih Pasien</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Waktu Pemberian</label>
                                <input type="datetime-local" class="form-control" name="given_time" required>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">Nama Obat</label>
                                <input type="text" class="form-control" name="medication_name"
                                    placeholder="Nama obat yang diberikan" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Dosis</label>
                                <input type="text" class="form-control" name="dosage"
                                    placeholder="1 tablet, 5ml, dst" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Rute Pemberian</label>
                                <select class="form-select" name="route" required>
                                    <option value="">Pilih Rute</option>
                                    <option value="Oral">Oral (Mulut)</option>
                                    <option value="IV">Intravena (IV)</option>
                                    <option value="IM">Intramuskular (IM)</option>
                                    <option value="SC">Subkutan (SC)</option>
                                    <option value="Topikal">Topikal</option>
                                    <option value="Inhalasi">Inhalasi</option>
                                    <option value="Rektal">Rektal</option>
                                    <option value="Sublingual">Sublingual</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status Pemberian</label>
                                <select class="form-select" name="status" required>
                                    <option value="Given">Diberikan</option>
                                    <option value="Refused">Ditolak Pasien</option>
                                    <option value="Held">Ditunda</option>
                                    <option value="Not Available">Obat Tidak Tersedia</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Catatan</label>
                                <textarea class="form-control" name="notes" rows="3" placeholder="Catatan tambahan (opsional)"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">
                            <i class="ri-check-line me-1"></i>Konfirmasi Pemberian
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Nursing Notes -->
    <div class="modal fade" id="modalNursingNotes" tabindex="-1" data-bs-backdrop="false">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <div>
                        <h5 class="modal-title mb-1">
                            <i class="ri-file-text-line me-2"></i>Catatan Keperawatan
                        </h5>
                        <small class="opacity-75">Dokumentasi aktivitas dan observasi keperawatan</small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <div class="input-group">
                            <span class="input-group-text"><i class="ri-search-line"></i></span>
                            <input type="text" class="form-control" id="searchNursingNotes"
                                placeholder="Cari pasien atau catatan..." onkeyup="searchNursingNotes()">
                        </div>
                    </div>

                    <div id="nursing-notes-container">
                        <div class="text-center py-4">
                            <div class="spinner-border text-info" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-info" onclick="addNursingNote()">
                        <i class="ri-add-line me-1"></i>Tambah Catatan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Add Nursing Note -->
    <div class="modal fade" id="modalAddNursingNote" tabindex="-1" data-bs-backdrop="false">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">
                        <i class="ri-file-text-line me-2"></i>Tambah Catatan Keperawatan
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="formAddNursingNote" onsubmit="saveNursingNote(event)">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Pasien</label>
                                <select class="form-select" id="nursing-patient-select" name="admission_id" required>
                                    <option value="">Pilih Pasien</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Waktu Observasi</label>
                                <input type="datetime-local" class="form-control" name="observation_time" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Kategori</label>
                                <select class="form-select" name="category" required>
                                    <option value="">Pilih Kategori</option>
                                    <option value="Assessment">Assessment</option>
                                    <option value="Intervention">Intervensi</option>
                                    <option value="Evaluation">Evaluasi</option>
                                    <option value="Education">Edukasi</option>
                                    <option value="Monitoring">Monitoring</option>
                                    <option value="Communication">Komunikasi</option>
                                    <option value="Safety">Keselamatan</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Prioritas</label>
                                <select class="form-select" name="priority" required>
                                    <option value="Normal">Normal</option>
                                    <option value="High">Tinggi</option>
                                    <option value="Critical">Kritis</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Subjective (Keluhan Pasien)</label>
                                <textarea class="form-control" name="subjective" rows="2"
                                    placeholder="Apa yang dikatakan atau dikeluhkan pasien..."></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Objective (Observasi)</label>
                                <textarea class="form-control" name="objective" rows="2"
                                    placeholder="Apa yang diamati perawat (tanda vital, kondisi fisik, dll)..." required></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Assessment (Penilaian)</label>
                                <textarea class="form-control" name="assessment" rows="2"
                                    placeholder="Analisis kondisi dan masalah keperawatan..." required></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Plan (Rencana Tindakan)</label>
                                <textarea class="form-control" name="plan" rows="2"
                                    placeholder="Rencana tindakan keperawatan selanjutnya..." required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-info">
                            <i class="ri-save-line me-1"></i>Simpan Catatan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Prescription Orders Management -->
    <div class="modal fade" id="modalPrescriptionOrders" tabindex="-1" data-bs-backdrop="false">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <div>
                        <h5 class="modal-title mb-1">
                            <i class="ri-prescription-line me-2"></i>Prescription Orders Management
                        </h5>
                        <small class="opacity-75">Kelola resep obat dokter untuk pasien rawat inap</small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Search Bar -->
                    <div class="mb-3">
                        <div class="row">
                            <div class="col-12">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ri-search-line"></i></span>
                                    <input type="text" class="form-control" id="searchPrescriptionPatients"
                                        placeholder="Cari berdasarkan nama pasien atau nomor rekam medis..."
                                        onkeyup="searchPrescriptionPatients()">
                                </div>
                                <small class="text-muted mt-1">
                                    <i class="ri-information-line"></i>
                                    Gunakan tombol "Resep" pada setiap baris pasien untuk membuat resep baru
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Prescription Orders List -->
                    <div id="prescription-orders-container">
                        <div class="text-center py-4">
                            <div class="spinner-border text-danger" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-info" onclick="refreshPrescriptionOrders()">
                        <i class="ri-refresh-line me-1"></i>Refresh Data
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Create/Edit Prescription Order -->
    <div class="modal fade" id="modalPrescriptionForm" tabindex="-1" data-bs-backdrop="false">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        <i class="ri-prescription-line me-2"></i><span id="prescription-form-title">Buat Resep Baru</span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="formPrescriptionOrder" onsubmit="savePrescriptionOrder(event)">
                    <input type="hidden" id="prescription-order-id" name="prescription_order_id">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Pasien <span class="text-danger">*</span></label>
                                <select class="form-select" id="prescription-patient-select" name="encounter_id"
                                    required>
                                    <option value="">Pilih Pasien</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Dokter <span class="text-danger">*</span></label>
                                <select class="form-select" id="prescription-doctor-select" name="doctor_id" required>
                                    <option value="">Pilih Dokter</option>
                                </select>
                                <small class="text-muted" id="doctor-auto-note" style="display: none;">
                                    <i class="ri-information-line"></i> Otomatis diisi dengan nama Anda
                                </small>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Diagnosis/Indikasi</label>
                                <textarea class="form-control" name="diagnosis" rows="2"
                                    placeholder="Diagnosis atau indikasi medis untuk resep ini..."></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Catatan Khusus</label>
                                <textarea class="form-control" name="notes" rows="2"
                                    placeholder="Catatan khusus untuk farmasi atau perawat..."></textarea>
                            </div>
                        </div>

                        <!-- Medication List Section -->
                        <div class="border-top mt-4 pt-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="fw-bold">Daftar Obat</h6>
                                <button type="button" class="btn btn-sm btn-primary" onclick="addMedicationRow()">
                                    <i class="ri-add-line me-1"></i>Tambah Obat
                                </button>
                            </div>
                            <div id="medications-list">
                                <!-- Medication rows will be added here dynamically -->
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">
                            <i class="ri-save-line me-1"></i>Simpan Resep
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Handover Notes -->
    <div class="modal fade" id="modalHandover" tabindex="-1" data-bs-backdrop="false">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <div>
                        <h5 class="modal-title mb-1">
                            <i class="ri-exchange-line me-2"></i>Handover Shift
                        </h5>
                        <small class="opacity-75">Serah terima pasien antar shift</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Shift Sekarang</label>
                            <select class="form-select" id="current-shift">
                                <option value="Pagi">Pagi (07:30 - 14:30)</option>
                                <option value="Sore">Sore (14:30 - 21:30)</option>
                                <option value="Malam">Malam (21:30 - 07:30)</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Shift Selanjutnya</label>
                            <select class="form-select" id="next-shift">
                                <option value="Sore">Sore (14:30 - 21:30)</option>
                                <option value="Malam">Malam (21:30 - 07:30)</option>
                                <option value="Pagi">Pagi (07:30 - 14:30)</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text"><i class="ri-search-line"></i></span>
                                <input type="text" class="form-control" id="searchHandover"
                                    placeholder="Cari pasien..." onkeyup="searchHandover()">
                            </div>
                        </div>
                    </div>

                    <div id="handover-container">
                        <div class="text-center py-4">
                            <div class="spinner-border text-warning" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-warning" onclick="generateHandoverReport()">
                        <i class="ri-file-download-line me-1"></i>Download Laporan
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Overlay Scroll JS -->
    <script src="{{ asset('vendor/overlay-scroll/jquery.overlayScrollbars.min.js') }}"></script>
    <script src="{{ asset('vendor/overlay-scroll/custom-scrollbar.js') }}"></script>

    <!-- Dropzone JS -->
    <script src="{{ asset('vendor/dropzone/dropzone.min.js') }}"></script>

    <!-- Select2 JS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Custom JS files -->
    <script src="{{ asset('js/custom.js') }}"></script>
    <script>
        // Auto update time
        setInterval(() => {
            const now = new Date();
            const options = {
                day: 'numeric',
                month: 'long',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            };
            document.getElementById('current-time').textContent = now.toLocaleDateString('id-ID', options);
        }, 1000);

        // Show room selection modal
        function showRoomSelection(admissionId, patientName) {
            document.getElementById('selected-admission-id').value = admissionId;
            document.getElementById('modal-patient-name').textContent = patientName;

            const modal = new bootstrap.Modal(document.getElementById('modalSelectRoom'));
            modal.show();
        }

        // Select room
        function selectRoom(roomId, roomNumber, categoryName = '', className = '') {
            const admissionId = document.getElementById('selected-admission-id').value;
            const patientName = document.getElementById('modal-patient-name').textContent;

            // Close first modal
            bootstrap.Modal.getInstance(document.getElementById('modalSelectRoom')).hide();

            // Show confirmation modal
            document.getElementById('confirm-admission-id').value = admissionId;
            document.getElementById('confirm-room-id').value = roomId;
            document.getElementById('confirm-patient-name').textContent = patientName;

            // Format room info with category and class if available
            let roomInfo = roomNumber;
            if (categoryName && className) {
                roomInfo += ' (' + categoryName + ' - Kelas ' + className + ')';
            }
            document.getElementById('confirm-room-number').textContent = roomInfo;
            document.getElementById('bed-number').value = '';

            const modal = new bootstrap.Modal(document.getElementById('modalConfirmAssignment'));
            modal.show();
        }

        // Handle room click - show patients if occupied, or assign if available
        function handleRoomClick(roomId, roomNumber, categoryName, className, occupied, available) {
            if (occupied > 0) {
                // Show patients in room
                showRoomPatients(roomId, roomNumber, categoryName, className);
            } else if (available > 0) {
                // Assign room to patient
                selectRoomFromTable(roomId, roomNumber, categoryName, className);
            }
        }

        // Show patients in room
        function showRoomPatients(roomId, roomNumber, categoryName, className) {
            // Set room info
            document.getElementById('room-info-number').textContent = roomNumber;
            document.getElementById('room-info-category').textContent = categoryName;
            document.getElementById('room-info-class').textContent = 'Kelas ' + className;

            // Show loading
            document.getElementById('room-patients-container').innerHTML = `
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            `;

            // Show modal
            const modalElement = document.getElementById('modalRoomPatients');
            const modal = new bootstrap.Modal(modalElement);

            // Add event listener to handle focus when modal is hidden
            modalElement.addEventListener('hidden.bs.modal', function(event) {
                // Remove focus from any focused element inside modal
                const focusedElement = modalElement.querySelector(':focus');
                if (focusedElement) {
                    focusedElement.blur();
                }
            }, {
                once: true
            });

            modal.show();

            // Fetch room patients
            fetch(`/kunjungan/nurse-dashboard/room-patients/${roomId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayRoomPatients(data.data);
                    } else {
                        document.getElementById('room-patients-container').innerHTML = `
                            <div class="alert alert-danger">
                                <i class="ri-error-warning-line me-2"></i>
                                ${data.message}
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    document.getElementById('room-patients-container').innerHTML = `
                        <div class="alert alert-danger">
                            <i class="ri-error-warning-line me-2"></i>
                            Error loading room patients
                        </div>
                    `;
                    console.error('Error:', error);
                });
        }

        // Display room patients
        function displayRoomPatients(data) {
            const room = data.room || {
                occupied: 0,
                capacity: 0
            };
            const patients = Array.isArray(data?.patients) ? data.patients : [];

            document.getElementById('room-info-capacity').textContent = `${room.occupied}/${room.capacity}`;

            if (patients.length === 0) {
                document.getElementById('room-patients-container').innerHTML = `
                    <div class="alert alert-info">
                        <i class="ri-information-line me-2"></i>
                        Tidak ada pasien di ruangan ini
                    </div>
                `;
                return;
            }

            let html = '<div class="row">';
            patients.forEach((patient, index) => {
                // Badge kerabat
                let kerabatBadge = '';
                if (patient.kerabat_type === 'Owner') {
                    kerabatBadge =
                        '<span class="badge bg-warning text-dark ms-2" style="font-size: 10px;"><i class="ri-vip-crown-line"></i> Kerabat Owner</span>';
                } else if (patient.kerabat_type === 'Dokter') {
                    kerabatBadge =
                        '<span class="badge bg-primary text-white ms-2" style="font-size: 10px;"><i class="ri-user-heart-line"></i> Kerabat Dokter</span>';
                } else if (patient.kerabat_type === 'Karyawan') {
                    kerabatBadge =
                        '<span class="badge bg-success text-white ms-2" style="font-size: 10px;"><i class="ri-user-smile-line"></i> Kerabat Karyawan</span>';
                } else if (patient.kerabat_type === 'Reguler') {
                    kerabatBadge =
                        '<span class="badge bg-primary text-white ms-2" style="font-size: 10px;"><i class="ri-user-line"></i> Reguler</span>';
                }

                html += `
                    <div class="col-md-6 mb-3">
                        <div class="card h-100">
                            <div class="card-header bg-light py-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong class="text-primary">Bed ${patient.bed_number}</strong>
                                        ${kerabatBadge}
                                    </div>
                                    <span class="badge bg-success" style="font-size: 10px;">${patient.days_stayed}</span>
                                </div>
                            </div>
                            <div class="card-body py-2">
                                <div class="row g-1" style="font-size: 0.875rem;">
                                    <div class="col-12 mb-1">
                                        <strong class="text-dark">${patient.patient_name}</strong>
                                        <small class="text-muted ms-2">RM: ${patient.rekam_medis}</small>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">Umur:</small>
                                        <div>${patient.age}</div>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">Gender:</small>
                                        <div>${patient.gender}</div>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">Dokter:</small>
                                        <div>${patient.doctor}</div>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">Jaminan:</small>
                                        <div>${patient.jaminan}</div>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">Masuk:</small>
                                        <div>${patient.admission_date}</div>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">Telepon:</small>
                                        <div>${patient.phone || '-'}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            html += '</div>';

            document.getElementById('room-patients-container').innerHTML = html;
        }

        // Select room directly from table (without modal)
        function selectRoomFromTable(roomId, roomNumber, categoryName, className) {
            // Check if there's a pending patient selected
            const pendingCards = document.querySelectorAll('.pending-card');
            let selectedCard = null;

            pendingCards.forEach(card => {
                if (card.classList.contains('border-primary') || card.style.borderColor === 'rgb(13, 110, 253)') {
                    selectedCard = card;
                }
            });

            if (!selectedCard) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Pilih Pasien',
                    text: 'Silakan pilih pasien terlebih dahulu dari daftar pending',
                    confirmButtonColor: '#22c55e'
                });
                return;
            }

            const admissionId = selectedCard.dataset.admissionId;
            const patientName = selectedCard.dataset.patientName;

            // Show confirmation modal directly
            document.getElementById('confirm-admission-id').value = admissionId;
            document.getElementById('confirm-room-id').value = roomId;
            document.getElementById('confirm-patient-name').textContent = patientName;
            document.getElementById('confirm-room-number').textContent = roomNumber + ' (' + categoryName + ' - Kelas ' +
                className + ')';
            document.getElementById('bed-number').value = '';

            const modal = new bootstrap.Modal(document.getElementById('modalConfirmAssignment'));
            modal.show();
        }

        // Add click event to pending cards to highlight selection
        document.addEventListener('DOMContentLoaded', function() {
            const pendingCards = document.querySelectorAll('.pending-card');
            pendingCards.forEach(card => {
                card.addEventListener('click', function() {
                    // Remove highlight from all cards
                    pendingCards.forEach(c => {
                        c.style.borderColor = '';
                        c.style.borderWidth = '';
                    });
                    // Highlight selected card
                    this.style.borderColor = '#0d6efd';
                    this.style.borderWidth = '2px';
                });
            });
        });

        // Confirm assignment
        function confirmAssignment() {
            const admissionId = document.getElementById('confirm-admission-id').value;
            const roomId = document.getElementById('confirm-room-id').value;
            const bedNumber = document.getElementById('bed-number').value;

            // Show loading
            Swal.fire({
                title: 'Memproses...',
                text: 'Sedang menempatkan pasien ke ruangan',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Send request
            fetch('{{ route('api.nurse-dashboard.assign-room') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        admission_id: admissionId,
                        ruangan_id: roomId,
                        bed_number: bedNumber
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            // Close modal
                            bootstrap.Modal.getInstance(document.getElementById('modalConfirmAssignment'))
                                .hide();
                            // Refresh dashboard
                            refreshDashboard();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: data.message || 'Terjadi kesalahan saat menempatkan pasien'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Terjadi kesalahan koneksi'
                    });
                });
        }

        // Refresh dashboard
        function refreshDashboard() {
            const btn = document.getElementById('btn-refresh');
            if (btn) {
                btn.querySelector('i').classList.add('spin');
            }

            fetch('{{ route('api.nurse-dashboard.refresh') }}')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update summary stats
                        if (data.data.summary) {
                            document.getElementById('pending-count').textContent = data.data.pending_count || 0;
                            const occupiedBedsStat = document.querySelector('.stat-card.occupied .stat-number');
                            const availableBedsStat = document.querySelector('.stat-card.available .stat-number');
                            if (occupiedBedsStat) occupiedBedsStat.textContent = data.data.summary.occupied_beds || 0;
                            if (availableBedsStat) availableBedsStat.textContent = data.data.summary.available_beds ||
                                0;
                        }

                        // Update pending list
                        updatePendingList(data.data.pending_list || []);

                        // Update room availability
                        updateRoomAvailability(data.data.availability || []);

                        // Show success notification (subtle)
                        console.log('Dashboard updated:', new Date().toLocaleTimeString());
                    }
                })
                .catch(error => {
                    console.error('Error refreshing dashboard:', error);
                })
                .finally(() => {
                    if (btn) {
                        btn.querySelector('i').classList.remove('spin');
                    }
                });
        }

        function updatePendingList(pendingList) {
            const container = document.getElementById('pending-list-container');
            if (!container) return;

            if (pendingList.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="ri-checkbox-circle-line"></i>
                        </div>
                        <div class="empty-state-title">Tidak Ada Pasien Menunggu</div>
                        <div class="empty-state-text">Semua pasien rawat inap sudah ditempatkan di ruangan</div>
                    </div>
                `;
                return;
            }

            // Update existing cards or add new ones
            let html = '';
            pendingList.forEach(pending => {
                html += generatePendingCard(pending);
            });
            container.innerHTML = html;
        }

        function generatePendingCard(pending) {
            const priorityBadge = pending.priority_class === 'danger' ? '<div class="priority-high">!</div>' : '';
            const sourceBadge = pending.priority_class === 'danger' ? 'igd' : 'registration';

            let kerabatBadge = '';
            if (pending.kerabat_type === 'Kerabat Owner') {
                kerabatBadge =
                    '<span class="badge bg-warning text-dark" style="font-size: 10px;"><i class="ri-vip-crown-line"></i> Kerabat Owner</span>';
            } else if (pending.kerabat_type === 'Kerabat Dokter') {
                kerabatBadge =
                    '<span class="badge bg-primary text-white" style="font-size: 10px;"><i class="ri-user-heart-line"></i> Kerabat Dokter</span>';
            } else if (pending.kerabat_type === 'Kerabat Karyawan') {
                kerabatBadge =
                    '<span class="badge bg-success text-white" style="font-size: 10px;"><i class="ri-user-smile-line"></i> Kerabat Karyawan</span>';
            } else {
                kerabatBadge =
                    '<span class="badge bg-primary text-white" style="font-size: 10px;"><i class="ri-user-line"></i> Reguler</span>';
            }

            const birthDate = pending.birth_date !== 'N/A' ? pending.birth_date.replace(/\//g, '/') : 'N/A';

            return `
                <div class="pending-card" data-admission-id="${pending.id}" data-patient-name="${pending.patient_name}" data-medical-record="${pending.medical_record}">
                    ${priorityBadge}
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                                <span class="source-badge badge-${sourceBadge}">${pending.source_type}</span>
                                <span class="time-badge"><i class="ri-time-line"></i> ${pending.waiting_time}</span>
                                ${kerabatBadge}
                            </div>
                            <h6 class="fw-bold mb-1">${pending.patient_name}</h6>
                            <div class="small text-muted">RM: ${pending.medical_record}</div>
                        </div>
                    </div>
                    <div class="patient-info-table mb-2" style="background: #f8f9fa; border-radius: 8px; padding: 10px; font-size: 11px;">
                        <div class="row g-1">
                            <div class="col-6 d-flex align-items-center py-1">
                                <i class="ri-user-line text-muted me-2"></i>
                                <span class="text-muted" style="width: 50px;">Umur</span>
                                <span class="fw-semibold">: ${pending.age} thn (${birthDate})</span>
                            </div>
                            <div class="col-6 d-flex align-items-center py-1">
                                <i class="ri-${pending.gender === 'Laki-laki' ? 'men' : 'women'}-line text-muted me-2"></i>
                                <span class="text-muted" style="width: 50px;">Gender</span>
                                <span class="fw-semibold">: ${pending.gender}</span>
                            </div>
                            <div class="col-12 d-flex align-items-center py-1 border-top pt-2 mt-1">
                                <i class="ri-stethoscope-line text-muted me-2"></i>
                                <span class="text-muted" style="width: 50px;">Dokter</span>
                                <span class="fw-semibold" title="${pending.doctor_name}">: ${pending.doctor_name}</span>
                            </div>
                            <div class="col-12 d-flex align-items-center py-1">
                                <i class="ri-shield-check-line text-muted me-2"></i>
                                <span class="text-muted" style="width: 50px;">Jaminan</span>
                                <span class="fw-semibold">: ${pending.jenis_jaminan}</span>
                            </div>
                            ${pending.phone && pending.phone !== 'N/A' ? `
                                                                                                                                                                                                                                                                                                                                                                                    <div class="col-12 d-flex align-items-center py-1">
                                                                                                                                                                                                                                                                                                                                                                                        <i class="ri-phone-line text-muted me-2"></i>
                                                                                                                                                                                                                                                                                                                                                                                        <span class="text-muted" style="width: 50px;">Telepon</span>
                                                                                                                                                                                                                                                                                                                                                                                        <span class="fw-semibold">: ${pending.phone}</span>
                                                                                                                                                                                                                                                                                                                                                                                    </div>` : ''}
                            ${pending.address && pending.address !== 'N/A' ? `
                                                                                                                                                                                                                                                                                                                                                                                    <div class="col-12 d-flex align-items-start py-1">
                                                                                                                                                                                                                                                                                                                                                                                        <i class="ri-map-pin-line text-muted me-2 mt-1"></i>
                                                                                                                                                                                                                                                                                                                                                                                        <span class="text-muted" style="width: 50px;">Alamat</span>
                                                                                                                                                                                                                                                                                                                                                                                        <span class="fw-semibold" style="flex: 1;" title="${pending.address}">: ${pending.address}</span>
                                                                                                                                                                                                                                                                                                                                                                                    </div>` : ''}
                        </div>
                    </div>
                    ${pending.admission_reason && pending.admission_reason !== 'N/A' ? `
                                                                                                                                                                                                                                                                                                                                                                            <div class="alert alert-light border-0 mb-3 py-2">
                                                                                                                                                                                                                                                                                                                                                                                <small class="text-muted">
                                                                                                                                                                                                                                                                                                                                                                                    <i class="ri-file-text-line me-1"></i>
                                                                                                                                                                                                                                                                                                                                                                                    <strong>Alasan:</strong> ${pending.admission_reason}
                                                                                                                                                                                                                                                                                                                                                                                </small>
                                                                                                                                                                                                                                                                                                                                                                            </div>` : ''}
                    <button class="btn btn-assign w-100" onclick="showRoomSelection('${pending.id}', '${pending.patient_name}')">
                        <i class="ri-door-open-line me-2"></i>
                        Tempatkan di Ruangan
                    </button>
                </div>
            `;
        }

        function updateRoomAvailability(availability) {
            const container = document.getElementById('rooms-availability-container');
            if (!container) return;

            let html = '';
            availability.forEach(category => {
                Object.entries(category.classes).forEach(([className, classData]) => {
                    html += `
                        <div class="card mb-3 border-0 shadow-sm">
                            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-2">
                                <div>
                                    <span class="badge bg-primary me-2">${category.category_name}</span>
                                    <span class="badge bg-info">Kelas ${className}</span>
                                </div>
                                <div class="text-muted small">
                                    <i class="ri-door-line"></i> ${classData.rooms.length} Ruangan
                                    | <i class="ri-checkbox-circle-line text-success"></i> ${classData.available_beds} Tersedia
                                </div>
                            </div>
                            <div class="card-body p-2">
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover mb-0" style="font-size: 12px;">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="20%" class="text-center">No. Ruangan</th>
                                                <th width="25%" class="text-center">Kapasitas</th>
                                                <th width="35%">Tingkat Hunian</th>
                                                <th width="20%" class="text-center">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>`;

                    classData.rooms.forEach(room => {
                        const percentage = room.capacity > 0 ? Math.round((room.occupied / room
                            .capacity) * 100) : 0;

                        html += `
                            <tr class="align-middle cursor-pointer"
                                onclick="handleRoomClick('${room.id}', '${room.room_number}', '${category.category_name}', '${className}', ${room.occupied}, ${room.available})"
                                style="cursor: pointer;">
                                <td class="text-center">
                                    <strong class="text-primary">${room.room_number}</strong>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border">
                                        <i class="ri-user-line"></i> ${room.occupied}/${room.capacity}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1" style="height: 8px;">
                                            <div class="progress-bar ${room.available > 0 ? 'bg-success' : 'bg-danger'}"
                                                style="width: ${percentage}%">
                                            </div>
                                        </div>
                                        <small class="text-muted" style="min-width: 60px;">${percentage}%</small>
                                    </div>
                                </td>
                                <td class="text-center">
                                    ${room.available > 0 ?
                                        `<span class="badge bg-success-subtle text-success border border-success">
                                                                                                                                                                                                                                                                                                                                                                                                    <i class="ri-check-line"></i> ${room.available} Kosong
                                                                                                                                                                                                                                                                                                                                                                                                </span>` :
                                        `<span class="badge bg-danger-subtle text-danger border border-danger">
                                                                                                                                                                                                                                                                                                                                                                                                    <i class="ri-close-line"></i> Penuh
                                                                                                                                                                                                                                                                                                                                                                                                </span>`
                                    }
                                </td>
                            </tr>`;
                    });

                    html += `
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>`;
                });
            });

            container.innerHTML = html;
        }

        // Auto refresh every 30 seconds
        setInterval(refreshDashboard, 30000);

        // All modals have backdrop disabled - no backdrop management needed

        // Add spin animation
        const style = document.createElement('style');
        style.textContent = `
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .spin {
            animation: spin 1s linear infinite;
        }
    `;
        document.head.appendChild(style);

        // ==================== VITAL SIGNS FUNCTIONS ====================

        // Open vital signs input modal
        function openVitalSignsModal(admissionId, patientName, bedNumber) {
            document.getElementById('vital-admission-id').value = admissionId;
            document.getElementById('vital-patient-name').textContent = patientName;
            document.getElementById('vital-bed-number').textContent = bedNumber;

            // Reset form
            document.getElementById('formVitalSigns').reset();
            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());

            // Show modal
            const modalElement = document.getElementById('modalVitalSigns');
            const modal = new bootstrap.Modal(modalElement);

            // No backdrop management needed

            modal.show();

            // Fix aria-hidden issue
            modalElement.addEventListener('hidden.bs.modal', function() {
                if (document.activeElement) {
                    document.activeElement.blur();
                }
            });
        }

        // Save vital signs
        function saveVitalSigns(event) {
            event.preventDefault();

            const form = document.getElementById('formVitalSigns');
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());

            // Show loading
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="ri-loader-4-line spin me-1"></i>Menyimpan...';

            // Send AJAX request
            fetch('{{ route('kunjungan.nurse-dashboard.store-vital-signs') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        // Close modal
                        bootstrap.Modal.getInstance(document.getElementById('modalVitalSigns')).hide();

                        // Show success message with abnormal warnings
                        let message = 'Vital signs berhasil disimpan!';
                        if (result.warnings && result.warnings.length > 0) {
                            message += '<br><br><strong>Perhatian:</strong><br>' + result.warnings.join('<br>');
                        }

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            html: message,
                            confirmButtonText: 'OK'
                        });

                        // Refresh room patients modal if open
                        const roomModal = bootstrap.Modal.getInstance(document.getElementById('modalRoomPatients'));
                        if (roomModal && document.getElementById('modalRoomPatients').classList.contains('show')) {
                            const roomId = document.getElementById('modalRoomPatients').getAttribute('data-room-id');
                            if (roomId) {
                                // Refresh the room patients list
                                const roomInfo = {
                                    id: roomId,
                                    number: document.getElementById('room-info-number').textContent,
                                    category: document.getElementById('room-info-category').textContent,
                                    className: document.getElementById('room-info-class').textContent
                                };
                                showRoomPatients(roomInfo.id, roomInfo.number, roomInfo.category, roomInfo.className,
                                    true, 0);
                            }
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: result.message || 'Terjadi kesalahan saat menyimpan vital signs.',
                            confirmButtonText: 'OK'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Terjadi kesalahan sistem. Silakan coba lagi.',
                        confirmButtonText: 'OK'
                    });
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                });
        }



        // View vital signs history
        function viewVitalSignsHistory(admissionId, patientName) {
            document.getElementById('history-patient-name').textContent = patientName;

            // Show loading
            document.getElementById('vital-signs-history-container').innerHTML = `
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            `;

            // Show modal directly without closing parent
            const historyModalElement = document.getElementById('modalVitalSignsHistory');
            const historyModal = new bootstrap.Modal(historyModalElement);

            // No backdrop management needed

            // Add event listener for when history modal is closed
            document.getElementById('modalVitalSignsHistory').addEventListener('hidden.bs.modal', function(event) {
                if (document.activeElement) {
                    document.activeElement.blur();
                }
            }, {
                once: true
            });

            historyModal.show();

            // Fetch history
            fetch(`/kunjungan/nurse-dashboard/vital-signs-history/${admissionId}`)
                .then(response => response.json())
                .then(data => {
                    console.debug('[VitalHistory] Response:', data);

                    // Handle different response formats
                    let vitalSigns = [];
                    if (data && data.vital_signs) {
                        vitalSigns = data.vital_signs;
                    } else if (data && data.data && data.data.vital_signs) {
                        vitalSigns = data.data.vital_signs;
                    } else if (Array.isArray(data)) {
                        vitalSigns = data;
                    }

                    displayVitalSignsHistory(vitalSigns);
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('vital-signs-history-container').innerHTML = `
                        <div class="alert alert-danger">
                            <i class="ri-error-warning-line me-2"></i>
                            Gagal memuat riwayat vital signs
                        </div>
                    `;
                });
        }

        // Display vital signs history
        function displayVitalSignsHistory(vitalSigns) {
            const container = document.getElementById('vital-signs-history-container');

            // Ensure vitalSigns is an array
            const signs = Array.isArray(vitalSigns) ? vitalSigns : [];

            if (signs.length === 0) {
                container.innerHTML = `
                    <div class="alert alert-info">
                        <i class="ri-information-line me-2"></i>
                        Belum ada riwayat vital signs
                    </div>
                `;
                return;
            }

            let html = `
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead class="table-light">
                            <tr>
                                <th>Tanggal & Waktu</th>
                                <th>TD (mmHg)</th>
                                <th>Nadi</th>
                                <th>Suhu (°C)</th>
                                <th>RR</th>
                                <th>SpO2 (%)</th>
                                <th>Kesadaran</th>
                                <th>Dicatat Oleh</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            signs.forEach(vs => {
                // Check for abnormal values
                const tdClass = (vs.blood_pressure_systolic < 90 || vs.blood_pressure_systolic > 140 ||
                        vs.blood_pressure_diastolic < 60 || vs.blood_pressure_diastolic > 90) ?
                    'text-danger fw-bold' :
                    '';
                const hrClass = (vs.heart_rate < 60 || vs.heart_rate > 100) ? 'text-danger fw-bold' : '';
                const tempClass = (vs.temperature < 36.0 || vs.temperature > 37.5) ? 'text-danger fw-bold' : '';
                const rrClass = (vs.respiratory_rate < 12 || vs.respiratory_rate > 20) ? 'text-danger fw-bold' : '';
                const spo2Class = (vs.oxygen_saturation < 95) ? 'text-danger fw-bold' : '';
                const consciousnessClass = (vs.consciousness_level !== 'Compos Mentis') ? 'text-danger fw-bold' :
                    '';

                // Format consciousness level display (use the value as is since it's already user-friendly)
                let consciousnessDisplay = vs.consciousness_level || '-';
                html += `
                    <tr>
                        <td>${vs.measurement_time}</td>
                        <td class="${tdClass}">${vs.blood_pressure_systolic}/${vs.blood_pressure_diastolic}</td>
                        <td class="${hrClass}">${vs.heart_rate}</td>
                        <td class="${tempClass}">${vs.temperature}</td>
                        <td class="${rrClass}">${vs.respiratory_rate}</td>
                        <td class="${spo2Class}">${vs.oxygen_saturation}</td>
                        <td class="${consciousnessClass}">${consciousnessDisplay}</td>
                        <td><small>${vs.recorded_by}</small></td>
                    </tr>
                `;

                if (vs.notes) {
                    html += `
                        <tr class="table-active">
                            <td colspan="8" class="py-2">
                                <small><i class="ri-file-text-line me-1"></i><strong>Catatan:</strong> ${vs.notes}</small>
                            </td>
                        </tr>
                    `;
                }
            });

            html += `
                        </tbody>
                    </table>
                </div>
            `;

            container.innerHTML = html;
        }

        // ==================== ALL PATIENTS VITAL SIGNS FUNCTIONS ====================
        let allPatientsData = [];
        let currentPage = 1;
        const itemsPerPage = 50;

        function normalizePatientsData(raw) {
            if (Array.isArray(raw)) {
                return raw;
            }

            if (raw && typeof raw === 'object') {
                return Object.values(raw);
            }

            return [];
        }

        function resolvePatientsPayload(apiResponse) {
            if (!apiResponse || typeof apiResponse !== 'object') {
                return [];
            }

            // Handle controller response format: { success: true, data: { patients: [...] } }
            if (apiResponse.data && apiResponse.data.patients) {
                return apiResponse.data.patients;
            }

            // Handle direct patients array
            if (apiResponse.patients) {
                return apiResponse.patients;
            }

            // Handle wrapped data format
            const candidate = apiResponse.data ?? apiResponse;
            if (candidate && typeof candidate === 'object' && 'data' in candidate && Array.isArray(candidate.data)) {
                return candidate.data;
            }

            return Array.isArray(candidate) ? candidate : [];
        }

        function getValueByPaths(source, paths, fallback = null) {
            if (!source) {
                return fallback;
            }

            const candidates = Array.isArray(paths) ? paths : [paths];

            for (const path of candidates) {
                const segments = path.split('.');
                let current = source;
                let valid = true;

                for (const segment of segments) {
                    if (current && Object.prototype.hasOwnProperty.call(current, segment)) {
                        current = current[segment];
                    } else {
                        valid = false;
                        break;
                    }
                }

                if (valid && current !== undefined && current !== null && current !== '') {
                    return current;
                }
            }

            return fallback;
        }

        function getPatientField(patient, paths, fallback = '-') {
            const value = getValueByPaths(patient, paths, fallback);
            return value === undefined || value === null || value === '' ? fallback : value;
        }

        function getPatientSearchFields(patient) {
            return {
                name: (getPatientField(patient, ['patient_name', 'name', 'patient.name'], '') + '').toLowerCase(),
                rm: (getPatientField(patient, ['rekam_medis', 'rekamMedis', 'patient.rekam_medis'], '') + '').toLowerCase(),
                room: (getPatientField(patient, ['room_number', 'room.number', 'room_name', 'room'], '') + '').toLowerCase()
            };
        }

        // Open all patients vital signs modal
        function openAllPatientsVitalSigns() {
            const modal = new bootstrap.Modal(document.getElementById('modalAllPatientsVitalSigns'));
            modal.show();

            // Fix aria-hidden issue
            document.getElementById('modalAllPatientsVitalSigns').addEventListener('hidden.bs.modal', function() {
                if (document.activeElement) {
                    document.activeElement.blur();
                }
            });

            // Load all patients
            loadAllInpatients();
        }

        // Load all inpatients
        function loadAllInpatients() {
            document.getElementById('all-patients-container').innerHTML = `
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            `;

            fetch('/kunjungan/nurse-dashboard/all-inpatients')
                .then(response => response.json())
                .then(data => {
                    console.debug('[NurseBed] all-inpatients response:', data);
                    console.debug('[NurseBed] response structure check:', {
                        hasSuccess: 'success' in data,
                        success: data.success,
                        hasData: 'data' in data,
                        dataType: typeof data.data,
                        hasPatients: data.data && 'patients' in data.data,
                        patientsType: data.data && data.data.patients ? typeof data.data.patients : 'undefined',
                        patientsLength: data.data && data.data.patients ? data.data.patients.length : 0
                    });
                    if (data.success) {
                        const resolvedPatients = resolvePatientsPayload(data);
                        console.debug('[NurseBed] resolved patients:', resolvedPatients);
                        allPatientsData = normalizePatientsData(resolvedPatients);
                        console.debug('[NurseBed] normalized inpatient list:', allPatientsData);
                        if (allPatientsData.length > 0) {
                            console.debug('[NurseBed] sample patient data:', allPatientsData[0]);
                        }
                        currentPage = 1;
                        displayAllPatients();
                    } else {
                        document.getElementById('all-patients-container').innerHTML = `
                            <div class="alert alert-danger">
                                <i class="ri-error-warning-line me-2"></i>
                                ${data.message || 'Gagal memuat data pasien'}
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('[NurseBed] all-inpatients error:', error);
                    document.getElementById('all-patients-container').innerHTML = `
                        <div class="alert alert-danger">
                            <i class="ri-error-warning-line me-2"></i>
                            Terjadi kesalahan saat memuat data pasien
                        </div>
                    `;
                });
        }

        // Search patients
        function searchAllPatients() {
            const searchTerm = document.getElementById('searchAllPatients').value.toLowerCase();
            const sourceList = normalizePatientsData(allPatientsData);
            const filtered = sourceList.filter(patient => {
                const fields = getPatientSearchFields(patient);
                return fields.name.includes(searchTerm) ||
                    fields.rm.includes(searchTerm) ||
                    fields.room.includes(searchTerm);
            });

            currentPage = 1;
            displayAllPatients(filtered);
        }

        // Display all patients with pagination
        function displayAllPatients(dataToDisplay = null) {
            const chosen = (dataToDisplay !== null && dataToDisplay !== undefined) ? dataToDisplay : allPatientsData;
            const data = normalizePatientsData(chosen);
            const container = document.getElementById('all-patients-container');

            if (data.length === 0) {
                container.innerHTML = `
                    <div class="alert alert-info">
                        <i class="ri-information-line me-2"></i>
                        ${dataToDisplay ? 'Tidak ada pasien yang sesuai dengan pencarian' : 'Belum ada pasien rawat inap di ruangan'}
                    </div>
                `;
                document.getElementById('pagination-container').innerHTML = '';
                return;
            }

            // Calculate pagination
            const totalPages = Math.ceil(data.length / itemsPerPage);
            const startIndex = (currentPage - 1) * itemsPerPage;
            const endIndex = startIndex + itemsPerPage;
            const paginatedData = data.slice(startIndex, endIndex);

            // Build table
            let html = `
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 5%;">No</th>
                                <th style="width: 15%;">Nama Pasien</th>
                                <th style="width: 10%;">RM</th>
                                <th style="width: 10%;">Ruangan</th>
                                <th style="width: 8%;">Bed</th>
                                <th style="width: 12%;">Dokter</th>
                                <th style="width: 10%;">Jaminan</th>
                                <th style="width: 10%;">Status Kerabat</th>
                                <th style="width: 10%;">Lama Rawat</th>
                                <th style="width: 10%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            paginatedData.forEach((patient, index) => {
                const rowNumber = startIndex + index + 1;
                const admissionId = getPatientField(patient, ['admission_id', 'id', 'admissionId'], '');
                const patientName = getPatientField(patient, ['patient_name', 'name', 'patient.name'],
                    'Tidak diketahui');
                const age = getPatientField(patient, ['age', 'patient_age', 'patient.age'], 'N/A');
                const gender = getPatientField(patient, ['gender', 'patient_gender', 'patient.gender'], 'N/A');
                const rekamMedis = getPatientField(patient, ['rekam_medis', 'rekamMedis', 'patient.rekam_medis'],
                    '-');
                const roomNumber = getPatientField(patient, ['room_number', 'room.number', 'room_name'], '-');
                const bedNumber = getPatientField(patient, ['bed_number', 'bedNumber'], '-');
                const doctorName = getPatientField(patient, ['doctor', 'doctor_name', 'doctor.name'], '-');
                const jaminan = getPatientField(patient, ['jaminan', 'jaminan_name', 'encounter.jenisJaminan.name'],
                    '-');
                const daysStayed = getPatientField(patient, ['days_stayed', 'length_of_stay'], '-');
                const kerabatType = getPatientField(patient, ['kerabat_type', 'kerabatType'], 'Reguler');

                // Badge kerabat
                let kerabatBadge = '';
                if (kerabatType === 'Owner') {
                    kerabatBadge =
                        '<span class="badge bg-warning text-dark" style="font-size: 9px;"><i class="ri-vip-crown-line"></i> Kerabat Owner</span>';
                } else if (kerabatType === 'Dokter') {
                    kerabatBadge =
                        '<span class="badge bg-primary text-white" style="font-size: 9px;"><i class="ri-user-heart-line"></i> Kerabat Dokter</span>';
                } else if (kerabatType === 'Karyawan') {
                    kerabatBadge =
                        '<span class="badge bg-success text-white" style="font-size: 9px;"><i class="ri-user-smile-line"></i> Kerabat Karyawan</span>';
                } else {
                    kerabatBadge =
                        '<span class="badge bg-primary text-white" style="font-size: 9px;"><i class="ri-user-line"></i> Reguler</span>';
                }

                html += `
                    <tr>
                        <td class="text-center">${rowNumber}</td>
                        <td>
                            <strong>${patientName}</strong>
                            <div class="small text-muted">${age}, ${gender}</div>
                        </td>
                        <td><span class="badge bg-primary">${rekamMedis}</span></td>
                        <td>${roomNumber}</td>
                        <td class="text-center"><span class="badge bg-info">${bedNumber}</span></td>
                        <td><small>${doctorName}</small></td>
                        <td><small>${jaminan}</small></td>
                        <td>${kerabatBadge}</td>
                        <td>
                            <span class="badge bg-success">${daysStayed}</span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <button class="btn btn-primary" onclick="openVitalSignsFromList('${admissionId}', '${patientName}', '${bedNumber}')" title="Input Vital Signs">
                                    <i class="ri-heart-pulse-line"></i>
                                </button>
                                <button class="btn btn-outline-primary" onclick="viewVitalSignsHistory('${admissionId}', '${patientName}')" title="Riwayat Vital Signs">
                                    <i class="ri-history-line"></i>
                                </button>
                                <button class="btn btn-success" onclick="addMedicationForPatient('${admissionId}', '${patientName}')" title="Catat Obat">
                                    <i class="ri-medicine-bottle-line"></i>
                                </button>
                                <button class="btn btn-info" onclick="addNursingNoteForPatient('${admissionId}', '${patientName}')" title="Catatan Keperawatan">
                                    <i class="ri-file-text-line"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });

            html += `
                        </tbody>
                    </table>
                </div>
            `;

            container.innerHTML = html;

            // Build pagination
            buildPagination(totalPages, data);
        }

        // Build pagination controls
        function buildPagination(totalPages, data) {
            const container = document.getElementById('pagination-container');

            if (totalPages <= 1) {
                container.innerHTML = '';
                return;
            }

            let html = `
                <nav>
                    <ul class="pagination justify-content-center mb-0">
                        <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                            <a class="page-link" href="#" onclick="changePage(${currentPage - 1}, event)">
                                <i class="ri-arrow-left-s-line"></i>
                            </a>
                        </li>
            `;

            // Page numbers
            const maxVisiblePages = 5;
            let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
            let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);

            if (endPage - startPage < maxVisiblePages - 1) {
                startPage = Math.max(1, endPage - maxVisiblePages + 1);
            }

            if (startPage > 1) {
                html += `
                    <li class="page-item">
                        <a class="page-link" href="#" onclick="changePage(1, event)">1</a>
                    </li>
                `;
                if (startPage > 2) {
                    html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                }
            }

            for (let i = startPage; i <= endPage; i++) {
                html += `
                    <li class="page-item ${i === currentPage ? 'active' : ''}">
                        <a class="page-link" href="#" onclick="changePage(${i}, event)">${i}</a>
                    </li>
                `;
            }

            if (endPage < totalPages) {
                if (endPage < totalPages - 1) {
                    html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                }
                html += `
                    <li class="page-item">
                        <a class="page-link" href="#" onclick="changePage(${totalPages}, event)">${totalPages}</a>
                    </li>
                `;
            }

            html += `
                        <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                            <a class="page-link" href="#" onclick="changePage(${currentPage + 1}, event)">
                                <i class="ri-arrow-right-s-line"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
                <div class="text-center mt-2">
                    <small class="text-muted">
                        Halaman ${currentPage} dari ${totalPages} | Total: ${data.length} pasien
                    </small>
                </div>
            `;

            container.innerHTML = html;
        }

        // Change page
        function changePage(page, event) {
            if (event) {
                event.preventDefault();
            }

            const searchTerm = document.getElementById('searchAllPatients').value.toLowerCase();
            const baseList = normalizePatientsData(allPatientsData);
            const filtered = searchTerm ? baseList.filter(patient => {
                const fields = getPatientSearchFields(patient);
                return fields.name.includes(searchTerm) ||
                    fields.rm.includes(searchTerm) ||
                    fields.room.includes(searchTerm);
            }) : baseList;

            const totalPages = Math.ceil(filtered.length / itemsPerPage);

            if (page < 1 || page > totalPages) return;

            currentPage = page;
            displayAllPatients(filtered);

            // Scroll to top of table
            document.getElementById('all-patients-container').scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }

        // Open vital signs modal from list
        function openVitalSignsFromList(admissionId, patientName, bedNumber) {
            // Open vital signs input modal directly without closing parent
            openVitalSignsModal(admissionId, patientName, bedNumber);

            // Add event listener for when vital signs modal is closed
            document.getElementById('modalVitalSigns').addEventListener('hidden.bs.modal', function(event) {
                if (document.activeElement) {
                    document.activeElement.blur();
                }
            }, {
                once: true
            });
        }

        // ==================== MEDICATION MONITORING FUNCTIONS ====================

        // Open medication schedule modal
        function openMedicationSchedule() {
            const modal = new bootstrap.Modal(document.getElementById('modalMedicationSchedule'));
            modal.show();
            loadMedicationSchedule();
        }

        // Load medication schedule from prescription orders
        function loadMedicationSchedule() {
            document.getElementById('medication-schedule-container').innerHTML = `
                <div class="text-center py-4">
                    <div class="spinner-border text-success" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Memuat jadwal obat dari resep dokter...</p>
                </div>
            `;

            // Fetch actual prescription orders for today
            fetch('/kunjungan/nurse-dashboard/medication-schedule')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayMedicationSchedule(data.data);
                    } else {
                        document.getElementById('medication-schedule-container').innerHTML = `
                            <div class="alert alert-warning">
                                <i class="ri-alert-line me-2"></i>
                                ${data.message || 'Gagal memuat jadwal obat'}
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error loading medication schedule:', error);
                    document.getElementById('medication-schedule-container').innerHTML = `
                        <div class="alert alert-danger">
                            <i class="ri-error-warning-line me-2"></i>
                            Terjadi kesalahan saat memuat jadwal obat. Silakan refresh halaman.
                        </div>
                    `;
                });
        }

        // Display medication schedule from prescriptions
        function displayMedicationSchedule(data) {
            const container = document.getElementById('medication-schedule-container');

            if (!data || data.length === 0) {
                container.innerHTML = `
                    <div class="alert alert-info">
                        <i class="ri-information-line me-2"></i>
                        Tidak ada jadwal pemberian obat untuk hari ini.<br>
                        <small class="text-muted">Pastikan dokter sudah menulis resep dan farmasi sudah memverifikasi.</small>
                    </div>
                `;
                return;
            }

            let html = '<div class="row">';
            data.forEach(patient => {
                html += `
                    <div class="col-lg-6 mb-3">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-gradient-success text-white">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>${patient.patient_name}</strong><br>
                                        <small>RM: ${patient.medical_record} | ${patient.room_number}</small>
                                    </div>
                                    <span class="badge bg-light text-dark">${patient.medications?.length || 0} obat</span>
                                </div>
                            </div>
                            <div class="card-body p-0">
                `;

                if (patient.medications && patient.medications.length > 0) {
                    patient.medications.forEach((med, index) => {
                        const now = new Date();
                        const scheduledTime = new Date(`${now.toDateString()} ${med.scheduled_time}`);
                        const isOverdue = now > scheduledTime && med.status === 'Pending';
                        const isUpcoming = scheduledTime > now && (scheduledTime - now) <= 30 * 60 *
                            1000; // 30 minutes

                        let statusClass = 'secondary';
                        let statusIcon = 'ri-time-line';
                        let statusText = med.status;

                        if (med.status === 'Given') {
                            statusClass = 'success';
                            statusIcon = 'ri-check-line';
                            statusText = 'Sudah Diberikan';
                        } else if (isOverdue) {
                            statusClass = 'danger';
                            statusIcon = 'ri-alert-line';
                            statusText = 'Terlambat';
                        } else if (isUpcoming) {
                            statusClass = 'warning';
                            statusIcon = 'ri-alarm-line';
                            statusText = 'Segera';
                        } else if (med.status === 'Pending') {
                            statusClass = 'info';
                            statusIcon = 'ri-time-line';
                            statusText = 'Terjadwal';
                        }

                        html += `
                            <div class="medication-item p-3 ${index < patient.medications.length - 1 ? 'border-bottom' : ''}">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center mb-1">
                                            <strong class="me-2">${med.medication_name}</strong>
                                            <span class="badge bg-outline-primary">${med.dosage}</span>
                                        </div>
                                        <div class="text-muted small mb-2">
                                            <i class="ri-route-line me-1"></i>${med.route} |
                                            <i class="ri-time-line me-1"></i>${med.scheduled_time} |
                                            <i class="ri-user-line me-1"></i>Dr. ${med.doctor_name}
                                        </div>
                                        <div class="text-muted small">
                                            <i class="ri-medicine-bottle-line me-1"></i>Farmasi: ${med.pharmacy_status || 'Belum Diverifikasi'}
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-${statusClass} mb-2">
                                            <i class="${statusIcon} me-1"></i>${statusText}
                                        </span>
                                        ${med.status === 'Pending' && med.pharmacy_status === 'Verified' ? `
                                                                                                                                                                                    <br><button class="btn btn-sm btn-success" onclick="confirmMedicationAdministration('${med.prescription_order_id}', '${patient.patient_name}', '${med.medication_name}')">
                                                                                                                                                                                        <i class="ri-check-line me-1"></i>Berikan
                                                                                                                                                                                    </button>
                                                                                                                                                                                ` : ''}
                                    </div>
                                </div>
                                ${med.administration_notes ? `
                                                                                                                                                                            <div class="mt-2 p-2 bg-light rounded">
                                                                                                                                                                                <small><strong>Catatan:</strong> ${med.administration_notes}</small>
                                                                                                                                                                            </div>
                                                                                                                                                                        ` : ''}
                            </div>
                        `;
                    });
                } else {
                    html += `
                        <div class="p-3 text-center text-muted">
                            <i class="ri-medicine-bottle-line fs-1 mb-2"></i>
                            <p>Belum ada resep obat untuk pasien ini</p>
                        </div>
                    `;
                }

                html += `
                            </div>
                        </div>
                    </div>
                `;
            });
            html += '</div>';

            container.innerHTML = html;
        }

        // Refresh medication schedule
        function refreshMedicationSchedule() {
            loadMedicationSchedule();
        }

        // Confirm medication administration
        function confirmMedicationAdministration(prescriptionOrderId, patientName, medicationName) {
            // Set prescription order ID
            document.getElementById('prescription-order-id').value = prescriptionOrderId;
            document.getElementById('selected-patient-name').value = patientName;

            // Fetch prescription details
            fetch(`/kunjungan/nurse-dashboard/prescription-order/${prescriptionOrderId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const prescription = data.data;
                        document.getElementById('prescription-info').textContent =
                            `${prescription.medication_name} - ${prescription.dosage} | Dr. ${prescription.doctor_name}`;
                        document.getElementById('pharmacy-verification').textContent = prescription.pharmacy_status;
                        document.getElementById('prescribed-medication').value = prescription.medication_name;
                        document.getElementById('prescribed-dosage').value = prescription.dosage;
                        document.getElementById('prescribed-route').value = prescription.route;

                        // Set current time
                        const now = new Date();
                        const timeString = now.toISOString().slice(0, 16);
                        document.querySelector('#modalAddMedication input[name="actual_given_time"]').value =
                            timeString;

                        const modal = new bootstrap.Modal(document.getElementById('modalAddMedication'));
                        modal.show();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Gagal memuat detail resep'
                    });
                });
        }

        // Add medication for specific patient - show pending prescriptions
        function addMedicationForPatient(admissionId, patientName) {
            // Fetch pending medications for this patient
            fetch(`/kunjungan/nurse-dashboard/patient-pending-medications/${admissionId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.data.length > 0) {
                        // Show list of pending medications for this patient
                        showPatientPendingMedications(data.data, patientName);
                    } else {
                        Swal.fire({
                            icon: 'info',
                            title: 'Tidak Ada Obat',
                            text: `Tidak ada obat yang perlu diberikan untuk ${patientName} saat ini.`
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Gagal memuat data obat pasien'
                    });
                });
        }

        // Show patient pending medications modal
        function showPatientPendingMedications(medications, patientName) {
            let html = `
                <div class="mb-3">
                    <h6>Obat yang perlu diberikan untuk <strong>${patientName}</strong>:</h6>
                </div>
            `;

            medications.forEach(med => {
                html += `
                    <div class="card mb-2">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>${med.medication_name}</strong> ${med.dosage}<br>
                                    <small class="text-muted">
                                        ${med.route} | Jadwal: ${med.scheduled_time} | Dr. ${med.doctor_name}
                                    </small>
                                </div>
                                <button class="btn btn-success btn-sm" onclick="confirmMedicationAdministration('${med.prescription_order_id}', '${patientName}', '${med.medication_name}')">
                                    <i class="ri-check-line me-1"></i>Berikan
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            });

            Swal.fire({
                title: 'Obat Tertunda',
                html: html,
                width: '600px',
                confirmButtonText: 'Tutup',
                confirmButtonColor: '#6c757d'
            });
        }

        // Save medication administration record
        function saveMedicationRecord(event) {
            event.preventDefault();

            const formData = new FormData(event.target);
            const data = Object.fromEntries(formData.entries());

            Swal.fire({
                title: 'Menyimpan...',
                text: 'Sedang mencatat pemberian obat',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Send to medication administration endpoint
            fetch('/kunjungan/nurse-dashboard/record-medication-administration', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Pemberian obat berhasil dicatat',
                            timer: 2000,
                            showConfirmButton: false
                        });

                        bootstrap.Modal.getInstance(document.getElementById('modalAddMedication')).hide();
                        loadMedicationSchedule();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: result.message || 'Terjadi kesalahan saat mencatat pemberian obat'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Terjadi kesalahan koneksi'
                    });
                });
        }

        // Search medication
        function searchMedication() {
            // Implementation for medication search
            console.log('Searching medication...');
        }

        // ==================== NURSING NOTES FUNCTIONS ====================

        // Open nursing notes modal
        function openNursingNotes() {
            const modal = new bootstrap.Modal(document.getElementById('modalNursingNotes'));
            modal.show();
            loadNursingNotes();
        }

        // Load nursing notes
        function loadNursingNotes() {
            document.getElementById('nursing-notes-container').innerHTML = `
                <div class="text-center py-4">
                    <div class="spinner-border text-info" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            `;

            // Simulate API call
            setTimeout(() => {
                displayNursingNotes([{
                    id: 1,
                    patient_name: 'Jane Smith',
                    room_number: 'R002',
                    observation_time: '2024-11-28 10:30',
                    category: 'Assessment',
                    priority: 'Normal',
                    subjective: 'Pasien mengeluh nyeri pada bagian perut',
                    objective: 'TD: 120/80, Nadi: 80x/menit, tampak gelisah',
                    assessment: 'Nyeri akut berhubungan dengan kondisi post operasi',
                    plan: 'Berikan analgetik sesuai instruksi dokter, monitor tanda vital'
                }]);
            }, 1000);
        }

        // Display nursing notes
        function displayNursingNotes(data) {
            const container = document.getElementById('nursing-notes-container');

            if (data.length === 0) {
                container.innerHTML = `
                    <div class="alert alert-info">
                        <i class="ri-information-line me-2"></i>
                        Belum ada catatan keperawatan hari ini
                    </div>
                `;
                return;
            }

            let html = '<div class="row">';
            data.forEach(note => {
                const priorityClass = note.priority === 'Critical' ? 'danger' : note.priority === 'High' ?
                    'warning' : 'success';
                html += `
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>${note.patient_name}</strong> - ${note.room_number}
                                    <span class="badge bg-primary ms-2">${note.category}</span>
                                </div>
                                <span class="badge bg-${priorityClass}">${note.priority}</span>
                            </div>
                            <div class="card-body">
                                <small class="text-muted">${note.observation_time}</small>
                                <div class="mt-2">
                                    <strong>S:</strong> ${note.subjective || '-'}<br>
                                    <strong>O:</strong> ${note.objective}<br>
                                    <strong>A:</strong> ${note.assessment}<br>
                                    <strong>P:</strong> ${note.plan}
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            html += '</div>';

            container.innerHTML = html;
        }

        // Add nursing note
        function addNursingNote() {
            loadPatientList('nursing-patient-select');

            const now = new Date();
            const timeString = now.toISOString().slice(0, 16);
            document.querySelector('#modalAddNursingNote input[name="observation_time"]').value = timeString;

            const modal = new bootstrap.Modal(document.getElementById('modalAddNursingNote'));
            modal.show();
        }

        // Add nursing note for specific patient
        function addNursingNoteForPatient(admissionId, patientName) {
            addNursingNote();

            setTimeout(() => {
                const select = document.getElementById('nursing-patient-select');
                const option = Array.from(select.options).find(opt => opt.value == admissionId);
                if (option) {
                    select.value = admissionId;
                }
            }, 100);
        }

        // Save nursing note
        function saveNursingNote(event) {
            event.preventDefault();

            Swal.fire({
                title: 'Menyimpan...',
                text: 'Sedang menyimpan catatan keperawatan',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            setTimeout(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Catatan keperawatan berhasil disimpan',
                    timer: 2000,
                    showConfirmButton: false
                });

                bootstrap.Modal.getInstance(document.getElementById('modalAddNursingNote')).hide();
                loadNursingNotes();
            }, 1500);
        }

        // Search nursing notes
        function searchNursingNotes() {
            console.log('Searching nursing notes...');
        }

        // ==================== PRESCRIPTION ORDERS FUNCTIONS ====================

        // Open prescription orders modal
        function openPrescriptionOrders() {
            const modal = new bootstrap.Modal(document.getElementById('modalPrescriptionOrders'));
            modal.show();
            loadPrescriptionOrders();
        }

        // Load prescription orders
        function loadPrescriptionOrders() {
            document.getElementById('prescription-orders-container').innerHTML = `
                <div class="text-center py-4">
                    <div class="spinner-border text-success" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            `;

            // Check if we already have patient data
            if (allPatientsData && allPatientsData.length > 0) {
                const patients = normalizePatientsData(allPatientsData);
                displayPrescriptionOrders(patients);
                return;
            }

            // Fetch patient data from API if not available
            fetch('/kunjungan/nurse-dashboard/all-inpatients')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        allPatientsData = data.data; // Store for future use
                        const patients = normalizePatientsData(data.data);
                        displayPrescriptionOrders(patients);
                    } else {
                        document.getElementById('prescription-orders-container').innerHTML = `
                            <div class="alert alert-warning">
                                <i class="ri-alert-line me-2"></i>
                                ${data.message || 'Gagal memuat data pasien'}
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error loading patients for prescription orders:', error);
                    document.getElementById('prescription-orders-container').innerHTML = `
                        <div class="alert alert-danger">
                            <i class="ri-error-warning-line me-2"></i>
                            Terjadi kesalahan saat memuat data pasien. Silakan refresh halaman.
                        </div>
                    `;
                });
        }

        // Display prescription orders
        function displayPrescriptionOrders(patients) {
            const container = document.getElementById('prescription-orders-container');

            if (patients.length === 0) {
                const message = auth_user_role === 2 ?
                    'Tidak ada pasien rawat inap yang menjadi tanggung jawab Anda' :
                    'Tidak ada pasien rawat inap untuk resep obat';

                container.innerHTML = `
                    <div class="alert alert-info">
                        <i class="ri-information-line me-2"></i>
                        ${message}
                    </div>
                `;
                return;
            }

            let html = `
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle">
                        <thead class="table-success">
                            <tr>
                                <th width="5%" class="text-center">#</th>
                                <th width="20%">Pasien</th>
                                <th width="15%">Lokasi</th>
                                <th width="20%">Dokter DPJP</th>
                                <th width="15%">Status</th>
                                <th width="25%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            patients.forEach((patient, index) => {
                const admissionId = getPatientField(patient, ['admission_id', 'id'], '');
                const encounterId = getPatientField(patient, ['encounter_id'], admissionId);
                const patientName = getPatientField(patient, ['patient_name', 'name'], 'Tidak diketahui');
                const roomNumber = getPatientField(patient, ['room_number', 'room.number'], '-');
                const bedNumber = getPatientField(patient, ['bed_number'], '-');
                const doctorName = getPatientField(patient, ['doctor', 'doctor_name'], 'Dr. Tidak diketahui');
                const rekamMedis = getPatientField(patient, ['rekam_medis', 'medical_record'], '-');
                const gender = getPatientField(patient, ['gender'], 'L');
                const age = getPatientField(patient, ['age'], '-');
                const kerabatType = getPatientField(patient, ['kerabat_type'], 'Reguler');
                const daysStayed = getPatientField(patient, ['days_stayed'], '-');

                // Badge untuk kerabat type sesuai standar rawat jalan
                let kerabatBadge = '';
                switch (kerabatType) {
                    case 'Owner':
                        kerabatBadge =
                            '<span class="badge bg-warning text-dark ms-1" style="font-size: 0.7em;"><i class="ri-vip-crown-line"></i> Kerabat Owner</span>';
                        break;
                    case 'Dokter':
                        kerabatBadge =
                            '<span class="badge bg-primary text-white ms-1" style="font-size: 0.7em;"><i class="ri-user-heart-line"></i> Kerabat Dokter</span>';
                        break;
                    case 'Karyawan':
                        kerabatBadge =
                            '<span class="badge bg-success text-white ms-1" style="font-size: 0.7em;"><i class="ri-user-smile-line"></i> Kerabat Karyawan</span>';
                        break;
                    default:
                        kerabatBadge =
                            '<span class="badge bg-primary text-white ms-1" style="font-size: 0.7em;"><i class="ri-user-line"></i> Reguler</span>';
                        break;
                }

                // Icon gender
                const genderIcon = gender === 'Laki-laki' ? 'ri-men-line text-primary' :
                    'ri-women-line text-danger';

                html += `
                    <tr class="prescription-row" data-patient-id="${encounterId}">
                        <td class="text-center">
                            <div class="fw-bold text-primary">${index + 1}</div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="me-2 position-relative">
                                    <i class="${genderIcon}" style="font-size: 1.2em;"></i>
                                    ${kerabatType !== 'Reguler' ? `
                                                                                                                                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill ${kerabatType === 'Owner' ? 'bg-warning text-dark' : kerabatType === 'Dokter' ? 'bg-primary text-white' : 'bg-success text-white'}"
                                                                                                                                                          style="font-size: 0.6em; padding: 2px 4px;">
                                                                                                                                                        <i class="${kerabatType === 'Owner' ? 'ri-vip-crown-line' : kerabatType === 'Dokter' ? 'ri-user-heart-line' : 'ri-user-smile-line'}" style="font-size: 0.8em;"></i>
                                                                                                                                                    </span>
                                                                                                                                                ` : ''}
                                </div>
                                <div>
                                    <div class="fw-bold text-dark">
                                        ${patientName}
                                        ${kerabatBadge}
                                    </div>
                                    <small class="text-muted">
                                        <i class="ri-profile-line"></i> RM: ${rekamMedis} |
                                        <i class="ri-user-line"></i> ${age}
                                    </small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="text-center">
                                <span class="badge bg-info text-white px-2 py-1" style="font-size: 0.8em;">
                                    <i class="ri-hospital-line me-1"></i>${roomNumber}
                                </span>
                                <div class="small text-muted mt-1">
                                    <i class="ri-hotel-bed-line"></i> Bed ${bedNumber}
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="fw-semibold text-success">
                                <i class="ri-stethoscope-line me-1"></i>${doctorName}
                            </div>
                        </td>
                        <td>
                            <div class="text-center">
                                <span class="badge bg-success-subtle text-success border border-success px-2 py-1">
                                    <i class="ri-calendar-check-line me-1"></i>Aktif
                                </span>
                                <div class="small text-muted mt-1">
                                    <i class="ri-time-line"></i> ${daysStayed}
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex gap-1 justify-content-center">
                                ${auth_user_role === 2 || auth_user_role === 1 ? `
                                                                                                                                                        <button class="btn btn-primary btn-sm" onclick="createPrescriptionForPatient('${encounterId}', '${patientName}')" title="Buat Resep Baru">
                                                                                                                                                            <i class="ri-add-line"></i> Resep
                                                                                                                                                        </button>
                                                                                                                                                    ` : ''}
                                <button class="btn btn-info btn-sm" onclick="viewPatientPrescriptions('${encounterId}', '${patientName}')" title="Lihat Riwayat Resep">
                                    <i class="ri-eye-line"></i> Lihat
                                </button>
                                <button class="btn btn-success btn-sm" onclick="viewMedicationSchedule('${encounterId}', '${patientName}')" title="Jadwal Obat">
                                    <i class="ri-calendar-event-line"></i> Jadwal
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });

            html += `
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted small">
                        <i class="ri-information-line"></i>
                        Menampilkan ${patients.length} pasien rawat inap
                    </div>
                    <div class="text-muted small">
                        <i class="ri-lightbulb-line text-warning"></i>
                        Klik baris untuk detail lengkap
                    </div>
                </div>
            `;

            container.innerHTML = html;
        }

        // Show create prescription form
        function showCreatePrescriptionForm() {
            loadPatientList('prescription-patient-select');
            loadDoctorList();
            clearPrescriptionForm();

            document.getElementById('prescription-form-title').textContent = 'Buat Resep Baru';
            const modal = new bootstrap.Modal(document.getElementById('modalPrescriptionForm'));
            modal.show();
        }

        // Create prescription for specific patient
        function createPrescriptionForPatient(encounterId, patientName) {
            // Open form first
            clearPrescriptionForm();
            loadDoctorList();
            document.getElementById('prescription-form-title').textContent = 'Buat Resep Baru';
            const modal = new bootstrap.Modal(document.getElementById('modalPrescriptionForm'));
            modal.show();

            // Load patient list and auto-select
            const select = document.getElementById('prescription-patient-select');
            select.innerHTML = '<option value="">Loading patients...</option>';

            // Check if we already have patient data
            if (allPatientsData && allPatientsData.length > 0) {
                populatePatientSelect(select, allPatientsData);
                select.value = encounterId;
            } else {
                // Fetch patient data
                fetch('/kunjungan/nurse-dashboard/all-inpatients')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            allPatientsData = data.data;
                            populatePatientSelect(select, data.data);
                            // Set the selected patient
                            select.value = encounterId;
                        } else {
                            select.innerHTML = '<option value="">Gagal memuat data pasien</option>';
                        }
                    })
                    .catch(error => {
                        console.error('Error loading patients:', error);
                        select.innerHTML = '<option value="">Error memuat data</option>';
                    });
            }
        }

        // View medication schedule for specific patient
        function viewMedicationSchedule(encounterId, patientName) {
            Swal.fire({
                title: `Jadwal Obat untuk ${patientName}`,
                html: `
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Memuat jadwal obat hari ini...</p>
                    </div>
                `,
                width: '900px',
                showConfirmButton: false,
                didOpen: () => {
                    fetch(`/kunjungan/prescription-orders/schedule/${encounterId}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                const schedules = data.data;
                                let html = '';

                                if (schedules.length === 0) {
                                    html = `
                                        <div class="alert alert-info">
                                            <i class="ri-information-line me-2"></i>
                                            Tidak ada jadwal obat untuk hari ini
                                        </div>
                                    `;
                                } else {
                                    html = `
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead class="table-success">
                                                    <tr>
                                                        <th>Waktu</th>
                                                        <th>Obat</th>
                                                        <th>Dosis</th>
                                                        <th>Rute</th>
                                                        <th>Dokter</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                    `;

                                    schedules.forEach(schedule => {
                                        const statusBadge = schedule.status === 'administered' ?
                                            '<span class="badge bg-success"><i class="ri-check-line"></i> Diberikan</span>' :
                                            schedule.status === 'skipped' ?
                                            '<span class="badge bg-danger"><i class="ri-close-line"></i> Dilewati</span>' :
                                            '<span class="badge bg-warning"><i class="ri-time-line"></i> Pending</span>';

                                        const administeredInfo = schedule.administered_by ?
                                            `<small class="d-block text-muted">oleh ${schedule.administered_by} (${schedule.administered_at})</small>` :
                                            '';

                                        html += `
                                            <tr>
                                                <td><strong>${schedule.scheduled_time}</strong></td>
                                                <td>${schedule.medication_name}</td>
                                                <td>${schedule.dosage}</td>
                                                <td>${schedule.route}</td>
                                                <td><small>${schedule.doctor_name}</small></td>
                                                <td>
                                                    ${statusBadge}
                                                    ${administeredInfo}
                                                    ${schedule.notes ? `<small class="d-block text-muted mt-1">${schedule.notes}</small>` : ''}
                                                </td>
                                            </tr>
                                        `;
                                    });

                                    html += `
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="text-muted small mt-2">
                                            <i class="ri-information-line"></i> Menampilkan jadwal obat untuk hari ini
                                        </div>
                                    `;
                                }

                                Swal.fire({
                                    title: `Jadwal Obat untuk ${patientName}`,
                                    html: html,
                                    width: '900px',
                                    confirmButtonText: 'Tutup',
                                    confirmButtonColor: '#6c757d'
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: data.message || 'Gagal memuat jadwal obat',
                                    confirmButtonColor: '#dc3545'
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Terjadi kesalahan saat memuat data',
                                confirmButtonColor: '#dc3545'
                            });
                        });
                }
            });
        }

        // View patient prescriptions
        function viewPatientPrescriptions(encounterId, patientName) {
            Swal.fire({
                title: `Resep untuk ${patientName}`,
                html: `
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Memuat resep pasien...</p>
                    </div>
                `,
                width: '800px',
                showConfirmButton: false,
                didOpen: () => {
                    // Call API to get prescriptions
                    fetch(`/kunjungan/prescription-orders/patient/${encounterId}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                const prescriptions = data.data;

                                let html = '';
                                if (prescriptions.length === 0) {
                                    html = `
                                        <div class="alert alert-info">
                                            <i class="ri-information-line me-2"></i>
                                            Belum ada resep untuk pasien ini
                                        </div>
                                    `;
                                } else {
                                    prescriptions.forEach(prescription => {
                                        // Status badges with tooltips
                                        let statusBadge, statusText, statusTooltip;
                                        if (prescription.status === 'active') {
                                            statusBadge = 'bg-success';
                                            statusText = 'Aktif';
                                            statusTooltip =
                                                'Resep masih berlaku dan sedang dijalankan';
                                        } else if (prescription.status === 'completed') {
                                            statusBadge = 'bg-secondary';
                                            statusText = 'Selesai';
                                            statusTooltip = 'Resep sudah selesai dilaksanakan';
                                        } else if (prescription.status === 'cancelled') {
                                            statusBadge = 'bg-danger';
                                            statusText = 'Dibatalkan';
                                            statusTooltip = 'Resep dibatalkan oleh dokter';
                                        } else {
                                            statusBadge = 'bg-warning';
                                            statusText = prescription.status;
                                            statusTooltip = 'Status resep';
                                        }

                                        let pharmacyBadge, pharmacyText, pharmacyTooltip;
                                        if (prescription.pharmacy_status === 'Dispensed') {
                                            pharmacyBadge = 'bg-success';
                                            pharmacyText = 'Sudah Diserahkan';
                                            pharmacyTooltip = 'Obat sudah diserahkan ke pasien';
                                        } else if (prescription.pharmacy_status === 'Ready') {
                                            pharmacyBadge = 'bg-info';
                                            pharmacyText = 'Siap Diambil';
                                            pharmacyTooltip = 'Obat sudah siap diambil di apotek';
                                        } else if (prescription.pharmacy_status === 'Verified') {
                                            pharmacyBadge = 'bg-warning text-dark';
                                            pharmacyText = 'Sedang Disiapkan';
                                            pharmacyTooltip =
                                                'Resep sudah diverifikasi, obat sedang disiapkan';
                                        } else {
                                            pharmacyBadge = 'bg-warning text-dark';
                                            pharmacyText = 'Menunggu';
                                            pharmacyTooltip = 'Menunggu verifikasi dari apotek';
                                        }

                                        html += `
                                            <div class="card mb-3 shadow-sm border-0" style="border-left: 4px solid #17a2b8 !important;">
                                                <div class="card-header bg-light py-3">
                                                    <div class="row align-items-center g-2">
                                                        <div class="col-auto">
                                                            <div class="bg-primary bg-opacity-10 rounded-circle p-2">
                                                                <i class="ri-prescription-line text-primary fs-4"></i>
                                                            </div>
                                                        </div>
                                                        <div class="col">
                                                            <h6 class="mb-1 fw-bold">Resep Obat</h6>
                                                            <small class="text-muted"><i class="ri-user-line"></i> ${prescription.doctor_name}</small>
                                                            <br>
                                                            <small class="text-muted"><i class="ri-time-line"></i> ${prescription.created_at}</small>
                                                        </div>
                                                        <div class="col-auto text-end">
                                                            <div class="d-flex flex-column gap-2 align-items-end">
                                                                <div title="${statusTooltip}" style="cursor: help;">
                                                                    <i class="ri-checkbox-circle-${prescription.status === 'active' ? 'fill' : 'line'} fs-3"
                                                                       style="color: ${prescription.status === 'active' ? '#28a745' : prescription.status === 'completed' ? '#6c757d' : '#dc3545'};"></i>
                                                                </div>
                                                                <div title="${pharmacyTooltip}" style="cursor: help;">
                                                                    <i class="ri-medicine-bottle-${prescription.pharmacy_status === 'Dispensed' ? 'fill' : 'line'} fs-3"
                                                                       style="color: ${prescription.pharmacy_status === 'Dispensed' ? '#28a745' : prescription.pharmacy_status === 'Ready' ? '#17a2b8' : prescription.pharmacy_status === 'Verified' ? '#ffc107' : '#6c757d'};"></i>
                                                                </div>
                                                                ${prescription.pharmacy_status === 'Pending' ? `
                                                                                        <button class="btn btn-sm btn-danger"
                                                                                                onclick="deletePrescription('${prescription.id}', '${patientName}')"
                                                                                                title="Hapus resep yang belum disiapkan">
                                                                                            <i class="ri-delete-bin-line"></i>
                                                                                        </button>
                                                                                    ` : ''}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    ${prescription.notes ? `
                                                                            <div class="alert alert-info mb-3" role="alert">
                                                                                <div class="d-flex">
                                                                                    <i class="ri-information-line me-2 fs-5"></i>
                                                                                    <div>
                                                                                        <strong>Catatan Dokter:</strong><br>
                                                                                        ${prescription.notes}
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        ` : ''}

                                                    <h6 class="fw-bold mb-3">
                                                        <i class="ri-medicine-bottle-line text-primary me-2"></i>Daftar Obat
                                                    </h6>
                                                    <div class="table-responsive">
                                                        <table class="table table-hover align-middle mb-0">
                                                            <thead class="table-primary">
                                                                <tr>
                                                                    <th class="fw-semibold" style="width: 20%;"><i class="ri-capsule-line me-1"></i>Obat</th>
                                                                    <th class="fw-semibold text-center" style="width: 10%;">Dosis</th>
                                                                    <th class="fw-semibold text-center" style="width: 12%;">Frekuensi</th>
                                                                    <th class="fw-semibold text-center" style="width: 8%;">Rute</th>
                                                                    <th class="fw-semibold text-center" style="width: 8%;">Durasi</th>
                                                                    <th class="fw-semibold text-center" style="width: 15%;">Jadwal</th>
                                                                    <th class="fw-semibold text-center" style="width: 12%;">Progress</th>
                                                                    ${prescription.pharmacy_status === 'Dispensed' ? '<th class="fw-semibold text-center" style="width: 15%;">Aksi</th>' : ''}
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                        `;
                                        prescription.medications.forEach((med, idx) => {
                                            const progress = med.total_administrations > 0 ?
                                                `${med.completed_administrations}/${med.total_administrations}` :
                                                '-';
                                            const progressPercent = med
                                                .total_administrations > 0 ?
                                                Math.round((med.completed_administrations /
                                                    med.total_administrations) * 100) : 0;
                                            const progressColor = progressPercent === 100 ?
                                                'success' : progressPercent > 50 ? 'info' :
                                                'warning';

                                            const scheduledTimes = Array.isArray(med
                                                    .scheduled_times) ?
                                                med.scheduled_times.join(', ') : '-';

                                            // Medication name sudah di-resolve di backend
                                            const medicationName =
                                                '<i class="ri-capsule-line me-1"></i>' +
                                                (med.medication_name || 'Unknown');

                                            html += `
                                                <tr>
                                                    <td class="med-name-${idx}">
                                                        <strong class="d-block">${medicationName}</strong>
                                                        ${med.instructions ? `<small class="text-muted">${med.instructions}</small>` : ''}
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-light text-dark px-3 py-2" style="font-size: 0.85rem;">${med.dosage}</span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-light text-dark px-3 py-2" style="font-size: 0.85rem;">${med.frequency}</span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-info px-3 py-2" style="font-size: 0.85rem;">${med.route}</span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-primary px-3 py-2" style="font-size: 0.85rem;">${med.duration_days} hari</span>
                                                    </td>
                                                    <td class="text-center">
                                                        <small class="text-muted d-block" style="line-height: 1.4;">${scheduledTimes}</small>
                                                    </td>
                                                    <td class="text-center">
                                                        ${med.total_administrations > 0 ? `
                                                                                <div class="d-flex flex-column align-items-center gap-1">
                                                                                    <span class="badge bg-${progressColor} px-3 py-2" style="font-size: 0.85rem;">${progress}</span>
                                                                                    <div class="progress" style="width: 80px; height: 8px;">
                                                                                        <div class="progress-bar bg-${progressColor}"
                                                                                             style="width: ${progressPercent}%"></div>
                                                                                    </div>
                                                                                </div>
                                                                            ` : '<span class="text-muted">-</span>'}
                                                    </td>
                                                    ${prescription.pharmacy_status === 'Dispensed' ? `
                                                                                <td class="text-center">
                                                                                    <div class="d-flex gap-1 justify-content-center">
                                                                                        <button class="btn btn-sm btn-success px-2 py-1"
                                                                                                onclick="recordMedicationAdministration('${med.id}', '${med.medication_name}', '${prescription.encounter_id}')"
                                                                                                title="Catat pemberian obat"
                                                                                                style="font-size: 0.8rem;">
                                                                                            <i class="ri-check-line"></i> Catat
                                                                                        </button>
                                                                                        ${med.total_administrations > 0 ? `
                                                                                        <button class="btn btn-sm btn-info px-2 py-1"
                                                                                                onclick="viewMedicationHistory('${med.id}', '${med.medication_name}')"
                                                                                                title="Lihat histori pemberian"
                                                                                                style="font-size: 0.8rem;">
                                                                                            <i class="ri-history-line"></i>
                                                                                        </button>
                                                                                    ` : ''}
                                                                                    </div>
                                                                                </td>
                                                                            ` : ''}
                                                </tr>
                                            `;
                                        });

                                        html += `
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        `;
                                    });
                                }

                                Swal.fire({
                                    title: `Resep untuk ${patientName}`,
                                    html: html,
                                    width: '900px',
                                    confirmButtonText: 'Tutup',
                                    confirmButtonColor: '#6c757d'
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: data.message || 'Gagal memuat resep pasien',
                                    confirmButtonColor: '#dc3545'
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Terjadi kesalahan saat memuat data',
                                confirmButtonColor: '#dc3545'
                            });
                        });
                }
            });
        }

        // Load doctor list
        function loadDoctorList() {
            const select = document.getElementById('prescription-doctor-select');
            const autoNote = document.getElementById('doctor-auto-note');

            // Jika user adalah dokter, langsung set value dan readonly
            if (auth_user_role === 2 && auth_user_id) {
                select.innerHTML = `<option value="${auth_user_id}" selected>${auth_user_name}</option>`;
                // Gunakan readonly class agar tetap terkirim di form
                select.classList.add('bg-light');
                select.style.pointerEvents = 'none'; // Prevent clicking
                if (autoNote) autoNote.style.display = 'block';
                return;
            }

            // Jika bukan dokter, load semua dokter
            select.innerHTML = '<option value="">Loading...</option>';

            fetch('/kunjungan/api/doctors')
                .then(response => response.json())
                .then(data => {
                    select.innerHTML = '<option value="">Pilih Dokter</option>';

                    if (data.success && data.data) {
                        data.data.forEach(doctor => {
                            const option = document.createElement('option');
                            option.value = doctor.id;
                            option.textContent = doctor.name;
                            select.appendChild(option);
                        });
                        select.disabled = false;
                    } else {
                        select.innerHTML = '<option value="">Tidak ada data dokter</option>';
                    }
                })
                .catch(error => {
                    console.error('Error loading doctors:', error);
                    select.innerHTML = '<option value="">Error loading doctors</option>';
                });
        }

        // Initialize medication select2
        function initializeMedicationSelect(index) {
            $(`#medication_select_${index}`).select2({
                theme: 'bootstrap-5',
                placeholder: 'Ketik untuk mencari obat...',
                allowClear: true,
                dropdownParent: $('#modalPrescriptionForm'),
                ajax: {
                    url: '/kunjungan/api/medications',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            search: params.term
                        };
                    },
                    processResults: function(data) {
                        if (data.success) {
                            return {
                                results: data.data.map(item => ({
                                    id: item.name,
                                    text: item.name + (item.unit ? ' - ' + item.unit : ''),
                                    product_id: item.id,
                                    stock: item.stock,
                                    status: item.status,
                                    disabled: item.disabled === true || item.status ===
                                        'out_of_stock' || item.status === 'expired',
                                    expired_date: item.expired_date_formatted,
                                    is_near_expiry: item.is_near_expiry,
                                    price: item.price,
                                    unit: item.unit
                                }))
                            };
                        }
                        return {
                            results: []
                        };
                    },
                    cache: true
                },
                minimumInputLength: 2,
                templateResult: formatMedicationResult,
                templateSelection: formatMedicationSelection
            });
        }

        // Format medication result in dropdown
        function formatMedicationResult(medication) {
            if (medication.loading) {
                return medication.text;
            }

            // Determine status badge and styling
            let statusHtml = '';
            let containerClass = 'select2-result-medication';
            let disabledText = '';

            if (medication.status === 'out_of_stock') {
                statusHtml = '<span class="badge bg-danger ms-2" style="font-size: 0.7em;">Stok Habis</span>';
                containerClass += ' disabled-medication';
                disabledText =
                    '<small class="text-danger d-block mt-1"><i class="ri-close-circle-line"></i> Tidak dapat dipilih - Stok habis</small>';
            } else if (medication.status === 'expired') {
                statusHtml = '<span class="badge bg-dark ms-2" style="font-size: 0.7em;">Expired</span>';
                containerClass += ' disabled-medication';
                disabledText =
                    '<small class="text-danger d-block mt-1"><i class="ri-close-circle-line"></i> Tidak dapat dipilih - Sudah expired</small>';
            } else if (medication.is_near_expiry) {
                statusHtml =
                    '<span class="badge bg-warning text-dark ms-2" style="font-size: 0.7em;">Hampir Expired</span>';
            }

            let stockInfo = '';
            if (medication.stock !== undefined) {
                let stockClass = 'text-muted';
                if (medication.stock <= 0) {
                    stockClass = 'text-danger fw-bold';
                } else if (medication.stock < 10) {
                    stockClass = 'text-warning fw-bold';
                }
                stockInfo = `<small class="${stockClass}">Stok: ${medication.stock}</small>`;
            }

            let expiryInfo = '';
            if (medication.expired_date) {
                const expiryClass = medication.status === 'expired' ? 'text-danger fw-bold' : 'text-muted';
                expiryInfo = `<small class="${expiryClass} ms-2">| Exp: ${medication.expired_date}</small>`;
            }

            var $container = $(
                `<div class="${containerClass}">
                    <div class="d-flex align-items-center">
                        <div class="medication-name flex-grow-1">${medication.text}</div>
                        ${statusHtml}
                    </div>
                    <div class="medication-info">
                        ${stockInfo}
                        ${expiryInfo}
                    </div>
                    ${disabledText}
                </div>`
            );

            return $container;
        }

        // Format medication selection
        function formatMedicationSelection(medication) {
            return medication.text;
        }

        // Delete prescription
        function deletePrescription(prescriptionId, patientName) {
            Swal.fire({
                title: 'Hapus Resep?',
                html: `Apakah Anda yakin ingin menghapus resep untuk pasien <strong>${patientName}</strong>?<br><small class="text-danger">Resep yang sudah disiapkan tidak dapat dihapus.</small>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Menghapus...',
                        text: 'Sedang menghapus resep',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    fetch(`/kunjungan/prescription-orders/${prescriptionId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Content-Type': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: 'Resep berhasil dihapus',
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    // Reload modal
                                    viewPatientPrescriptions(data.encounter_id, patientName);
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: data.message || 'Gagal menghapus resep'
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Terjadi kesalahan saat menghapus resep'
                            });
                        });
                }
            });
        }

        // Clear prescription form
        function clearPrescriptionForm() {
            document.getElementById('formPrescriptionOrder').reset();
            document.getElementById('prescription-order-id').value = '';
            document.getElementById('medications-list').innerHTML = '';

            // Destroy existing select2 instances
            $('.medication-select').each(function() {
                if ($(this).data('select2')) {
                    $(this).select2('destroy');
                }
            });

            addMedicationRow(); // Add first empty row
        }

        // Add medication row
        function addMedicationRow() {
            const container = document.getElementById('medications-list');
            const index = container.children.length;

            const row = document.createElement('div');
            row.className = 'card mb-3 medication-row';
            row.innerHTML = `
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0"><i class="ri-medicine-bottle-line"></i> Obat #${index + 1}</h6>
                        <button type="button" class="btn btn-danger btn-sm" onclick="removeMedicationRow(this)">
                            <i class="ri-delete-bin-line"></i> Hapus
                        </button>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label small">Nama Obat <span class="text-danger">*</span></label>
                            <select class="form-select medication-select" name="medications[${index}][medication_name]"
                                id="medication_select_${index}" required>
                                <option value="">Pilih Obat...</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">Dosis <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="medications[${index}][dosage]"
                                placeholder="Contoh: 500mg" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">Rute <span class="text-danger">*</span></label>
                            <select class="form-select" name="medications[${index}][route]" required>
                                <option value="">Pilih Rute</option>
                                <option value="Oral">Oral</option>
                                <option value="IV">IV (Intravena)</option>
                                <option value="IM">IM (Intramuskular)</option>
                                <option value="SC">SC (Subkutan)</option>
                                <option value="Topikal">Topikal</option>
                                <option value="Rektal">Rektal</option>
                                <option value="Inhalasi">Inhalasi</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Frekuensi <span class="text-danger">*</span></label>
                            <select class="form-select" name="medications[${index}][frequency]" required>
                                <option value="">Pilih Frekuensi</option>
                                <option value="1x sehari">1x sehari</option>
                                <option value="2x sehari">2x sehari</option>
                                <option value="3x sehari">3x sehari</option>
                                <option value="4x sehari">4x sehari</option>
                                <option value="Setiap 4 jam">Setiap 4 jam</option>
                                <option value="Setiap 6 jam">Setiap 6 jam</option>
                                <option value="Setiap 8 jam">Setiap 8 jam</option>
                                <option value="Bila perlu">Bila perlu (PRN)</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">Durasi (hari) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="medications[${index}][duration_days]"
                                placeholder="7" min="1" value="7" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">Jadwal Pemberian <span class="text-danger">*</span></label>
                            <div class="d-flex gap-2 flex-wrap mt-1">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="08:00"
                                        id="time_${index}_08">
                                    <label class="form-check-label small" for="time_${index}_08">08:00</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="12:00"
                                        id="time_${index}_12">
                                    <label class="form-check-label small" for="time_${index}_12">12:00</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="18:00"
                                        id="time_${index}_18">
                                    <label class="form-check-label small" for="time_${index}_18">18:00</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="20:00"
                                        id="time_${index}_20">
                                    <label class="form-check-label small" for="time_${index}_20">20:00</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label small">Instruksi Khusus</label>
                            <textarea class="form-control" name="medications[${index}][instructions]" rows="2"
                                placeholder="Contoh: Diminum setelah makan, Hindari susu..."></textarea>
                        </div>
                    </div>
                </div>
            `;

            container.appendChild(row);

            // Initialize select2 for medication name
            initializeMedicationSelect(index);
        }

        // Remove medication row
        function removeMedicationRow(button) {
            const container = document.getElementById('medications-list');
            if (container.children.length > 1) {
                button.closest('.medication-row').remove();
                // Reindex rows
                Array.from(container.children).forEach((row, index) => {
                    const inputs = row.querySelectorAll('input, select');
                    inputs.forEach(input => {
                        const name = input.name;
                        if (name && name.includes('medications[')) {
                            input.name = name.replace(/medications\[\d+\]/, `medications[${index}]`);
                        }
                    });
                });
            }
        }

        // Save prescription order
        function savePrescriptionOrder(event) {
            event.preventDefault();

            const formData = new FormData(event.target);
            const data = {
                medications: []
            };

            // Collect basic form data
            data.encounter_id = formData.get('encounter_id');
            data.doctor_id = formData.get('doctor_id');
            data.diagnosis = formData.get('diagnosis');
            data.notes = formData.get('notes');

            // Debug: Log collected values
            console.log('Form Data Debug:', {
                encounter_id: data.encounter_id,
                doctor_id: data.doctor_id,
                encounter_select_value: document.getElementById('prescription-patient-select')?.value,
                doctor_select_value: document.getElementById('prescription-doctor-select')?.value
            });

            // Collect medications
            const medicationRows = document.querySelectorAll('#medications-list .medication-row');
            medicationRows.forEach((row, index) => {
                const selectElement = row.querySelector(`[name="medications[${index}][medication_name]"]`);
                const selectedOption = $(selectElement).select2('data')[0];

                const medication = {
                    medication_name: selectedOption ? selectedOption.id : selectElement?.value,
                    dosage: row.querySelector(`[name="medications[${index}][dosage]"]`)?.value,
                    route: row.querySelector(`[name="medications[${index}][route]"]`)?.value,
                    frequency: row.querySelector(`[name="medications[${index}][frequency]"]`)?.value,
                    scheduled_times: [],
                    instructions: row.querySelector(`[name="medications[${index}][instructions]"]`)?.value,
                    duration_days: parseInt(row.querySelector(`[name="medications[${index}][duration_days]"]`)
                        ?.value) || 1
                };

                // Collect scheduled times (checkboxes)
                const timeCheckboxes = row.querySelectorAll('input[type="checkbox"]:checked');
                timeCheckboxes.forEach(checkbox => {
                    medication.scheduled_times.push(checkbox.value);
                });

                // Only add if medication name is filled
                if (medication.medication_name && medication.medication_name.trim()) {
                    data.medications.push(medication);
                }
            });

            // Validation
            if (!data.encounter_id || !data.doctor_id) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Data Tidak Lengkap',
                    text: 'Silakan pilih pasien dan dokter',
                    confirmButtonColor: '#f39c12'
                });
                return;
            }

            if (data.medications.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Data Tidak Lengkap',
                    text: 'Silakan tambahkan minimal 1 obat',
                    confirmButtonColor: '#f39c12'
                });
                return;
            }

            // Check if all medications have scheduled times
            const invalidMeds = data.medications.filter(med => med.scheduled_times.length === 0);
            if (invalidMeds.length > 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Data Tidak Lengkap',
                    text: 'Setiap obat harus memiliki minimal 1 jadwal pemberian',
                    confirmButtonColor: '#f39c12'
                });
                return;
            }

            Swal.fire({
                title: 'Menyimpan...',
                text: 'Sedang menyimpan resep obat',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Call API
            fetch('/kunjungan/prescription-orders', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Resep obat berhasil disimpan',
                            timer: 2000,
                            showConfirmButton: false
                        });

                        bootstrap.Modal.getInstance(document.getElementById('modalPrescriptionForm')).hide();
                        loadPrescriptionOrders();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: data.message || 'Gagal menyimpan resep obat',
                            confirmButtonColor: '#dc3545'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Terjadi kesalahan saat menyimpan data',
                        confirmButtonColor: '#dc3545'
                    });
                });
        }

        // Search prescription patients
        function searchPrescriptionPatients() {
            console.log('Searching prescription patients...');
        }

        // Refresh prescription orders
        function refreshPrescriptionOrders() {
            loadPrescriptionOrders();
        }

        // ==================== HANDOVER FUNCTIONS ====================

        // Open handover modal
        function openHandover() {
            const modal = new bootstrap.Modal(document.getElementById('modalHandover'));
            modal.show();

            // Set current shift based on time
            setCurrentShift();
            loadHandoverData();
        }

        // Set current shift based on time
        function setCurrentShift() {
            const now = new Date();
            const hour = now.getHours();
            const minute = now.getMinutes();
            const currentTime = hour + (minute / 60);

            let currentShift, nextShift;

            if (currentTime >= 7.5 && currentTime < 14.5) {
                currentShift = 'Pagi';
                nextShift = 'Sore';
            } else if (currentTime >= 14.5 && currentTime < 21.5) {
                currentShift = 'Sore';
                nextShift = 'Malam';
            } else {
                currentShift = 'Malam';
                nextShift = 'Pagi';
            }

            document.getElementById('current-shift').value = currentShift;
            document.getElementById('next-shift').value = nextShift;
        }

        // Load handover data
        function loadHandoverData() {
            document.getElementById('handover-container').innerHTML = `
                <div class="text-center py-4">
                    <div class="spinner-border text-warning" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            `;

            // Use existing patient data
            const patients = normalizePatientsData(allPatientsData);
            displayHandoverData(patients);
        }

        // Display handover data
        function displayHandoverData(patients) {
            const container = document.getElementById('handover-container');

            if (patients.length === 0) {
                container.innerHTML = `
                    <div class="alert alert-info">
                        <i class="ri-information-line me-2"></i>
                        Tidak ada pasien untuk di-handover
                    </div>
                `;
                return;
            }

            let html = `
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-warning">
                            <tr>
                                <th>No</th>
                                <th>Pasien</th>
                                <th>Ruangan</th>
                                <th>Kondisi</th>
                                <th>Catatan Penting</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            patients.forEach((patient, index) => {
                const admissionId = getPatientField(patient, ['admission_id', 'id'], '');
                const patientName = getPatientField(patient, ['patient_name', 'name'], 'Tidak diketahui');
                const roomNumber = getPatientField(patient, ['room_number', 'room.number'], '-');
                const bedNumber = getPatientField(patient, ['bed_number'], '-');

                html += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>
                            <strong>${patientName}</strong><br>
                            <small class="text-muted">Bed: ${bedNumber}</small>
                        </td>
                        <td>${roomNumber}</td>
                        <td>
                            <span class="badge bg-success">Stable</span>
                        </td>
                        <td>
                            <textarea class="form-control form-control-sm" rows="2" placeholder="Catatan untuk shift selanjutnya..." id="handover-note-${admissionId}"></textarea>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-warning" onclick="saveHandoverNote('${admissionId}', '${patientName}')">
                                <i class="ri-save-line"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });

            html += `
                        </tbody>
                    </table>
                </div>
            `;

            container.innerHTML = html;
        }

        // Save handover note
        function saveHandoverNote(admissionId, patientName) {
            const note = document.getElementById(`handover-note-${admissionId}`).value;

            if (!note.trim()) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Catatan Kosong',
                    text: 'Silakan isi catatan handover terlebih dahulu'
                });
                return;
            }

            Swal.fire({
                icon: 'success',
                title: 'Tersimpan!',
                text: `Catatan handover untuk ${patientName} berhasil disimpan`,
                timer: 2000,
                showConfirmButton: false
            });
        }

        // Generate handover report
        function generateHandoverReport() {
            Swal.fire({
                title: 'Generating Report...',
                text: 'Sedang menyiapkan laporan handover',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            setTimeout(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Laporan Siap!',
                    text: 'Laporan handover berhasil di-generate dan akan didownload',
                    timer: 2000,
                    showConfirmButton: false
                });
            }, 2000);
        }

        // Search handover
        function searchHandover() {
            console.log('Searching handover...');
        }

        // ==================== UTILITY FUNCTIONS ====================

        // Load patient list for dropdowns
        function loadPatientList(selectId) {
            const select = document.getElementById(selectId);
            select.innerHTML = '<option value="">Loading patients...</option>';

            // Check if we already have patient data
            if (allPatientsData && allPatientsData.length > 0) {
                populatePatientSelect(select, allPatientsData);
                return;
            }

            // Fetch patient data if not available
            fetch('/kunjungan/nurse-dashboard/all-inpatients')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        allPatientsData = data.data; // Store for future use
                        populatePatientSelect(select, data.data);
                    } else {
                        select.innerHTML = '<option value="">Gagal memuat data pasien</option>';
                    }
                })
                .catch(error => {
                    console.error('Error loading patients for dropdown:', error);
                    select.innerHTML = '<option value="">Error memuat data</option>';
                });
        }

        // Helper function to populate patient select dropdown
        function populatePatientSelect(select, patientsData) {
            const patients = normalizePatientsData(patientsData);
            select.innerHTML = '<option value="">Pilih Pasien</option>';

            patients.forEach(patient => {
                const admissionId = getPatientField(patient, ['admission_id', 'id'], '');
                const encounterId = getPatientField(patient, ['encounter_id'], admissionId);
                const patientName = getPatientField(patient, ['patient_name', 'name'], 'Tidak diketahui');
                const roomNumber = getPatientField(patient, ['room_number', 'room.number'], '-');

                if ((admissionId || encounterId) && patientName) {
                    const option = document.createElement('option');
                    option.value = encounterId || admissionId;
                    option.textContent = `${patientName} - ${roomNumber}`;
                    select.appendChild(option);
                }
            });
        }

        // Global variables
        const auth_user_role = {{ auth()->user()->role ?? 0 }};
        const auth_user_id = {{ auth()->user()->id ?? 0 }};
        const auth_user_name = "{{ auth()->user()->name ?? '' }}";

        // Initialize features when page loads
        document.addEventListener('DOMContentLoaded', function() {
            // Load initial data if needed
            console.log('Dashboard loaded with enhanced features');

            // Fix aria-hidden focus issues on modals
            const modals = document.querySelectorAll('.modal');
            modals.forEach(modal => {
                modal.addEventListener('shown.bs.modal', function() {
                    // Find first focusable element that's not close button
                    const focusableElements = this.querySelectorAll(
                        'input:not([type="hidden"]), textarea, select, button:not(.btn-close)'
                    );
                    if (focusableElements.length > 0) {
                        focusableElements[0].focus();
                    }
                });
            });

            // Add event listener for prescription table row clicks
            document.addEventListener('click', function(e) {
                const row = e.target.closest('.prescription-row');
                if (row && !e.target.closest('.btn')) {
                    const patientId = row.dataset.patientId;
                    const patientName = row.querySelector('.fw-bold.text-dark').textContent.trim();

                    // Show patient detail or prescription history
                    viewPatientPrescriptions(patientId, patientName);
                }
            });
        });

        // Record medication administration
        function recordMedicationAdministration(medicationId, medicationName, encounterId) {
            Swal.fire({
                title: 'Catat Pemberian Obat',
                html: `
                    <div class="text-start">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Obat</label>
                            <input type="text" class="form-control" value="${medicationName}" disabled>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Waktu Pemberian <span class="text-danger">*</span></label>
                            <input type="datetime-local" id="administered_at" class="form-control"
                                   value="${new Date().toISOString().slice(0, 16)}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                            <select id="administration_status" class="form-control" required>
                                <option value="Given">Diberikan</option>
                                <option value="Given Late">Diberikan Terlambat</option>
                                <option value="Refused">Ditolak Pasien</option>
                                <option value="Held">Ditahan</option>
                                <option value="Not Available">Tidak Tersedia</option>
                                <option value="Patient NPO">Pasien NPO</option>
                                <option value="Patient Sleeping">Pasien Tidur</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Catatan</label>
                            <textarea id="administration_notes" class="form-control" rows="3"
                                      placeholder="Catatan tambahan, respon pasien, dll..."></textarea>
                        </div>
                    </div>
                `,
                width: '600px',
                showCancelButton: true,
                confirmButtonText: '<i class="ri-check-line me-1"></i>Simpan',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                preConfirm: () => {
                    const administeredAt = document.getElementById('administered_at').value;
                    const status = document.getElementById('administration_status').value;
                    const notes = document.getElementById('administration_notes').value;

                    if (!administeredAt || !status) {
                        Swal.showValidationMessage('Mohon lengkapi field yang wajib diisi');
                        return false;
                    }

                    return {
                        administered_at: administeredAt,
                        status: status,
                        notes: notes
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Submit administration record directly
                    fetch('/kunjungan/medication-administration', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                medication_id: medicationId,
                                admission_id: encounterId,
                                administered_at: result.value.administered_at,
                                status: result.value.status,
                                notes: result.value.notes
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: 'Pemberian obat berhasil dicatat',
                                    confirmButtonColor: '#28a745'
                                }).then(() => {
                                    // Reload prescription view to show updated progress
                                    location.reload();
                                });
                            } else {
                                throw new Error(data.message || 'Gagal menyimpan data');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: error.message || 'Terjadi kesalahan saat menyimpan data',
                                confirmButtonColor: '#dc3545'
                            });
                        });
                }
            });
        }

        // View medication administration history
        function viewMedicationHistory(medicationId, medicationName) {
            Swal.fire({
                title: `Histori Pemberian: ${medicationName}`,
                html: `
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Memuat histori pemberian...</p>
                    </div>
                `,
                width: '800px',
                showConfirmButton: false,
                didOpen: () => {
                    // Fetch medication history
                    fetch(`/kunjungan/medication-administration/history/${medicationId}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success && data.data) {
                                const history = data.data;

                                let html = '';
                                if (history.length === 0) {
                                    html = `
                                        <div class="alert alert-info">
                                            <i class="ri-information-line me-2"></i>
                                            Belum ada histori pemberian untuk obat ini
                                        </div>
                                    `;
                                } else {
                                    html = `
                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle">
                                                <thead class="table-success">
                                                    <tr>
                                                        <th class="text-center">#</th>
                                                        <th>Waktu Pemberian</th>
                                                        <th>Status</th>
                                                        <th>Perawat</th>
                                                        <th>Catatan</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                    `;

                                    history.forEach((item, idx) => {
                                        let statusBadge = '';
                                        switch (item.status) {
                                            case 'Given':
                                                statusBadge = 'bg-success';
                                                break;
                                            case 'Given Late':
                                                statusBadge = 'bg-warning text-dark';
                                                break;
                                            case 'Refused':
                                                statusBadge = 'bg-danger';
                                                break;
                                            case 'Held':
                                                statusBadge = 'bg-secondary';
                                                break;
                                            default:
                                                statusBadge = 'bg-info';
                                        }

                                        html += `
                                            <tr>
                                                <td class="text-center">${idx + 1}</td>
                                                <td><i class="ri-time-line text-muted"></i> ${item.administered_at}</td>
                                                <td><span class="badge ${statusBadge}">${item.status}</span></td>
                                                <td><i class="ri-user-line text-muted"></i> ${item.nurse_name}</td>
                                                <td><small class="text-muted">${item.notes || '-'}</small></td>
                                            </tr>
                                        `;
                                    });

                                    html += `
                                                </tbody>
                                            </table>
                                        </div>
                                    `;
                                }

                                Swal.update({
                                    html: html,
                                    showConfirmButton: true,
                                    confirmButtonText: 'Tutup',
                                    confirmButtonColor: '#6c757d'
                                });
                            } else {
                                throw new Error(data.message || 'Gagal memuat histori');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: error.message || 'Terjadi kesalahan saat memuat histori',
                                confirmButtonColor: '#dc3545'
                            });
                        });
                }
            });
        }
    </script>

    {{-- SweetAlert2 Library --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.0/dist/sweetalert2.all.min.js"></script>
@endpush
