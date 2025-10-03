@extends('layouts.app')
@section('title', 'Edit Supplier')
@push('style')
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
@endpush
@section('content')
    <div class="row gx-3">
        <div class="col-12 col-lg-10">
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Edit Supplier</h5>
                    <a href="{{ route('suppliers.index') }}" class="btn btn-sm btn-secondary">Kembali</a>
                </div>
                <div class="card-body">
                    <form action="{{ route('suppliers.update', $supplier->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label">Nama</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    name="name" value="{{ old('name', $supplier->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Contact Person</label>
                                <input type="text" class="form-control @error('contact') is-invalid @enderror"
                                    name="contact" value="{{ old('contact', $supplier->contact) }}">
                                @error('contact')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Telepon</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                    name="phone" value="{{ old('phone', $supplier->phone) }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    name="email" value="{{ old('email', $supplier->email) }}">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="active" id="active"
                                        {{ old('active', $supplier->active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="active">Aktif</label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Alamat</label>
                                <input type="text" class="form-control @error('address') is-invalid @enderror"
                                    name="address" value="{{ old('address', $supplier->address) }}">
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">NPWP</label>
                                <input type="text" class="form-control @error('npwp') is-invalid @enderror"
                                    name="npwp" value="{{ old('npwp', $supplier->npwp) }}">
                                @error('npwp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Payment Terms</label>
                                <input type="text" class="form-control @error('payment_terms') is-invalid @enderror"
                                    name="payment_terms" value="{{ old('payment_terms', $supplier->payment_terms) }}">
                                @error('payment_terms')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="d-flex justify-content-end gap-2 mt-3">
                            <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">Batal</a>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
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
