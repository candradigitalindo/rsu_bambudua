@if($encounter->resep && $encounter->resep->details && count($encounter->resep->details))
    <table class="table truncate m-0">
        <thead>
            <tr>
                <th>Nama Obat</th>
                <th>Qty</th>
                <th>Aturan Pakai</th>
                <th>Expired</th>
            </tr>
        </thead>
        <tbody>
            @foreach($encounter->resep->details as $detail)
                <tr>
                    <td>{{ $detail->nama_obat }}</td>
                    <td>{{ $detail->qty }}</td>
                    <td>{{ $detail->aturan_pakai }}</td>
                    {{-- Format tanggal expired --}}
                    <td>{{ \Carbon\Carbon::parse($detail->expired_at)->format('d-m-Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <div class="text-center text-muted">Tidak ada detail resep.</div>
@endif
