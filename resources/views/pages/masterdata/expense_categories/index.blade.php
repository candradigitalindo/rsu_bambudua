@extends('layouts.app')
@section('title','Master Kategori Pengeluaran')
@push('style')
  <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
  <link rel="stylesheet" href="{{ asset('vendor/datatables/dataTables.bs5.css') }}">
@endpush
@section('content')
<div class="row gx-3">
  <div class="col-12">
    <div class="card mb-3">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Kategori Pengeluaran</h5>
      </div>
      <div class="card-body">
        @if (session('success'))
          <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <div class="mb-3">
          <a href="{{ route('master.expense-categories.create') }}" class="btn btn-primary">Tambah Kategori Pengeluaran</a>
        </div>
        <div class="table-responsive">
          <table class="table m-0" id="ecTable">
            <thead><tr><th>Nama</th><th>Kode</th><th>Deskripsi</th><th>Aktif</th><th class="text-end">Aksi</th></tr></thead>
            <tbody>
              @foreach($items as $item)
                <tr>
                  <td>{{ $item->name }}</td>
                  <td>{{ $item->code }}</td>
                  <td>{{ $item->description }}</td>
                  <td>{!! $item->is_active ? '<span class="badge bg-success">Ya</span>' : '<span class="badge bg-secondary">Tidak</span>' !!}</td>
                  <td class="text-end">
                    <a href="{{ route('master.expense-categories.edit', $item) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                    <form method="POST" action="{{ route('master.expense-categories.destroy', $item) }}" class="d-inline" onsubmit="return confirm('Hapus kategori?')">
                      @csrf @method('DELETE')
                      <button class="btn btn-sm btn-danger" type="submit">Hapus</button>
                    </form>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        <div class="mt-3">{{ $items->links() }}</div>
      </div>
    </div>
  </div>
</div>
@endsection
@push('scripts')
  <script src="{{ asset('vendor/overlay-scroll/jquery.overlayScrollbars.min.js') }}"></script>
  <script src="{{ asset('vendor/overlay-scroll/custom-scrollbar.js') }}"></script>
  <script src="{{ asset('vendor/datatables/dataTables.min.js') }}"></script>
  <script src="{{ asset('vendor/datatables/dataTables.bootstrap.min.js') }}"></script>
  <script src="{{ asset('js/custom.js') }}"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function(){
      if (window.jQuery && jQuery.fn.DataTable) {
        jQuery('#ecTable').DataTable({ paging: false, searching: true });
      }
    });
  </script>
@endpush
