@extends('layouts.app')
@section('title')
    Manajemen Insentif Manual
@endsection
@push('style')
    <!-- Data Tables -->
    <link rel="stylesheet" href="{{ asset('vendor/datatables/dataTables.bs5.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/datatables/dataTables.bs5-custom.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/datatables/buttons/dataTables.bs5-custom.css') }}">
    <!-- Scrollbar CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Data Insentif Manual</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                        <a href="{{ route('keuangan.insentif.create') }}" class="btn btn-primary">
                            <i class="ri-add-line"></i> Tambah Insentif
                        </a>
                        <form action="{{ route('keuangan.insentif.index') }}" method="GET" class="d-flex flex-wrap gap-2">
                            <div class="d-flex align-items-center">
                                <select name="month" id="month" class="form-select form-select-sm">
                                    @for ($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}" {{ $m == $month ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="d-flex align-items-center">
                                <select name="year" id="year" class="form-select form-select-sm">
                                    @for ($y = now()->year; $y >= now()->year - 5; $y--)
                                        <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>
                                            {{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                            <button type="submit" class="btn btn-info btn-sm">
                                <i class="ri-filter-3-line"></i> Filter
                            </button>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table id="datatable-button" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Nama Karyawan</th>
                                    <th>Deskripsi</th>
                                    <th class="text-end">Jumlah (Rp)</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($incentives as $incentive)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ \Carbon\Carbon::parse($incentive->created_at)->translatedFormat('d F Y') }}
                                        </td>
                                        <td>{{ $incentive->user->name ?? 'N/A' }}</td>
                                        <td>{{ $incentive->description }}</td>
                                        <td class="text-end">{{ formatPrice($incentive->amount) }}</td>
                                        <td>
                                            <div class="d-inline-flex gap-1">
                                                <a href="{{ route('keuangan.insentif.edit', $incentive->id) }}"
                                                    class="btn btn-warning btn-sm">
                                                    <i class="ri-edit-line"></i>
                                                </a>
                                                <form action="{{ route('keuangan.insentif.destroy', $incentive->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm btn-delete">
                                                        <i class="ri-delete-bin-line"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Tidak ada data insentif manual.</td>
                                    </tr>
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
    <!-- Data Tables -->
    <script src="{{ asset('vendor/datatables/dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/buttons/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/buttons/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/buttons/buttons.print.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/buttons/buttons.colVis.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/buttons/jszip.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/buttons/pdfmake.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/buttons/vfs_fonts.js') }}"></script>
    <script src="{{ asset('vendor/datatables/custom/custom-datatables.js') }}"></script>
    <!-- Overlay Scroll JS -->
    <script src="{{ asset('vendor/overlay-scroll/jquery.overlayScrollbars.min.js') }}"></script>
    <script src="{{ asset('vendor/overlay-scroll/custom-scrollbar.js') }}"></script>
    <!-- Custom JS files -->
    <script src="{{ asset('js/custom.js') }}"></script>
@endpush
