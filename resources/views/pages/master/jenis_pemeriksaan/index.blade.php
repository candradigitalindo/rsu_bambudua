@extends('layouts.app')

@section('title', 'Master Jenis Pemeriksaan Penunjang')

@push('style')
    <!-- Scrollbar CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Daftar Jenis Pemeriksaan Penunjang</h4>
                    <a href="{{ route('jenis-pemeriksaan.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i>
                        Tambah Data</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Pemeriksaan</th>
                                    <th>Tipe</th>
                                    <th>Harga</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($jenisPemeriksaan as $key => $item)
                                    <tr>
                                        <td>{{ $jenisPemeriksaan->firstItem() + $key }}</td>
                                        <td>{{ $item->name }}</td>
                                        <td>
                                            <span
                                                class="badge bg-{{ $item->type == 'lab' ? 'info' : 'primary' }}">{{ ucfirst($item->type) }}</span>
                                        </td>
                                        <td>Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('jenis-pemeriksaan.fields.index', $item->id) }}"
                                                class="btn btn-info btn-sm">
                                                <i class="bi bi-tools"></i> Atur Kolom
                                            </a>
                                            <a href="{{ route('jenis-pemeriksaan.edit', $item->id) }}"
                                                class="btn btn-warning btn-sm">
                                                <i class="bi bi-pencil-square"></i> Edit
                                            </a>
                                            <form action="{{ route('jenis-pemeriksaan.destroy', $item->id) }}"
                                                method="POST" class="d-inline" data-confirm-delete="true">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="bi bi-trash"></i> Hapus
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Tidak ada data</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $jenisPemeriksaan->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Overlay Scroll JS -->
    <script src="{{ asset('vendor/overlay-scroll/jquery.overlayScrollbars.min.js') }}"></script>
    <script src="{{ asset('vendor/overlay-scroll/custom-scrollbar.js') }}"></script>
    <script src="{{ asset('js/custom.js') }}"></script>
@endpush
