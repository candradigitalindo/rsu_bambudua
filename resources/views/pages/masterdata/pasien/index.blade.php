@extends('layouts.app')
@section('title', 'Data Pasien')
@push('style')
    <style>
        a.disabled {
            color: gray;
            pointer-events: none;
        }
    </style>
@endpush

@section('content')
    <div class="row gx-3">
        <div class="col-xxl-12 col-sm-12">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title">Data Pasien</h5>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="card-info rounded-1 small lh-1">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="ms-auto d-flex gap-2">
                                <div class="search-container d-xl-block d-none">
                                    <form method="GET" action="{{ route('master.pasien.index') }}">
                                        <input type="text" class="form-control" name="q" id="searchPasien"
                                            placeholder="Cari nama / RM / No HP..." value="{{ request('q') }}">
                                        <i class="ri-search-line"></i>
                                    </form>
                                </div>
                                <div class="ms-2">
                                    <a href="{{ route('master.pasien.create') }}" class="btn btn-outline-primary">
                                        <i class="ri-user-add-line"></i>
                                        Tambah Pasien
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-outer">
                        <div class="table-responsive">
                            <table class="table truncate m-0">
                                <thead>
                                    <tr>
                                        <th>No. RM</th>
                                        <th>Nama Pasien</th>
                                        <th>Jenis Kelamin</th>
                                        <th>Tgl Lahir / Umur</th>
                                        <th>No HP</th>
                                        <th>Alamat</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($pasiens as $pasien)
                                        <tr>
                                            <td>
                                                <span class="badge bg-primary">{{ $pasien->rekam_medis }}</span>
                                            </td>
                                            <td>
                                                <span class="fw-bold">{{ $pasien->name }}</span>
                                                @if ($pasien->is_kerabat_dokter)
                                                    <i class="ri-user-heart-line text-primary" title="Kerabat Dokter"></i>
                                                @endif
                                                @if ($pasien->is_kerabat_karyawan)
                                                    <i class="ri-team-line text-success" title="Kerabat Karyawan"></i>
                                                @endif
                                                @if ($pasien->is_kerabat_owner)
                                                    <i class="ri-vip-crown-line text-warning" title="Kerabat Owner"></i>
                                                @endif
                                            </td>
                                            <td>{{ $pasien->jenis_kelamin == 1 ? 'Laki-laki' : 'Perempuan' }}</td>
                                            <td>
                                                {{ $pasien->tgl_lahir ? $pasien->tgl_lahir->format('d M Y') : '-' }}
                                                @if ($pasien->tgl_lahir)
                                                    <br><small class="text-muted">{{ \Carbon\Carbon::parse($pasien->tgl_lahir)->age }} tahun</small>
                                                @endif
                                            </td>
                                            <td>{{ $pasien->no_hp ?: '-' }}</td>
                                            <td>
                                                <small>{{ \Illuminate\Support\Str::limit($pasien->alamat, 40) ?: '-' }}</small>
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('master.pasien.edit', $pasien->id) }}"
                                                    class="btn btn-primary btn-sm">
                                                    <i class="ri-edit-2-line"></i> Edit
                                                </a>
                                                <form action="{{ route('master.pasien.destroy', $pasien->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm"
                                                        onclick="return confirm('Apakah anda yakin ingin menghapus data pasien {{ $pasien->name }}?')">
                                                        <i class="ri-delete-bin-5-line"></i> Hapus
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">Data pasien tidak ditemukan</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="mt-3">
                        {{ $pasiens->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
@endpush
