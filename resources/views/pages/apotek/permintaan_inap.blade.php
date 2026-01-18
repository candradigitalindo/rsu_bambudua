@extends('layouts.app')

@section('title', 'Permintaan Obat Rawat Inap')

@push('style')
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endpush

@section('content')
    <div class="row gx-3">
        <div class="col-12">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ri-medicine-bottle-line me-2"></i>Permintaan Obat Rawat Inap
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Filter Section -->
                    <form method="GET" class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label class="form-label">Tanggal Mulai</label>
                            <input type="date" name="start_date" class="form-control"
                                value="{{ request('start_date') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tanggal Akhir</label>
                            <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Semua Status
                                </option>
                                <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Menunggu
                                </option>
                                <option value="Verified" {{ request('status') == 'Verified' ? 'selected' : '' }}>
                                    Diverifikasi</option>
                                <option value="Ready" {{ request('status') == 'Ready' ? 'selected' : '' }}>Siap Diambil
                                </option>
                                <option value="Dispensed" {{ request('status') == 'Dispensed' ? 'selected' : '' }}>
                                    Diserahkan</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1">
                                <i class="ri-search-line"></i> Filter
                            </button>
                            <a href="{{ route('apotek.permintaan-inap') }}" class="btn btn-outline-secondary">
                                <i class="ri-refresh-line"></i>
                            </a>
                        </div>
                    </form>

                    <!-- Status Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3 col-sm-6">
                            <div class="alert alert-info mb-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <small class="text-muted">Total Resep</small>
                                        <h4 class="mb-0">{{ $statusCounts['all'] }}</h4>
                                    </div>
                                    <i class="ri-file-list-line fs-1 opacity-25"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="alert alert-warning mb-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <small class="text-muted">Menunggu</small>
                                        <h4 class="mb-0">{{ $statusCounts['Pending'] }}</h4>
                                    </div>
                                    <i class="ri-time-line fs-1 opacity-25"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="alert alert-primary mb-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <small class="text-muted">Siap Diambil</small>
                                        <h4 class="mb-0">{{ $statusCounts['Ready'] }}</h4>
                                    </div>
                                    <i class="ri-checkbox-circle-line fs-1 opacity-25"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="alert alert-success mb-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <small class="text-muted">Diserahkan</small>
                                        <h4 class="mb-0">{{ $statusCounts['Dispensed'] }}</h4>
                                    </div>
                                    <i class="ri-check-double-line fs-1 opacity-25"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" width="50">No</th>
                                    <th>Tanggal</th>
                                    <th>Pasien</th>
                                    <th>Ruangan</th>
                                    <th>Dokter</th>
                                    <th class="text-center">Jml Obat</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center" width="180">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($prescriptions as $index => $prescription)
                                    @php
                                        $statusConfig = match ($prescription->pharmacy_status) {
                                            'Pending' => [
                                                'class' => 'bg-warning text-dark',
                                                'text' => 'Menunggu',
                                                'icon' => 'time',
                                            ],
                                            'Verified' => [
                                                'class' => 'bg-info',
                                                'text' => 'Diverifikasi',
                                                'icon' => 'checkbox-circle',
                                            ],
                                            'Ready' => [
                                                'class' => 'bg-primary',
                                                'text' => 'Siap Diambil',
                                                'icon' => 'archive',
                                            ],
                                            'Dispensed' => [
                                                'class' => 'bg-success',
                                                'text' => 'Diserahkan',
                                                'icon' => 'check-double',
                                            ],
                                            default => [
                                                'class' => 'bg-secondary',
                                                'text' => $prescription->pharmacy_status,
                                                'icon' => 'question',
                                            ],
                                        };
                                    @endphp
                                    <tr>
                                        <td class="text-center">{{ $prescriptions->firstItem() + $index }}</td>
                                        <td>
                                            <small>{{ $prescription->created_at->format('d/m/Y') }}</small><br>
                                            <small
                                                class="text-muted">{{ $prescription->created_at->format('H:i') }}</small>
                                        </td>
                                        <td>
                                            <strong>{{ $prescription->encounter->inpatientAdmission->patient->name ?? '-' }}</strong>
                                            @if ($prescription->encounter->inpatientAdmission->patient->is_kerabat ?? false)
                                                <span class="badge bg-warning text-dark ms-1"
                                                    style="font-size: 0.7em;">Kerabat</span>
                                            @else
                                                <span class="badge bg-info ms-1" style="font-size: 0.7em;">Reguler</span>
                                            @endif
                                            <br>
                                            <small
                                                class="text-muted">{{ $prescription->encounter->inpatientAdmission->patient->rekam_medis ?? '-' }}</small>
                                        </td>
                                        <td>
                                            @php
                                                $ruangan =
                                                    $prescription->encounter->inpatientAdmission->ruangan ?? null;
                                                $ruanganName = $ruangan
                                                    ? $ruangan->no_kamar ?? ($ruangan->description ?? '-')
                                                    : '-';
                                            @endphp
                                            <span class="badge bg-primary">{{ $ruanganName }}</span>
                                        </td>
                                        <td>{{ $prescription->doctor->name ?? '-' }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-info rounded-pill">
                                                {{ $prescription->medications->count() }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge {{ $statusConfig['class'] }}">
                                                <i class="ri-{{ $statusConfig['icon'] }}-line"></i>
                                                {{ $statusConfig['text'] }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-info"
                                                onclick="viewDetail('{{ $prescription->id }}')">
                                                <i class="ri-eye-line"></i>
                                            </button>
                                            @if ($prescription->pharmacy_status !== 'Dispensed')
                                                <button class="btn btn-sm btn-primary"
                                                    onclick="updateStatus('{{ $prescription->id }}', '{{ $prescription->pharmacy_status }}')">
                                                    <i class="ri-arrow-right-line"></i>
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-5">
                                            <i class="ri-inbox-line" style="font-size: 64px; opacity: 0.2;"></i>
                                            <p class="text-muted mt-3">Tidak ada permintaan obat rawat inap</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if ($prescriptions->hasPages())
                        <div class="mt-3">
                            {{ $prescriptions->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail -->
    <div class="modal fade" id="modalDetail" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="ri-file-list-line me-2"></i>Detail Resep</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="detailContent">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Memuat detail resep...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Update Status -->
    <div class="modal fade" id="modalUpdateStatus" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="ri-refresh-line me-2"></i>Update Status Resep</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="formUpdateStatus">
                    <div class="modal-body">
                        <input type="hidden" id="prescription_id">
                        <div class="alert alert-info">
                            <i class="ri-information-line me-2"></i>
                            Pilih status selanjutnya untuk resep ini
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="pharmacy_status" required>
                                <option value="">Pilih Status</option>
                                <option value="Verified">Diverifikasi</option>
                                <option value="Ready">Siap Diambil</option>
                                <option value="Dispensed">Diserahkan</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Catatan</label>
                            <textarea class="form-control" id="pharmacy_notes" rows="3"
                                placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="ri-close-line"></i> Batal
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-check-line"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('vendor/overlay-scroll/jquery.overlayScrollbars.min.js') }}"></script>
    <script src="{{ asset('vendor/overlay-scroll/custom-scrollbar.js') }}"></script>
    <!-- Custom JS files -->
    <script src="{{ asset('js/custom.js') }}"></script>
    <script>
        function viewDetail(id) {
            const modal = new bootstrap.Modal(document.getElementById('modalDetail'), {
                backdrop: false
            });
            modal.show();

            fetch(`/apotek/permintaan-inap/detail/${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const p = data.data;
                        const admission = p.encounter.inpatient_admission;

                        let html = `
                            <div class="card border-primary mb-3">
                                <div class="card-header bg-light">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6 class="mb-2"><i class="ri-user-line text-primary"></i> Informasi Pasien</h6>
                                            <table class="table table-sm table-borderless mb-0">
                                                <tr>
                                                    <td width="100">Nama</td>
                                                    <td>: <strong>${admission.patient.name}</strong>
                                                    ${admission.patient.is_kerabat ? '<span class="badge bg-warning text-dark ms-1" style="font-size: 0.7em;">Kerabat</span>' : '<span class="badge bg-info ms-1" style="font-size: 0.7em;">Reguler</span>'}
                                                    </td>
                                                </tr>
                                                <tr><td>No. RM</td><td>: ${admission.patient.rekam_medis || '-'}</td></tr>
                                                <tr><td>Ruangan</td><td>: <span class="badge bg-primary">${admission.ruangan.no_kamar || admission.ruangan.description || '-'}</span></td></tr>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="mb-2"><i class="ri-stethoscope-line text-primary"></i> Informasi Resep</h6>
                                            <table class="table table-sm table-borderless mb-0">
                                                <tr><td width="100">Dokter</td><td>: <strong>${p.doctor.name}</strong></td></tr>
                                                <tr><td>Tanggal</td><td>: ${new Date(p.created_at).toLocaleString('id-ID')}</td></tr>
                                                <tr><td>Status</td><td>: <span class="badge bg-${p.pharmacy_status === 'Pending' ? 'warning text-dark' : p.pharmacy_status === 'Verified' ? 'info' : p.pharmacy_status === 'Ready' ? 'primary' : 'success'}">${p.pharmacy_status}</span></td></tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    ${p.notes ? `<div class="alert alert-info"><i class="ri-information-line me-2"></i><strong>Catatan Dokter:</strong><br>${p.notes}</div>` : ''}

                                    <h6 class="fw-bold mb-3"><i class="ri-medicine-bottle-line text-primary me-2"></i>Daftar Obat</h6>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover">
                                            <thead class="table-primary">
                                                <tr>
                                                    <th width="30">No</th>
                                                    <th>Nama Obat</th>
                                                    <th width="100">Dosis</th>
                                                    <th width="100">Rute</th>
                                                    <th width="120">Frekuensi</th>
                                                    <th width="80">Durasi</th>
                                                    <th>Jadwal</th>
                                                    <th width="150">Instruksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                        `;

                        p.medications.forEach((med, idx) => {
                            const schedules = Array.isArray(med.scheduled_times) ? med.scheduled_times.join(
                                ', ') : '-';
                            html += `
                                <tr>
                                    <td class="text-center">${idx + 1}</td>
                                    <td><strong><i class="ri-capsule-line text-info me-1"></i>${med.medication_name}</strong></td>
                                    <td><span class="badge bg-light text-dark">${med.dosage}</span></td>
                                    <td><span class="badge bg-info">${med.route}</span></td>
                                    <td>${med.frequency}</td>
                                    <td>${med.duration_days} hari</td>
                                    <td><small class="text-muted">${schedules}</small></td>
                                    <td><small>${med.instructions || '-'}</small></td>
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

                        document.getElementById('detailContent').innerHTML = html;
                    } else {
                        document.getElementById('detailContent').innerHTML = `
                            <div class="alert alert-danger">
                                <i class="ri-error-warning-line me-2"></i>Gagal memuat detail resep
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('detailContent').innerHTML = `
                        <div class="alert alert-danger">
                            <i class="ri-error-warning-line me-2"></i>Terjadi kesalahan saat memuat data
                        </div>
                    `;
                });
        }

        function updateStatus(id, currentStatus) {
            document.getElementById('prescription_id').value = id;
            document.getElementById('pharmacy_notes').value = '';

            const statusSelect = document.getElementById('pharmacy_status');
            statusSelect.innerHTML = '<option value="">Pilih Status</option>';

            // Set available next statuses
            if (currentStatus === 'Pending') {
                statusSelect.innerHTML += '<option value="Verified" selected>Diverifikasi</option>';
                statusSelect.innerHTML += '<option value="Ready">Siap Diambil</option>';
                statusSelect.innerHTML += '<option value="Dispensed">Diserahkan</option>';
            } else if (currentStatus === 'Verified') {
                statusSelect.innerHTML += '<option value="Ready" selected>Siap Diambil</option>';
                statusSelect.innerHTML += '<option value="Dispensed">Diserahkan</option>';
            } else if (currentStatus === 'Ready') {
                statusSelect.innerHTML += '<option value="Dispensed" selected>Diserahkan</option>';
            }

            const modal = new bootstrap.Modal(document.getElementById('modalUpdateStatus'), {
                backdrop: false
            });
            modal.show();
        }

        document.getElementById('formUpdateStatus').addEventListener('submit', function(e) {
            e.preventDefault();

            const id = document.getElementById('prescription_id').value;
            const status = document.getElementById('pharmacy_status').value;
            const notes = document.getElementById('pharmacy_notes').value;

            if (!status) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian',
                    text: 'Silakan pilih status terlebih dahulu'
                });
                return;
            }

            Swal.fire({
                title: 'Memproses...',
                text: 'Sedang mengupdate status resep',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch(`/apotek/permintaan-inap/update-status/${id}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        pharmacy_status: status,
                        notes: notes
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Status resep berhasil diupdate',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: data.message || 'Gagal mengupdate status'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Terjadi kesalahan saat mengupdate status'
                    });
                });
        });
    </script>
@endpush
