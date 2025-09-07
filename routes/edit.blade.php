@extends('layouts.app')
@section('title')
    Ubah Pendapatan Lainnya
@endsection

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Form Ubah Pendapatan Lainnya</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('pendapatan-lain.update', $income->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="income_date" class="form-label">Tanggal Pendapatan</label>
                            <input type="date" class="form-control @error('income_date') is-invalid @enderror"
                                id="income_date" name="income_date"
                                value="{{ old('income_date', $income->income_date->format('Y-m-d')) }}" required>
                            @error('income_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Deskripsi</label>
                            <input type="text" class="form-control @error('description') is-invalid @enderror"
                                id="description" name="description" value="{{ old('description', $income->description) }}"
                                placeholder="Contoh: Sewa alat medis" required>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="amount" class="form-label">Jumlah (Rp)</label>
                            <input type="number" class="form-control @error('amount') is-invalid @enderror" id="amount"
                                name="amount" value="{{ old('amount', $income->amount) }}" placeholder="Contoh: 1000000"
                                required>
                            @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('pendapatan-lain.index') }}" class="btn btn-secondary me-2">Batal</a>
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
