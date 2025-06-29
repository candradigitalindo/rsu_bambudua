@extends('layouts.app')
@section('title', 'Pendaftaran')
@push('style')
    <!-- Scrollbar CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">

    <!-- Uploader CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/dropzone/dropzone.min.css') }}">
@endpush
@section('content')
    <div class="row gx-3">
        <!-- Row starts -->

        <div class="col-sm-9 col-12">
            <div class="card border mb-3">
                <div class="card-body">
                    <div class="card-info rounded-1 small lh-1">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-pasien" id="loket">
                            <i class="ri-folder-user-fill"></i>
                            <span class="btn-text" id="text-loket">Buka Data Pasien</span>
                            <span class="spinner-border spinner-border-sm d-none" id="spiner-loket"></span>

                        </button>
                    </div>
                    <!-- Modal XL -->
                    <div class="modal fade" id="modal-pasien" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" aria-labelledby="exampleModalXlLabel" aria-hidden="true">
                        <div class="modal-dialog modal-xl modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalXlLabel">
                                        Data Pasien
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row gx-3">
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label class="form-label" for="a5">Cari Pasien <span
                                                        class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <input name="name" type="text" class="form-control" id="search"
                                                        placeholder="Cari Nama, RM, RM Lama, No HP, KTP">
                                                </div>
                                                <p class="text-danger">{{ $errors->first('name') }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="" id="data">

                                    </div>
                                    <div class="card-body">
                                        <div class="d-grid gap-1">
                                        </div>
                                    </div>
                                    <div id="loading">
                                        <div class="card border mb-3">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center text-center">
                                                    <div class="spinner-border text-success me-2" role="status"
                                                        aria-hidden="true"></div>
                                                    <strong>Loading...</strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#form-pasien" id="btn-buatPasienBaru">
                                        <i class="ri-user-add-fill"></i>
                                        Buat Data Pasien Baru
                                    </button>

                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                        Tutup
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade" id="form-edit-pasien" tabindex="-1"
                        aria-labelledby="exampleModalScrollableTitle" aria-hidden="true" data-bs-backdrop="static"
                        data-bs-keyboard="false">
                        <div class="modal-dialog modal-xl modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalScrollableTitle">
                                        Form Edit Data Pasien
                                    </h5>

                                </div>
                                <div class="modal-body">
                                    <div class="alert alert-danger print-error-msg" style="display:none" id="error-edit">
                                        <ul></ul>
                                    </div>
                                    <form id="formpasien">
                                        <div class="row gx-3">
                                            <div class="col-xxl-2 col-lg-4 col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="a7">Jenis Identitas</label>
                                                    <div class="input-group">

                                                        <select class="form-select" name="jenis_identitas"
                                                            id="jenis_identitas_edit">
                                                            <option value="">Pilih Jenis Identitas</option>
                                                            <option value="1">
                                                                KTP</option>
                                                            <option value="2">
                                                                SIM</option>
                                                            <option value="3">
                                                                Paspor</option>

                                                        </select>
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="col-xxl-5 col-lg-4 col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="a1">Nomor Identitas</label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" id="no_identitas_edit"
                                                            name="no_identitas">
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="col-xxl-5 col-lg-4 col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="a1">Nama Pasien
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" id="name_pasien_edit"
                                                            name="name_pasien">
                                                    </div>

                                                </div>
                                            </div>
                                        </div>

                                        <div class="row gx-3">
                                            <div class="col-xxl-4 col-lg-4 col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="a7">Jenis Kelamin
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <div class="input-group">

                                                        <select class="form-select" name="jenis_kelamin"
                                                            id="jenis_kelamin_edit">
                                                            <option value="">Pilih Jenis Kelamin</option>
                                                            <option value="1">
                                                                Pria</option>
                                                            <option value="2">
                                                                Wanita</option>
                                                        </select>
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="col-xxl-4 col-lg-4 col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="a1">Tanggal Lahir
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <div class="input-group">
                                                        <input type="date" class="form-control" id="tgl_lahir_edit"
                                                            name="tgl_lahir">
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="col-xxl-4 col-lg-4 col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="a7">Gol Darah</label>
                                                    <div class="input-group">
                                                        <select class="form-select" name="golongan_darah"
                                                            id="golongan_darah_edit">
                                                            <option value="">-- Pilih Gol Darah --</option>
                                                            <option value="A">A</option>
                                                            <option value="B">B</option>
                                                            <option value="AB">AB</option>
                                                            <option value="O">O</option>
                                                        </select>
                                                    </div>

                                                </div>
                                            </div>


                                        </div>
                                        <div class="row gx-3">
                                            <div class="col-xxl-4 col-lg-4 col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="a7">Kewarganegaraan

                                                    </label>
                                                    <div class="input-group">

                                                        <select class="form-select" name="kewarganegaraan"
                                                            id="kewarganegaraan_edit">

                                                            <option value="1">
                                                                WNI</option>
                                                            <option value="2">
                                                                WNA</option>


                                                        </select>
                                                    </div>

                                                </div>
                                            </div>

                                            <div class="col-xxl-4 col-lg-4 col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="a7">Pekerjaan</label>
                                                    <div class="input-group">
                                                        <select class="form-select" name="pekerjaan" id="pekerjaan_edit">
                                                            <option value="">-- Pilih Pekerjaan --</option>
                                                            @foreach ($pekerjaan as $p)
                                                                <option value="{{ $p->name }}">{{ $p->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="col-xxl-4 col-lg-4 col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="a7">Status Menikah

                                                    </label>
                                                    <div class="input-group">

                                                        <select class="form-select" name="status_menikah"
                                                            id="status_menikah_edit">
                                                            <option value="">-- Pilih Status --</option>
                                                            <option value="1">
                                                                Belum Menikah</option>
                                                            <option value="2">
                                                                Menikah</option>
                                                            <option value="3">
                                                                Cerai Hidup</option>
                                                            <option value="4">
                                                                Cerai Mati</option>
                                                        </select>
                                                    </div>

                                                </div>
                                            </div>


                                        </div>
                                        <div class="row gx-3">
                                            <div class="col-xxl-4 col-lg-4 col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="a7">Agama</label>
                                                    <div class="input-group">
                                                        <select class="form-select" name="agama" id="agama_edit">
                                                            <option value="">-- Pilih Agama --</option>
                                                            @foreach ($agama as $a)
                                                                <option value="{{ $a->name }}">{{ $a->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="col-xxl-4 col-lg-4 col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="a1">No Handphone
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" id="no_hp_edit"
                                                            name="no_hp">
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="col-xxl-4 col-lg-4 col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="a1">No Telp

                                                    </label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" id="no_telepon_edit"
                                                            name="no_telepon">
                                                    </div>

                                                </div>
                                            </div>

                                        </div>

                                        <div class="row gx-3">
                                            <div class="col-xxl-4 col-lg-4 col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="a1">Alamat
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" id="alamat_edit"
                                                            name="alamat">
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="col-xxl-4 col-lg-4 col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="a7">Provinsi</label>
                                                    <div class="input-group">
                                                        <select class="form-select" name="province" id="province_edit">
                                                            <option value="">-- Pilih Provinsi --</option>
                                                            @foreach ($provinsi as $p)
                                                                <option value="{{ $p->code }}">{{ $p->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                </div>
                                            </div>

                                            <div class="col-xxl-4 col-lg-4 col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="a1">Kota / Kabupaten

                                                    </label>
                                                    <div class="input-group">
                                                        <select class="form-select" name="city" id="city_edit">
                                                            <option value="">-- Pilih Provinsi dulu --</option>

                                                        </select>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                        <input type="text" style="visibility: hidden" id="id">
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-target="#modal-pasien"
                                        data-bs-toggle="modal" id="btn-edit-kembali">
                                        Kembali
                                    </button>
                                    <button type="button" class="btn btn-primary" id="btn-edit">
                                        Simpan
                                    </button>

                                </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade" id="form-pasien" tabindex="-1"
                        aria-labelledby="exampleModalScrollableTitle" aria-hidden="true" data-bs-backdrop="static"
                        data-bs-keyboard="false">
                        <div class="modal-dialog modal-xl modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalScrollableTitle">
                                        Form Data Pasien
                                    </h5>

                                </div>
                                <div class="modal-body">
                                    <div class="alert alert-danger print-error-msg" style="display:none" id="error">
                                        <ul></ul>
                                    </div>
                                    <form id="formpasien">
                                        <div class="row gx-3">
                                            <div class="col-xxl-2 col-lg-4 col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="a7">Jenis Identitas</label>
                                                    <div class="input-group">

                                                        <select class="form-select" name="jenis_identitas"
                                                            id="jenis_identitas">
                                                            <option value="">Pilih Jenis Identitas</option>
                                                            <option value="1">
                                                                KTP</option>
                                                            <option value="2">
                                                                SIM</option>
                                                            <option value="3">
                                                                Paspor</option>

                                                        </select>
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="col-xxl-5 col-lg-4 col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="a1">Nomor Identitas</label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" id="no_identitas"
                                                            name="no_identitas">
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="col-xxl-5 col-lg-4 col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="a1">Nama Pasien
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" id="name_pasien"
                                                            name="name_pasien">
                                                    </div>

                                                </div>
                                            </div>
                                        </div>

                                        <div class="row gx-3">
                                            <div class="col-xxl-4 col-lg-4 col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="a7">Jenis Kelamin
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <div class="input-group">

                                                        <select class="form-select" name="jenis_kelamin"
                                                            id="jenis_kelamin">
                                                            <option value="">Pilih Jenis Kelamin</option>
                                                            <option value="1">
                                                                Pria</option>
                                                            <option value="2">
                                                                Wanita</option>
                                                        </select>
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="col-xxl-4 col-lg-4 col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="a1">Tanggal Lahir
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <div class="input-group">
                                                        <input type="date" class="form-control" id="tgl_lahir"
                                                            name="tgl_lahir">
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="col-xxl-4 col-lg-4 col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="a7">Gol Darah</label>
                                                    <div class="input-group">
                                                        <select class="form-select" name="golongan_darah"
                                                            id="golongan_darah">
                                                            <option value="">-- Pilih Gol Darah --</option>
                                                            <option value="A">A</option>
                                                            <option value="B">B</option>
                                                            <option value="AB">AB</option>
                                                            <option value="O">O</option>
                                                        </select>
                                                    </div>

                                                </div>
                                            </div>


                                        </div>
                                        <div class="row gx-3">
                                            <div class="col-xxl-4 col-lg-4 col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="a7">Kewarganegaraan

                                                    </label>
                                                    <div class="input-group">

                                                        <select class="form-select" name="kewarganegaraan"
                                                            id="kewarganegaraan">

                                                            <option value="1">
                                                                WNI</option>
                                                            <option value="2">
                                                                WNA</option>


                                                        </select>
                                                    </div>

                                                </div>
                                            </div>

                                            <div class="col-xxl-4 col-lg-4 col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="a7">Pekerjaan</label>
                                                    <div class="input-group">
                                                        <select class="form-select" name="pekerjaan" id="pekerjaan">
                                                            <option value="">-- Pilih Pekerjaan --</option>
                                                            @foreach ($pekerjaan as $p)
                                                                <option value="{{ $p->name }}">{{ $p->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="col-xxl-4 col-lg-4 col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="a7">Status Menikah

                                                    </label>
                                                    <div class="input-group">

                                                        <select class="form-select" name="status_menikah"
                                                            id="status_menikah">
                                                            <option value="">-- Pilih Status --</option>
                                                            <option value="1">
                                                                Belum Menikah</option>
                                                            <option value="2">
                                                                Menikah</option>
                                                            <option value="3">
                                                                Cerai Hidup</option>
                                                            <option value="4">
                                                                Cerai Mati</option>
                                                        </select>
                                                    </div>

                                                </div>
                                            </div>


                                        </div>
                                        <div class="row gx-3">
                                            <div class="col-xxl-4 col-lg-4 col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="a7">Agama</label>
                                                    <div class="input-group">
                                                        <select class="form-select" name="agama" id="agama">
                                                            <option value="">-- Pilih Agama --</option>
                                                            @foreach ($agama as $a)
                                                                <option value="{{ $a->name }}">{{ $a->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="col-xxl-4 col-lg-4 col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="a1">No Handphone
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" id="no_hp"
                                                            name="no_hp">
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="col-xxl-4 col-lg-4 col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="a1">No Telp

                                                    </label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" id="no_telepon"
                                                            name="no_telepon">
                                                    </div>

                                                </div>
                                            </div>

                                        </div>

                                        <div class="row gx-3">
                                            <div class="col-xxl-4 col-lg-4 col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="a1">Alamat
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" id="alamat"
                                                            name="alamat">
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="col-xxl-4 col-lg-4 col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="a7">Provinsi</label>
                                                    <div class="input-group">
                                                        <select class="form-select" name="province" id="province">
                                                            <option value="">-- Pilih Provinsi --</option>
                                                            @foreach ($provinsi as $p)
                                                                <option value="{{ $p->code }}">{{ $p->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                </div>
                                            </div>

                                            <div class="col-xxl-4 col-lg-4 col-sm-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="a1">Kota / Kabupaten

                                                    </label>
                                                    <div class="input-group">
                                                        <select class="form-select" name="city" id="city">
                                                            <option value="">-- Pilih Provinsi dulu --</option>

                                                        </select>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>

                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-target="#modal-pasien"
                                        data-bs-toggle="modal" id="btn-kembali">
                                        Kembali
                                    </button>
                                    <button type="button" class="btn btn-primary" id="btn-simpan">
                                        Simpan
                                    </button>

                                </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade" id="modal-rawatJalan" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" aria-labelledby="exampleModalXlLabel" aria-hidden="true">
                        <div class="modal-dialog modal-xl">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalXlLabel">
                                        Pedaftaran Rawat Jalan
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">

                                    <div class="card border mt-3">
                                        <div class="card-body">
                                            <span class="badge bg-primary-subtle rounded-pill text-primary">
                                                <i class="ri-circle-fill me-1"></i>Status : <a
                                                    id="status-pasien"></a></span>
                                            <hr>
                                            <div class="row justify-content-between">
                                                <div class="col-4">
                                                    <div class="text-primary fw-semibold">
                                                        Identitas Pasien
                                                    </div>
                                                    <table>
                                                        <tr>
                                                            <td>No. RM</td>
                                                            <td>:</td>
                                                            <td id="no_rm_rawatJalan">XXXXXXXXXXX</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Nama Pasien</td>
                                                            <td>:</td>
                                                            <td id="name_rawatJalan">XXXXXXXXXXX</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Umur</td>
                                                            <td>:</td>
                                                            <td id="tgl_lahir_rawatJalan">XXXXXXXXXXX</td>
                                                        </tr>
                                                    </table>
                                                </div>
                                                <div class="col-4">
                                                    <div class="text-primary fw-semibold">
                                                        Kunjungan Terakhir
                                                    </div>
                                                    <table>
                                                        <tr>
                                                            <td>No. Kunjungan</td>
                                                            <td>:</td>
                                                            <td id="no_encounter_rawatJalan">XXXXXXXXXX</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Tanggal Kujungan</td>
                                                            <td>:</td>
                                                            <td id="created_rawatJalan">XXXXXXXXXXX</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Tipe</td>
                                                            <td>:</td>
                                                            <td id="type_rawatJalan">XXXXXXXXXXX</td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="alert alert-danger print-error-msg mt-2 mb-2" style="display:none"
                                        id="error-rawatJalan">
                                        <ul></ul>
                                    </div>
                                    <hr>
                                    <div class="row gx-3 mt-3">
                                        <div class="col-xxl-4 col-lg-4 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="a7">Jenis Jaminan</label>
                                                <div class="input-group">
                                                    <select class="form-select" name="jenis_jaminan" id="jenis_jaminan">
                                                        <option value="">-- Pilih Jaminan --</option>
                                                        <option value="1">Umum</option>
                                                        {{-- <option value="1">BPJS</option>
                                                        <option value="1">Asuransi</option> --}}
                                                    </select>
                                                </div>

                                            </div>
                                        </div>

                                        <div class="col-xxl-4 col-lg-4 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="a1">Dokter

                                                </label>
                                                <div class="input-group">
                                                    <select class="form-select" name="dokter" id="dokter">
                                                        <option value="">-- Pilih Dokter --</option>
                                                        @foreach ($dokter as $do)
                                                            <option value="{{ $do->user->name }}">{{ $do->user->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="col-xxl-4 col-lg-4 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="a1">Tujuan Kunjungan

                                                </label>
                                                <div class="input-group">
                                                    <select class="form-select" name="tujuan_kunjungan"
                                                        id="tujuan_kunjungan">
                                                        <option value="">-- Pilih Tujuan --</option>
                                                        <option value="1">Kunjungan Sehat (Promotif/Preventif)
                                                        </option>
                                                        <option value="2">Rehabilitatif</option>
                                                        <option value="3">Kunjungan Sakit</option>
                                                        <option value="4">Darurat</option>
                                                        <option value="5">Kontrol / Tindak Lanjut</option>
                                                        <option value="6">Treatment</option>
                                                        <option value="7">Konsultasi</option>
                                                    </select>
                                                </div>

                                            </div>
                                        </div>
                                        <input type="text" style="visibility: hidden" id="id-rawatJalan">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-primary" id="btn-submit-rawatJalan">
                                        <i class="ri-user-add-fill"></i>
                                        Simpan
                                    </button>

                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                                        id="btn-tutup-rawatJalan">
                                        Tutup
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade" id="modal-rawatInap" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" aria-labelledby="exampleModalXlLabel" aria-hidden="true">
                        <div class="modal-dialog modal-xl">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalXlLabel">
                                        Pendaftaran Rawat Inap
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">

                                    <div class="card border mt-3">
                                        <div class="card-body">
                                            <span class="badge bg-primary-subtle rounded-pill text-primary">
                                                <i class="ri-circle-fill me-1"></i>Status : <a
                                                    id="status-pasien-rawatInap"></a></span>
                                            <hr>
                                            <div class="row justify-content-between">
                                                <div class="col-4">
                                                    <div class="text-primary fw-semibold">
                                                        Identitas Pasien
                                                    </div>
                                                    <table>
                                                        <tr>
                                                            <td>No. RM</td>
                                                            <td>:</td>
                                                            <td id="no_rm_rawatInap">XXXXXXXXXXX</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Nama Pasien</td>
                                                            <td>:</td>
                                                            <td id="name_rawatInap">XXXXXXXXXXX</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Umur</td>
                                                            <td>:</td>
                                                            <td id="tgl_lahir_rawatInap">XXXXXXXXXXX</td>
                                                        </tr>
                                                    </table>
                                                </div>
                                                <div class="col-4">
                                                    <div class="text-primary fw-semibold">
                                                        Kunjungan Terakhir
                                                    </div>
                                                    <table>
                                                        <tr>
                                                            <td>No. Kunjungan</td>
                                                            <td>:</td>
                                                            <td id="no_encounter_rawatInap">XXXXXXXXXX</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Tanggal Kujungan</td>
                                                            <td>:</td>
                                                            <td id="created_rawatInap">XXXXXXXXXXX</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Tipe</td>
                                                            <td>:</td>
                                                            <td id="type_rawatInap">XXXXXXXXXXX</td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="alert alert-danger print-error-msg mt-2 mb-2" style="display:none"
                                        id="error-rawatInap">
                                        <ul></ul>
                                    </div>
                                    <hr>
                                    <div class="row gx-3 mt-3">
                                        <div class="col-xxl-4 col-lg-4 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="a7">Jenis Jaminan</label>
                                                <div class="input-group">
                                                    <select class="form-select" name="jenis_jaminan_rawatInap" id="jenis_jaminan_rawatInap">
                                                        <option value="">-- Pilih Jaminan --</option>
                                                        <option value="1">Umum</option>
                                                        {{-- <option value="1">BPJS</option>
                                                        <option value="1">Asuransi</option> --}}
                                                    </select>
                                                </div>

                                            </div>
                                        </div>

                                        <div class="col-xxl-4 col-lg-4 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="a1">Dokter

                                                </label>
                                                <div class="input-group">
                                                    <select class="form-select" name="dokter" id="dokter_rawatInap">
                                                        <option value="">-- Pilih Dokter --</option>
                                                        @foreach ($dokter as $do)
                                                            <option value="{{ $do->user->name }}">{{ $do->user->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="col-xxl-4 col-lg-4 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="a1">Tujuan Kunjungan

                                                </label>
                                                <div class="input-group">
                                                    <select class="form-select" name="tujuan_kunjungan_rawatInap"
                                                        id="tujuan_kunjungan_rawatInap">
                                                        <option value="">-- Pilih Tujuan --</option>
                                                        <option value="1">Kunjungan Sehat (Promotif/Preventif)
                                                        </option>
                                                        <option value="2">Rehabilitatif</option>
                                                        <option value="3">Kunjungan Sakit</option>
                                                        <option value="4">Darurat</option>
                                                        <option value="5">Kontrol / Tindak Lanjut</option>
                                                        <option value="6">Treatment</option>
                                                        <option value="7">Konsultasi</option>
                                                    </select>
                                                </div>

                                            </div>
                                        </div>
                                        <input type="text" style="visibility: hidden" id="id-rawatInap">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-primary" id="btn-submit-rawatInap">
                                        <i class="ri-user-add-fill"></i>
                                        Simpan
                                    </button>

                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                                        id="btn-tutup-rawatInap">
                                        Tutup
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="modal-rawatDarurat" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" aria-labelledby="exampleModalXlLabel" aria-hidden="true">
                        <div class="modal-dialog modal-xl">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalXlLabel">
                                        Pendaftaran Rawat Darurat
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">

                                    <div class="card border mt-3">
                                        <div class="card-body">
                                            <span class="badge bg-primary-subtle rounded-pill text-primary">
                                                <i class="ri-circle-fill me-1"></i>Status : <a
                                                    id="status-pasien-rawatDarurat"></a></span>
                                            <hr>
                                            <div class="row justify-content-between">
                                                <div class="col-4">
                                                    <div class="text-primary fw-semibold">
                                                        Identitas Pasien
                                                    </div>
                                                    <table>
                                                        <tr>
                                                            <td>No. RM</td>
                                                            <td>:</td>
                                                            <td id="no_rm_rawatDarurat">XXXXXXXXXXX</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Nama Pasien</td>
                                                            <td>:</td>
                                                            <td id="name_rawatDarurat">XXXXXXXXXXX</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Umur</td>
                                                            <td>:</td>
                                                            <td id="tgl_lahir_rawatDarurat">XXXXXXXXXXX</td>
                                                        </tr>
                                                    </table>
                                                </div>
                                                <div class="col-4">
                                                    <div class="text-primary fw-semibold">
                                                        Kunjungan Terakhir
                                                    </div>
                                                    <table>
                                                        <tr>
                                                            <td>No. Kunjungan</td>
                                                            <td>:</td>
                                                            <td id="no_encounter_rawatDarurat">XXXXXXXXXX</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Tanggal Kujungan</td>
                                                            <td>:</td>
                                                            <td id="created_rawatDarurat">XXXXXXXXXXX</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Tipe</td>
                                                            <td>:</td>
                                                            <td id="type_rawatDarurat">XXXXXXXXXXX</td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="alert alert-danger print-error-msg mt-2 mb-2" style="display:none"
                                        id="error-rawatDarurat">
                                        <ul></ul>
                                    </div>
                                    <hr>
                                    <div class="row gx-3 mt-3">
                                        <div class="col-xxl-4 col-lg-4 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="a7">Jenis Jaminan</label>
                                                <div class="input-group">
                                                    <select class="form-select" name="jenis_jaminan_rawatDarurat"
                                                        id="jenis_jaminan_rawatDarurat">
                                                        <option value="">-- Pilih Jaminan --</option>
                                                        <option value="1">Umum</option>
                                                        {{-- <option value="1">BPJS</option>
                                                        <option value="1">Asuransi</option> --}}
                                                    </select>
                                                </div>

                                            </div>
                                        </div>

                                        <div class="col-xxl-4 col-lg-4 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="a1">Dokter

                                                </label>
                                                <div class="input-group">
                                                    <select class="form-select" name="dokter_rawatDarurat"
                                                        id="dokter_rawatDarurat">
                                                        <option value="">-- Pilih Dokter --</option>
                                                        @foreach ($dokter as $do)
                                                            <option value="{{ $do->user->name }}">{{ $do->user->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="col-xxl-4 col-lg-4 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="a1">Tujuan Kunjungan

                                                </label>
                                                <div class="input-group">
                                                    <select class="form-select" name="tujuan_kunjungan_rawatDarurat"
                                                        id="tujuan_kunjungan_rawatDarurat">
                                                        <option value="">-- Pilih Tujuan --</option>
                                                        <option value="1">Kunjungan Sehat (Promotif/Preventif)
                                                        </option>
                                                        <option value="2">Rehabilitatif</option>
                                                        <option value="3">Kunjungan Sakit</option>
                                                        <option value="4">Darurat</option>
                                                        <option value="5">Kontrol / Tindak Lanjut</option>
                                                        <option value="6">Treatment</option>
                                                        <option value="7">Konsultasi</option>
                                                    </select>
                                                </div>

                                            </div>
                                        </div>
                                        <input type="text" style="visibility: hidden" id="id-rawatDarurat">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-primary" id="btn-submit-rawatDarurat">
                                        <i class="ri-user-add-fill"></i>
                                        Simpan
                                    </button>

                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                                        id="btn-tutup-rawatDarurat">
                                        Tutup
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Custom tabs starts -->
                    <div class="custom-tabs-container mt-5">

                        <!-- Nav tabs starts -->
                        <ul class="nav nav-tabs" id="customTab2" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active" id="tab-rawatJalan" data-bs-toggle="tab" href="#rawatJalan"
                                    role="tab" aria-controls="oneA" aria-selected="true"><i
                                        class="ri-stethoscope-line"></i>Rawat Jalan</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="tab-igd" data-bs-toggle="tab" href="#rawatDarurat"
                                    role="tab" aria-controls="twoA" aria-selected="false"><i
                                        class="ri-dossier-fill"></i>
                                    IGD</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="tab-rawatInap" data-bs-toggle="tab" href="#rawatInap"
                                    role="tab" aria-controls="threeA" aria-selected="false"><i
                                        class="ri-hotel-bed-fill"></i>
                                    Rawat Inap</a>
                            </li>

                        </ul>
                        <!-- Nav tabs ends -->

                        <!-- Tab content starts -->
                        <div class="tab-content h-350">
                            <div class="tab-pane fade show active" id="rawatJalan" role="tabpanel">
                                {{-- Tabel Rawat Jalan --}}
                                <div class="col-sm-12">
                                    <div class="table-outer">
                                        <div class="table-responsive">
                                            <table class="table truncate m-0">
                                                <thead>
                                                    <tr>
                                                        <th>Kunjungan</th>
                                                        <th>Pasien / Dokter</th>
                                                        <th>Jaminan / Tujuan</th>
                                                        <th class="text-center">Aksi</th>

                                                    </tr>
                                                </thead>
                                                <tbody id="showRawatJalan">


                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                </div>

                            </div>
                            <div class="tab-pane fade" id="rawatDarurat" role="tabpanel">

                                {{-- Rawat Darurat --}}
                                <div class="col-sm-12">
                                    <div class="table-outer">
                                        <div class="table-responsive">
                                            <table class="table truncate m-0">
                                                <thead>
                                                    <tr>
                                                        <th>Kunjungan</th>
                                                        <th>Pasien / Dokter</th>
                                                        <th>Jaminan / Tujuan</th>
                                                        <th class="text-center">Aksi</th>

                                                    </tr>
                                                </thead>
                                                <tbody id="showRawatDarurat">


                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="tab-pane fade" id="rawatInap" role="tabpanel">

                                {{-- Rawat Darurat --}}
                                <div class="col-sm-12">
                                    <div class="table-outer">
                                        <div class="table-responsive">
                                            <table class="table truncate m-0">
                                                <thead>
                                                    <tr>
                                                        <th>Kunjungan</th>
                                                        <th>Pasien / Dokter</th>
                                                        <th>Jaminan / Tujuan</th>
                                                        <th class="text-center">Aksi</th>

                                                    </tr>
                                                </thead>
                                                <tbody id="showRawatInap">


                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div>
                        <!-- Tab content ends -->

                    </div>
                </div>
            </div>

        </div>
        <div class="col-sm-3">
            <div class="card border mb-3">
                <div class="card-body">
                    @if (auth()->user()->role == 5)
                        <!-- Card details start -->
                        <div class="text-center">
                            <div class="icon-box md border border-primary rounded-5 mb-2 m-auto">
                                <i class="ri-empathize-line fs-5 text-primary"></i>
                            </div>
                            <h6>No Antrian sekarang</h6>
                            <h3 class="text-primary display-1" id="antrian">{{ $antrian['antrian'] }}</h3>

                            <button type="submit" class="btn btn-primary mt-2" id="btn-next">
                                <i class="ri-arrow-right-double-fill"></i>
                                <span class="btn-txt" id="text-next">Antrian Selanjutnya</span>
                                <span class="spinner-border spinner-border-sm d-none" id="spinner-next"></span>
                            </button>
                            <br>
                            <small class="text-primary">Ada <span class="fw-bold"
                                    id="jumlah">{{ $antrian['jumlah'] }}</span> antrian lagi</small>

                        </div>

                        <!-- Card details end -->
                    @else
                        <h6>Fitur Antrian hanya untuk Hak Akses Akun Pendaftaran</h6>
                    @endif

                </div>
            </div>
        </div>
    </div>
    <!-- Row ends -->
@endsection
@push('scripts')
    <!-- Overlay Scroll JS -->
    <script src="{{ asset('vendor/overlay-scroll/jquery.overlayScrollbars.min.js') }}"></script>
    <script src="{{ asset('vendor/overlay-scroll/custom-scrollbar.js') }}"></script>

    <!-- Dropzone JS -->
    <script src="{{ asset('vendor/dropzone/dropzone.min.js') }}"></script>

    <!-- Custom JS files -->
    <script src="{{ asset('js/custom.js') }}"></script>
    <script src="{{ asset('js/validations.js') }}"></script>
    <script src="https://code.responsivevoice.org/responsivevoice.js?key=Pqiovi6G"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
        $(document).ready(function() {
            rawatJalan();
            $("#tab-rawatJalan").on("click", function() {
                rawatJalan();
            });
            $("#tab-igd").on("click", function() {
                rawatDarurat();
            });
            $("#tab-rawatInap").on("click", function() {
                rawatInap();
            });
            $('#loading').hide();
            $("#submit").submit(function() {
                $(".spinner-border").removeClass("d-none");
                $("#btn-update").attr("disabled", true);
                $(".btn-txt").text("Mohon Tunggu ...");
            });

            function rawatJalan() {
                $.ajax({
                    url: "{{ route('pendaftaran.showRawatJalan') }}",
                    type: 'GET',
                    data: {
                        _token: "{{ csrf_token() }}",
                    },
                    dataType: 'json',
                    success: function(rawatJalan) {
                        $('#showRawatJalan').html(rawatJalan);
                    }
                })
            }

            function rawatDarurat() {
                $.ajax({
                    url: "{{ route('pendaftaran.showRawatDarurat') }}",
                    type: 'GET',
                    data: {
                        _token: "{{ csrf_token() }}",
                    },
                    dataType: 'json',
                    success: function(rawatDarurat) {
                        $('#showRawatDarurat').html(rawatDarurat);
                    }
                })
            }

            function rawatInap() {
                $.ajax({
                    url: "{{ route('pendaftaran.showRawatInap') }}",
                    type: 'GET',
                    data: {
                        _token: "{{ csrf_token() }}",
                    },
                    dataType: 'json',
                    success: function(rawatInap) {
                        $('#showRawatInap').html(rawatInap);
                    }
                })
            }


            $(document).on('keyup', '#search', function() {
                var query = $(this).val();
                if (query.length >= 2) {
                    $('#loading').show();
                    $.ajax({
                        url: "{{ route('pendaftaran.caripasien') }}",
                        method: 'GET',
                        data: {
                            q: query
                        },
                        dataType: 'json',
                        success: function(data) {

                            $('#data').hide();
                            setTimeout(function() {
                                $('#loading').hide();
                                $('#data').show();
                                $('#data').html(data);
                            }, 1000);

                        }
                    })
                } else {
                    $('#loading').hide();
                    $('#data').hide();
                }
            });

            $("#btn-next").click(function() {
                $.ajax({
                    url: "{{ route('pendaftaran.update_antrian') }}",
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}"
                    },

                    success: function(res) {
                        if (res.status == true) {
                            responsiveVoice.speak("Nomor Antrian " + res.antrian.prefix + "-" +
                                res.antrian.nomor +
                                ", ke loket " + res.loket.kode_loket, "Indonesian Female", {
                                    rate: 0.9,
                                    pitch: 1,
                                    volume: 1
                                });
                            $("#antrian").text(res.antrian.prefix + " " + res.antrian.nomor);
                            $("#jumlah").text(res.jumlah);
                        }
                    }
                });
            });

            $("#btn-buatPasienBaru").click(function() {
                $("#error").css("display", "none");
                $("#jenis_identitas").val("");
                $("#no_identitas").val(null);
                $("#name_pasien").val(null);
                $("#jenis_kelamin").val("");
                $("#tgl_lahir").val(null);
                $("#golongan_darah").val("");
                $("#kewarganegaraan").val(1);
                $("#pekerjaan").val("");
                $("#status_menikah").val("");
                $("#agama").val("");
                $("#no_hp").val(null);
                $("#no_telepon").val(null);
                $("#mr_lama").val(null);
                $("#alamat").val(null);
                $("#province").val("");
                $("#city").val(null);

            });

            $("#btn-simpan").click(function() {
                $.ajax({
                    url: "{{ route('pendaftaran.store_pasien') }}",
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        jenis_identitas: $("#jenis_identitas").val(),
                        no_identitas: $("#no_identitas").val(),
                        name_pasien: $("#name_pasien").val(),
                        jenis_kelamin: $("#jenis_kelamin").val(),
                        tgl_lahir: $("#tgl_lahir").val(),
                        golongan_darah: $("#golongan_darah").val(),
                        kewarganegaraan: $("#kewarganegaraan").val(),
                        pekerjaan: $("#pekerjaan").val(),
                        status_menikah: $("#status_menikah").val(),
                        agama: $("#agama").val(),
                        no_hp: $("#no_hp").val(),
                        no_telepon: $("#no_telepon").val(),
                        mr_lama: $("#mr_lama").val(),
                        alamat: $("#alamat").val(),
                        province: $("#province").val(),
                        city: $("#city").val()
                    },

                    success: function(res) {
                        if ($.isEmptyObject(res.error)) {
                            $("#error").css("display", "none");
                            if (res.status == false) {
                                swal(res.text, {
                                    icon: "error",
                                });
                            } else {
                                swal(res.text, {
                                    icon: "success",
                                });
                                $("#jenis_identitas").val("");
                                $("#no_identitas").val(null);
                                $("#name_pasien").val(null);
                                $("#jenis_kelamin").val("");
                                $("#tgl_lahir").val(null);
                                $("#golongan_darah").val("");
                                $("#kewarganegaraan").val(1);
                                $("#pekerjaan").val("");
                                $("#status_menikah").val("");
                                $("#agama").val("");
                                $("#no_hp").val(null);
                                $("#no_telepon").val(null);
                                $("#mr_lama").val(null);
                                $("#alamat").val(null);
                                $("#province").val("");
                                $("city").val(null);
                                $("#btn-kembali").click();

                            }
                        } else {
                            error(res.error)
                        }
                    }
                });

            });

            function error(msg) {
                $("#error").find("ul").html('');
                $("#error").css('display', 'block');
                $.each(msg, function(key, value) {
                    $("#error").find("ul").append('<li>' + value + '</li>');
                });
            }


        });
        document.getElementById('province').addEventListener('change', function() {
            var provinceId = this.value;
            let url = "{{ route('wilayah.city', ':code') }}";
            url = url.replace(':code', provinceId)
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    var cityDropdown = document.getElementById('city');
                    cityDropdown.innerHTML = '';
                    data.forEach(function(city) {
                        var option = document.createElement('option');
                        option.value = city.code;
                        option.textContent = city.name;
                        cityDropdown.appendChild(option);
                    });
                });
        });

        document.getElementById('province_edit').addEventListener('change', function() {
            var provinceId = this.value;
            let url = "{{ route('wilayah.city', ':code') }}";
            url = url.replace(':code', provinceId)
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    var cityDropdown = document.getElementById('city_edit');
                    cityDropdown.innerHTML = '';
                    data.forEach(function(city) {
                        var option = document.createElement('option');
                        option.value = city.code;
                        option.textContent = city.name;
                        cityDropdown.appendChild(option);
                    });
                });
        });
        $(document).on('click', '.edit', function() {
            let id = $(this).attr('id');
            let url = "{{ route('pendaftaran.editPasien', ':id') }}"
            url = url.replace(':id', id);
            $("#error-edit").css("display", "none");
            $.ajax({
                url: url,
                type: 'GET',
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(res) {
                    $("#jenis_identitas_edit").val(res.data.jenis_identitas);
                    $("#no_identitas_edit").val(res.data.no_identitas);
                    $("#name_pasien_edit").val(res.data.name);
                    $("#jenis_kelamin_edit").val(res.data.jenis_kelamin);
                    $("#tgl_lahir_edit").val(res.data.tgl_lahir);
                    $("#golongan_darah_edit").val(res.data.golongan_darah);
                    $("#kewarganegaraan_edit").val(res.data.kewarganegaraan);
                    $("#pekerjaan_edit").val(res.data.pekerjaan);
                    $("#status_menikah_edit").val(res.data.status_menikah);
                    $("#agama_edit").val(res.data.agama);
                    $("#no_hp_edit").val(res.data.no_hp);
                    $("#no_telepon_edit").val(res.data.no_telepon);
                    $("#mr_lama_edit").val(res.data.mr_lama);
                    $("#alamat_edit").val(res.data.alamat);
                    $("#province_edit").val(res.data.province_code);
                    $("#id").val(res.data.id);
                    let url = "{{ route('wilayah.city', ':code') }}";
                    url = url.replace(':code', res.data.province_code);
                    let city_code = res.data.city_code;
                    $.ajax({
                        url: url,
                        type: 'GET',
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            $.each(response, function(id, item) {
                                if (city_code == item.code) {
                                    $("#city_edit").append("<option value=" + item
                                        .code +
                                        " selected>" + item.name +
                                        "</option>");
                                } else {
                                    $("#city_edit").append("<option value=" + item
                                        .code +
                                        ">" + item.name +
                                        "</option>");
                                }
                            })
                        }
                    })
                }
            })
        });

        $("#btn-edit").click(function() {
            let url = "{{ route('pendaftaran.updatePasien', ':id') }}"
            url = url.replace(':id', $("#id").val());
            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    jenis_identitas: $("#jenis_identitas_edit").val(),
                    no_identitas: $("#no_identitas_edit").val(),
                    name_pasien: $("#name_pasien_edit").val(),
                    jenis_kelamin: $("#jenis_kelamin_edit").val(),
                    tgl_lahir: $("#tgl_lahir_edit").val(),
                    golongan_darah: $("#golongan_darah_edit").val(),
                    kewarganegaraan: $("#kewarganegaraan_edit").val(),
                    pekerjaan: $("#pekerjaan_edit").val(),
                    status_menikah: $("#status_menikah_edit").val(),
                    agama: $("#agama_edit").val(),
                    no_hp: $("#no_hp_edit").val(),
                    no_telepon: $("#no_telepon_edit").val(),
                    mr_lama: $("#mr_lama_edit").val(),
                    alamat: $("#alamat_edit").val(),
                    province: $("#province_edit").val(),
                    city: $("#city_edit").val()
                },

                success: function(res) {
                    if ($.isEmptyObject(res.error)) {
                        $("#error-edit").css("display", "none");
                        if (res.status == false) {
                            swal(res.text, {
                                icon: "error",
                            });
                        } else {
                            $("#btn-edit-kembali").click();
                            swal(res.text, {
                                icon: "success",
                            });

                        }
                    } else {
                        error_edit(res.error)
                    }
                }
            });
        });

        function error_edit(msg) {
            $("#error-edit").find("ul").html('');
            $("#error-edit").css('display', 'block');
            $.each(msg, function(key, value) {
                $("#error-edit").find("ul").append('<li>' + value + '</li>');
            });
        }

        $(document).on('click', '.rawatJalan', function() {
            let id = $(this).attr('id');
            let url = "{{ route('pendaftaran.showPasien', ':id') }}"
            url = url.replace(':id', id);
            $("#error-rawatJalan").css("display", "none");
            $("#jenis_jaminan").val("");
            $("#dokter").val("");
            $("#tujuan_kunjungan").val("");
            $("#btn-submit-rawatJalan").text("Simpan");
            $.ajax({
                url: url,
                type: 'GET',
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(res) {
                    $("#no_rm_rawatJalan").text(res.data.rekam_medis);
                    $("#name_rawatJalan").text(res.data.name);
                    $("#tgl_lahir_rawatJalan").text(res.data.umur);
                    $("#id-rawatJalan").val(id);
                    $("#status-pasien").text(res.data.status);
                    $("#no_encounter_rawatJalan").text(res.data.no_encounter);
                    $("#created_rawatJalan").text(res.data.tgl_encounter);
                    $("#type_rawatJalan").text(res.data.type);
                }
            })
        });
        $("#btn-submit-rawatJalan").on('click', function() {
            if ($(this).text() === 'Update Rawat Jalan') {
                update_rawatJalan();
            } else {
                submit_rawatJalan();
            }
        })

        function submit_rawatJalan() {
            let url = "{{ route('pendaftaran.postRawatJalan', ':id') }}"
            url = url.replace(':id', $("#id-rawatJalan").val());

            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    jenis_jaminan: $("#jenis_jaminan").val(),
                    dokter: $("#dokter").val(),
                    tujuan_kunjungan: $("#tujuan_kunjungan").val(),
                },

                success: function(res) {
                    if ($.isEmptyObject(res.error)) {
                        $("#error-rawatJalan").css("display", "none");
                        if (res.status == false) {
                            swal(res.text, {
                                icon: "error",
                            });
                        } else {
                            $("#tab-rawatJalan").click();
                            $("#btn-tutup-rawatJalan").click();
                            swal(res.text, {
                                icon: "success",
                            });
                        }
                    } else {
                        error_rawatJalan(res.error)
                    }
                }
            });
        }

        function update_rawatJalan() {
            let url = "{{ route('pendaftaran.updateRawatJalan', ':id') }}"
            url = url.replace(':id', $("#id-rawatJalan").val());

            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    jenis_jaminan: $("#jenis_jaminan").val(),
                    dokter: $("#dokter").val(),
                    tujuan_kunjungan: $("#tujuan_kunjungan").val(),
                },

                success: function(res) {
                    if ($.isEmptyObject(res.error)) {
                        $("#error-rawatJalan").css("display", "none");
                        if (res.status == false) {
                            swal(res.text, {
                                icon: "error",
                            });
                        } else {
                            $("#tab-rawatJalan").click();
                            $("#btn-tutup-rawatJalan").click();
                            swal(res.text, {
                                icon: "success",
                            });
                        }
                    } else {
                        error_rawatJalan(res.error)
                    }
                }
            });
        }

        $(document).on('click', '.editrawatJalan', function() {
            let id = $(this).attr('id');
            let url = "{{ route('pendaftaran.editEncounterRajal', ':id') }}"
            url = url.replace(':id', id);
            $("#error-rawatJalan").css("display", "none");
            $("#jenis_jaminan").val("");
            $("#dokter").val("");
            $("#tujuan_kunjungan").val("");
            $.ajax({
                url: url,
                type: 'GET',
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(res) {
                    $("#no_rm_rawatJalan").text(res.data.rekam_medis);
                    $("#name_rawatJalan").text(res.data.name_pasien);
                    $("#tgl_lahir_rawatJalan").text(res.data.umur);
                    $("#id-rawatJalan").val(id);
                    $("#status-pasien").text(res.data.status);
                    $("#no_encounter_rawatJalan").text(res.data.no_encounter);
                    $("#created_rawatJalan").text(res.data.tgl_encounter);
                    $("#type_rawatJalan").text(res.data.type);
                    $("#jenis_jaminan").val(res.data.jenis_jaminan);
                    $("#dokter").val(res.data.dokter);
                    $("#tujuan_kunjungan").val(res.data.tujuan_kunjungan);
                    $("#btn-submit-rawatJalan").text("Update Rawat Jalan");
                    $("#error-rawatJalan").css("display", "none");
                }
            })
        });

        function error_rawatJalan(msg) {
            $("#error-rawatJalan").find("ul").html('');
            $("#error-rawatJalan").css('display', 'block');
            $.each(msg, function(key, value) {
                $("#error-rawatJalan").find("ul").append('<li>' + value + '</li>');
            });
        }

        $(document).on('click', '.destoryRawatJalan', function() {
            let id = $(this).attr('id');
            let url = "{{ route('pendaftaran.destroyEncounterRajal', ':id') }}"
            url = url.replace(':id', id);
            swal({
                title: "Apakah Anda Yakin?",
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        data: {
                            _token: "{{ csrf_token() }}",
                        },
                        success: function(res) {
                            if (res.status == true) {
                                $("#tab-rawatJalan").click();
                                swal(res.text, {
                                    icon: "success",
                                });
                            } else {
                                swal(res.text, {
                                    icon: "error",
                                });
                            }
                        }
                    });
                }
            });
        });

        $(document).on('click', '.rawatInap', function() {
            let id = $(this).attr('id');
            let url = "{{ route('pendaftaran.showPasien', ':id') }}"
            url = url.replace(':id', id);
            $("#error-rawatInap").css("display", "none");
            $("#jenis_jaminan_rawatInap").val("");
            $("#dokter_rawatInap").val("");
            $("#tujuan_kunjungan_rawatInap").val("");
            $("#btn-submit-rawatInap").text("Simpan");
            $.ajax({
                url: url,
                type: 'GET',
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(res) {
                    $("#no_rm_rawatInap").text(res.data.rekam_medis);
                    $("#name_rawatInap").text(res.data.name);
                    $("#tgl_lahir_rawatInap").text(res.data.umur);
                    $("#id-rawatInap").val(id);
                    $("#status-pasien-rawatInap").text(res.data.status);
                    $("#no_encounter_rawatInap").text(res.data.no_encounter);
                    $("#created_rawatInap").text(res.data.tgl_encounter);
                    $("#type_rawatInap").text(res.data.type);
                }
            });
        });
        $("#btn-submit-rawatInap").on('click', function() {
            if ($(this).text() === 'Update Rawat Inap') {
                update_rawatInap();
            } else {
                submit_rawatInap();
            }
        });
        $(document).on('click', '.igd', function() {
            let id = $(this).attr('id');
            let url = "{{ route('pendaftaran.showPasien', ':id') }}"
            url = url.replace(':id', id);
            $("#error-rawatDarurat").css("display", "none");
            $("#jenis_jaminan_rawatDarurat").val("");
            $("#dokter_rawatDarurat").val("");
            $("#tujuan_kunjungan_rawatDarurat").val("");
            $("#btn-submit-rawatDarurat").text("Simpan");
            $.ajax({
                url: url,
                type: 'GET',
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(res) {
                    $("#no_rm_rawatDarurat").text(res.data.rekam_medis);
                    $("#name_rawatDarurat").text(res.data.name);
                    $("#tgl_lahir_rawatDarurat").text(res.data.umur);
                    $("#id-rawatDarurat").val(id);
                    $("#status-pasien-rawatDarurat").text(res.data.status);
                    $("#no_encounter_rawatDarurat").text(res.data.no_encounter);
                    $("#created_rawatDarurat").text(res.data.tgl_encounter);
                    $("#type_rawatDarurat").text(res.data.type);
                }
            });
        });
        $("#btn-submit-rawatDarurat").on('click', function() {
            if ($(this).text() === 'Update Rawat Darurat') {
                update_rawatDarurat();
            } else {
                submit_rawatDarurat();
            }
        });

        function submit_rawatDarurat() {
            let url = "{{ route('pendaftaran.postRawatDarurat', ':id') }}"
            url = url.replace(':id', $("#id-rawatDarurat").val());

            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    jenis_jaminan: $("#jenis_jaminan_rawatDarurat").val(),
                    dokter: $("#dokter_rawatDarurat").val(),
                    tujuan_kunjungan: $("#tujuan_kunjungan_rawatDarurat").val(),
                },

                success: function(res) {
                    if ($.isEmptyObject(res.error)) {
                        $("#error-rawatDarurat").css("display", "none");
                        if (res.status == false) {
                            swal(res.text, {
                                icon: "error",
                            });
                        } else {
                            $("#tab-igd").click();
                            $("#btn-tutup-rawatDarurat").click();
                            swal(res.text, {
                                icon: "success",
                            });
                        }
                    } else {
                        error_rawatDarurat(res.error)
                    }
                }
            });
        }

        function update_rawatDarurat() {
            let url = "{{ route('pendaftaran.updateRawatDarurat', ':id') }}"
            url = url.replace(':id', $("#id-rawatDarurat").val());

            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    jenis_jaminan: $("#jenis_jaminan_rawatDarurat").val(),
                    dokter: $("#dokter_rawatDarurat").val(),
                    tujuan_kunjungan: $("#tujuan_kunjungan_rawatDarurat").val(),
                },

                success: function(res) {
                    if ($.isEmptyObject(res.error)) {
                        $("#error-rawatDarurat").css("display", "none");
                        if (res.status == false) {
                            swal(res.text, {
                                icon: "error",
                            });
                        } else {
                            $("#tab-igd").click();
                            $("#btn-tutup-rawatDarurat").click();
                            swal(res.text, {
                                icon: "success",
                            });
                        }
                    } else {
                        error_rawatDarurat(res.error)
                    }
                }
            });
        }
        $(document).on('click', '.editrawatDarurat', function() {
            let id = $(this).attr('id');
            let url = "{{ route('pendaftaran.editEncounterRdarurat', ':id') }}"
            url = url.replace(':id', id);
            $("#error-rawatDarurat").css("display", "none");
            $("#jenis_jaminan_rawatDarurat").val("");
            $("#dokter_rawatDarurat").val("");
            $("#tujuan_kunjungan_rawatDarurat").val("");
            $.ajax({
                url: url,
                type: 'GET',
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(res) {
                    console.log(res);
                    $("#no_rm_rawatDarurat").text(res.data.rekam_medis);
                    $("#name_rawatDarurat").text(res.data.name_pasien);
                    $("#tgl_lahir_rawatDarurat").text(res.data.umur);
                    $("#id-rawatDarurat").val(id);
                    $("#status-pasien-rawatDarurat").text(res.data.status);
                    $("#no_encounter_rawatDarurat").text(res.data.no_encounter);
                    $("#created_rawatDarurat").text(res.data.tgl_encounter);
                    $("#type_rawatDarurat").text(res.data.type);
                    $("#jenis_jaminan_rawatDarurat").val(res.data.jenis_jaminan);
                    $("#dokter_rawatDarurat").val(res.data.dokter);
                    $("#tujuan_kunjungan_rawatDarurat").val(res.data.tujuan_kunjungan);
                    $("#btn-submit-rawatDarurat").text("Update Rawat Darurat");
                    $("#error-rawatDarurat").css("display", "none");
                }
            })
        });
        $(document).on('click', '.destroyRawatDarurat', function() {
            let id = $(this).attr('id');
            let url = "{{ route('pendaftaran.destroyEncounterRdarurat', ':id') }}"
            url = url.replace(':id', id);
            swal({
                title: "Apakah Anda Yakin?",
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        data: {
                            _token: "{{ csrf_token() }}",
                        },
                        success: function(res) {
                            if (res.status == true) {
                                $("#tab-igd").click();
                                swal(res.text, {
                                    icon: "success",
                                });
                            } else {
                                swal(res.text, {
                                    icon: "error",
                                });
                            }
                        }
                    });
                }
            });
        });
        $(document).on('click', '.editRawatDarurat', function() {
            let id = $(this).attr('id');
            let url = "{{ route('pendaftaran.editEncounterRdarurat', ':id') }}"
            url = url.replace(':id', id);
            $("#error-rawatDarurat").css("display", "none");
            $("#jenis_jaminan_rawatDarurat").val("");
            $("#dokter_rawatDarurat").val("");
            $("#tujuan_kunjungan_rawatDarurat").val("");
            $.ajax({
                url: url,
                type: 'GET',
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(res) {
                    $("#no_rm_rawatDarurat").text(res.data.rekam_medis);
                    $("#name_rawatDarurat").text(res.data.name_pasien);
                    $("#tgl_lahir_rawatDarurat").text(res.data.umur);
                    $("#id-rawatDarurat").val(id);
                    $("#status-pasien-rawatDarurat").text(res.data.status);
                    $("#no_encounter_rawatDarurat").text(res.data.no_encounter);
                    $("#created_rawatDarurat").text(res.data.tgl_encounter);
                    $("#type_rawatDarurat").text(res.data.type);
                    $("#jenis_jaminan_rawatDarurat").val(res.data.jenis_jaminan);
                    $("#dokter_rawatDarurat").val(res.data.dokter);
                    $("#tujuan_kunjungan_rawatDarurat").val(res.data.tujuan_kunjungan);
                    $("#btn-submit-rawatDarurat").text("Update Rawat Darurat");
                    $("#error-rawatDarurat").css("display", "none");
                }
            })
        });

        function error_rawatDarurat(msg) {
            $("#error-rawatDarurat").find("ul").html('');
            $("#error-rawatDarurat").css('display', 'block');
            $.each(msg, function(key, value) {
                $("#error-rawatDarurat").find("ul").append('<li>' + value + '</li>');
            });
        }
    </script>
@endpush
