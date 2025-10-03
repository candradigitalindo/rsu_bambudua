@extends('layouts.app')
@section('title')
    Dashboard Perawat - Status Bed
@endsection
@push('style')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/datatables.net-bs5@1.13.6/css/dataTables.bootstrap5.min.css">
    <!-- Scrollbar CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.3.2/styles/overlayscrollbars.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.min.css">
    <!-- Animate CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/animate.css@4.1.1/animate.min.css">
    <!-- Additional CSS for nurse dashboard -->
    <style>
        .patient-card {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .patient-card:hover {
            border-color: #007bff;
            box-shadow: 0 2px 8px rgba(0, 123, 255, 0.15);
        }

        .bed-status {
            font-weight: bold;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 0.8rem;
        }

        .bed-available {
            background-color: #d4edda;
            color: #155724;
        }

        .bed-occupied {
            background-color: #f8d7da;
            color: #721c24;
        }

        .bed-maintenance {
            background-color: #fff3cd;
            color: #856404;
        }

        .priority-urgent {
            border-left: 4px solid #dc3545;
        }

        .priority-high {
            border-left: 4px solid #fd7e14;
        }

        .priority-normal {
            border-left: 4px solid #28a745;
        }

        .action-btn {
            border-radius: 20px;
            padding: 4px 12px;
            font-size: 0.8rem;
        }

        .room-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1rem;
        }

        .nurse-tools {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
        }

        /* Quick Stats Cards - matching owner dashboard style */
        .stats-card {
            transition: all 0.3s ease;
            border-radius: 10px;
            position: relative;
            overflow: hidden;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .stats-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.6), transparent);
            animation: shimmer 2s infinite;
        }

        @keyframes shimmer {
            0% {
                transform: translateX(-100%);
            }

            100% {
                transform: translateX(100%);
            }
        }

        .icon-box {
            transition: all 0.3s ease;
        }

        .stats-card:hover .icon-box {
            transform: scale(1.1) rotate(5deg);
        }

        /* Real-time Dashboard Effects */
        .ri-refresh-line {
            animation-duration: 1s;
            animation-timing-function: linear;
        }

        .spin {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        /* Toast Notifications */
        .toast-container {
            z-index: 1060;
        }

        .toast {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        /* Modal Enhancements */
        .modal-content {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }

        .modal-header {
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            border-radius: 15px 15px 0 0;
        }

        .modal-footer {
            border-top: 1px solid rgba(0, 0, 0, 0.1);
            border-radius: 0 0 15px 15px;
        }

        /* Form Enhancements */
        .form-control:focus,
        .form-select:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
        }

        /* Button Animations */
        .btn {
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        /* Progress Bars */
        .progress {
            overflow: visible;
        }

        .progress-bar {
            transition: width 0.6s ease;
        }

        /* Pulse Animation for Important Elements */
        .pulse {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                opacity: 1;
            }

            50% {
                opacity: 0.7;
            }

            100% {
                opacity: 1;
            }
        }

        /* Real-time Indicator */
        .real-time-indicator {
            display: inline-block;
            width: 8px;
            height: 8px;
            background: #28a745;
            border-radius: 50%;
            animation: pulse 2s infinite;
            margin-right: 5px;
        }

        /* Loading States */
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }

        .loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 20px;
            height: 20px;
            margin: -10px 0 0 -10px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid #0d6efd;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        /* Card Hover Effects */
        .card {
            transition: all 0.3s ease;
        }

        .card:hover:not(.stats-card) {
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        /* Badge Enhancements */
        .badge {
            font-size: 0.7rem;
            padding: 0.35em 0.65em;
        }

        /* Alert Enhancements */
        .alert {
            border: none;
            border-radius: 10px;
        }

        /* Table Enhancements */
        .table th {
            border-top: none;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
        }

        /* Floating Action Button */
        .nurse-tools {
            animation: slideInUp 0.5s ease;
        }

        @keyframes slideInUp {
            from {
                transform: translateY(100px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .nurse-tools .btn {
            transition: all 0.3s ease;
        }

        .nurse-tools .btn:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
        }

        /* Tab Enhancements */
        .nav-tabs .nav-link {
            border: none;
            border-radius: 10px 10px 0 0;
            margin-right: 5px;
            transition: all 0.3s ease;
        }

        .nav-tabs .nav-link.active {
            background: linear-gradient(135deg, #0d6efd, #0dcaf0);
            color: white;
        }

        .nav-pills .nav-link {
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .nav-pills .nav-link.active {
            background: linear-gradient(135deg, #0d6efd, #0dcaf0);
        }

        /* Input Group Enhancements */
        .input-group-text {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            font-weight: 500;
        }

        /* List Group Enhancements */
        .list-group-item {
            border: 1px solid rgba(0, 0, 0, 0.1);
            border-radius: 8px !important;
            margin-bottom: 5px;
            transition: all 0.3s ease;
        }

        .list-group-item:hover {
            background: #f8f9fa;
            transform: translateX(5px);
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <!-- Header Section -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="text-dark fw-bold mb-1">Dashboard Perawat - Rawat Inap</h4>
                        <p class="text-muted mb-0">
                            <i class="ri-time-line me-1"></i>
                            {{ now()->format('d/m/Y') }} <span class="live-clock">{{ now()->format('H:i:s') }}</span>
                            <span class="real-time-indicator" title="Real-time updates active"></span>
                        </p>
                    </div>
                    <div>
                        <button class="btn btn-primary btn-sm me-2" onclick="refreshData()">
                            <i class="ri-refresh-line me-1"></i> Refresh
                        </button>
                        <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal"
                            data-bs-target="#quickActionsModal">
                            <i class="ri-add-line me-1"></i> Quick Actions
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6">
                <div class="card mb-3 stats-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="p-2 border border-success rounded-circle me-3">
                                <div class="icon-box md bg-success-lighten rounded-5">
                                    <i class="ri-hotel-bed-line fs-4 text-success"></i>
                                </div>
                            </div>
                            <div class="d-flex flex-column">
                                <h3 class="lh-1">{{ $summary['available_beds'] }}</h3>
                                <p class="m-0">Bed Kosong</p>
                                <small class="text-muted">Siap untuk pasien baru</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card mb-3 stats-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="p-2 border border-primary rounded-circle me-3">
                                <div class="icon-box md bg-primary-lighten rounded-5">
                                    <i class="ri-user-heart-line fs-4 text-primary"></i>
                                </div>
                            </div>
                            <div class="d-flex flex-column">
                                <h3 class="lh-1">{{ $summary['occupied_beds'] }}</h3>
                                <p class="m-0">Pasien Aktif</p>
                                <small class="text-muted">Membutuhkan perawatan</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card mb-3 stats-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="p-2 border border-info rounded-circle me-3">
                                <div class="icon-box md bg-info-lighten rounded-5">
                                    <i class="ri-user-star-line fs-4 text-info"></i>
                                </div>
                            </div>
                            <div class="d-flex flex-column">
                                <h3 class="lh-1">{{ collect($nurseAssignments ?? [])->count() }}</h3>
                                <p class="m-0">Pasien Saya</p>
                                <small class="text-muted">Tanggung jawab shift ini</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card mb-3 stats-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="p-2 border border-warning rounded-circle me-3">
                                <div class="icon-box md bg-warning-lighten rounded-5">
                                    <i class="ri-alarm-warning-line fs-4 text-warning"></i>
                                </div>
                            </div>
                            <div class="d-flex flex-column">
                                <h3 class="lh-1">{{ collect($urgentTasks ?? [])->count() }}</h3>
                                <p class="m-0">Task Urgent</p>
                                <small class="text-muted">Perlu perhatian segera</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Key Performance Indicators for Nursing -->
        <div class="row gx-3 mb-4">
            <div class="col-12">
                <h5 class="mb-3"><i class="ri-dashboard-line"></i> Key Performance Indicators - Nursing</h5>
            </div>
            <div class="col-lg-3 col-md-6 col-12">
                <div class="card mb-3 border-start border-success border-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                @php
                                    $occupied = $summary['occupied_beds'] ?? 0;
                                    $available = $summary['available_beds'] ?? 0;
                                    $total = $occupied + $available;
                                    $occupancyPercent = $total > 0 ? number_format(($occupied / $total) * 100, 1) : 0;
                                @endphp
                                <h4 class="text-success">
                                    {{ $occupancyPercent }}%
                                </h4>
                                <p class="m-0 small">Bed Occupancy Rate</p>
                                <small class="text-muted">Target: 75-85%</small>
                            </div>
                            <div class="text-success">
                                <i class="ri-hotel-bed-line fs-2"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-12">
                <div class="card mb-3 border-start border-primary border-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="text-primary">{{ collect($nurseAssignments ?? [])->count() }}/8</h4>
                                <p class="m-0 small">Patient Load Ratio</p>
                                <small class="text-muted">Optimal: ≤ 8 patients</small>
                            </div>
                            <div class="text-primary">
                                <i class="ri-user-heart-line fs-2"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-12">
                <div class="card mb-3 border-start border-info border-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="text-info">{{ collect($urgentTasks ?? [])->count() }}</h4>
                                <p class="m-0 small">Pending Tasks</p>
                                <small class="text-muted">Priority actions</small>
                            </div>
                            <div class="text-info">
                                <i class="ri-task-line fs-2"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-12">
                <div class="card mb-3 border-start border-warning border-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="text-warning">{{ now()->format('H:i') }}</h4>
                                <p class="m-0 small">Current Time</p>
                                <small class="text-muted">Live clock</small>
                            </div>
                            <div class="text-warning">
                                <i class="ri-time-line fs-2"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="row">
            <!-- Room Status Grid -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0">Status Ruangan Real-time</h6>
                        <div>
                            <button class="btn btn-sm btn-outline-secondary" onclick="toggleView('grid')"
                                id="gridViewBtn">
                                <i class="ri-grid-line"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-secondary" onclick="toggleView('list')"
                                id="listViewBtn">
                                <i class="ri-list-check-2"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body" style="max-height: 600px; overflow-y: auto;">
                        <div id="roomGridView" class="room-grid">
                            @forelse($availability as $category)
                                @foreach ($category['classes'] as $className => $classData)
                                    @foreach ($classData['rooms'] as $room)
                                        <div
                                            class="patient-card p-3 {{ $room['available'] > 0 ? 'priority-normal' : 'priority-high' }}">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div>
                                                    <h6 class="mb-0">{{ $room['room_number'] }}</h6>
                                                    <small class="text-muted">{{ $category['category_name'] }} - Kelas
                                                        {{ $className }}</small>
                                                </div>
                                                <span
                                                    class="bed-status {{ $room['available'] > 0 ? 'bed-available' : 'bed-occupied' }}">
                                                    {{ $room['available'] > 0 ? 'KOSONG' : 'TERISI' }}
                                                </span>
                                            </div>

                                            <div class="mb-2">
                                                <div class="d-flex justify-content-between text-sm">
                                                    <span>Kapasitas:</span>
                                                    <strong>{{ $room['occupied'] }}/{{ $room['capacity'] }}</strong>
                                                </div>
                                                <div class="progress mt-1" style="height: 6px;">
                                                    <div class="progress-bar {{ $room['available'] > 0 ? 'bg-success' : 'bg-danger' }}"
                                                        style="width: {{ $room['capacity'] > 0 ? ($room['occupied'] / $room['capacity']) * 100 : 0 }}%">
                                                    </div>
                                                </div>
                                            </div>

                                            @if ($room['occupied'] > 0)
                                                <div class="border-top pt-2">
                                                    <small class="text-muted d-block mb-1">Pasien aktif:</small>
                                                    @php
                                                        $roomNumber = $room['room_number'];
                                                        $patientsInRoom = $roomPatients[$roomNumber] ?? collect();
                                                    @endphp

                                                    @if ($patientsInRoom->count() > 0)
                                                        @foreach ($patientsInRoom as $patient)
                                                            <div
                                                                class="d-flex justify-content-between align-items-center mb-1">
                                                                <div>
                                                                    <small
                                                                        class="fw-bold">{{ $patient['patient_name'] }}</small>
                                                                    <br>
                                                                    <small class="text-muted">
                                                                        <span
                                                                            class="badge bg-{{ $patient['condition'] === 'Critical' ? 'danger' : ($patient['condition'] === 'Stable' ? 'success' : 'warning') }} badge-sm">{{ $patient['condition'] }}</span>
                                                                        | {{ $patient['days_admitted'] }} hari
                                                                    </small>
                                                                </div>
                                                                <div>
                                                                    <button
                                                                        class="btn btn-sm btn-outline-primary action-btn"
                                                                        onclick="showPatientDetail('{{ $roomNumber }}', '{{ $patient['id'] }}')"
                                                                        title="Lihat Detail">
                                                                        <i class="ri-eye-line"></i>
                                                                    </button>
                                                                    <button
                                                                        class="btn btn-sm btn-outline-warning action-btn"
                                                                        onclick="addNursingNote('{{ $roomNumber }}', '{{ $patient['id'] }}')"
                                                                        title="Catatan Perawat">
                                                                        <i class="ri-file-text-line"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    @else
                                                        @for ($i = 1; $i <= $room['occupied']; $i++)
                                                            <div
                                                                class="d-flex justify-content-between align-items-center mb-1">
                                                                <small>Pasien {{ $i }} <span
                                                                        class="text-muted">(Data loading...)</span></small>
                                                                <div>
                                                                    <button
                                                                        class="btn btn-sm btn-outline-primary action-btn"
                                                                        onclick="showPatientDetail('{{ $room['room_number'] }}', {{ $i }})">
                                                                        <i class="ri-eye-line"></i>
                                                                    </button>
                                                                    <button
                                                                        class="btn btn-sm btn-outline-warning action-btn"
                                                                        onclick="addNursingNote('{{ $room['room_number'] }}', {{ $i }})">
                                                                        <i class="ri-file-text-line"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        @endfor
                                                    @endif
                                                </div>
                                            @endif

                                            @if ($room['available'] > 0)
                                                <div class="border-top pt-2 text-center">
                                                    <button class="btn btn-success btn-sm"
                                                        onclick="admitPatient('{{ $room['room_number'] }}')">
                                                        <i class="ri-user-add-line me-1"></i> Terima Pasien
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                @endforeach
                            @empty
                                <div class="col-12 text-center py-5">
                                    <i class="ri-hotel-bed-line display-1 text-muted"></i>
                                    <p class="text-muted">Tidak ada data ruangan</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Side Panel -->
            <div class="col-lg-4">
                <!-- My Assignments -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="card-title">Pasien Tanggung Jawab Saya</h6>
                    </div>
                    <div class="card-body" style="max-height: 250px; overflow-y: auto;">
                        @forelse($nurseAssignments ?? [] as $assignment)
                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
                                <div>
                                    <strong>{{ $assignment['patient_name'] ?? 'Pasien ' . $loop->iteration }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $assignment['room'] ?? 'R.101' }} |
                                        {{ $assignment['condition'] ?? 'Stabil' }}</small>
                                </div>
                                <button class="btn btn-sm btn-primary"
                                    onclick="openPatientFile('{{ $assignment['id'] ?? $loop->iteration }}')">
                                    <i class="ri-folder-open-line"></i>
                                </button>
                            </div>
                        @empty
                            <div class="text-center text-muted py-3">
                                <i class="ri-user-line"></i>
                                <p class="mb-0 small">Belum ada pasien yang ditugaskan</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Urgent Tasks -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="card-title text-danger">Task Urgent</h6>
                    </div>
                    <div class="card-body" style="max-height: 200px; overflow-y: auto;">
                        @forelse($urgentTasks ?? [] as $index => $task)
                            <div class="alert alert-{{ $task['priority'] === 'urgent' ? 'danger' : ($task['priority'] === 'high' ? 'warning' : 'info') }} p-2 mb-2"
                                id="task-{{ $index }}">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <small>
                                            <i
                                                class="ri-alarm-warning-line text-{{ $task['priority'] === 'urgent' ? 'danger' : ($task['priority'] === 'high' ? 'warning' : 'info') }} me-1"></i>
                                            <strong>{{ $task['message'] ?? 'Periksa tanda vital pasien R.102' }}</strong>
                                            <br>
                                            <em class="text-muted">{{ $task['time'] ?? '10 menit yang lalu' }}</em>
                                            @if (isset($task['type']))
                                                <br>
                                                <span
                                                    class="badge bg-secondary badge-sm">{{ ucfirst($task['type']) }}</span>
                                                <span
                                                    class="badge bg-{{ $task['priority'] === 'urgent' ? 'danger' : ($task['priority'] === 'high' ? 'warning' : 'info') }} badge-sm">{{ ucfirst($task['priority']) }}</span>
                                            @endif
                                        </small>
                                    </div>
                                    <div class="ms-2">
                                        <button class="btn btn-sm btn-outline-success"
                                            onclick="markTaskComplete({{ $index }})" title="Mark as Complete">
                                            <i class="ri-check-line"></i>
                                        </button>
                                        @if (isset($task['type']) && $task['type'] === 'critical_monitoring')
                                            <button class="btn btn-sm btn-outline-primary" onclick="quickVitalSigns()"
                                                title="Record Vital Signs">
                                                <i class="ri-heart-pulse-line"></i>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-3">
                                <i class="ri-check-double-line"></i>
                                <p class="mb-0 small">Tidak ada task urgent</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Quick Info -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title">Informasi Shift</h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6 border-end">
                                <h5 class="text-primary">{{ now()->format('H:i') }}</h5>
                                <small class="text-muted">Waktu saat ini</small>
                            </div>
                            <div class="col-6">
                                <h5 class="text-success">{{ collect($nurseAssignments ?? [])->count() }}</h5>
                                <small class="text-muted">Pasien aktif</small>
                            </div>
                        </div>
                        <hr>
                        <div class="d-grid gap-2">
                            <button class="btn btn-primary btn-sm" onclick="startHandover()">
                                <i class="ri-hand-heart-line me-1"></i> Serah Terima
                            </button>
                            <button class="btn btn-outline-secondary btn-sm" onclick="viewReports()">
                                <i class="ri-file-list-3-line me-1"></i> Laporan Shift
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Floating Nurse Tools -->
    <div class="nurse-tools">
        <div class="btn-group-vertical">
            <button class="btn btn-primary mb-2 rounded-circle" style="width: 50px; height: 50px;" data-bs-toggle="modal"
                data-bs-target="#emergencyModal" title="Emergency Call">
                <i class="ri-alarm-warning-line"></i>
            </button>
            <button class="btn btn-success mb-2 rounded-circle" style="width: 50px; height: 50px;"
                onclick="quickVitalSigns()" title="Vital Signs">
                <i class="ri-heart-pulse-line"></i>
            </button>
            <button class="btn btn-info rounded-circle" style="width: 50px; height: 50px;" onclick="nursingNotes()"
                title="Nursing Notes">
                <i class="ri-clipboard-line"></i>
            </button>
        </div>
    </div>

    <!-- Quick Actions Modal -->
    <div class="modal fade" id="quickActionsModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Quick Actions</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-6 mb-3">
                            <button class="btn btn-outline-primary w-100" onclick="newAdmission()">
                                <i class="ri-user-add-line d-block mb-1"></i>
                                <small>Pasien Baru</small>
                            </button>
                        </div>
                        <div class="col-6 mb-3">
                            <button class="btn btn-outline-success w-100" onclick="dischargePatient()">
                                <i class="ri-user-unfollow-line d-block mb-1"></i>
                                <small>Pulangkan</small>
                            </button>
                        </div>
                        <div class="col-6 mb-3">
                            <button class="btn btn-outline-warning w-100" onclick="transferPatient()">
                                <i class="ri-arrow-left-right-line d-block mb-1"></i>
                                <small>Transfer</small>
                            </button>
                        </div>
                        <div class="col-6 mb-3">
                            <button class="btn btn-outline-danger w-100" onclick="emergencyCall()">
                                <i class="ri-alarm-warning-line d-block mb-1"></i>
                                <small>Emergency</small>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- DataTables JS -->
    <script src="https://cdn.jsdelivr.net/npm/datatables.net@1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/datatables.net-bs5@1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <!-- Chart JS -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.44.0/dist/apexcharts.min.js"></script>
    <!-- OverlayScrollbars JS -->
    <script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@1.13.1/js/jquery.overlayScrollbars.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.9.0/dist/sweetalert2.all.min.js"></script>
    <!-- Moment JS -->
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
    <!-- Custom JS -->
    <script src="{{ asset('js/custom.js') }}"></script>
    <script>
        // Dashboard initialization
        $(document).ready(function() {
            initializeDashboard();
        });

        // Dashboard configuration
        const DASHBOARD_CONFIG = {
            refreshInterval: 30000, // 30 seconds
            maxToasts: 5
        };

        let currentView = 'grid';
        let toastCount = 0;

        // Initialize dashboard components
        function initializeDashboard() {
            // Initialize tooltips
            $('[title], [data-bs-toggle="tooltip"]').tooltip({
                placement: 'top',
                trigger: 'hover focus'
            });

            // Initialize OverlayScrollbars for modal content
            if (typeof OverlayScrollbars !== 'undefined') {
                $('.modal-body').each(function() {
                    if (this.scrollHeight > this.clientHeight) {
                        OverlayScrollbars(this, {
                            className: 'os-theme-light',
                            scrollbars: {
                                autoHide: 'move'
                            }
                        });
                    }
                });
            }

            // Setup auto-refresh
            setTimeout(function() {
                setInterval(function() {
                    if (document.visibilityState === 'visible') {
                        refreshData(true); // true = silent refresh
                    }
                }, DASHBOARD_CONFIG.refreshInterval);
            }, 10000); // Start after 10 seconds

            // Update clock every second
            setInterval(updateClock, 1000);

            console.log('✅ Nurse Dashboard initialized successfully');
        }

        // Update live clock
        function updateClock() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('id-ID', {
                hour12: false
            });
            const clockElements = document.querySelectorAll('.live-clock');
            clockElements.forEach(el => {
                el.textContent = timeString;
            });
        }

        function refreshData(silent = false) {
            const refreshBtn = document.querySelector('[onclick="refreshData()"]');
            let originalContent;

            if (!silent && refreshBtn) {
                // Add loading indicator for manual refresh
                originalContent = refreshBtn.innerHTML;
                refreshBtn.innerHTML = '<i class="ri-loader-2-line spin"></i> Refreshing...';
                refreshBtn.disabled = true;
            }

            // Call actual API endpoint
            fetch('{{ route('api.nurse-dashboard.refresh') }}', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update dashboard data
                        updateDashboardData(data.data);
                        if (!silent) {
                            showToast('success', 'Dashboard data refreshed successfully');
                        }
                    } else {
                        if (!silent) {
                            showToast('error', 'Failed to refresh data: ' + data.message);
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    if (!silent) {
                        showToast('error', 'Error refreshing data');
                    }
                })
                .finally(() => {
                    if (!silent && refreshBtn) {
                        refreshBtn.innerHTML = originalContent;
                        refreshBtn.disabled = false;
                    }
                });
        }

        function updateDashboardData(data) {
            // Update summary cards
            if (data.summary) {
                document.querySelector('.stats-card:nth-child(1) h3').textContent = data.summary.available_beds || 0;
                document.querySelector('.stats-card:nth-child(2) h3').textContent = data.summary.occupied_beds || 0;
            }

            // Update last updated timestamp
            const timestampElement = document.querySelector('.last-updated');
            if (timestampElement && data.last_updated) {
                timestampElement.textContent = 'Last updated: ' + data.last_updated;
            }
        }

        function toggleView(view) {
            currentView = view;
            // Implementation for grid/list view toggle
            if (view === 'grid') {
                document.getElementById('gridViewBtn').classList.add('active');
                document.getElementById('listViewBtn').classList.remove('active');
                document.getElementById('roomGridView').style.display = 'grid';
                // Hide list view if exists
                const listView = document.getElementById('roomListView');
                if (listView) listView.style.display = 'none';
            } else {
                document.getElementById('listViewBtn').classList.add('active');
                document.getElementById('gridViewBtn').classList.remove('active');
                document.getElementById('roomGridView').style.display = 'none';
                // Show list view if exists
                const listView = document.getElementById('roomListView');
                if (listView) listView.style.display = 'block';
            }
        }

        function showPatientDetail(roomNumber, patientId) {
            // Show loading modal
            showLoadingModal('Loading patient details...');

            fetch(`{{ url('/kunjungan/nurse-dashboard/patient') }}/${roomNumber}/${patientId || ''}`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    hideLoadingModal();
                    if (data.success) {
                        showPatientDetailModal(data.data);
                    } else {
                        showToast('error', data.message);
                    }
                })
                .catch(error => {
                    hideLoadingModal();
                    console.error('Error:', error);
                    showToast('error', 'Error loading patient details');
                });
        }

        function addNursingNote(roomNumber, patientId) {
            // First get patient details to get admission_id
            fetch(`{{ url('/kunjungan/nurse-dashboard/patient') }}/${roomNumber}/${patientId || ''}`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNursingNoteModal(data.data);
                    } else {
                        showToast('error', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('error', 'Error loading patient data');
                });
        }

        function submitNursingNote(admissionId) {
            const form = document.getElementById('nursingNoteForm');
            const formData = new FormData(form);

            fetch('{{ route('api.nurse-dashboard.add-nursing-note') }}', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast('success', 'Nursing note added successfully');
                        // Hide modal
                        const modal = bootstrap.Modal.getInstance(document.getElementById('nursingNoteModal'));
                        if (modal) modal.hide();
                        // Reset form
                        form.reset();
                    } else {
                        showToast('error', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('error', 'Error adding nursing note');
                });
        }

        function admitPatient(roomNumber) {
            // Show admission options modal
            showAdmissionModal(roomNumber);
        }

        function showAdmissionModal(roomNumber) {
            const modalHtml = `
        <div class="modal fade" id="admissionModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Terima Pasien - Ruangan ${roomNumber}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-3">Pilih jenis penerimaan pasien untuk ruangan ${roomNumber}:</p>

                        <div class="d-grid gap-2">
                            <button class="btn btn-primary" onclick="redirectToAdmission('rawatInap', '${roomNumber}')">
                                <i class="ri-user-add-line me-2"></i>
                                Rawat Inap Baru
                                <small class="d-block text-white-50">Pasien baru masuk rawat inap</small>
                            </button>

                            <button class="btn btn-info" onclick="redirectToAdmission('rawatDarurat', '${roomNumber}')">
                                <i class="ri-alarm-warning-line me-2"></i>
                                Transfer dari IGD
                                <small class="d-block text-white-50">Pasien dari rawat darurat</small>
                            </button>

                            <button class="btn btn-warning" onclick="transferFromRoom('${roomNumber}')">
                                <i class="ri-arrow-left-right-line me-2"></i>
                                Transfer Antar Ruangan
                                <small class="d-block text-white-50">Pindah dari ruangan lain</small>
                            </button>
                        </div>

                        <hr>
                        <div class="alert alert-info">
                            <small>
                                <i class="ri-information-line me-1"></i>
                                Pastikan ruangan ${roomNumber} siap untuk menerima pasien
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    </div>
                </div>
            </div>
        </div>
    `;

            // Remove existing modal
            const existing = document.getElementById('admissionModal');
            if (existing) existing.remove();

            // Add and show new modal
            document.body.insertAdjacentHTML('beforeend', modalHtml);
            const modal = new bootstrap.Modal(document.getElementById('admissionModal'));
            modal.show();
        }

        function redirectToAdmission(type, roomNumber) {
            let url;
            if (type === 'rawatInap') {
                url = '{{ route('pendaftaran.showRawatInap') }}' + `?room=${roomNumber}`;
            } else if (type === 'rawatDarurat') {
                url = '{{ route('pendaftaran.showRawatDarurat') }}' + `?transfer_to_room=${roomNumber}`;
            }

            // Hide modal first
            const modal = bootstrap.Modal.getInstance(document.getElementById('admissionModal'));
            if (modal) modal.hide();

            // Redirect
            window.location.href = url;
        }

        function transferFromRoom(roomNumber) {
            // Show available patients for transfer
            showTransferModal(roomNumber);
        }

        function showTransferModal(destinationRoom) {
            // First, get list of current patients in other rooms
            fetch('{{ route('api.nurse-dashboard.occupied-rooms') }}')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        createTransferModal(destinationRoom, data.occupied_rooms);
                    } else {
                        showToast('error', 'Failed to load room data');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('error', 'Error loading transfer data');
                });
        }

        function createTransferModal(destinationRoom, occupiedRooms) {
            let patientsHtml = '';

            if (occupiedRooms && occupiedRooms.length > 0) {
                occupiedRooms.forEach(room => {
                    if (room.room_number !== destinationRoom && room.patients && room.patients.length > 0) {
                        room.patients.forEach(patient => {
                            patientsHtml += `
                                <div class="patient-transfer-option border rounded p-3 mb-2 cursor-pointer"
                                     onclick="selectPatientForTransfer('${patient.id}', '${patient.patient_name}', '${room.room_number}', '${destinationRoom}')">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">${patient.patient_name}</h6>
                                            <small class="text-muted">RM: ${patient.medical_record || 'N/A'} | Ruangan: ${room.room_number}</small>
                                            <br>
                                            <small class="text-muted">Kondisi: <span class="badge bg-${patient.condition === 'Critical' ? 'danger' : patient.condition === 'Stable' ? 'success' : 'warning'}">${patient.condition || 'Active'}</span></small>
                                        </div>
                                        <div>
                                            <i class="ri-arrow-right-line fs-4 text-primary"></i>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                    }
                });
            }

            if (!patientsHtml) {
                patientsHtml = `
                    <div class="text-center py-4">
                        <i class="ri-user-line display-1 text-muted"></i>
                        <p class="text-muted">Tidak ada pasien yang dapat dipindah</p>
                    </div>
                `;
            }

            const modalHtml = `
                <div class="modal fade" id="transferModal" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header bg-warning text-white">
                                <h5 class="modal-title"><i class="ri-arrow-left-right-line me-2"></i>Transfer Pasien ke Ruangan ${destinationRoom}</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <p class="mb-3">Pilih pasien yang akan dipindah ke ruangan ${destinationRoom}:</p>

                                <div class="transfer-patients-list" style="max-height: 400px; overflow-y: auto;">
                                    ${patientsHtml}
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            // Remove existing modal
            const existing = document.getElementById('transferModal');
            if (existing) existing.remove();

            // Add CSS for hover effects
            const style = document.createElement('style');
            style.textContent = `
                .patient-transfer-option {
                    cursor: pointer;
                    transition: all 0.3s ease;
                }
                .patient-transfer-option:hover {
                    background-color: #f8f9fa;
                    border-color: #007bff !important;
                    transform: translateX(5px);
                }
            `;
            document.head.appendChild(style);

            // Add and show new modal
            document.body.insertAdjacentHTML('beforeend', modalHtml);
            const modal = new bootstrap.Modal(document.getElementById('transferModal'));
            modal.show();
        }

        function selectPatientForTransfer(patientId, patientName, fromRoom, toRoom) {
            // Show confirmation dialog
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Konfirmasi Transfer',
                    html: `Apakah Anda yakin ingin memindahkan pasien:<br><strong>${patientName}</strong><br>dari ruangan <strong>${fromRoom}</strong> ke ruangan <strong>${toRoom}</strong>?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Pindah!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        executePatientTransfer(patientId, patientName, fromRoom, toRoom);
                    }
                });
            } else {
                // Fallback if SweetAlert is not available
                if (confirm(
                        `Apakah Anda yakin ingin memindahkan pasien ${patientName} dari ruangan ${fromRoom} ke ruangan ${toRoom}?`
                    )) {
                    executePatientTransfer(patientId, patientName, fromRoom, toRoom);
                }
            }
        }

        function executePatientTransfer(patientId, patientName, fromRoom, toRoom) {
            // Hide transfer modal first
            const transferModal = bootstrap.Modal.getInstance(document.getElementById('transferModal'));
            if (transferModal) transferModal.hide();

            // Show loading
            showLoadingModal('Memproses transfer pasien...');

            // Make API call to transfer patient
            fetch('{{ route('api.nurse-dashboard.transfer-patient') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        patient_id: patientId,
                        from_room: fromRoom,
                        to_room: toRoom
                    })
                })
                .then(response => response.json())
                .then(data => {
                    hideLoadingModal();

                    if (data.success) {
                        showToast('success',
                            `Pasien ${patientName} berhasil dipindah dari ruangan ${fromRoom} ke ruangan ${toRoom}`);

                        // Refresh the dashboard data
                        refreshData(true);

                        // Show success notification
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                title: 'Transfer Berhasil!',
                                text: `Pasien ${patientName} telah dipindah ke ruangan ${toRoom}`,
                                icon: 'success',
                                timer: 3000
                            });
                        }
                    } else {
                        showToast('error', data.message || 'Gagal memindahkan pasien');
                    }
                })
                .catch(error => {
                    hideLoadingModal();
                    console.error('Error:', error);
                    showToast('error', 'Terjadi kesalahan saat memindahkan pasien');
                });
        }

        function emergencyCall() {
            showEmergencyCallModal();
        }

        function submitEmergencyCall() {
            const form = document.getElementById('emergencyCallForm');
            const formData = new FormData(form);

            fetch('{{ route('api.nurse-dashboard.emergency-call') }}', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast('success', 'Emergency call initiated successfully');
                        if (data.notification) {
                            showToast('info', data.notification);
                        }
                        // Hide modal
                        const modal = bootstrap.Modal.getInstance(document.getElementById('emergencyModal'));
                        if (modal) modal.hide();
                        // Reset form
                        form.reset();
                    } else {
                        showToast('error', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('error', 'Error initiating emergency call');
                });
        }

        function quickVitalSigns() {
            showVitalSignsModal();
        }

        function showVitalSignsModal() {
            const modalHtml = `
        <div class="modal fade" id="vitalSignsModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title"><i class="ri-heart-pulse-line me-2"></i>Quick Vital Signs Entry</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="vitalSignsForm">
                        <div class="modal-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Pilih Pasien</label>
                                    <select class="form-select" name="admission_id" required>
                                        <option value="">Pilih pasien...</option>
                                        @forelse($nurseAssignments ?? [] as $patient)
                                        <option value="{{ $patient['id'] ?? '' }}">{{ $patient['patient_name'] ?? 'N/A' }} - {{ $patient['room'] ?? 'N/A' }}</option>
                                        @empty
                                        <option value="">Tidak ada pasien aktif</option>
                                        @endforelse
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Waktu Pengukuran</label>
                                    <input type="datetime-local" class="form-control" name="measurement_time" value="{{ now()->format('Y-m-d\TH:i') }}" required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Perawat yang Mencatat</label>
                                    <input type="text" class="form-control" value="{{ auth()->user()->name ?? 'N/A' }}" readonly>
                                    <input type="hidden" name="recorded_by_id" value="{{ auth()->id() }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Status</label>
                                    <input type="text" class="form-control" value="On Duty - {{ auth()->user()->email ?? 'N/A' }}" readonly>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Tekanan Darah (Sistol/Diastol)</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" name="blood_pressure_systolic" placeholder="120" min="60" max="300">
                                            <span class="input-group-text">/</span>
                                            <input type="number" class="form-control" name="blood_pressure_diastolic" placeholder="80" min="30" max="200">
                                            <span class="input-group-text">mmHg</span>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Nadi</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" name="heart_rate" placeholder="72" min="30" max="200">
                                            <span class="input-group-text">bpm</span>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Suhu Tubuh</label>
                                        <div class="input-group">
                                            <input type="number" step="0.1" class="form-control" name="temperature" placeholder="36.5" min="30" max="45">
                                            <span class="input-group-text">°C</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Pernapasan</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" name="respiratory_rate" placeholder="18" min="5" max="60">
                                            <span class="input-group-text">/menit</span>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Saturasi Oksigen</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" name="oxygen_saturation" placeholder="98" min="70" max="100">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Tingkat Kesadaran</label>
                                        <select class="form-select" name="consciousness_level">
                                            <option value="alert">Alert</option>
                                            <option value="drowsy">Drowsy</option>
                                            <option value="confused">Confused</option>
                                            <option value="unconscious">Unconscious</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Catatan Tambahan</label>
                                <textarea class="form-control" name="notes" rows="3" placeholder="Catatan khusus tentang kondisi pasien atau hasil pengukuran..."></textarea>
                            </div>

                            <div class="alert alert-info">
                                <small>
                                    <i class="ri-information-line me-1"></i>
                                    <strong>Normal Values:</strong> TD: 120/80 mmHg | Nadi: 60-100 bpm | Suhu: 36.1-37.2°C | RR: 12-20/menit | SpO2: >95%
                                </small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-success">Simpan Vital Signs</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    `;

            // Remove existing modal
            const existing = document.getElementById('vitalSignsModal');
            if (existing) existing.remove();

            // Add and show new modal
            document.body.insertAdjacentHTML('beforeend', modalHtml);
            const modal = new bootstrap.Modal(document.getElementById('vitalSignsModal'));
            modal.show();

            // Add form submit handler
            document.getElementById('vitalSignsForm').addEventListener('submit', function(e) {
                e.preventDefault();
                submitVitalSigns();
            });
        }

        function submitVitalSigns() {
            const form = document.getElementById('vitalSignsForm');
            const formData = new FormData(form);

            // Validate required fields
            if (!formData.get('admission_id')) {
                showToast('error', 'Please select a patient');
                return;
            }

            fetch('{{ route('api.nurse-dashboard.vital-signs') }}', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show detailed success message with nurse info
                        const nurseInfo = data.nurse_info || {};
                        const patientName = data.data.patient_name || 'Unknown Patient';
                        const successMessage =
                            `Vital signs untuk ${patientName} berhasil dicatat oleh ${nurseInfo.name || 'Unknown Nurse'}`;

                        showToast('success', successMessage);

                        // Show detailed confirmation if SweetAlert is available
                        if (typeof Swal !== 'undefined') {
                            setTimeout(() => {
                                Swal.fire({
                                    title: 'Vital Signs Recorded!',
                                    html: `
                                        <div class="text-start">
                                            <p><strong>Pasien:</strong> ${data.data.patient_name}</p>
                                            <p><strong>Ruangan:</strong> ${data.data.room_number}</p>
                                            <p><strong>Dicatat oleh:</strong> ${nurseInfo.name} (${nurseInfo.role})</p>
                                            <p><strong>Waktu:</strong> ${nurseInfo.timestamp}</p>
                                            ${data.data.blood_pressure ? `<p><strong>TD:</strong> ${data.data.blood_pressure}</p>` : ''}
                                            ${data.data.heart_rate ? `<p><strong>Nadi:</strong> ${data.data.heart_rate}</p>` : ''}
                                            ${data.data.temperature ? `<p><strong>Suhu:</strong> ${data.data.temperature}</p>` : ''}
                                            ${data.data.respiratory_rate ? `<p><strong>Pernapasan:</strong> ${data.data.respiratory_rate}</p>` : ''}
                                            ${data.data.oxygen_saturation ? `<p><strong>SpO2:</strong> ${data.data.oxygen_saturation}</p>` : ''}
                                        </div>
                                    `,
                                    icon: 'success',
                                    timer: 5000,
                                    showConfirmButton: false
                                });
                            }, 500);
                        }

                        // Hide modal
                        const modal = bootstrap.Modal.getInstance(document.getElementById('vitalSignsModal'));
                        if (modal) modal.hide();

                        // Reset form
                        form.reset();
                    } else {
                        showToast('error', data.message || 'Error saving vital signs');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('error', 'Error saving vital signs');
                });
        }

        function nursingNotes() {
            showGeneralNursingNotesModal();
        }

        function showGeneralNursingNotesModal() {
            const modalHtml = `
        <div class="modal fade" id="generalNotesModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title"><i class="ri-clipboard-line me-2"></i>General Nursing Documentation</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <ul class="nav nav-pills mb-3" id="noteTabs">
                            <li class="nav-item">
                                <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#addNote">Tambah Catatan</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="pill" data-bs-target="#viewNotes">Lihat Catatan</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="pill" data-bs-target="#templates">Template</button>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <!-- Add Note Tab -->
                            <div class="tab-pane fade show active" id="addNote">
                                <form id="generalNoteForm">
                                    <div class="row mb-3">
                                        <div class="col-md-8">
                                            <label class="form-label">Pilih Pasien</label>
                                            <select class="form-select" name="admission_id" required>
                                                <option value="">Pilih pasien...</option>
                                                @forelse($nurseAssignments ?? [] as $patient)
                                                <option value="{{ $patient['id'] ?? '' }}">{{ $patient['patient_name'] ?? 'N/A' }} - {{ $patient['room'] ?? 'N/A' }}</option>
                                                @empty
                                                <option value="">Tidak ada pasien aktif</option>
                                                @endforelse
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Template</label>
                                            <select class="form-select" id="noteTemplate" onchange="applyTemplate()">
                                                <option value="">Pilih template...</option>
                                                <option value="admission">Penerimaan Pasien</option>
                                                <option value="shift_handover">Serah Terima</option>
                                                <option value="medication">Pemberian Obat</option>
                                                <option value="observation">Observasi</option>
                                                <option value="discharge">Persiapan Pulang</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Tipe Catatan</label>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <select class="form-select" name="note_type">
                                                    <option value="general">General</option>
                                                    <option value="observation">Observation</option>
                                                    <option value="medication">Medication</option>
                                                    <option value="procedure">Procedure</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <select class="form-select" name="priority">
                                                    <option value="normal">Normal</option>
                                                    <option value="low">Low</option>
                                                    <option value="high">High</option>
                                                    <option value="urgent">Urgent</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Catatan Keperawatan</label>
                                        <textarea class="form-control" name="note" id="noteContent" rows="6" placeholder="Tulis catatan keperawatan di sini..." required></textarea>
                                        <div class="form-text">Format SOAP: Subjective, Objective, Assessment, Plan</div>
                                    </div>

                                    <div class="alert alert-info">
                                        <small>
                                            <i class="ri-information-line me-1"></i>
                                            <strong>Tips:</strong> Gunakan bahasa yang jelas dan objektif. Sertakan waktu, kondisi pasien, tindakan yang dilakukan, dan respons pasien.
                                        </small>
                                    </div>
                                </form>
                            </div>

                            <!-- View Notes Tab -->
                            <div class="tab-pane fade" id="viewNotes">
                                <div class="mb-3">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <input type="date" class="form-control" id="filterDate" value="{{ now()->format('Y-m-d') }}">
                                        </div>
                                        <div class="col-md-6">
                                            <button class="btn btn-outline-primary" onclick="loadNursingNotes()">Filter Notes</button>
                                        </div>
                                    </div>
                                </div>
                                <div id="nursingNotesList">Loading notes...</div>
                            </div>

                            <!-- Templates Tab -->
                            <div class="tab-pane fade" id="templates">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-header">Template Penerimaan</div>
                                            <div class="card-body">
                                                <small>Pasien diterima dalam keadaan [kondisi], kesadaran [tingkat kesadaran], TTV: TD [tekanan darah], N [nadi], S [suhu], RR [respirasi]. Keluhan utama: [keluhan]. Riwayat penyakit: [riwayat].</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-header">Template Observasi</div>
                                            <div class="card-body">
                                                <small>Jam [waktu]: Pasien tampak [kondisi umum], mengeluh [keluhan]. TTV stabil/tidak stabil. Aktivitas [aktivitas]. Intake/output: [cairan]. Rencana: [tindakan selanjutnya].</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" form="generalNoteForm" class="btn btn-info">Simpan Catatan</button>
                    </div>
                </div>
            </div>
        </div>
    `;

            // Remove existing modal
            const existing = document.getElementById('generalNotesModal');
            if (existing) existing.remove();

            // Add and show new modal
            document.body.insertAdjacentHTML('beforeend', modalHtml);
            const modal = new bootstrap.Modal(document.getElementById('generalNotesModal'));
            modal.show();

            // Add form submit handler
            document.getElementById('generalNoteForm').addEventListener('submit', function(e) {
                e.preventDefault();
                submitGeneralNote();
            });

            // Load notes when view tab is clicked
            document.querySelector('[data-bs-target="#viewNotes"]').addEventListener('shown.bs.tab', loadNursingNotes);
        }

        function applyTemplate() {
            const templateSelect = document.getElementById('noteTemplate');
            const noteContent = document.getElementById('noteContent');
            const templates = {
                admission: 'Pasien diterima dalam keadaan [kondisi umum], kesadaran [compos mentis/somnolen/koma], TTV: TD [120/80] mmHg, N [80] x/menit, S [36.5]°C, RR [20] x/menit.\n\nKeluhan utama: [keluhan pasien]\nRiwayat penyakit: [riwayat singkat]\nAlergi: [ada/tidak ada]\nDiet: [jenis diet]\nAktivitas: [bed rest/mobilisasi bertahap]',
                shift_handover: 'Serah terima shift [pagi/sore/malam]:\n\nKondisi umum pasien: [stabil/perlu observasi]\nTTV terakhir: TD [tekanan], N [nadi], S [suhu], RR [respirasi]\nKeluhan: [ada/tidak ada keluhan]\nObat yang sudah diberikan: [nama obat dan waktu]\nTindakan yang dilakukan: [tindakan]\nRencana selanjutnya: [rencana untuk shift berikutnya]',
                medication: 'Pemberian obat:\n\nNama obat: [nama obat]\nDosis: [dosis]\nRute: [oral/IV/IM/SC]\nWaktu: [waktu pemberian]\nReaksi: [tidak ada reaksi/reaksi yang terjadi]\nKateter/infus: [kondisi akses vaskular]\nEfek yang diharapkan: [efek terapi]',
                observation: 'Observasi pasien:\n\nWaktu: [jam observasi]\nKondisi umum: [baik/sedang/lemah]\nTTV: TD [tekanan], N [nadi], S [suhu], RR [respirasi]\nKesadaran: [tingkat kesadaran]\nAktivitas: [aktivitas yang dilakukan]\nKeluhan: [keluhan yang disampaikan]\nIntake/Output: [cairan masuk/keluar]\nRencana: [tindakan selanjutnya]',
                discharge: 'Persiapan pemulangan pasien:\n\nKondisi saat pulang: [kondisi umum]\nTTV terakhir: [vital signs]\nObat pulang: [obat dan instruksi]\nEdukasi yang diberikan: [edukasi perawatan di rumah]\nKontrol: [jadwal kontrol]\nPesan khusus: [pesan untuk keluarga]\nTransportasi: [cara pulang]'
            };

            if (templates[templateSelect.value]) {
                noteContent.value = templates[templateSelect.value];
            }
        }

        function submitGeneralNote() {
            const form = document.getElementById('generalNoteForm');
            const formData = new FormData(form);

            fetch('{{ route('api.nurse-dashboard.add-nursing-note') }}', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast('success', 'Catatan keperawatan berhasil disimpan');
                        // Reset form
                        form.reset();
                        document.getElementById('noteTemplate').value = '';
                    } else {
                        showToast('error', data.message || 'Error saving nursing note');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('error', 'Error saving nursing note');
                });
        }

        function loadNursingNotes() {
            const container = document.getElementById('nursingNotesList');
            if (!container) return;

            container.innerHTML =
                '<div class="text-center"><div class="spinner-border spinner-border-sm"></div> Loading...</div>';

            // Simulate loading notes
            setTimeout(() => {
                container.innerHTML = `
            <div class="list-group">
                <div class="list-group-item">
                    <div class="d-flex justify-content-between">
                        <h6 class="mb-1">Observasi Rutin</h6>
                        <small class="text-muted">{{ now()->format('H:i') }}</small>
                    </div>
                    <p class="mb-1">Kondisi umum pasien baik, TTV dalam batas normal. Tidak ada keluhan khusus.</p>
                    <small class="text-muted">Type: General | Priority: Normal | Perawat: {{ auth()->user()->name ?? 'N/A' }}</small>
                </div>
            </div>
        `;
            }, 1000);
        }

        function openPatientFile(patientId) {
            showToast('info', `Opening patient file for ID: ${patientId} - Not implemented yet`);
            // This would typically redirect to patient file/chart
        }

        function startHandover() {
            showHandoverModal();
        }

        function showHandoverModal() {
            const modalHtml = `
        <div class="modal fade" id="handoverModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title"><i class="ri-hand-heart-line me-2"></i>Serah Terima Shift</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="handoverForm">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Tanggal & Waktu</label>
                                    <input type="text" class="form-control" value="{{ now()->format('d/m/Y H:i') }}" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Perawat Pengganti</label>
                                    <select class="form-select" name="next_nurse" required>
                                        <option value="">Pilih perawat...</option>
                                        @foreach ($allNurses as $nurse)
                                            @if ($nurse->id !== auth()->id())
                                                <option value="{{ $nurse->id }}">{{ $nurse->name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Ringkasan Pasien ({{ collect($nurseAssignments ?? [])->count() }} pasien aktif)</label>
                                <div class="border rounded p-2" style="max-height: 200px; overflow-y: auto; background: #f8f9fa;">
                                    @forelse($nurseAssignments ?? [] as $patient)
                                    <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-white rounded">
                                        <div>
                                            <strong>{{ $patient['patient_name'] ?? 'N/A' }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $patient['room'] ?? 'N/A' }} | {{ $patient['condition'] ?? 'N/A' }} | {{ $patient['days_admitted'] ?? 0 }} hari</small>
                                        </div>
                                        <span class="badge bg-{{ $patient['condition'] === 'Critical' ? 'danger' : ($patient['condition'] === 'Stable' ? 'success' : 'warning') }}">{{ $patient['condition'] ?? 'Active' }}</span>
                                    </div>
                                    @empty
                                    <p class="text-muted mb-0">Tidak ada pasien aktif</p>
                                    @endforelse
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Catatan Khusus / Perhatian</label>
                                <textarea class="form-control" name="special_notes" rows="4" placeholder="Catatan khusus untuk shift berikutnya (kondisi pasien, obat khusus, family concerns, dll...)" required></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Task yang Belum Selesai</label>
                                <textarea class="form-control" name="pending_tasks" rows="3" placeholder="Task yang perlu dilanjutkan oleh shift berikutnya"></textarea>
                            </div>

                            <div class="alert alert-info">
                                <small>
                                    <i class="ri-information-line me-1"></i>
                                    Pastikan semua informasi penting telah dicatat dengan lengkap
                                </small>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" form="handoverForm" class="btn btn-primary">Selesaikan Handover</button>
                    </div>
                </div>
            </div>
        </div>
    `;

            // Remove existing modal
            const existing = document.getElementById('handoverModal');
            if (existing) existing.remove();

            // Add and show new modal
            document.body.insertAdjacentHTML('beforeend', modalHtml);
            const modal = new bootstrap.Modal(document.getElementById('handoverModal'));
            modal.show();

            // Add form submit handler
            document.getElementById('handoverForm').addEventListener('submit', function(e) {
                e.preventDefault();
                submitHandover();
            });
        }

        function submitHandover() {
            const form = document.getElementById('handoverForm');
            const formData = new FormData(form);

            // Simulate handover submission
            showToast('success', 'Serah terima shift berhasil didokumentasikan');

            // Hide modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('handoverModal'));
            if (modal) modal.hide();

            // In real system, this would save to database and notify next shift
            console.log('Handover data:', Object.fromEntries(formData));
        }

        function viewReports() {
            showReportsModal();
        }

        function showReportsModal() {
            // Get PHP data passed to JavaScript
            @php
                $occupied = $summary['occupied_beds'] ?? 0;
                $available = $summary['available_beds'] ?? 0;
                $total = $occupied + $available;
                $occupancyRate = $total > 0 ? number_format(($occupied / $total) * 100, 1) : 0;
            @endphp

            const shiftInfo = {
                nurseName: '{!! addslashes(auth()->user()->name ?? 'N/A') !!}',
                shiftDate: '{{ now()->format('d/m/Y') }}',
                currentTime: '{{ now()->format('H:i') }}',
                totalPatients: {{ collect($nurseAssignments ?? [])->count() }},
                urgentTasks: {{ collect($urgentTasks ?? [])->count() }},
                occupiedBeds: {{ $occupied }},
                availableBeds: {{ $available }},
                occupancyRate: {{ $occupancyRate }}
            };

            // Pre-render patient data in PHP
            const patientData = [
                @forelse($nurseAssignments ?? [] as $patient)
                    {
                        name: '{!! addslashes($patient['patient_name'] ?? 'N/A') !!}',
                        room: '{!! addslashes($patient['room'] ?? 'N/A') !!}',
                        condition: '{!! addslashes($patient['condition'] ?? 'Active') !!}',
                        conditionClass: '{{ $patient['condition'] === 'Critical' ? 'danger' : ($patient['condition'] === 'Stable' ? 'success' : 'warning') }}',
                        daysAdmitted: {{ $patient['days_admitted'] ?? 0 }},
                        doctorName: '{!! addslashes($patient['doctor_name'] ?? 'N/A') !!}',
                        medicalRecord: '{!! addslashes($patient['medical_record'] ?? 'N/A') !!}'
                    },
                @empty
                @endforelse
            ];

            let patientTableRows = '';
            if (patientData.length === 0) {
                patientTableRows = '<tr><td colspan="6" class="text-center text-muted">Tidak ada pasien aktif</td></tr>';
            } else {
                patientData.forEach(patient => {
                    patientTableRows += `
                        <tr>
                            <td><strong>${patient.name}</strong></td>
                            <td>${patient.room}</td>
                            <td><span class="badge bg-${patient.conditionClass}">${patient.condition}</span></td>
                            <td>${patient.daysAdmitted} hari</td>
                            <td>${patient.doctorName}</td>
                            <td>${patient.medicalRecord}</td>
                        </tr>
                    `;
                });
            }

            const modalHtml = `
        <div class="modal fade" id="reportsModal" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="ri-file-list-3-line me-2"></i>Laporan Shift Keperawatan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Report Navigation -->
                        <ul class="nav nav-tabs" id="reportTabs">
                            <li class="nav-item">
                                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#currentShift">Shift Saat Ini</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#patientSummary">Ringkasan Pasien</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#nursingNotes">Catatan Keperawatan</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#statistics">Statistik</button>
                            </li>
                        </ul>

                        <div class="tab-content mt-3">
                            <!-- Current Shift Tab -->
                            <div class="tab-pane fade show active" id="currentShift">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-header">
                                                <h6>Informasi Shift</h6>
                                            </div>
                                            <div class="card-body">
                                                <p><strong>Perawat:</strong> ` + shiftInfo.nurseName + `</p>
                                                <p><strong>Tanggal:</strong> ` + shiftInfo.shiftDate + `</p>
                                                <p><strong>Waktu:</strong> ` + shiftInfo.currentTime + `</p>
                                                <p><strong>Status:</strong> <span class="badge bg-success">Active</span></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-header">
                                                <h6>Ringkasan Aktivitas</h6>
                                            </div>
                                            <div class="card-body">
                                                <p><strong>Total Pasien:</strong> ` + shiftInfo.totalPatients + `</p>
                                                <p><strong>Task Selesai:</strong> <span id="completedTasks">0</span></p>
                                                <p><strong>Task Pending:</strong> ` + shiftInfo.urgentTasks + `</p>
                                                <p><strong>Catatan Dibuat:</strong> <span id="notesCreated">0</span></p>
                                                <p><strong>Emergency Calls:</strong> <span id="emergencyCalls">0</span></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Patient Summary Tab -->
                            <div class="tab-pane fade" id="patientSummary">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Nama Pasien</th>
                                                <th>Ruangan</th>
                                                <th>Kondisi</th>
                                                <th>Lama Rawat</th>
                                                <th>Dokter</th>
                                                <th>RM</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ` + patientTableRows + `
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Nursing Notes Tab -->
                            <div class="tab-pane fade" id="nursingNotes">
                                <div class="alert alert-info">
                                    <i class="ri-information-line me-1"></i>
                                    Menampilkan catatan keperawatan untuk shift saat ini
                                </div>
                                <div id="todayNotes">Loading nursing notes...</div>
                            </div>

                            <!-- Statistics Tab -->
                            <div class="tab-pane fade" id="statistics">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="text-center">
                                            <h3 class="text-primary">` + shiftInfo.occupiedBeds + `</h3>
                                            <p class="text-muted">Bed Terisi</p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center">
                                            <h3 class="text-success">` + shiftInfo.availableBeds + `</h3>
                                            <p class="text-muted">Bed Kosong</p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center">
                                            <h3 class="text-warning">` + shiftInfo.urgentTasks + `</h3>
                                            <p class="text-muted">Task Urgent</p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center">
                                            <h3 class="text-info">` + shiftInfo.occupancyRate + `%</h3>
                                            <p class="text-muted">Occupancy Rate</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-primary" onclick="printReport()">Print Report</button>
                        <button type="button" class="btn btn-outline-success" onclick="exportReport()">Export Excel</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    `;

            // Remove existing modal
            const existing = document.getElementById('reportsModal');
            if (existing) existing.remove();

            // Add and show new modal
            document.body.insertAdjacentHTML('beforeend', modalHtml);
            const modal = new bootstrap.Modal(document.getElementById('reportsModal'));
            modal.show();

            // Load nursing notes when tab is clicked
            const nursingNotesTab = document.querySelector('#reportTabs button[data-bs-target="#nursingNotes"]');
            if (nursingNotesTab) {
                nursingNotesTab.addEventListener('shown.bs.tab', function(event) {
                    loadTodayNotes();
                });
            }
        }

        function loadTodayNotes() {
            const container = document.getElementById('todayNotes');
            if (!container) return;

            container.innerHTML =
                '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div><p class="mt-2 text-muted">Memuat catatan...</p></div>';

            fetch('{{ route('api.nurse-dashboard.nursing-notes') }}')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.data.length > 0) {
                        let notesHtml = '<div class="list-group">';
                        data.data.forEach(note => {
                            notesHtml += `
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between">
                                        <h6 class="mb-1">Pasien: ${note.patient_name}</h6>
                                        <small class="text-muted">${note.time}</small>
                                    </div>
                                    <p class="mb-1">${note.note_preview}</p>
                                    <small class="text-muted">Dicatat oleh: ${note.nurse_name}</small>
                                </div>
                            `;
                        });
                        notesHtml += '</div>';
                        container.innerHTML = notesHtml;
                    } else if (data.success) {
                        container.innerHTML = `
                            <div class="text-center text-muted py-3">
                                <i class="ri-file-text-line fs-3"></i>
                                <p class="mb-0">Belum ada catatan keperawatan hari ini.</p>
                            </div>
                        `;
                    } else {
                        container.innerHTML =
                            `<div class="alert alert-danger">${data.message || 'Gagal memuat catatan.'}</div>`;
                    }
                })
                .catch(error => {
                    console.error('Error loading nursing notes:', error);
                    container.innerHTML = '<div class="alert alert-danger">Terjadi kesalahan saat memuat data.</div>';
                });
        }

        function printReport() {
            window.print();
            showToast('info', 'Report siap untuk dicetak');
        }

        function exportReport() {
            // Simulate export
            showToast('success', 'Report berhasil diexport ke Excel');
        }

        function newAdmission() {
            showToast('info', 'New patient admission - Not implemented yet');
        }

        function dischargePatient() {
            showToast('info', 'Patient discharge - Not implemented yet');
        }

        function transferPatient() {
            showToast('info', 'Patient transfer - Not implemented yet');
        }

        function markTaskComplete(taskIndex) {
            // Visual feedback - mark task as completed
            const taskElement = document.getElementById(`task-${taskIndex}`);
            if (taskElement) {
                taskElement.style.opacity = '0.5';
                taskElement.style.textDecoration = 'line-through';

                // Change the complete button to indicate it's done
                const completeBtn = taskElement.querySelector('button[onclick*="markTaskComplete"]');
                if (completeBtn) {
                    completeBtn.innerHTML = '<i class="ri-check-double-line"></i>';
                    completeBtn.classList.remove('btn-outline-success');
                    completeBtn.classList.add('btn-success');
                    completeBtn.disabled = true;
                }

                showToast('success', 'Task marked as completed');

                // Optionally, remove the task after a delay
                setTimeout(() => {
                    taskElement.style.transition = 'all 0.3s ease';
                    taskElement.style.transform = 'translateX(100%)';
                    setTimeout(() => {
                        taskElement.remove();
                        updateTaskCount();
                    }, 300);
                }, 1500);
            }
        }

        function updateTaskCount() {
            // Update the task count in the stats card
            const remainingTasks = document.querySelectorAll('[id^="task-"]:not([style*="line-through"])').length;
            const taskCountElements = document.querySelectorAll('.stats-card h3');
            if (taskCountElements.length >= 4) {
                taskCountElements[3].textContent = remainingTasks;
            }
        }

        // Utility functions
        function showToast(type, message, duration = 5000) {
            // Limit toast count
            toastCount++;
            if (toastCount > DASHBOARD_CONFIG.maxToasts) {
                return;
            }

            // Icon mapping
            const iconMap = {
                'success': 'ri-check-line',
                'error': 'ri-error-warning-line',
                'info': 'ri-information-line',
                'warning': 'ri-alert-line'
            };

            // Create toast element
            const toastHtml = `
        <div class="toast align-items-center text-white bg-${type === 'success' ? 'success' : type === 'error' ? 'danger' : type === 'info' ? 'info' : 'warning'}"
            role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="${iconMap[type] || 'ri-information-line'} me-2"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto"
                    data-bs-dismiss="toast"></button>
            </div>
        </div>
        `;

            // Get or create toast container
            let toastContainer = document.querySelector('.toast-container');
            if (!toastContainer) {
                toastContainer = document.createElement('div');
                toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
                document.body.appendChild(toastContainer);
            }

            // Add toast to container
            toastContainer.insertAdjacentHTML('beforeend', toastHtml);

            // Initialize and show toast
            const toastElements = toastContainer.querySelectorAll('.toast:last-child');
            const toastElement = toastElements[toastElements.length - 1];
            const toast = new bootstrap.Toast(toastElement, {
                delay: duration
            });

            // Decrease count when toast is hidden
            toastElement.addEventListener('hidden.bs.toast', function() {
                toastCount--;
                this.remove();
            });

            toast.show();
        }

        function showLoadingModal(message = 'Loading...') {
            const modalHtml = `
        <div class="modal fade" id="loadingModal" tabindex="-1" data-bs-backdrop="static">
            <div class="modal-dialog modal-sm modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body text-center py-4">
                        <div class="spinner-border text-primary mb-3" role="status"></div>
                        <p class="mb-0">${message}</p>
                    </div>
                </div>
            </div>
        </div>
        `;

            // Remove existing loading modal
            const existing = document.getElementById('loadingModal');
            if (existing) existing.remove();

            // Add new loading modal
            document.body.insertAdjacentHTML('beforeend', modalHtml);
            const modal = new bootstrap.Modal(document.getElementById('loadingModal'));
            modal.show();
        }

        function hideLoadingModal() {
            const modal = document.getElementById('loadingModal');
            if (modal) {
                const bsModal = bootstrap.Modal.getInstance(modal);
                if (bsModal) bsModal.hide();
                setTimeout(() => modal.remove(), 300);
            }
        }

        function showPatientDetailModal(patientData) {
            const modalHtml = `
        <div class="modal fade" id="patientDetailModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Patient Details - ${patientData.patient_name}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Patient Information</h6>
                                <p><strong>Name:</strong> ${patientData.patient_name}</p>
                                <p><strong>Medical Record:</strong> ${patientData.medical_record}</p>
                                <p><strong>Age:</strong> ${patientData.age}</p>
                                <p><strong>Gender:</strong> ${patientData.gender}</p>
                                <p><strong>Condition:</strong> <span
                                        class="badge bg-${patientData.condition === 'Critical' ? 'danger' : patientData.condition === 'Stable' ? 'success' : 'warning'}">${patientData.condition}</span>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <h6>Admission Information</h6>
                                <p><strong>Room:</strong> ${patientData.room_number} (${patientData.room_category})</p>
                                <p><strong>Class:</strong> ${patientData.room_class}</p>
                                <p><strong>Doctor:</strong> ${patientData.doctor_name}</p>
                                <p><strong>Admission Date:</strong> ${patientData.admission_date}</p>
                                <p><strong>Days Admitted:</strong> ${patientData.days_admitted} days</p>
                            </div>
                        </div>
                        <hr>
                        <h6>Vital Signs (Last Recorded)</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <p><strong>Blood Pressure:</strong> ${patientData.vital_signs.blood_pressure}</p>
                                <p><strong>Heart Rate:</strong> ${patientData.vital_signs.heart_rate}</p>
                            </div>
                            <div class="col-md-4">
                                <p><strong>Temperature:</strong> ${patientData.vital_signs.temperature}</p>
                                <p><strong>Respiratory Rate:</strong> ${patientData.vital_signs.respiratory_rate}</p>
                            </div>
                            <div class="col-md-4">
                                <p><strong>Oxygen Saturation:</strong> ${patientData.vital_signs.oxygen_saturation}</p>
                                <p><strong>Last Checked:</strong> ${patientData.vital_signs.last_checked}</p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary"
                            onclick="addNursingNote('${patientData.room_number}', '${patientData.id}')">Add Nursing
                            Note</button>
                    </div>
                </div>
            </div>
        </div>
        `;

            // Remove existing modal
            const existing = document.getElementById('patientDetailModal');
            if (existing) existing.remove();

            // Add and show new modal
            document.body.insertAdjacentHTML('beforeend', modalHtml);
            const modal = new bootstrap.Modal(document.getElementById('patientDetailModal'));
            modal.show();
        }

        function showNursingNoteModal(patientData) {
            const modalHtml = `
        <div class="modal fade" id="nursingNoteModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Nursing Note - ${patientData.patient_name}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="nursingNoteForm">
                        <div class="modal-body">
                            <input type="hidden" name="admission_id" value="${patientData.id}">

                            <div class="mb-3">
                                <label class="form-label">Note Type</label>
                                <select class="form-select" name="note_type">
                                    <option value="general">General</option>
                                    <option value="observation">Observation</option>
                                    <option value="medication">Medication</option>
                                    <option value="procedure">Procedure</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Priority</label>
                                <select class="form-select" name="priority">
                                    <option value="normal">Normal</option>
                                    <option value="low">Low</option>
                                    <option value="high">High</option>
                                    <option value="urgent">Urgent</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nursing Note</label>
                                <textarea class="form-control" name="note" rows="4" placeholder="Enter your nursing note here..." required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save Note</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        `;

            // Remove existing modal
            const existing = document.getElementById('nursingNoteModal');
            if (existing) existing.remove();

            // Add and show new modal
            document.body.insertAdjacentHTML('beforeend', modalHtml);
            const modal = new bootstrap.Modal(document.getElementById('nursingNoteModal'));
            modal.show();

            // Add form submit handler
            document.getElementById('nursingNoteForm').addEventListener('submit', function(e) {
                e.preventDefault();
                submitNursingNote(patientData.id);
            });
        }

        function showEmergencyCallModal() {
            const modalHtml = `
        <div class="modal fade" id="emergencyModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title"><i class="ri-alarm-warning-line me-2"></i>Emergency Call</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="emergencyCallForm">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Emergency Type</label>
                                <select class="form-select" name="emergency_type" required>
                                    <option value="">Select emergency type...</option>
                                    <option value="medical">Medical Emergency</option>
                                    <option value="fire">Fire Emergency</option>
                                    <option value="security">Security Issue</option>
                                    <option value="technical">Technical Problem</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Room Number (Optional)</label>
                                <input type="text" class="form-control" name="room_number"
                                    placeholder="e.g., R.101">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Priority</label>
                                <select class="form-select" name="priority">
                                    <option value="critical">Critical</option>
                                    <option value="high">High</option>
                                    <option value="medium">Medium</option>
                                    <option value="low">Low</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" name="description" rows="3" placeholder="Describe the emergency situation..."></textarea>
                            </div>

                            <div class="alert alert-warning">
                                <small><i class="ri-information-line me-1"></i>Emergency response team will be notified
                                    immediately upon submission.</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger">Call Emergency</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        `;

            // Remove existing modal
            const existing = document.getElementById('emergencyModal');
            if (existing) existing.remove();

            // Add and show new modal
            document.body.insertAdjacentHTML('beforeend', modalHtml);
            const modal = new bootstrap.Modal(document.getElementById('emergencyModal'));
            modal.show();

            // Add form submit handler
            document.getElementById('emergencyCallForm').addEventListener('submit', function(e) {
                e.preventDefault();
                submitEmergencyCall();
            });
        }

        // End of JavaScript functions
    </script>
@endpush
