@extends('layouts.app')
@section('title','Reagensia Laboratorium')
@push('style')
<link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
@endpush
@section('content')
<div class="row gx-3">
  <div class="col-12">
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    <div class="card mb-3">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Reagensia</h5>
        <a href="{{ route('lab.reagents.create') }}" class="btn btn-primary btn-sm"><i class="ri-add-line"></i> Tambah</a>
      </div>
      <div class="card-body">
        <form method="GET" class="row g-2 mb-3">
          <div class="col-md-4"><input type="text" name="q" class="form-control" placeholder="Cari reagensia..." value="{{ $q ?? '' }}"></div>
          <div class="col-md-2"><button class="btn btn-outline-primary">Cari</button></div>
        </form>
        <div class="table-responsive">
          <table class="table table-sm">
            <thead><tr><th>Nama</th><th>Satuan</th><th>Stok</th><th>Warning</th><th class="text-end">Aksi</th></tr></thead>
            <tbody>
              @forelse($reagents as $r)
              <tr>
                <td>{{ $r->name }}</td>
                <td>{{ $r->unit ?? '-' }}</td>
                <td><span class="badge {{ $r->stock <= $r->warning_stock ? 'bg-danger':'bg-success' }}">{{ $r->stock }}</span></td>
                <td>{{ $r->warning_stock }}</td>
                <td class="text-end">
                  <a class="btn btn-sm btn-outline-primary" href="{{ route('lab.reagents.edit', $r->id) }}">Edit</a>
                  <form action="{{ route('lab.reagents.stock', $r->id) }}" method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" name="type" value="in" />
                    <input type="hidden" name="qty" value="1" />
                    <button class="btn btn-sm btn-outline-success" title="Tambah 1">+1</button>
                  </form>
                  <form action="{{ route('lab.reagents.stock', $r->id) }}" method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" name="type" value="out" />
                    <input type="hidden" name="qty" value="1" />
                    <button class="btn btn-sm btn-outline-danger" title="Kurangi 1">-1</button>
                  </form>
                </td>
              </tr>
              @empty
              <tr><td colspan="5" class="text-center text-muted">Belum ada data</td></tr>
              @endforelse
            </tbody>
          </table>
          {{ $reagents->onEachSide(1)->links() }}
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
@push('scripts')
<script src="{{ asset('vendor/overlay-scroll/jquery.overlayScrollbars.min.js') }}"></script>
<script src="{{ asset('vendor/overlay-scroll/custom-scrollbar.js') }}"></script>
<script src="{{ asset('js/custom.js') }}"></script>
@endpush
