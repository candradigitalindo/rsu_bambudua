@extends('layouts.app')
@section('title', 'Tambah Pasien')
@push('style')
@endpush

@section('content')
    <div class="row gx-3">
        <div class="col-12">
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Tambah Data Pasien</h5>
                    <a href="{{ route('master.pasien.index') }}" class="btn btn-sm btn-warning">
                        <i class="ri-arrow-left-line"></i> Kembali
                    </a>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('master.pasien.store') }}" id="formPasien">
                        @csrf

                        {{-- Data Identitas --}}
                        <h6 class="fw-bold text-primary mb-3"><i class="ri-user-line"></i> Data Identitas</h6>
                        <div class="row gx-3 mb-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Nama Pasien <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                                    <select name="jenis_kelamin" class="form-select" required>
                                        <option value="">-- Pilih --</option>
                                        <option value="1" {{ old('jenis_kelamin') == '1' ? 'selected' : '' }}>Laki-laki</option>
                                        <option value="2" {{ old('jenis_kelamin') == '2' ? 'selected' : '' }}>Perempuan</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                                    <input type="date" name="tgl_lahir" class="form-control" value="{{ old('tgl_lahir') }}" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Jenis Identitas</label>
                                    <select name="jenis_identitas" class="form-select">
                                        <option value="">-- Pilih --</option>
                                        <option value="KTP" {{ old('jenis_identitas') == 'KTP' ? 'selected' : '' }}>KTP</option>
                                        <option value="SIM" {{ old('jenis_identitas') == 'SIM' ? 'selected' : '' }}>SIM</option>
                                        <option value="Paspor" {{ old('jenis_identitas') == 'Paspor' ? 'selected' : '' }}>Paspor</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">No. Identitas</label>
                                    <input type="text" name="no_identitas" class="form-control" value="{{ old('no_identitas') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Golongan Darah</label>
                                    <select name="golongan_darah" class="form-select">
                                        <option value="">-- Pilih --</option>
                                        <option value="A" {{ old('golongan_darah') == 'A' ? 'selected' : '' }}>A</option>
                                        <option value="B" {{ old('golongan_darah') == 'B' ? 'selected' : '' }}>B</option>
                                        <option value="AB" {{ old('golongan_darah') == 'AB' ? 'selected' : '' }}>AB</option>
                                        <option value="O" {{ old('golongan_darah') == 'O' ? 'selected' : '' }}>O</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Status Pernikahan</label>
                                    <select name="status_menikah" class="form-select">
                                        <option value="">-- Pilih --</option>
                                        <option value="Belum Menikah" {{ old('status_menikah') == 'Belum Menikah' ? 'selected' : '' }}>Belum Menikah</option>
                                        <option value="Menikah" {{ old('status_menikah') == 'Menikah' ? 'selected' : '' }}>Menikah</option>
                                        <option value="Cerai Hidup" {{ old('status_menikah') == 'Cerai Hidup' ? 'selected' : '' }}>Cerai Hidup</option>
                                        <option value="Cerai Mati" {{ old('status_menikah') == 'Cerai Mati' ? 'selected' : '' }}>Cerai Mati</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Kontak --}}
                        <h6 class="fw-bold text-primary mb-3"><i class="ri-phone-line"></i> Kontak</h6>
                        <div class="row gx-3 mb-4">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">No. HP <span class="text-danger">*</span></label>
                                    <input type="tel" name="no_hp" class="form-control" value="{{ old('no_hp') }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">No. Telepon</label>
                                    <input type="text" name="no_telepon" class="form-control" value="{{ old('no_telepon') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                                </div>
                            </div>
                        </div>

                        {{-- Alamat --}}
                        <h6 class="fw-bold text-primary mb-3"><i class="ri-map-pin-line"></i> Alamat</h6>
                        <div class="row gx-3 mb-4">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Alamat <span class="text-danger">*</span></label>
                                    <input type="text" name="alamat" class="form-control" value="{{ old('alamat') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Provinsi</label>
                                    <select name="province" class="form-select" id="province">
                                        <option value="">-- Pilih Provinsi --</option>
                                        @foreach ($provinces as $prov)
                                            <option value="{{ $prov->code }}" {{ old('province') == $prov->code ? 'selected' : '' }}>{{ $prov->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Kota / Kabupaten</label>
                                    <select name="city" class="form-select" id="city">
                                        <option value="">-- Pilih Kota --</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Info Lainnya --}}
                        <h6 class="fw-bold text-primary mb-3"><i class="ri-information-line"></i> Informasi Lainnya</h6>
                        <div class="row gx-3 mb-4">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Agama</label>
                                    <select name="agama" class="form-select">
                                        <option value="">-- Pilih --</option>
                                        @foreach ($agamas as $agama)
                                            <option value="{{ $agama->name }}" {{ old('agama') == $agama->name ? 'selected' : '' }}>{{ $agama->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Kewarganegaraan</label>
                                    <select name="kewarganegaraan" class="form-select">
                                        <option value="1" {{ old('kewarganegaraan', '1') == '1' ? 'selected' : '' }}>WNI</option>
                                        <option value="2" {{ old('kewarganegaraan') == '2' ? 'selected' : '' }}>WNA</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Pendidikan</label>
                                    <select name="pendidikan" class="form-select">
                                        <option value="">-- Pilih --</option>
                                        @foreach (['Tidak Sekolah', 'SD', 'SMP', 'SMA/SMK', 'D1', 'D2', 'D3', 'D4/S1', 'S2', 'S3'] as $p)
                                            <option value="{{ $p }}" {{ old('pendidikan') == $p ? 'selected' : '' }}>{{ $p }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Pekerjaan</label>
                                    <select name="pekerjaan" class="form-select">
                                        <option value="">-- Pilih --</option>
                                        @foreach ($pekerjaans as $pkj)
                                            <option value="{{ $pkj->name }}" {{ old('pekerjaan') == $pkj->name ? 'selected' : '' }}>{{ $pkj->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Kerabat --}}
                        <h6 class="fw-bold text-primary mb-3"><i class="ri-group-line"></i> Status Kerabat</h6>
                        <div class="row gx-3 mb-4">
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="is_kerabat_dokter" id="is_kerabat_dokter" {{ old('is_kerabat_dokter') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_kerabat_dokter">
                                        <i class="ri-user-heart-line text-primary"></i> Kerabat Dokter
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="is_kerabat_karyawan" id="is_kerabat_karyawan" {{ old('is_kerabat_karyawan') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_kerabat_karyawan">
                                        <i class="ri-team-line text-success"></i> Kerabat Karyawan
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="is_kerabat_owner" id="is_kerabat_owner" {{ old('is_kerabat_owner') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_kerabat_owner">
                                        <i class="ri-vip-crown-line text-warning"></i> Kerabat Owner
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2 justify-content-end mt-3">
                            <a href="{{ route('master.pasien.index') }}" class="btn btn-light">Batal</a>
                            <button type="submit" class="btn btn-primary" id="btn-submit">
                                <span class="btn-txt">Simpan</span>
                                <span class="spinner-border spinner-border-sm d-none"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Province → City cascade
            $('#province').on('change', function() {
                var code = $(this).val();
                var citySelect = $('#city');
                citySelect.html('<option value="">-- Memuat... --</option>');

                if (code) {
                    $.get("{{ url('masterdata/pasien/cities') }}/" + code, function(data) {
                        var options = '<option value="">-- Pilih Kota --</option>';
                        $.each(data, function(i, city) {
                            options += '<option value="' + city.code + '">' + city.name + '</option>';
                        });
                        citySelect.html(options);
                    });
                } else {
                    citySelect.html('<option value="">-- Pilih Kota --</option>');
                }
            });

            // Submit loading state
            $("#formPasien").submit(function() {
                $(".spinner-border").removeClass("d-none");
                $("#btn-submit").attr("disabled", true);
                $(".btn-txt").text("Mohon Tunggu ...");
            });
        });
    </script>
@endpush
