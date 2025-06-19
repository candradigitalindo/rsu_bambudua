@if($encounter->tindakan && count($encounter->tindakan))
    <table class="table truncate m-0">
        <thead>
            <tr>
                <th>Nama Tindakan</th>
                <th>Harga</th>
                <th>Qty</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($encounter->tindakan as $detail)
                <tr>
                    <td>{{ $detail->tindakan_name }}</td>
                    <td>{{ formatPrice($detail->tindakan_harga) }}</td>
                    <td>{{ $detail->qty }}</td>
                    <td>{{ formatPrice($detail->total_harga) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <div class="text-center text-muted">Tidak ada detail tindakan.</div>
@endif
