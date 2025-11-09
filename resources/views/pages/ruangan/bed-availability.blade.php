@extends('layouts.app')
@section('title')
    Dashboard Ketersediaan Bed
@endsection
@push('style')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/datatables/dataTables.bs5.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
    <!-- Additional CSS for bed status indicators -->
    <style>
        .bed-status-card {
            transition: all 0.3s ease;
            border-radius: 8px;
            overflow: hidden;
        }

        .bed-status-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .availability-high {
            border-left: 4px solid #28a745;
        }

        .availability-medium {
            border-left: 4px solid #ffc107;
        }

        .availability-low {
            border-left: 4px solid #dc3545;
        }

        .availability-full {
            border-left: 4px solid #6c757d;
        }

        .bed-icon {
            font-size: 2rem;
        }

        .occupancy-progress {
            height: 8px;
        }

        .last-updated {
            font-size: 0.85rem;
            color: #6c757d;
        }

        .refresh-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 1000;
            border-radius: 50px;
            padding: 12px 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .category-header {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            border-radius: 8px 8px 0 0;
        }

        .room-detail-card {
            border: 1px solid #e9ecef;
            border-radius: 6px;
            transition: all 0.2s ease;
        }

        .room-detail-card:hover {
            border-color: #007bff;
            box-shadow: 0 2px 8px rgba(0, 123, 255, 0.15);
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <!-- Summary Cards Row -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="text-dark fw-bold">Dashboard Ketersediaan Bed</h4>
                    <div class="last-updated" id="lastUpdated">
                        <i class="ri-time-line me-1"></i>
                        Terakhir diperbarui: {{ now()->format('d/m/Y H:i:s') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Statistics -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bed-status-card availability-high">
                    <div class="card-body text-center">
                        <div class="bed-icon text-primary mb-2">
                            <i class="ri-hotel-bed-fill"></i>
                        </div>
                        <h3 class="text-primary mb-1">{{ $summary['total_beds'] }}</h3>
                        <p class="text-muted mb-0">Total Bed</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bed-status-card availability-medium">
                    <div class="card-body text-center">
                        <div class="bed-icon text-success mb-2">
                            <i class="ri-check-double-line"></i>
                        </div>
                        <h3 class="text-success mb-1">{{ $summary['available_beds'] }}</h3>
                        <p class="text-muted mb-0">Bed Tersedia</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bed-status-card availability-low">
                    <div class="card-body text-center">
                        <div class="bed-icon text-warning mb-2">
                            <i class="ri-user-3-fill"></i>
                        </div>
                        <h3 class="text-warning mb-1">{{ $summary['occupied_beds'] }}</h3>
                        <p class="text-muted mb-0">Bed Terisi</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bed-status-card availability-full">
                    <div class="card-body text-center">
                        <div class="bed-icon text-info mb-2">
                            <i class="ri-percent-line"></i>
                        </div>
                        <h3 class="text-info mb-1">{{ $summary['occupancy_rate'] }}%</h3>
                        <p class="text-muted mb-0">Tingkat Hunian</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bed Availability by Category -->
        <div class="row">
            @forelse($availability as $category)
                <div class="col-12 mb-4">
                    <div class="card">
                        <div class="category-header p-3">
                            <h5 class="mb-0">
                                <i class="ri-building-2-line me-2"></i>
                                {{ $category['category_name'] }}
                            </h5>
                            @if ($category['description'])
                                <small class="opacity-75">{{ $category['description'] }}</small>
                            @endif
                        </div>
                        <div class="card-body p-0">
                            @forelse($category['classes'] as $className => $classData)
                                <div class="border-bottom">
                                    <div class="p-3">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div>
                                                <h6 class="mb-1 text-dark fw-semibold">
                                                    <i class="ri-price-tag-3-line me-2 text-primary"></i>
                                                    Kelas {{ $className }}
                                                </h6>
                                                <small class="text-muted">
                                                    {{ $classData['total_rooms'] }} ruangan •
                                                    {{ $classData['total_beds'] }} bed
                                                </small>
                                            </div>
                                            <div class="text-end">
                                                <span
                                                    class="badge bg-{{ $classData['available_beds'] > 5 ? 'success' : ($classData['available_beds'] > 2 ? 'warning' : 'danger') }} fs-6">
                                                    {{ $classData['available_beds'] }} bed tersedia
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Progress Bar -->
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between mb-1">
                                                <small class="text-muted">Tingkat Hunian</small>
                                                <small class="text-muted">{{ $classData['occupancy_rate'] }}%</small>
                                            </div>
                                            <div class="progress occupancy-progress">
                                                <div class="progress-bar bg-{{ $classData['occupancy_rate'] > 80 ? 'danger' : ($classData['occupancy_rate'] > 60 ? 'warning' : 'success') }}"
                                                    style="width: {{ $classData['occupancy_rate'] }}%"></div>
                                            </div>
                                        </div>

                                        <!-- Room Details -->
                                        @if (count($classData['rooms']) > 0)
                                            <div class="row">
                                                @foreach ($classData['rooms'] as $room)
                                                    <div class="col-md-4 mb-2">
                                                        <div class="room-detail-card p-2">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div>
                                                                    <strong
                                                                        class="text-dark">{{ $room['room_number'] }}</strong>
                                                                    <br>
                                                                    <small class="text-muted">{{ $room['capacity'] }}
                                                                        bed</small>
                                                                </div>
                                                                <div class="text-end">
                                                                    <span
                                                                        class="badge bg-{{ $room['available'] > 0 ? 'success' : 'secondary' }}">
                                                                        {{ $room['available'] }}/{{ $room['capacity'] }}
                                                                    </span>
                                                                    <br>
                                                                    <small class="text-primary fw-semibold">
                                                                        Rp {{ number_format($room['price'], 0, ',', '.') }}
                                                                    </small>
                                                                </div>
                                                            </div>
                                                            @if ($room['description'])
                                                                <small
                                                                    class="text-muted d-block mt-1">{{ $room['description'] }}</small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="p-3 text-center text-muted">
                                    <i class="ri-information-line"></i>
                                    Tidak ada ruangan untuk kategori ini
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="ri-hotel-bed-line display-1 text-muted mb-3"></i>
                            <h5 class="text-muted">Tidak ada data ruangan</h5>
                            <p class="text-muted">Silakan tambahkan kategori ruangan dan ruangan terlebih dahulu</p>
                            <a href="{{ route('ruangan.create') }}" class="btn btn-primary">
                                <i class="ri-add-line me-1"></i>
                                Tambah Ruangan
                            </a>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Refresh Button -->
    <button class="btn btn-primary refresh-btn" onclick="refreshData()" id="refreshBtn">
        <i class="ri-refresh-line me-1"></i>
        Refresh Data
    </button>
@endsection

@push('scripts')
    <!-- DataTables JS -->
    <script src="{{ asset('vendor/datatables/dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/dataTables.bs5.min.js') }}"></script>
    <script src="{{ asset('vendor/overlay-scroll/jquery.overlayScrollbars.min.js') }}"></script>
    <script src="{{ asset('assets/js/custom-scrollbar.js') }}"></script>
    <script src="{{ asset('js/custom.js') }}"></script>

    <script>
        function refreshData() {
            const refreshBtn = document.getElementById('refreshBtn');
            const originalContent = refreshBtn.innerHTML;

            // Show loading state
            refreshBtn.innerHTML = '<i class="ri-loader-2-line me-1 spin"></i> Memperbarui...';
            refreshBtn.disabled = true;

            // Make AJAX request to get fresh data
            fetch('{{ route('ruangan.bed-availability.api') }}')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update last updated time
                        document.getElementById('lastUpdated').innerHTML =
                            '<i class="ri-time-line me-1"></i>Terakhir diperbarui: ' + data.data.last_updated;

                        // Reload the page to show updated data
                        location.reload();
                    } else {
                        alert('Gagal memperbarui data: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat memperbarui data');
                })
                .finally(() => {
                    // Restore button state
                    refreshBtn.innerHTML = originalContent;
                    refreshBtn.disabled = false;
                });
        }

        // Auto refresh every 5 minutes
        setInterval(function() {
            refreshData();
        }, 300000); // 300000 ms = 5 minutes

        // Add spinning animation for refresh icon
        const style = document.createElement('style');
        style.textContent = `
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    .spin {
        animation: spin 1s linear infinite;
    }
`;
        document.head.appendChild(style);
    </script>
@endpush
