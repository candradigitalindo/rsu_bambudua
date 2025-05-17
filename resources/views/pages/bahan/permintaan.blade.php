@extends('layouts.app')
@section('title', 'Permintaan Bahan')
@push('style')
    <!-- Scrollbar CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
    <style>
        a.disabled {
            /* Make the disabled links grayish*/
            color: gray;
            /* And disable the pointer events */
            pointer-events: none;
        }
    </style>
@endpush
@section('content')
    <div class="row gx-3">
        <div class="col-xxl-12 col-sm-12">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title">Permintaan Bahan dari Tindakan Medis</h5>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    <div class="card-info rounded-1 small lh-1">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="ms-auto d-flex gap-2">
                                <!-- Search Patient Ends -->
                                <div class="ms-2">
                                    <a href="{{ route('bahans.index') }}" class="btn btn-outline-primary" id="permintaanTindakan">
                                        <i class="ri-calendar-todo-fill"></i>
                                        <span class="btn-text" id="textPermintaanTindakan">Kembali</span>
                                        <span class="spinner-border spinner-border-sm d-none"
                                            id="spinerPermintaanTindakan"></span>
                                    </a>
                                </div>

                            </div>
                        </div>

                    </div>
                    <div class="table-outer">
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle" id="permintaan">
                                <thead class="table-light">
                                    <tr>
                                        <th>Pasien</th>
                                        <th>Nama Bahan</th>
                                        <th>Jumlah</th>
                                        <th>Tanggal Expired</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($requestBahans as $permintaan)
                                        @if($permintaan->requestBahan->count())
                                            @foreach ($permintaan->requestBahan as $bahan)
                                                <tr>
                                                    @if($loop->first)
                                                        <td rowspan="{{ $permintaan->requestBahan->count() }}" class="align-middle">
                                                            {{ ucwords($permintaan->name_pasien) }}
                                                        </td>
                                                    @endif
                                                    <td>{{ $bahan->nama_bahan }}</td>
                                                    <td>{{ $bahan->qty }}</td>
                                                    <td>{{ $bahan->expired_at ? \Carbon\Carbon::parse($bahan->expired_at)->format('d-m-Y') : '-' }}</td>
                                                    <td>
                                                        @if($bahan->status == 1)
                                                            <span class="badge bg-success">Sudah Diserahkan</span>
                                                        @else
                                                            <button type="button"
                                                                class="btn btn-warning btn-sm btn-serahkan"
                                                                data-bahan-id="{{ $bahan->id }}"
                                                                data-nama-bahan="{{ $bahan->nama_bahan }}"
                                                                data-max-qty="{{ $bahan->qty }}">
                                                                Diserahkan
                                                            </button>

                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td>{{ ucwords($permintaan->name_pasien) }}</td>
                                                <td colspan="3" class="text-center">Tidak ada data bahan</td>
                                            </tr>
                                        @endif
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">Data tidak ada</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>

    <!-- Modal Serahkan Bahan -->
    <div class="modal fade" id="modalSerahkanBahan" tabindex="-1" aria-labelledby="modalSerahkanBahanLabel" aria-hidden="true">
      <div class="modal-dialog">
        <form id="formSerahkanBahan" method="POST">
          @csrf
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="modalSerahkanBahanLabel">Serahkan Bahan</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <input type="hidden" name="bahan_id" id="modal_bahan_id">
              <div class="mb-3">
                <label for="modal_nama_bahan" class="form-label">Nama Bahan</label>
                <input type="text" class="form-control" id="modal_nama_bahan" readonly>
              </div>
              <div class="mb-3">
                <label for="modal_jumlah" class="form-label">Jumlah Diserahkan</label>
                <input type="number" class="form-control" name="jumlah" id="modal_jumlah" min="1" required>
              </div>
              <div class="mb-3">
                <label for="modal_kepada" class="form-label">Kepada</label>
                <input type="text" class="form-control" name="kepada" id="modal_kepada" required>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
              <button type="submit" class="btn btn-success">Serahkan</button>
            </div>
          </div>
        </form>
      </div>
    </div>
@endsection
@push('scripts')
    <!-- Overlay Scroll JS -->
    <script src="{{ asset('vendor/overlay-scroll/jquery.overlayScrollbars.min.js') }}"></script>
    <script src="{{ asset('vendor/overlay-scroll/custom-scrollbar.js') }}"></script>
    <!-- Custom JS files -->
    <script src="{{ asset('js/custom.js') }}"></script>
    <script>
    $(document).on('click', '.btn-serahkan', function() {
        let bahanId = $(this).data('bahan-id');
        let namaBahan = $(this).data('nama-bahan');
        let maxQty = $(this).data('max-qty');

        $('#modal_bahan_id').val(bahanId);
        $('#modal_nama_bahan').val(namaBahan);
        $('#modal_jumlah').val(maxQty).attr('max', maxQty);

        // Unbind event submit sebelum bind baru
        $('#formSerahkanBahan').off('submit').on('submit', function(e) {
            e.preventDefault();
            let formData = $(this).serialize();
            $.ajax({
                url: '/bahans/diserahkan/' + bahanId,
                type: 'POST',
                data: formData,
                success: function(response) {
                    $('#modalSerahkanBahan').modal('hide');
                    location.reload();
                },
                error: function(xhr) {
                    alert('Terjadi kesalahan. Silakan coba lagi.');
                }
            });
        });

        $('#modalSerahkanBahan').modal('show');
    });
    </script>
@endpush
