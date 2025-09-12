<div class="container-fluid">
    <div class="row">
        <div class="col-md-6">
            <p><strong>Nama Obat:</strong><br>{{ $permintaan->medication_name }}</p>
        </div>
        <div class="col-md-6">
            <p><strong>Kode Obat:</strong><br>{{ $permintaan->medication_code }}</p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <p><strong>Jumlah:</strong><br>{{ $permintaan->jumlah }} {{ $permintaan->satuan }}</p>
        </div>
        <div class="col-md-6">
            <p><strong>Tanggal
                    Expired:</strong><br>{{ $permintaan->expiration_date ? \Carbon\Carbon::parse($permintaan->expiration_date)->format('d F Y') : 'Tidak ada' }}
            </p>
        </div>
    </div>
    <hr>
    <p><strong>Dosis & Aturan Pakai:</strong><br>{{ $permintaan->dosage_instructions }}</p>
    <p><strong>Frekuensi:</strong><br>{{ $permintaan->frequency }}</p>
    <p><strong>Rute Pemberian:</strong><br>{{ $permintaan->route }}</p>
    <hr>
    <p><strong>Tanggal Pemberian:</strong><br>{{ \Carbon\Carbon::parse($permintaan->medicine_date)->format('d F Y') }}
    </p>
    <p><strong>Catatan:</strong><br>{{ $permintaan->notes ?? 'Tidak ada catatan.' }}</p>
    <hr>
    <p><strong>Dokter Penanggung Jawab:</strong><br>{{ $permintaan->authorized?->name ?? 'N/A' }}</p>

</div>
