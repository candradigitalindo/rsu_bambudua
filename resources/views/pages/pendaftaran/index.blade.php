@extends('layouts.app')
@section('title', 'Pendaftaran')
@push('style')
    <!-- Existing CSS -->
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/dropzone/dropzone.min.css') }}">
    <style>
    /* Simple fix for modal backdrop issue */
    body:not(.modal-open) .modal-backdrop {
        display: none !important;
    }
    </style>
@endpush
@section('content')
    <div class="row gx-3">
        <!-- Row starts -->

        <div class="col-sm-9 col-12">
            <div class="card border mb-3">
                <div class="card-body">
                    <div class="card-info rounded-1 small lh-1">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-pasien"
                            id="loket">
                            <i class="ri-folder-user-fill"></i>
                            <span class="btn-text" id="text-loket">Buka Data Pasien</span>
                            <span class="spinner-border spinner-border-sm d-none" id="spiner-loket"></span>

                        </button>
                    </div>
                    {{-- Modal Pencarian Pasien dengan Komponen Baru --}}
                    <x-modal id="modal-pasien" 
                             title="Data Pasien" 
                             icon="ri-folder-user-fill"
                             size="modal-xl" 
                             scrollable 
                             backdrop="static" 
                             keyboard="false">
                        
                        {{-- Advanced Search Component --}}
                        <x-search.advanced 
                            name="name"
                            placeholder="Cari Nama, RM, RM Lama, No HP, KTP..."
                            ajax-url="{{ route('pendaftaran.caripasien') }}"
                            result-container="data"
                            :show-button="false"
                            debounce="300"
                            min-length="2"
                        />
                        
                        {{-- Search Results --}}
                        <div id="data" class="search-results">
                            {{-- Results will be populated via AJAX --}}
                        </div>
                        
                        {{-- Loading Component --}}
                        @include('components.loading', [
                            'id' => 'search-loading',
                            'message' => 'Mencari data pasien...',
                            'style' => 'display: none;'
                        ])

                        <x-slot name="footerButtons">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#form-pasien" id="btn-buatPasienBaru">
                                <i class="ri-user-add-fill"></i>
                                <span class="btn-text">Buat Data Pasien Baru</span>
                                <span class="spinner-border spinner-border-sm d-none"></span>
                            </button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        </x-slot>
                    </x-modal>
                    {{-- Modal Edit Pasien dengan Komponen Baru --}}
                    <x-modal id="form-edit-pasien" 
                             title="Form Edit Data Pasien" 
                             icon="ri-user-edit-line"
                             size="modal-xl" 
                             scrollable 
                             backdrop="static" 
                             keyboard="false">
                        
                        {{-- Error Alert Component --}}
                        <div id="error-edit-container"></div>
                        
                        <form id="formpasien">
                            <div class="row gx-3">
                                <div class="col-xxl-2 col-lg-4 col-sm-6">
                                    <x-form.select 
                                        name="jenis_identitas"
                                        label="Jenis Identitas"
                                        placeholder="Pilih Jenis Identitas"
                                        :options="[
                                            '1' => 'KTP',
                                            '2' => 'SIM', 
                                            '3' => 'Paspor'
                                        ]"
                                        id="jenis_identitas_edit"
                                    />
                                </div>
                                <div class="col-xxl-5 col-lg-4 col-sm-6">
                                    <x-form.input 
                                        name="no_identitas"
                                        label="Nomor Identitas"
                                        id="no_identitas_edit"
                                    />
                                </div>
                                <div class="col-xxl-5 col-lg-4 col-sm-6">
                                    <x-form.input 
                                        name="name_pasien"
                                        label="Nama Pasien"
                                        required
                                        id="name_pasien_edit"
                                    />
                                </div>
                            </div>

                            <div class="row gx-3">
                                <div class="col-xxl-4 col-lg-4 col-sm-6">
                                    <x-form.select 
                                        name="jenis_kelamin"
                                        label="Jenis Kelamin"
                                        placeholder="Pilih Jenis Kelamin"
                                        :options="[
                                            '1' => 'Pria',
                                            '2' => 'Wanita'
                                        ]"
                                        id="jenis_kelamin_edit"
                                        required
                                    />
                                </div>
                                <div class="col-xxl-4 col-lg-4 col-sm-6">
                                    <x-form.input 
                                        name="tgl_lahir"
                                        type="date"
                                        label="Tanggal Lahir"
                                        id="tgl_lahir_edit"
                                        required
                                    />
                                </div>
                                <div class="col-xxl-4 col-lg-4 col-sm-6">
                                    <x-form.select 
                                        name="golongan_darah"
                                        label="Golongan Darah"
                                        placeholder="-- Pilih Gol Darah --"
                                        :options="[
                                            'A' => 'A',
                                            'B' => 'B',
                                            'AB' => 'AB',
                                            'O' => 'O'
                                        ]"
                                        id="golongan_darah_edit"
                                    />
                                </div>
                            </div>

                            {{-- Continue with remaining form fields --}}
                            <div class="row gx-3">
                                <div class="col-xxl-4 col-lg-4 col-sm-6">
                                    <x-form.select 
                                        name="kewarganegaraan"
                                        label="Kewarganegaraan"
                                        :options="[
                                            '1' => 'WNI',
                                            '2' => 'WNA'
                                        ]"
                                        id="kewarganegaraan_edit"
                                    />
                                </div>
                                <div class="col-xxl-4 col-lg-4 col-sm-6">
                                    <x-form.select 
                                        name="pekerjaan"
                                        label="Pekerjaan"
                                        placeholder="-- Pilih Pekerjaan --"
                                        :options="$pekerjaan->pluck('name', 'name')"
                                        id="pekerjaan_edit"
                                    />
                                </div>
                                <div class="col-xxl-4 col-lg-4 col-sm-6">
                                    <x-form.select 
                                        name="status_menikah"
                                        label="Status Menikah"
                                        placeholder="-- Pilih Status --"
                                        :options="[
                                            '1' => 'Belum Menikah',
                                            '2' => 'Menikah',
                                            '3' => 'Cerai Hidup',
                                            '4' => 'Cerai Mati'
                                        ]"
                                        id="status_menikah_edit"
                                    />
                                </div>
                            </div>

                            <div class="row gx-3">
                                <div class="col-xxl-4 col-lg-4 col-sm-6">
                                    <x-form.select 
                                        name="agama"
                                        label="Agama"
                                        placeholder="-- Pilih Agama --"
                                        :options="$agama->pluck('name', 'name')"
                                        id="agama_edit"
                                    />
                                </div>
                                <div class="col-xxl-4 col-lg-4 col-sm-6">
                                    <x-form.input 
                                        name="no_hp"
                                        type="tel"
                                        label="No Handphone"
                                        required
                                        id="no_hp_edit"
                                    />
                                </div>
                                <div class="col-xxl-4 col-lg-4 col-sm-6">
                                    <x-form.input 
                                        name="no_telepon"
                                        type="tel"
                                        label="No Telepon"
                                        id="no_telepon_edit"
                                    />
                                </div>
                            </div>

                            <div class="row gx-3">
                                <div class="col-xxl-4 col-lg-4 col-sm-6">
                                    <x-form.input 
                                        name="alamat"
                                        label="Alamat"
                                        required
                                        id="alamat_edit"
                                    />
                                </div>
                                <div class="col-xxl-4 col-lg-4 col-sm-6">
                                    <x-form.select 
                                        name="province"
                                        label="Provinsi"
                                        placeholder="-- Pilih Provinsi --"
                                        :options="$provinsi->pluck('name', 'code')"
                                        id="province_edit"
                                    />
                                </div>
                                <div class="col-xxl-4 col-lg-4 col-sm-6">
                                    <x-form.select 
                                        name="city"
                                        label="Kota / Kabupaten"
                                        placeholder="-- Pilih Provinsi dulu --"
                                        :options="[]"
                                        id="city_edit"
                                    />
                                </div>
                            </div>

                            <input type="hidden" id="id">
                        </form>

                        <x-slot name="footerButtons">
                            <button type="button" class="btn btn-secondary" data-bs-target="#modal-pasien"
                                data-bs-toggle="modal" id="btn-edit-kembali">
                                Kembali
                            </button>
                            <button type="button" class="btn btn-primary" id="btn-edit">
                                <span class="btn-text">Simpan</span>
                                <span class="spinner-border spinner-border-sm d-none"></span>
                            </button>
                        </x-slot>
                    </x-modal>
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
                                        <div class="col-xxl-3 col-lg-4 col-sm-6">
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

                                        <div class="col-xxl-3 col-lg-4 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="a1">Poliklinik</label>
                                                <div class="input-group">
                                                    <select class="form-select" name="clinic" id="clinic">
                                                        <option value="">-- Pilih Poliklinik --</option>
                                                        @foreach ($clinics as $clinic)
                                                            <option value="{{ $clinic->id }}">{{ $clinic->nama }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="col-xxl-3 col-lg-4 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="dokter">Dokter</label>
                                                <div class="input-group">
                                                    <select class="form-select" name="dokter[]" id="dokter" multiple>
                                                        <option value="">-- Pilih Dokter --</option>
                                                        {{-- Akan diisi via JS --}}
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xxl-3 col-lg-4 col-sm-6">
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
                                                    id="status-pasien-rawatRinap"></a></span>
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
                                                            <td id="no_rm_rawatRinap">XXXXXXXXXXX</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Nama Pasien</td>
                                                            <td>:</td>
                                                            <td id="name_rawatRinap">XXXXXXXXXXX</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Umur</td>
                                                            <td>:</td>
                                                            <td id="tgl_lahir_rawatRinap">XXXXXXXXXXX</td>
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
                                                            <td id="no_encounter_rawatRinap">XXXXXXXXXX</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Tanggal Kujungan</td>
                                                            <td>:</td>
                                                            <td id="created_rawatRinap">XXXXXXXXXXX</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Tipe</td>
                                                            <td>:</td>
                                                            <td id="type_rawatRinap">XXXXXXXXXXX</td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="alert alert-danger print-error-msg mt-2 mb-2" style="display:none"
                                        id="error-rawatRinap">
                                        <ul></ul>
                                    </div>
                                    <hr>
                                    <div class="row gx-3 mt-3">
                                        <div class="col-xxl-4 col-lg-4 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="a7">Jenis Jaminan</label>
                                                <span class="text-danger">*</span>
                                                <div class="input-group">
                                                    <select class="form-select" name="jenis_jaminan_rawatRinap"
                                                        id="jenis_jaminan_rawatRinap">
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
                                                <label class="form-label" for="dokter_rawatRinap">Dokter</label>
                                                <span class="text-danger">*</span>
                                                <div class="input-group">
                                                    <select class="form-select" name="dokter_rawatRinap"
                                                        id="dokter_rawatRinap">
                                                        <option value="">-- Pilih Dokter --</option>
                                                        @foreach ($doctors as $doctor)
                                                            <option value="{{ $doctor->id }}">{{ $doctor->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="col-xxl-4 col-lg-4 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="a1">Ruang Rawat Inap
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <div class="input-group">
                                                    <select class="form-select" name="ruangan" id="ruangan_rawatRinap">
                                                        <option value="">-- Pilih Ruangan --</option>
                                                        @foreach ($ruangan as $ruang)
                                                            <option value="{{ $ruang->id }}">{{ $ruang->no_kamar }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                            </div>
                                        </div>
                                        <input type="text" style="visibility: hidden" id="id-rawatRinap">
                                    </div>
                                    <hr>
                                    <div class="row gx-3">
                                        <div class="col-xxl-3 col-lg-4 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="a7">Nama Pendamping
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" id="name_companion"
                                                        placeholder="Nama Pendamping">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xxl-3 col-lg-4 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="a7">NIK Pendamping

                                                </label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" id="nik_companion"
                                                        placeholder="NIK Pendamping">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xxl-3 col-lg-4 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="a7">No Handphone Pendamping
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" id="phone_companion"
                                                        placeholder="No Handphone Pendamping">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xxl-3 col-lg-4 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="a7">Hubungan Pendamping
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <div class="input-group">
                                                    <select class="form-select" name="relation_companion"
                                                        id="relation_companion">
                                                        <option value="">-- Pilih Hubungan --</option>
                                                        <option value="1">Ayah</option>
                                                        <option value="2">Ibu</option>
                                                        <option value="3">Saudara</option>
                                                        <option value="4">Suami</option>
                                                        <option value="5">Istri</option>
                                                        <option value="6">Lainnya</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-primary" id="btn-submit-rawatRinap">
                                        <i class="ri-user-add-fill"></i>
                                        Simpan
                                    </button>

                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                                        id="btn-tutup-rawatRinap">
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
                                                        <th>Keterangan</th>
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
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

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
    @include('components.scripts.utils')
    <script>
        $(document).ready(function() {
            
            // Simple modal backdrop cleanup
            $('.modal').on('hidden.bs.modal', function () {
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open').css('padding-right', '').css('overflow', '');
            });

            // Initialize Select2 untuk single select
            $('#dokter_rawatRinap').select2({
                placeholder: "Pilih Dokter",
                allowClear: true,
                width: '100%'
            });

            // [FIX] Initialize Select2 untuk dokter, pastikan modalnya juga di-handle
            $('#dokter').select2({
                placeholder: "Pilih satu atau lebih dokter",
                allowClear: true,
                width: '100%',
                dropdownParent: $('#modal-rawatJalan') // Penting untuk select2 di dalam modal
            });
            $('#dokter_rawatDarurat').select2({
                placeholder: "Pilih satu atau lebih dokter",
                width: '100%',
                dropdownParent: $('#modal-rawatDarurat')
            });

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
                    const data = res.data;
                    $("#id").val(data.id);
                    $("#jenis_identitas_edit").val(data.jenis_identitas);
                    $("#no_identitas_edit").val(data.no_identitas);
                    $("#name_pasien_edit").val(data.name);
                    $("#jenis_kelamin_edit").val(data.jenis_kelamin);
                    $("#tgl_lahir_edit").val(data.tgl_lahir);
                    $("#golongan_darah_edit").val(data.golongan_darah);
                    $("#kewarganegaraan_edit").val(data.kewarganegaraan);
                    $("#pekerjaan_edit").val(data.pekerjaan);
                    $("#status_menikah_edit").val(data.status_menikah);
                    $("#agama_edit").val(data.agama);
                    $("#no_hp_edit").val(data.no_hp);
                    $("#no_telepon_edit").val(data.no_telepon);
                    $("#mr_lama_edit").val(data.mr_lama);
                    $("#alamat_edit").val(data.alamat);

                    // [FIX] Logic for province and city dropdowns
                    const provinceDropdown = $('#province_edit');
                    const cityDropdown = $('#city_edit');

                    // Store the target city code
                    cityDropdown.data('selected-city', data.city_code);

                    // Set province and trigger change to load cities
                    provinceDropdown.val(data.province_code).trigger('change');
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
            $("#clinic").val("").trigger('change'); // [FIX] Reset clinic
            $("#jenis_jaminan").val("");
            $("#dokter").val(null).trigger('change'); // Clear multiple selection
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

            var selectedDoctors = $("#dokter").val();

            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    jenis_jaminan: $("#jenis_jaminan").val(),
                    dokter: selectedDoctors,
                    clinic_id: $("#clinic").val(),
                    tujuan_kunjungan: $("#tujuan_kunjungan").val(),
                },
                success: function(res) {
                    if ($.isEmptyObject(res.error)) {
                        $("#error-rawatJalan").css("display", "none");
                        if (res.status == false) {
                            swal(res.text, {
                                icon: "error"
                            });
                        } else {
                            $("#tab-rawatJalan").click();
                            $("#btn-tutup-rawatJalan").click();
                            swal(res.text, {
                                icon: "success"
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
                    clinic_id: $("#clinic").val(),
                    tujuan_kunjungan: $("#tujuan_kunjungan").val()
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
            $("#dokter").val(null).trigger('change'); // Clear multiple selection
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
                    $("#tujuan_kunjungan").val(res.data.tujuan_kunjungan);
                    $("#btn-submit-rawatJalan").text("Update Rawat Jalan");
                    $("#clinic").val(res.data.clinic_id).trigger(
                        'change'); // trigger agar dokter terupdate
                    $("#dokter").data('selected', res.data.dokter_ids); // [FIX] Use dokter_ids (array)
                    $("#error-rawatJalan").css("display", "none");
                }
            })
        });

        // [NEW] Handle edit for Rawat Darurat (IGD)
        $(document).on('click', '.editrawatDarurat', function() {
            let id = $(this).attr('id');
            let url = "{{ route('pendaftaran.editEncounterRdarurat', ':id') }}";
            url = url.replace(':id', id);

            // Reset form
            $("#error-rawatDarurat").css("display", "none");
            $("#jenis_jaminan_rawatDarurat").val("");
            $("#tujuan_kunjungan_rawatDarurat").val("");
            $("#btn-submit-rawatDarurat").text("Update Rawat Darurat");

            $.get(url, function(res) {
                const data = res.data;
                $("#no_rm_rawatDarurat").text(data.rekam_medis);
                $("#name_rawatDarurat").text(data.name_pasien);
                $("#tgl_lahir_rawatDarurat").text(data.umur);
                $("#id-rawatDarurat").val(id);
                $("#status-pasien-rawatDarurat").text(data.status);
                $("#no_encounter_rawatDarurat").text(data.no_encounter);
                $("#created_rawatDarurat").text(data.tgl_encounter);
                $("#type_rawatDarurat").text(data.type);
                $("#jenis_jaminan_rawatDarurat").val(data.jenis_jaminan);
                $("#tujuan_kunjungan_rawatDarurat").val(data.tujuan_kunjungan);
                $("#dokter_rawatDarurat").val(data.dokter_ids).trigger('change');
            });
        });

        function error_rawatJalan(msg) {
            $("#error-rawatJalan").find("ul").html('');
            $("#error-rawatJalan").css('display', 'block');
            $.each(msg, function(key, value) {
                $("#error-rawatJalan").find("ul").append('<li>' + value + '</li>');
            });
        }

        $(document).on('click', '.rawatInap', function() {
            let id = $(this).attr('id');
            let url = "{{ route('pendaftaran.showPasien', ':id') }}"
            url = url.replace(':id', id);
            $("#error-rawatInap").css("display", "none");
            $("#jenis_jaminan_rawatInap").val("");
            $("#dokter_rawatInap").val(null).trigger('change'); // Clear multiple selection
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
                    $("#id-rawatRinap").val(id);
                    $("#status-pasien-rawatRinap").text(res.data.status);
                    $("#no_encounter_rawatRinap").text(res.data.no_encounter);
                    $("#created_rawatRinap").text(res.data.tgl_encounter);
                    $("#type_rawatRinap").text(res.data.type);
                }
            });
        });
        $("#btn-submit-rawatInap").on('click', function() {
            if ($(this).text() === 'Update Rawat Inap') {
                update_rawatRinap();
            }
        });

        function update_rawatRinap() {
            let url = "{{ route('pendaftaran.postRawatInap', ':id') }}"
            url = url.replace(':id', $("#id-rawatRinap").val());

            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    jenis_jaminan: $("#jenis_jaminan_rawatRinap").val(),
                    dokter: $("#dokter_rawatRinap").val(),
                    ruangan: $("#ruangan_rawatRinap").val(),
                    name_companion: $("#name_companion").val(),
                    nik_companion: $("#nik_companion").val(),
                    phone_companion: $("#phone_companion").val(),
                    relation_companion: $("#relation_companion").val(),
                },

                success: function(res) {
                    if ($.isEmptyObject(res.error)) {
                        $("#error-rawatRinap").css("display", "none");
                        if (res.status == false) {
                            swal(res.text, {
                                icon: "error",
                            });
                        } else {
                            $("#tab-rawatInap").click();
                            $("#btn-tutup-rawatRinap").click();
                            swal(res.text, {
                                icon: "success",
                            });
                        }
                    } else {
                        error_rawatRinap(res.error)
                    }
                }
            });
        }

        // Event ketika poliklinik berubah - update dokter options
        // [FIX] Pindahkan event handler ini setelah inisialisasi Select2
        $('#clinic').on('change', function() {
            var clinicId = $(this).val();
            if (clinicId) {
                $.ajax({
                    url: "{{ route('ajax.dokterByClinic', ':id') }}".replace(':id',
                        clinicId),
                    type: 'GET',
                    success: function(doctors) {
                        $('#dokter').empty().append('<option value="">-- Pilih Dokter --</option>');
                        $.each(doctors, function(key, doctor) {
                            $('#dokter').append('<option value="' + doctor.id + '">' +
                                doctor
                                .name + '</option>');
                        });

                        // Set selected doctors jika ada data yang disimpan
                        var selectedDoctors = $('#dokter').data(
                            'selected'); // This will be an array of IDs
                        if (selectedDoctors) {
                            $('#dokter').val(selectedDoctors).trigger(
                                'change'); // Select2 handles array values for multiple selects
                            $('#dokter').removeData('selected');
                        }
                    }
                });
            } else {
                $('#dokter').empty().append('<option value="">-- Pilih Poliklinik dulu --</option>');
            }
        });

        // [FIX] Handle city selection after province change on edit form
        $('#province_edit').on('change', function() {
            const cityDropdown = $('#city_edit');
            const selectedCity = cityDropdown.data('selected-city');
            if (selectedCity) {
                // Set a brief timeout to allow city options to be populated by the other event listener
                setTimeout(() => cityDropdown.val(selectedCity).trigger('change'), 200);
                cityDropdown.removeData('selected-city'); // Clean up
            }
        });
    </script>
    
    {{-- Include BambuduaUtils for component functionality --}}
    @include('components.scripts.utils')
@endpush
