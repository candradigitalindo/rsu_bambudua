@extends('layouts.app')
@section('title', 'Profile')
@push('style')
    <!-- Scrollbar CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">

    <!-- Uploader CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/dropzone/dropzone.min.css') }}">
@endpush
@section('content')
    <div class="row gx-3">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('home.profile.update', $user->id) }}" method="POST" enctype="multipart/form-data"
                        id="submit">
                        @csrf
                        <!-- Custom tabs starts -->
                        <div class="custom-tabs-container">

                            <!-- Nav tabs starts -->
                            <ul class="nav nav-tabs" id="customTab2" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link active" id="tab-oneA" data-bs-toggle="tab" href="#oneA"
                                        role="tab" aria-controls="oneA" aria-selected="true"><i
                                            class="ri-account-pin-circle-line"></i>
                                        Profile dan Biodata</a>
                                </li>

                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="tab-fourA" data-bs-toggle="tab" href="#fourA" role="tab"
                                        aria-controls="fourA" aria-selected="false"><i class="ri-lock-password-line"></i>
                                        Detail
                                        Akun</a>
                                </li>
                            </ul>
                            <!-- Nav tabs ends -->

                            <!-- Tab content starts -->
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="oneA" role="tabpanel">

                                    <!-- Row starts -->
                                    <div class="row gx-3">
                                        <div class="col-xxl-3 col-lg-4 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="a1">Nama Lengkap <span
                                                        class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text">
                                                        <i class="ri-account-circle-line"></i>
                                                    </span>
                                                    <input type="text" class="form-control" id="a1" name="name"
                                                        value="{{ $user->name }}">
                                                </div>
                                                <p class="text-danger">{{ $errors->first('name') }}</p>
                                            </div>
                                        </div>
                                        <div class="col-xxl-3 col-lg-4 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="a2">NIK KTP <span
                                                        class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text">
                                                        <i class="ri-pass-pending-line"></i>
                                                    </span>
                                                    <input name="nik" type="text" class="form-control" id="a2"
                                                        value="{{ $user->profile == null ? null : $user->profile->nik }}">
                                                </div>
                                                <p class="text-danger">{{ $errors->first('nik') }}</p>
                                            </div>
                                        </div>
                                        <div class=" col-xxl-3 col-lg-4 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="a3">Tanggal Lahir <span
                                                        class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text">
                                                        <i class="ri-flower-line"></i>
                                                    </span>
                                                    <input type="date" name="tgl_lahir" class="form-control"
                                                        id="a2"
                                                        value="{{ $user->profile == null ? null : $user->profile->tgl_lahir }}">
                                                </div>
                                                <p class="text-danger">{{ $errors->first('tgl_lahir') }}</p>
                                            </div>
                                        </div>
                                        <div class="col-xxl-3 col-lg-4 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="selectGender1">Jenis Kelamin<span
                                                        class="text-danger">*</span>
                                                </label>
                                                @if ($user->profile == null)
                                                    <div class="m-0">
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="gender"
                                                                id="selectGender1" value="1"
                                                                {{ old('gender') == 1 ? 'selected' : '' }}>
                                                            <label class="form-check-label" for="selectGender1">Pria</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" name="gender" type="radio"
                                                                name="selectGenderOptions" id="selectGender2"
                                                                value="2" {{ old('gender') == 2 ? 'selected' : '' }}>
                                                            <label class="form-check-label"
                                                                for="selectGender2">Wanita</label>
                                                        </div>
                                                        <p class="text-danger">{{ $errors->first('gender') }}</p>
                                                    </div>
                                                @else
                                                    <div class="m-0">
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="gender"
                                                                id="selectGender1" value="1"
                                                                selected>
                                                            <label class="form-check-label"
                                                                for="selectGender1">Pria</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" name="gender" type="radio"
                                                                name="selectGenderOptions" id="selectGender2"
                                                                value="2" {{ $user->profile->gender == 2 ? 'selected' : '' }}>
                                                            <label class="form-check-label"
                                                                for="selectGender2">Wanita</label>
                                                        </div>
                                                        <p class="text-danger">{{ $errors->first('gender') }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-xxl-3 col-lg-4 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="a4">ID Petugas <span
                                                        class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text">
                                                        <i class="ri-secure-payment-line"></i>
                                                    </span>
                                                    <input type="text" class="form-control" id="a4"
                                                        value="{{ $user->id_petugas }}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xxl-3 col-lg-4 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="a5">Email <span
                                                        class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text">
                                                        <i class="ri-mail-open-line"></i>
                                                    </span>
                                                    <input name="email" type="email" class="form-control"
                                                        id="a5"
                                                        value="{{ $user->profile == null ? null : $user->profile->email }}">
                                                </div>
                                                <p class="text-danger">{{ $errors->first('email') }}</p>
                                            </div>
                                        </div>
                                        <div class="col-xxl-3 col-lg-4 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="a6">No Handphone <span
                                                        class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text">
                                                        <i class="ri-phone-line"></i>
                                                    </span>
                                                    <input name="no_hp" type="text" class="form-control"
                                                        id="a6"
                                                        value="{{ $user->profile == null ? null : $user->profile->no_hp }}">
                                                </div>
                                                <p class="text-danger">{{ $errors->first('no_hp') }}</p>
                                            </div>
                                        </div>
                                        <div class="col-xxl-3 col-lg-4 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="a7">Status Menikah</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">
                                                        <i class="ri-vip-crown-2-line"></i>
                                                    </span>
                                                    <select class="form-select" id="a7" name="status_menikah">
                                                        @if ($user->profile == null)
                                                            <option value="">Pilih Status</option>
                                                            <option value="1">Belum Menikah</option>
                                                            <option value="2">Menikah</option>
                                                        @else
                                                            <option value="1"
                                                                {{ $user->profile->status_menikah == 1 ? 'selected' : '' }}>Belum
                                                                Menikah</option>
                                                            <option value="2"
                                                                {{ $user->profile->status_menikah == 2 ? 'selected' : '' }}>
                                                                Menikah
                                                            </option>
                                                        @endif

                                                    </select>
                                                </div>
                                                <p class="text-danger">{{ $errors->first('status_menikah') }}</p>
                                            </div>
                                        </div>

                                        <div class="col-xxl-3 col-lg-4 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="a10">Gol Darah<span
                                                        class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text">
                                                        <i class="ri-drop-line"></i>
                                                    </span>
                                                    <select class="form-select" id="a10" name="gol_darah">
                                                        @if ($user->profile != null)
                                                            <option value="A"
                                                                {{ $user->profile->gol_darah == 'A' ? 'selected' : '' }}>A
                                                            </option>
                                                            <option value="B"
                                                                {{ $user->profile->gol_darah == 'B' ? 'selected' : '' }}>B
                                                            </option>
                                                            <option value="AB"
                                                                {{ $user->profile->gol_darah == 'AB' ? 'selected' : '' }}>
                                                                AB
                                                            </option>
                                                            <option value="O"
                                                                {{ $user->profile->gol_darah == 'O' ? 'selected' : '' }}>O
                                                            </option>
                                                        @else
                                                            <option value="">-- Pilih Golongan Darah --</option>
                                                            <option value="A">A</option>
                                                            <option value="B">B</option>
                                                            <option value="AB">AB</option>
                                                            <option value="O">O</option>
                                                        @endif
                                                    </select>
                                                </div>
                                                <p class="text-danger">{{ $errors->first('gol_darah') }}</p>
                                            </div>
                                        </div>
                                        <div class="col-xxl-3 col-lg-4 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="a11">Alamat</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">
                                                        <i class="ri-projector-line"></i>
                                                    </span>
                                                    <input name="alamat" type="text" class="form-control"
                                                        id="a11"
                                                        value="{{ $user->profile == null ? null : $user->profile->alamat }}">
                                                </div>
                                                <p class="text-danger">{{ $errors->first('alamat') }}</p>
                                            </div>
                                        </div>

                                        <div class="col-xxl-3 col-lg-4 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="a13">Provinsi</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">
                                                        <i class="ri-instance-line"></i>
                                                    </span>
                                                    <select class="form-select" id="province" name="provinsi">
                                                        <option value="">-- Pilih Provinsi --</option>
                                                        
                                                        @foreach ($provinces as $p)
                                                            <option value="{{ $p->code }}">{{ $p->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <p class="text-danger">{{ $errors->first('provinsi') }}</p>
                                            </div>
                                        </div>
                                        <div class="col-xxl-3 col-lg-4 col-sm-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="a14">Kota / Kabupaten</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">
                                                        <i class="ri-scan-line"></i>
                                                    </span>
                                                    <select class="form-select" id="city" name="kota">
                                                        @if ($user->profile == null)
                                                            <option value="">-- Pilih Kota / Kabupaten --</option>
                                                        @else
                                                            <option value="{{ $user->profile->kode_kota }}">
                                                                {{ $user->profile->provinsi }}</option>
                                                        @endif
                                                    </select>
                                                </div>
                                                <p class="text-danger">{{ $errors->first('kota') }}</p>
                                            </div>
                                        </div>

                                        <div class="col-sm-2">

                                            <div class="mb-3">
                                                @if ($user->profile == null)
                                                    <img src="{{ asset('images/no Photo.png') }}"
                                                        class="img-fluid rounded-2" alt="">
                                                @else
                                                    @if ($user->profile->foto == null)
                                                        <img src="{{ asset('images/no Photo.png') }}"
                                                            class="img-fluid rounded-2" alt="">
                                                    @else
                                                    @endif
                                                @endif

                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <input name="foto" type="file" class="form-control" id="a11">
                                        </div>
                                        <p class="text-danger">{{ $errors->first('foto') }}</p>
                                    </div>
                                    <!-- Row ends -->

                                </div>

                                <div class="tab-pane fade" id="fourA" role="tabpanel">

                                    <!-- Row starts -->
                                    <div class="row gx-3 justify-content-center">
                                        <div class="col-sm-4">
                                            <div class="mb-3">
                                                <label class="form-label" for="u1">Username</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">
                                                        <i class="ri-account-pin-circle-line"></i>
                                                    </span>
                                                    <input type="text" name="username" id="u1"
                                                        value="{{ $user->username }}" class="form-control">
                                                </div>
                                                <p class="text-danger">{{ $errors->first('username') }}</p>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label" for="u2">New Password</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">
                                                        <i class="ri-lock-password-line"></i>
                                                    </span>
                                                    <input type="text" id="u2" name="new_password"
                                                        class="form-control">
                                                    <button class="btn btn-outline-secondary" type="button">
                                                        <i class="ri-eye-line"></i>
                                                    </button>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <!-- Row ends -->

                                </div>
                            </div>
                            <!-- Tab content ends -->

                        </div>
                        <!-- Custom tabs ends -->

                        <!-- Card acrions starts -->
                        <div class="d-flex gap-2 justify-content-end mt-4">
                            <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary" id="btn-update">
                                <span class="btn-txt">Update Profile</span>
                                <span class="spinner-border spinner-border-sm d-none"></span>
                            </button>
                        </div>
                        <!-- Card acrions ends -->
                    </form>
                </div>
            </div>
        </div>
    </div>
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
    <script>
        $(document).ready(function() {
            $("#submit").submit(function() {
                $(".spinner-border").removeClass("d-none");
                $("#btn-update").attr("disabled", true);
                $(".btn-txt").text("Mohon Tunggu ...");
            });
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
    </script>
@endpush
