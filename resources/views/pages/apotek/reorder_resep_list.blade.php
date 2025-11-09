@extends('layouts.app')

@section('title', 'Siapkan Ulang Resep')

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
@endpush

@section('content')
    <!-- Row starts -->
    <div class="row gx-3">
        <div class="col-xxl-12 col-sm-12">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title">Daftar Resep Siap untuk Diajukan Ulang</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        Halaman ini berisi daftar resep yang sudah selesai disiapkan. Klik "Siapkan Ulang" untuk
                        mengembalikan status resep menjadi "Menunggu Penyiapan".
                        <strong>Perhatian:</strong> Tindakan ini tidak akan mengembalikan stok obat yang sudah dikurangi.
                    </div>
                    <form method="GET" class="mb-3">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control"
                                placeholder="Cari berdasarkan No. Resep atau Nama Pasien..."
                                value="{{ request('search') }}">
                            <button class="btn btn-primary" type="submit">Cari</button>
                        </div>
                    </form>

                    <hr>

                    <div class="table-responsive">
                        <table class="table truncate m-0">
                            <thead>
                                <tr>
                                    <th class="text-center">No</th>
                                    <th>No. Resep</th>
                                    <th>No. RM</th>
                                    <th>Nama Pasien</th>
                                    <th>Asal Resep</th>
                                    <th>Tanggal Resep</th>
                                    <th class="text-center">Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($reseps as $resep)
                                    <tr>
                                        <td class="text-center">
                                            {{ $loop->iteration + ($reseps->firstItem() ? $reseps->firstItem() - 1 : 0) }}
                                        </td>
                                        <td>{{ $resep->kode_resep }}</td>
                                        <td>{{ $resep->encounter?->rekam_medis ?? 'N/A' }}</td>
                                        <td>{{ $resep->encounter?->name_pasien ?? 'N/A' }}</td>
                                        <td>{{ $resep->encounter?->clinic?->name ?? 'Resep Pulang' }}</td>
                                        <td>{{ \Carbon\Carbon::parse($resep->created_at)->format('d-m-Y H:i') }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary">{{ $resep->status }}</span>
                                        </td>
                                        <td>
                                            <form action="{{ route('apotek.penyiapan-resep.reorder.action', $resep->id) }}"
                                                method="POST"
                                                onsubmit="return confirm('Anda yakin ingin mengajukan ulang resep ini?');">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-warning">
                                                    <i class="ri-refresh-line"></i> Siapkan Ulang
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">Tidak ada resep yang bisa diajukan ulang.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{-- Paginate --}}
                    <div class="mt-3">
                        {{ $reseps->appends(request()->query())->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('vendor/overlay-scroll/jquery.overlayScrollbars.min.js') }}"></script>
    <script src="{{ asset('assets/js/custom-scrollbar.js') }}"></script>
    <script src="{{ asset('js/custom.js') }}"></script>
@endpush
