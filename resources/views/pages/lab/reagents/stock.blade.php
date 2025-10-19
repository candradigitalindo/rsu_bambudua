@extends('layouts.app')

@section('title', 'Manajemen Stok Reagen')

@section('content')
    <div class="row">
        <div class="col-12 col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Manajemen Stok: {{ $reagent->name }}</h5>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $e)
                                    <li>{{ $e }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('lab.reagents.stock.store', $reagent->id) }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Tipe Transaksi</label>
                            <select name="type" class="form-select" required id="type">
                                <option value="">-- Pilih Tipe --</option>
                                <option value="in" @selected(old('type') === 'in')>Stok Masuk</option>
                                <option value="out" @selected(old('type') === 'out')>Stok Keluar (Pemakaian)</option>
                                <option value="adjustment" @selected(old('type') === 'adjustment')>Stok Keluar (Penyesuaian/Rusak)
                                </option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Jumlah</label>
                            <input type="number" name="quantity" class="form-control" value="{{ old('quantity') }}"
                                required min="1">
                        </div>

                        <div class="mb-3" id="expiry_date_group">
                            <label class="form-label">Tanggal Kedaluwarsa</label>
                            <input type="date" name="expiry_date" class="form-control" value="{{ old('expiry_date') }}">
                            <div class="form-text">Hanya wajib diisi untuk Stok Masuk.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Keterangan</label>
                            <textarea name="notes" class="form-control" rows="3"
                                placeholder="Contoh: Pembelian dari Supplier X, Pemakaian untuk tes, dll.">{{ old('notes') }}</textarea>
                        </div>

                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('lab.reagents.index') }}" class="btn btn-light">Batal</a>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const typeSelect = document.getElementById('type');
            const expiryDateGroup = document.getElementById('expiry_date_group');
            const expiryDateInput = expiryDateGroup.querySelector('input');

            function toggleExpiryDate() {
                if (typeSelect.value === 'in') {
                    expiryDateGroup.style.display = 'block';
                    expiryDateInput.required = true;
                } else {
                    expiryDateGroup.style.display = 'none';
                    expiryDateInput.required = false;
                }
            }

            typeSelect.addEventListener('change', toggleExpiryDate);

            // Initial check on page load
            toggleExpiryDate();
        });
    </script>
@endpush
