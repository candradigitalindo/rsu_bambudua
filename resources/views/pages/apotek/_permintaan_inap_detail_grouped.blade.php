<div class="table-responsive">
    <table class="table table-bordered m-0">
        <thead>
            <tr>
                <th>Nama Obat</th>
                <th>Jumlah</th>
                <th class="text-end">Harga Satuan</th>
                <th class="text-end">Total Harga</th>
                <th>Aturan Pakai</th>
                <th>Dokter</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalBiaya = 0;
            @endphp
            @forelse ($permintaan as $item)
                @php $totalBiaya += $item->total; @endphp
                <tr>
                    <td>{{ $item->medication_name }}</td>
                    <td>{{ $item->jumlah }} {{ $item->satuan }}</td>
                    <td class="text-end">{{ number_format($item->harga, 0, ',', '.') }}</td>
                    <td class="text-end">{{ number_format($item->total, 0, ',', '.') }}</td>
                    <td>{{ $item->dosage_instructions }}</td>
                    <td>{{ $item->authorized->name ?? 'N/A' }}</td>
                    <td>
                        @if ($item->status == 'Diajukan')
                            <span class="badge bg-warning status-badge">{{ $item->status }}</span>
                        @elseif ($item->status == 'Disiapkan')
                            <span class="badge bg-info status-badge">{{ $item->status }}</span>
                        @else
                            <span class="badge bg-success status-badge">{{ $item->status }}</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if ($item->status == 'Diajukan')
                            <button class="btn btn-sm btn-primary btn-siapkan" data-id="{{ $item->id }}">
                                <i class="ri-check-double-line"></i> Siapkan
                            </button>
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">Tidak ada item permintaan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="text-end mt-3">
        <h5>Total Tagihan Obat: <span class="text-success fw-bold">Rp
                {{ number_format($totalBiaya, 0, ',', '.') }}</span></h5>
    </div>
</div>
