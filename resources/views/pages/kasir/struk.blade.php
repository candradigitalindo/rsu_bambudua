<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Struk Pembayaran</title>
  <style>
    :root {
      --fg: #000;
      --muted: #666;
      --border: #ddd;
    }
    body {
      color: var(--fg);
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
      margin: 0;
      background: #fff;
    }
    .container {
      max-width: 800px;
      margin: 24px auto;
      padding: 0 16px;
    }
    .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 12px;
    }
    .brand { font-weight: 600; font-size: 18px; }
    .muted { color: var(--muted); font-size: 12px; }
    hr { border: none; border-top: 1px solid var(--border); margin: 12px 0; }
    .row { display: flex; flex-wrap: wrap; gap: 12px; }
    .col { flex: 1 1 300px; }
    .label { color: var(--muted); font-size: 12px; }
    .value { font-weight: 600; }
    table { width: 100%; border-collapse: collapse; }
    th, td { padding: 8px; border-bottom: 1px solid var(--border); text-align: left; }
    tfoot th { border-top: 2px solid var(--border); }
    .text-end { text-align: right; }
    .center { text-align: center; }
    .actions { margin-top: 16px; display: flex; gap: 8px; justify-content: center; }
    .btn { display: inline-block; padding: 8px 12px; border: 1px solid var(--border); background: #f5f5f5; color: #333; text-decoration: none; border-radius: 6px; }
    @media print {
      .actions { display: none; }
      body { margin: 0; }
      .container { margin: 0; padding: 0 12mm; }
      @page { size: A4; margin: 12mm; }
    }
  </style>
</head>
<body onload="window.print()">
  <div class="container">
    <div class="header">
      <div>
        <div class="brand">Bambu Dua Clinic</div>
        <div class="muted">Jl. Bambu II No.20, Medan</div>
      </div>
      <div class="right">
        <div class="label">Waktu</div>
        <div class="value">{{ now()->format('d M Y H:i') }}</div>
      </div>
    </div>
    <hr>
    <div class="row">
      <div class="col">
        <div class="label">Nama Pasien</div>
        <div class="value">{{ $pasien->name ?? '-' }}</div>
      </div>
      <div class="col">
        <div class="label">No. Rekam Medis</div>
        <div class="value">{{ $pasien->rekam_medis ?? '-' }}</div>
      </div>
    </div>
    <hr>
    <table>
      <thead>
        <tr>
          <th>No. Encounter</th>
          <th>Jenis Tagihan</th>
          <th class="text-end">Nominal</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($encounters as $enc)
          @php $paidItems = $paid[$enc->id] ?? []; @endphp
          @foreach ($paidItems as $type => $amount)
            <tr>
              <td>{{ $enc->no_encounter }}</td>
              <td style="text-transform: capitalize;">{{ $type }}</td>
              <td class="text-end">{{ 'Rp ' . number_format($amount, 0, ',', '.') }}</td>
            </tr>
          @endforeach
        @endforeach
      </tbody>
      <tfoot>
        <tr>
          <th colspan="2" class="text-end">Total</th>
          <th class="text-end">{{ 'Rp ' . number_format($total, 0, ',', '.') }}</th>
        </tr>
      </tfoot>
    </table>
    <div class="center" style="margin-top: 16px;">
      <small class="muted">Terima kasih atas kunjungan Anda.</small>
    </div>
    <div class="actions">
      <a class="btn" href="{{ route('kasir.index') }}">Kembali ke Kasir</a>
      <a class="btn" href="#" onclick="window.print()">Cetak</a>
    </div>
  </div>
</body>
</html>
