<div class="table-responsive">
    <table class="table table-sm table-bordered">
        <thead>
            <tr>
                <th>Nama Obat</th>
                <th class="text-center">Jumlah</th>
                <th>Aturan Pakai</th>
                <th>Catatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($resep->details as $detail)
                <tr>
                    <td>{{ $detail->product?->name ?? 'N/A' }}</td>
                    <td class="text-center">{{ $detail->jumlah }}</td>
                    <td>{{ $detail->aturan_pakai }}</td>
                    <td>{{ $detail->catatan }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">Tidak ada item dalam resep ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($resep->status == 'Diajukan')
    <div class="d-grid">
        <button class="btn btn-primary btn-siapkan-resep" data-id="{{ $resep->id }}">
            <i class="ri-check-double-line"></i> Tandai Semua Sebagai "Disiapkan"
        </button>
    </div>
@else
    <div class="alert alert-info text-center" role="alert">
        Resep ini sudah disiapkan.
    </div>
@endif
