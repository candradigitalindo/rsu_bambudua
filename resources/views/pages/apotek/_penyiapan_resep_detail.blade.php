@php
    $hasPendingItems = $resep->details->contains('status', 'Diajukan');
@endphp

@if ($hasPendingItems)
    <div class="alert alert-info small">Klik "Siapkan" pada setiap item atau klik "Siapkan Semua" di bagian bawah untuk
        memproses semua item sekaligus.</div>
@endif

<div class="table-responsive">
    <table class="table table-bordered m-0">
        <thead>
            <tr>
                <th>Nama Obat</th>
                <th class="text-center" width="120">Jumlah</th>
                <th class="text-end">Harga Satuan</th>
                <th class="text-end">Total Harga</th>
                <th>Aturan Pakai</th>
                <th class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalBiaya = 0;
            @endphp
            @forelse ($resep->details as $detail)
                @php $totalBiaya += $detail->total_harga; @endphp
                <tr>
                    <td>{{ $detail->nama_obat ?? 'N/A' }}</td>
                    <td class="text-center">
                        <span class="badge bg-light text-dark fs-6">{{ $detail->qty }}</span>
                        @if ($detail->satuan)
                            <small class="text-muted d-block">{{ $detail->satuan }}</small>
                        @endif
                    </td>
                    <td class="text-end">{{ number_format($detail->harga, 0, ',', '.') }}</td>
                    <td class="text-end">{{ number_format($detail->total_harga, 0, ',', '.') }}</td>
                    <td>{{ $detail->aturan_pakai }}</td>
                    <td class="text-center">
                        @if ($detail->status == 'Diajukan')
                            <button class="btn btn-sm btn-primary btn-siapkan-item" data-id="{{ $detail->id }}">
                                <i class="ri-check-line"></i> Siapkan
                            </button>
                        @else
                            <span class="badge bg-success">{{ $detail->status }}</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Tidak ada item dalam resep ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-3">
        <div class="d-flex justify-content-between">
            <div>
                <strong>Dokter:</strong> {{ $resep->dokter ?? 'N/A' }}
            </div>
            <div class="text-end">
                <h5>Total Tagihan Obat: <span class="text-success fw-bold">Rp
                        {{ number_format($totalBiaya, 0, ',', '.') }}</span></h5>
            </div>
        </div>
    </div>
</div>
