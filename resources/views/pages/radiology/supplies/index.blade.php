@extends('layouts.app')

@section('title', 'Kelola Bahan Radiologi')

@push('style')
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
@endpush

@section('content')
    <div class="container-fluid px-4 py-4">
        <!-- Header Card -->
        <div class="page-header-card mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1 fw-bold">
                        <i class="ri-medicine-bottle-line me-2"></i>
                        Kelola Bahan Radiologi
                    </h4>
                    <p class="text-muted mb-0">Manajemen stok bahan dan supplies radiologi</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('radiologi.supplies.history') }}" class="btn btn-outline-primary">
                        <i class="ri-history-line me-1"></i> Histori Transaksi
                    </a>
                    <a href="{{ route('radiologi.supplies.create') }}" class="btn btn-header-primary">
                        <i class="ri-add-line me-1"></i> Tambah Bahan
                    </a>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="ri-check-line me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Filter Alert -->
        @if (request('filter') === 'habis')
            <div class="alert alert-danger d-flex align-items-center justify-content-between" role="alert">
                <div>
                    <i class="ri-error-warning-line me-2"></i>
                    <strong>Filter Aktif:</strong> Menampilkan bahan dengan stok habis
                </div>
                <a href="{{ route('radiologi.supplies.index') }}" class="btn btn-sm btn-outline-danger">
                    <i class="ri-close-line"></i> Reset Filter
                </a>
            </div>
        @elseif(request('filter') === 'kadaluarsa')
            <div class="alert alert-warning d-flex align-items-center justify-content-between" role="alert">
                <div>
                    <i class="ri-error-warning-line me-2"></i>
                    <strong>Filter Aktif:</strong> Menampilkan bahan dengan batch kadaluarsa
                </div>
                <a href="{{ route('radiologi.supplies.index') }}" class="btn btn-sm btn-outline-warning">
                    <i class="ri-close-line"></i> Reset Filter
                </a>
            </div>
        @endif

        <!-- Filter & Search Card -->
        <div class="filter-card mb-4">
            <form method="GET" action="{{ route('radiologi.supplies.index') }}" class="row g-3">
                <div class="col-md-5">
                    <div class="position-relative">
                        <i class="ri-search-line position-absolute top-50 translate-middle-y ms-3 text-muted"></i>
                        <input type="text" class="form-control ps-5" name="search"
                            placeholder="Cari nama bahan atau satuan..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <select class="form-select" name="filter">
                        <option value="">Semua Status</option>
                        <option value="habis" {{ request('filter') === 'habis' ? 'selected' : '' }}>
                            🔴 Stok Habis
                        </option>
                        <option value="kadaluarsa" {{ request('filter') === 'kadaluarsa' ? 'selected' : '' }}>
                            ⚠️ Kadaluarsa
                        </option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-header-primary w-100">
                        <i class="ri-filter-3-line me-1"></i> Terapkan Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Table Card -->
        <div class="card shadow-sm border-0">
            <div class="table-responsive">
                <table class="table table-modern mb-0">
                    <thead>
                        <tr>
                            <th style="width: 5%;">No</th>
                            <th style="width: 25%;">Nama Bahan</th>
                            <th style="width: 10%;">Satuan</th>
                            <th style="width: 12%;">Stok</th>
                            <th style="width: 12%;">Peringatan</th>
                            <th style="width: 15%;">Status</th>
                            <th style="width: 21%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($supplies as $index => $supply)
                            <tr>
                                <td>{{ $supplies->firstItem() + $index }}</td>
                                <td>
                                    <span class="fw-semibold">{{ $supply->name }}</span>
                                    @if ($supply->hasExpiredBatches())
                                        <span class="badge bg-warning ms-2">
                                            <i class="ri-error-warning-line"></i> Batch Kadaluarsa
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-success">{{ $supply->unit ?: '-' }}</span>
                                </td>
                                <td>
                                    <span
                                        class="fw-bold {{ $supply->stock <= 0 ? 'text-danger' : ($supply->isLowStock() ? 'text-warning' : 'text-success') }}">
                                        {{ number_format($supply->stock) }}
                                    </span>
                                </td>
                                <td>{{ number_format($supply->warning_stock) }}</td>
                                <td>
                                    @if ($supply->stock <= 0)
                                        <span class="badge bg-danger">
                                            <i class="ri-close-circle-line"></i> Habis
                                        </span>
                                    @elseif($supply->isLowStock())
                                        <span class="badge bg-warning">
                                            <i class="ri-error-warning-line"></i> Stok Rendah
                                        </span>
                                    @else
                                        <span class="badge bg-success">
                                            <i class="ri-checkbox-circle-line"></i> Tersedia
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <button class="btn btn-sm btn-success"
                                            onclick="showStockModal('{{ $supply->id }}', '{{ $supply->name }}', 'in')"
                                            title="Tambah Stok">
                                            <i class="ri-add-line"></i>
                                        </button>
                                        <button class="btn btn-sm btn-warning"
                                            onclick="showStockModal('{{ $supply->id }}', '{{ $supply->name }}', 'out')"
                                            title="Kurang Stok">
                                            <i class="ri-subtract-line"></i>
                                        </button>
                                        <a href="{{ route('radiologi.supplies.edit', $supply) }}"
                                            class="btn btn-sm btn-primary" title="Edit">
                                            <i class="ri-edit-line"></i>
                                        </a>
                                        <form action="{{ route('radiologi.supplies.destroy', $supply) }}" method="POST"
                                            class="d-inline" onsubmit="return confirm('Yakin ingin menghapus bahan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="ri-medicine-bottle-line fs-1 text-muted mb-3 d-block"></i>
                                    <p class="text-muted mb-0">Belum ada data bahan radiologi</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($supplies->hasPages())
                <div class="card-footer bg-white">
                    {{ $supplies->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Modal Tambah/Kurang Stok -->
    <div class="modal fade" id="stockModal" tabindex="-1" data-bs-backdrop="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header">
                    <h5 class="modal-title" id="stockModalTitle">Kelola Stok</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="stockForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" id="stock_type" name="type">

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Bahan</label>
                            <input type="text" class="form-control" id="supply_name" readonly>
                        </div>

                        <div class="mb-3">
                            <label for="batch_number" class="form-label fw-semibold">
                                Nomor Batch <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="batch_number" name="batch_number" required>
                        </div>

                        <div class="mb-3">
                            <label for="quantity" class="form-label fw-semibold">
                                Jumlah <span class="text-danger">*</span>
                            </label>
                            <input type="number" class="form-control" id="quantity" name="quantity" min="1"
                                required>
                        </div>

                        <div class="mb-3" id="expiry_field">
                            <label for="expiry_date" class="form-label fw-semibold">Tanggal Kadaluarsa</label>
                            <input type="date" class="form-control" id="expiry_date" name="expiry_date">
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label fw-semibold">Catatan</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="submitBtn">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .page-header-card {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
            border-left: 5px solid #10b981;
        }

        .filter-card {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
        }

        .btn-header-primary {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            border: none;
            padding: 0.5rem 1.25rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-header-primary:hover {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .table-modern {
            border-collapse: separate;
            border-spacing: 0;
        }

        .table-modern thead {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }

        .table-modern thead th {
            border: none;
            padding: 1rem;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
        }

        .table-modern thead th:first-child {
            border-top-left-radius: 12px;
        }

        .table-modern thead th:last-child {
            border-top-right-radius: 12px;
        }

        .table-modern tbody td {
            padding: 1rem;
            vertical-align: middle;
            border-bottom: 1px solid #f3f4f6;
        }

        .table-modern tbody tr:last-child td {
            border-bottom: none;
        }

        .table-modern tbody tr {
            transition: background-color 0.2s ease;
        }

        .table-modern tbody tr:hover {
            background-color: #f0fdf4;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #10b981;
            box-shadow: 0 0 0 0.2rem rgba(16, 185, 129, 0.15);
        }
    </style>
@endsection

@push('scripts')
    <script src="{{ asset('vendor/overlay-scroll/jquery.overlayScrollbars.min.js') }}"></script>
    <script src="{{ asset('vendor/overlay-scroll/custom-scrollbar.js') }}"></script>
    <script src="{{ asset('js/custom.js') }}"></script>
    <script>
        function showStockModal(supplyId, supplyName, type) {
            const modal = new bootstrap.Modal(document.getElementById('stockModal'));
            const form = document.getElementById('stockForm');
            const title = document.getElementById('stockModalTitle');
            const submitBtn = document.getElementById('submitBtn');
            const expiryField = document.getElementById('expiry_field');

            // Set form action
            form.action = `/radiologi/supplies/${supplyId}/stock`;

            // Set supply name
            document.getElementById('supply_name').value = supplyName;
            document.getElementById('stock_type').value = type;

            // Reset form
            form.reset();
            document.getElementById('supply_name').value = supplyName;
            document.getElementById('stock_type').value = type;

            // Configure based on type
            if (type === 'in') {
                title.textContent = '➕ Tambah Stok - ' + supplyName;
                submitBtn.className = 'btn btn-success';
                submitBtn.innerHTML = '<i class="ri-add-line me-1"></i> Tambah Stok';
                expiryField.style.display = 'block';
                document.getElementById('batch_number').removeAttribute('readonly');
            } else {
                title.textContent = '➖ Kurang Stok - ' + supplyName;
                submitBtn.className = 'btn btn-warning';
                submitBtn.innerHTML = '<i class="ri-subtract-line me-1"></i> Kurang Stok';
                expiryField.style.display = 'none';
            }

            modal.show();
        }

        // Handle form submission
        document.getElementById('stockForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const submitBtn = document.getElementById('submitBtn');
            const originalText = submitBtn.innerHTML;

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Proses...';

            fetch(this.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                        'Accept': 'application/json',
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert(data.message || 'Terjadi kesalahan');
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    }
                })
                .catch(error => {
                    alert('Terjadi kesalahan saat memproses data');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                });
        });
    </script>
@endpush
