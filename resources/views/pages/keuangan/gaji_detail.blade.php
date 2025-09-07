@extends('layouts.app')

@section('title')
    Detail Gaji - {{ $employee->name }}
@endsection

@push('style')
    <!-- Scrollbar CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
@endpush

@section('content')
    <div class="d-flex align-items-center mb-3">
        <a href="{{ route('keuangan.gaji') }}" class="btn btn-primary me-2">
            <i class="ri-arrow-left-s-line"></i>
        </a>
        <div>
            <h4 class="mb-0">Detail Gaji & Insentif</h4>
            <p class="mb-0">Periode: {{ $current_month_name }}</p>
        </div>
    </div>

    <div class="row">
        <!-- Kolom Kiri: Rincian Gaji -->
        <div class="col-lg-7 col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-4">
                        <img src="{{ $employee->profile->foto ? route('home.profile.filename', $employee->profile->foto) : asset('images/no Photo.png') }}"
                            alt="Foto Karyawan" class="img-fluid rounded-circle me-3" width="60" height="60">
                        <div>
                            <h5 class="mb-0">{{ $employee->name }}</h5>
                            <p class="mb-0 text-muted">{{ $employee->role == 2 ? 'Dokter' : ($employee->role == 3 ? 'Perawat' : 'Staf') }}</p>
                        </div>
                    </div>

                    @php
                        $totalIncentive = $incentives->sum('amount');
                        $bonus = $adjustment->bonus ?? 0;
                        $deduction = $adjustment->deduction ?? 0;
                        $totalGaji = $gaji_pokok + $totalIncentive + $bonus - $deduction;
                    @endphp

                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Gaji Pokok
                            <span class="fw-bold">{{ formatPrice($gaji_pokok) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Total Insentif
                            <span class="fw-bold">{{ formatPrice($totalIncentive) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center text-success">
                            Bonus / Penyesuaian
                            <span class="fw-bold">+ {{ formatPrice($bonus) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center text-danger">
                            Potongan
                            <span class="fw-bold">- {{ formatPrice($deduction) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center fs-5">
                            <strong>Total Gaji Akhir</strong>
                            <strong class="text-primary">{{ formatPrice($totalGaji) }}</strong>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Rincian Insentif</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Deskripsi</th>
                                    <th class="text-end">Jumlah (Rp)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($incentives as $incentive)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($incentive->created_at)->format('d/m/Y') }}</td>
                                        <td>{{ $incentive->description }}</td>
                                        <td class="text-end">{{ formatPrice($incentive->amount) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center">Tidak ada insentif untuk periode ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Form Penyesuaian -->
        <div class="col-lg-5 col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Form Penyesuaian & Potongan</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('keuangan.gaji.penyesuaian.simpan', $employee->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="bonus" class="form-label">Bonus / Penyesuaian (Rp)</label>
                            <input type="text" class="form-control" id="bonus_display"
                                value="{{ old('bonus', number_format($adjustment->bonus ?? 0, 0, ',', '.')) }}"
                                placeholder="Contoh: 100.000">
                            <input type="hidden" name="bonus" id="bonus"
                                value="{{ old('bonus', $adjustment->bonus ?? 0) }}">
                            @error('bonus')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="deduction" class="form-label">Potongan (Rp)</label>
                            <input type="text" class="form-control" id="deduction_display"
                                value="{{ old('deduction', number_format($adjustment->deduction ?? 0, 0, ',', '.')) }}"
                                placeholder="Contoh: 50.000">
                            <input type="hidden" name="deduction" id="deduction"
                                value="{{ old('deduction', $adjustment->deduction ?? 0) }}">
                            @error('deduction')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Catatan</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"
                                placeholder="Contoh: Potongan karena terlambat 2x">{{ old('notes', $adjustment->notes ?? '') }}</textarea>
                            @error('notes')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-success">Simpan Penyesuaian</button>
                        </div>
                    </form>
                </div>
            </div>
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
        $(document).ready(function() {
            function formatRupiah(angka) {
                var number_string = String(angka).replace(/[^,\d]/g, '').toString(),
                    sisa = number_string.length % 3,
                    rupiah = number_string.substr(0, sisa),
                    ribuan = number_string.substr(sisa).match(/\d{3}/gi);
                if (ribuan) {
                    separator = sisa ? '.' : '';
                    rupiah += separator + ribuan.join('.');
                }
                return rupiah === '' ? '0' : rupiah;
            }

            function setupRupiahInput(displayId, hiddenId) {
                $(displayId).on('keyup', function() {
                    let displayValue = $(this).val();
                    let realValue = displayValue.replace(/\./g, '');
                    $(this).val(formatRupiah(displayValue));
                    $(hiddenId).val(realValue);
                });
            }

            setupRupiahInput('#bonus_display', '#bonus');
            setupRupiahInput('#deduction_display', '#deduction');
        });
    </script>
@endpush
