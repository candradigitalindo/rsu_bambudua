<div class="letterhead">
  <div class="lh-row">
    <div class="lh-left">
      <img src="{{ asset('images/bdc.png') }}" alt="Logo" class="lh-logo">
    </div>
    <div class="lh-center">
      <div class="lh-name">{{ config('app.clinic_name', 'Bambu Dua Clinic') }}</div>
      <div class="lh-sub">{{ config('app.clinic_address', 'Jl. Klinik No. 1, Kota') }}</div>
      <div class="lh-sub">Telp: {{ config('app.clinic_phone', '08xx-xxxx-xxxx') }} | Email: {{ config('app.clinic_email', 'info@klinik.com') }}</div>
    </div>
    <div class="lh-right">
      <!-- optional secondary logo or QR -->
    </div>
  </div>
  <hr class="lh-divider">
</div>
