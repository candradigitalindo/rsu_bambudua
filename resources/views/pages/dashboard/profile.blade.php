@extends('layouts.app')
@section('title', 'Profile')
@push('style')
@endpush
@section('content')
    <div class="row gx-3">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">

                    <!-- Custom tabs starts -->
                    <div class="custom-tabs-container">

                        <!-- Nav tabs starts -->
                        <ul class="nav nav-tabs" id="customTab2" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active" id="tab-oneA" data-bs-toggle="tab" href="#oneA" role="tab"
                                    aria-controls="oneA" aria-selected="true"><i class="ri-briefcase-4-line"></i> Informasi
                                    Personal</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="tab-twoA" data-bs-toggle="tab" href="#twoA" role="tab"
                                    aria-controls="twoA" aria-selected="false"><i class="ri-account-pin-circle-line"></i>
                                    Profile dan Biodata</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="tab-threeA" data-bs-toggle="tab" href="#threeA" role="tab"
                                    aria-controls="threeA" aria-selected="false"><i class="ri-calendar-check-line"></i>
                                    Availability</a>
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
                                                <input type="text" class="form-control" id="a1" value="{{ $user->name }}">
                                            </div>
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
                                                <input type="text" class="form-control" id="a2" value="Miller">
                                            </div>
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
                                                <input type="date" class="form-control" id="a2">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xxl-3 col-lg-4 col-sm-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="selectGender1">Gender<span
                                                    class="text-danger">*</span></label>
                                            <div class="m-0">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio"
                                                        name="selectGenderOptions" id="selectGender1" value="male"
                                                        checked="">
                                                    <label class="form-check-label" for="selectGender1">Male</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio"
                                                        name="selectGenderOptions" id="selectGender2" value="female">
                                                    <label class="form-check-label" for="selectGender2">Female</label>
                                                </div>
                                            </div>
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
                                                    value="#45489">
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
                                                <input type="email" class="form-control" id="a5"
                                                    value="test@testing.com">
                                            </div>
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
                                                <input type="text" class="form-control" id="a6"
                                                    value="0987654321">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xxl-3 col-lg-4 col-sm-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="a7">Marital Status</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri-vip-crown-2-line"></i>
                                                </span>
                                                <select class="form-select" id="a7">
                                                    <option value="0">Married</option>
                                                    <option value="1">Married</option>
                                                    <option value="2">Un Married</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-xxl-3 col-lg-4 col-sm-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="a10">Blood Group<span
                                                    class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri-drop-line"></i>
                                                </span>
                                                <select class="form-select" id="a10">
                                                    <option value="0">O+</option>
                                                    <option value="1">A+</option>
                                                    <option value="2">A-</option>
                                                    <option value="3">B+</option>
                                                    <option value="4">B-</option>
                                                    <option value="5">O+</option>
                                                    <option value="6">O-</option>
                                                    <option value="7">AB+</option>
                                                    <option value="8">AB-</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xxl-3 col-lg-4 col-sm-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="a11">Address</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri-projector-line"></i>
                                                </span>
                                                <input type="text" class="form-control" id="a11"
                                                    value="#1 Walnut Street">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-xxl-3 col-lg-4 col-sm-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="a13">State</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri-instance-line"></i>
                                                </span>
                                                <select class="form-select" id="a13">
                                                    <option value="0">California</option>
                                                    <option value="1">Alabama</option>
                                                    <option value="2">Alaska</option>
                                                    <option value="3">Arizona</option>
                                                    <option value="4">California</option>
                                                    <option value="5">Florida</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xxl-3 col-lg-4 col-sm-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="a14">City</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri-scan-line"></i>
                                                </span>
                                                <input type="text" class="form-control" id="a14"
                                                    value="Sanfransisco">
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <!-- Row ends -->

                            </div>
                            <div class="tab-pane fade" id="twoA" role="tabpanel">

                                <!-- Row starts -->
                                <div class="row gx-3">
                                    <div class="col-sm-2">
                                        <div class="mb-3">
                                            <img src="assets/images/doctor3.png" class="img-fluid rounded-2"
                                                alt="Medical Dashboard">
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div id="dropzone" class="mb-3">
                                            <form action="/upload" class="dropzone dz-clickable" id="demo-upload">
                                                <div class="dz-message">
                                                    <button type="button" class="dz-button">
                                                        Upload new photo.</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div id="fullEditor">
                                            <h1>Hello,</h1>
                                            <br>
                                            <p>My name is <strong>Dr. Alan Stern</strong>, MD: Born in DuBois,
                                                Pennsylvania, Dr. Stern graduated from Villanova University and Thomas
                                                Jefferson
                                                University in Philadelphia. His residency was at Thomas Jefferson and its
                                                affiliated
                                                Wills Eye. I provide compassionate and personalized care to each of my
                                                patients.
                                                Specializing in lifesaving surgeries and dedicated to delivering optimal
                                                patient
                                                outcomes.</p>
                                        </div>
                                    </div>
                                </div>
                                <!-- Row ends -->

                            </div>
                            <div class="tab-pane fade" id="threeA" role="tabpanel">

                                <!-- Row starts -->
                                <div class="row gx-3">
                                    <div class="col-xxl-3 col-lg-4 col-sm-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="d1">Sunday</label>
                                            <div class="input-group">
                                                <select class="form-select" id="d1">
                                                    <option value="0">6AM</option>
                                                    <option value="1">7AM</option>
                                                    <option value="2">8AM</option>
                                                    <option value="3">9AM</option>
                                                </select>
                                                <select class="form-select" id="d1sX">
                                                    <option value="0">2PM</option>
                                                    <option value="1">3PM</option>
                                                    <option value="2">4PM</option>
                                                    <option value="3">5PM</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xxl-3 col-lg-4 col-sm-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="d2">Monday</label>
                                            <div class="input-group">
                                                <select class="form-select" id="d2">
                                                    <option value="0">6AM</option>
                                                    <option value="1">7AM</option>
                                                    <option value="2">8AM</option>
                                                    <option value="3">9AM</option>
                                                </select>
                                                <select class="form-select" id="d2X">
                                                    <option value="0">2PM</option>
                                                    <option value="1">3PM</option>
                                                    <option value="2">4PM</option>
                                                    <option value="3">5PM</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xxl-3 col-lg-4 col-sm-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="d3">Tuesday</label>
                                            <div class="input-group">
                                                <select class="form-select" id="d3">
                                                    <option value="0">6AM</option>
                                                    <option value="1">7AM</option>
                                                    <option value="2">8AM</option>
                                                    <option value="3">9AM</option>
                                                </select>
                                                <select class="form-select" id="d3X">
                                                    <option value="0">2PM</option>
                                                    <option value="1">3PM</option>
                                                    <option value="2">4PM</option>
                                                    <option value="3">5PM</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xxl-3 col-lg-4 col-sm-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="d4">Wednesday</label>
                                            <div class="input-group">
                                                <select class="form-select" id="d4">
                                                    <option value="0">6AM</option>
                                                    <option value="1">7AM</option>
                                                    <option value="2">8AM</option>
                                                    <option value="3">9AM</option>
                                                </select>
                                                <select class="form-select" id="d4X">
                                                    <option value="0">2PM</option>
                                                    <option value="1">3PM</option>
                                                    <option value="2">4PM</option>
                                                    <option value="3">5PM</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xxl-3 col-lg-4 col-sm-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="d5">Thursday</label>
                                            <div class="input-group">
                                                <select class="form-select" id="d5">
                                                    <option value="0">6AM</option>
                                                    <option value="1">7AM</option>
                                                    <option value="2">8AM</option>
                                                    <option value="3">9AM</option>
                                                </select>
                                                <select class="form-select" id="d5X">
                                                    <option value="0">2PM</option>
                                                    <option value="1">3PM</option>
                                                    <option value="2">4PM</option>
                                                    <option value="3">5PM</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xxl-3 col-lg-4 col-sm-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="d6">Friday</label>
                                            <div class="input-group">
                                                <select class="form-select" id="d6">
                                                    <option value="0">6AM</option>
                                                    <option value="1">7AM</option>
                                                    <option value="2">8AM</option>
                                                    <option value="3">9AM</option>
                                                </select>
                                                <select class="form-select" id="d6X">
                                                    <option value="0">2PM</option>
                                                    <option value="1">3PM</option>
                                                    <option value="2">4PM</option>
                                                    <option value="3">5PM</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xxl-3 col-lg-4 col-sm-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="d7">Saturday</label>
                                            <div class="input-group">
                                                <select class="form-select" id="d7">
                                                    <option value="0">6AM</option>
                                                    <option value="1">7AM</option>
                                                    <option value="2">8AM</option>
                                                    <option value="3">9AM</option>
                                                </select>
                                                <select class="form-select" id="d7X">
                                                    <option value="0">2PM</option>
                                                    <option value="1">3PM</option>
                                                    <option value="2">4PM</option>
                                                    <option value="3">5PM</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Row ends -->

                            </div>
                            <div class="tab-pane fade" id="fourA" role="tabpanel">

                                <!-- Row starts -->
                                <div class="row gx-3 justify-content-center">
                                    <div class="col-sm-4">
                                        <div class="mb-3">
                                            <label class="form-label" for="u1">User Name</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri-account-pin-circle-line"></i>
                                                </span>
                                                <input type="text" id="u1" value="apollo99"
                                                    class="form-control">
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label" for="u2">New Password</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri-lock-password-line"></i>
                                                </span>
                                                <input type="password" id="u2" class="form-control"
                                                    value="***********#9">
                                                <button class="btn btn-outline-secondary" type="button">
                                                    <i class="ri-eye-line"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label" for="u3">Confirm New Password</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri-lock-password-line"></i>
                                                </span>
                                                <input type="password" id="u3" value="***********#9"
                                                    class="form-control">
                                                <button class="btn btn-outline-secondary" type="button">
                                                    <i class="ri-eye-off-line"></i>
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
                        <a href="doctors-list.html" class="btn btn-outline-secondary">
                            Cancel
                        </a>
                        <a href="doctors-list.html" class="btn btn-primary">
                            Update Doctor Profile
                        </a>
                    </div>
                    <!-- Card acrions ends -->

                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="{{ asset('js/validations.js') }}"></script>
    <script>
        $(document).ready(function() {
            $("#submit").submit(function() {
                if ($('#username').val() != "" && $('#password').val() != "") {
                    $(".spinner-border").removeClass("d-none");
                    $("#login").attr("disabled", true);
                    $(".btn-txt").text("Mohon Tunggu ...");
                }
            });
        });
    </script>
@endpush
