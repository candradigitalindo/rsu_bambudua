@extends('layouts.app')
@section('title')
    Dashboard Owner
@endsection
@push('style')
    <!-- *************
      ************ Vendor Css Files *************
     ************ -->

    <!-- Scrollbar CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">

    <!-- Date Range CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/daterange/daterange.css') }}">

    <!-- Data Tables -->
    <link rel="stylesheet" href="{{ asset('vendor/datatables/dataTables.bs5.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/datatables/dataTables.bs5-custom.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/datatables/buttons/dataTables.bs5-custom.css') }}">
@endpush
@section('content')
    <!-- Row starts -->
    <div class="row gx-3">
        <div class="col-xxl-9 col-sm-12">

            <!-- Row starts -->
            <div class="row gx-3">
                <div class="col-sm-4 col-12">
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="p-2 border border-primary rounded-circle me-3">
                                    <div class="icon-box md bg-primary-lighten rounded-5">
                                        <i class="ri-surgical-mask-line fs-4 text-primary"></i>
                                    </div>
                                </div>
                                <div class="d-flex flex-column">
                                    <h1 class="lh-1">980</h1>
                                    <p class="m-0">Patients</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-end justify-content-between mt-1">
                                <a class="text-primary" href="javascript:void(0);">
                                    <span>View All</span>
                                    <i class="ri-arrow-right-line text-primary ms-1"></i>
                                </a>
                                <div class="text-end">
                                    <p class="mb-0 text-primary">+40%</p>
                                    <span class="badge bg-primary-light text-primary small">this
                                        month</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4 col-12">
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="p-2 border border-primary rounded-circle me-3">
                                    <div class="icon-box md bg-primary-lighten rounded-5">
                                        <i class="ri-lungs-line fs-4 text-primary"></i>
                                    </div>
                                </div>
                                <div class="d-flex flex-column">
                                    <h1 class="lh-1">260</h1>
                                    <p class="m-0">Appointments</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-end justify-content-between mt-1">
                                <a class="text-primary" href="javascript:void(0);">
                                    <span>View All</span>
                                    <i class="ri-arrow-right-line ms-1"></i>
                                </a>
                                <div class="text-end">
                                    <p class="mb-0 text-primary">+30%</p>
                                    <span class="badge bg-primary-light text-primary small">this
                                        month</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4 col-12">
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="p-2 border border-primary rounded-circle me-3">
                                    <div class="icon-box md bg-primary-lighten rounded-5">
                                        <i class="ri-money-dollar-circle-line fs-4 text-primary"></i>
                                    </div>
                                </div>
                                <div class="d-flex flex-column">
                                    <h1 class="lh-1">$6800</h1>
                                    <p class="m-0">Revenue</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-end justify-content-between mt-1">
                                <a class="text-primary" href="javascript:void(0);">
                                    <span>View All</span>
                                    <i class="ri-arrow-right-line ms-1"></i>
                                </a>
                                <div class="text-end">
                                    <p class="mb-0 text-primary">+20%</p>
                                    <span class="badge bg-primary-light text-primary small">this
                                        month</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Row ends -->

            <!-- Row starts -->
            <div class="row gx-3">
                <div class="col-xxl-12 col-sm-12">
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5 class="card-title">Specialities</h5>
                        </div>
                        <div class="card-body pt-0">

                            <!-- Row starts -->
                            <div class="row g-3">
                                <div class="col-sm col-6">
                                    <div class="card border rounded-5">
                                        <div class="card-body">
                                            <div class="d-flex flex-column align-items-center">
                                                <img src="{{ asset('images/icons/bone.svg') }}" class="img-3x mb-4"
                                                    alt="Medical Admin">
                                                <h6>Orthopedic</h6>
                                                <h2 class="text-primary m-0">9</h2>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm col-6">
                                    <div class="card border rounded-5">
                                        <div class="card-body">
                                            <div class="d-flex flex-column align-items-center">
                                                <img src="{{ asset('images/icons/kidney.svg') }}" class="img-3x mb-4"
                                                    alt="Hoapital Admin">
                                                <h6>Kidney</h6>
                                                <h2 class="text-primary m-0">5</h2>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm col-6">
                                    <div class="card border rounded-5">
                                        <div class="card-body">
                                            <div class="d-flex flex-column align-items-center">
                                                <img src="{{ asset('images/icons/liver.svg') }}" class="img-3x mb-4"
                                                    alt="Hospital Dashboard">
                                                <h6>Liver</h6>
                                                <h2 class="text-primary m-0">6</h2>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm col-6">
                                    <div class="card border rounded-5">
                                        <div class="card-body">
                                            <div class="d-flex flex-column align-items-center">
                                                <img src="{{ asset('images/icons/stomach.svg') }}" class="img-3x mb-4"
                                                    alt="Medical Dashboard">
                                                <h6>Surgery</h6>
                                                <h2 class="text-primary m-0">12</h2>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm col-6">
                                    <div class="card border rounded-5">
                                        <div class="card-body">
                                            <div class="d-flex flex-column align-items-center">
                                                <img src="{{ asset('images/icons/microscope.svg') }}" class="img-3x mb-4"
                                                    alt="Hospital Dashboard">
                                                <h6>Laboratory</h6>
                                                <h2 class="text-primary m-0">5</h2>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Row ends -->

                        </div>
                    </div>
                </div>
            </div>
            <!-- Row ends -->

        </div>
        <div class="col-xxl-3 col-sm-12">
            <div class="card mb-3 display-card">
                <div class="card-body">
                    <div class="d-flex flex-column align-items-center m-auto">
                        <div class="display-card-body m-4">
                            <img src="{{ asset('images/lungs.png') }}" class="img-fluid" alt="Doctor Dashboard">
                            <span class="dot-circle one"></span>
                            <span class="dot-circle two"></span>
                            <span class="dot-circle three"></span>
                            <span class="dot-circle four"></span>
                            <span class="dot-circle five"></span>
                        </div>
                        <div class="d-flex gap-2">
                            <div class="icon-box border rounded-5">
                                <div class="text-center p-1">
                                    <h6 class="text-body small mt-2 mb-0">Left</h6>
                                    <div id="sparkline1"></div>
                                </div>
                            </div>
                            <div class="icon-box border rounded-5">
                                <div class="text-center p-1">
                                    <h6 class="text-body small mt-2 mb-0">Health</h6>
                                    <div id="sparkline2"></div>
                                </div>
                            </div>
                            <div class="icon-box border rounded-5">
                                <div class="text-center p-1">
                                    <h6 class="text-body small mt-2 mb-0">Right</h6>
                                    <div id="sparkline3"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Row ends -->

    <!-- Row starts -->
    <div class="row gx-3">
        <div class="col-xxl-12 col-sm-12">
            <div class="card mb-3">
                <div class="card-header pb-0">
                    <h5 class="card-title">Patients by Age</h5>
                </div>
                <div class="card-body pt-0">
                    <div class="overflow-hidden">
                        <div id="availableBeds"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Row ends -->

    <!-- Row starts -->
    <div class="row gx-3">
        <div class="col-sm-12">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title">Patients</h5>
                </div>
                <div class="card-body">
                    <div class="overflow-hidden">
                        <div id="patients"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title">Income By Department</h5>
                </div>
                <div class="card-body">
                    <div class="overflow-hidden">
                        <div id="departments"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Row ends -->

    <!-- Row starts -->
    <div class="row gx-3">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Recent Patient Visits</h5>
                </div>
                <div class="card-body pt-0">

                    <!-- Table starts -->
                    <div class="table-responsive">
                        <table id="hideSearchExample" class="table m-0 align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Patient Name</th>
                                    <th>Age</th>
                                    <th>Date of Birth</th>
                                    <th>Diagnosis</th>
                                    <th>Type</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>001</td>
                                    <td>
                                        <img src="{{ asset('images/patient.png') }}" class="img-2x rounded-5 me-1"
                                            alt="Doctors Admin Template">
                                        Willian Mathews
                                    </td>
                                    <td>21</td>
                                    <td>
                                        20/06/2010
                                    </td>
                                    <td>Heart Attack</td>
                                    <td>
                                        <span class="badge bg-danger-subtle text-danger fs-6">Emergency</span>
                                    </td>
                                    <td>
                                        <div class="d-inline-flex gap-1">
                                            <button type="button" class="btn btn-hover btn-sm rounded-5"
                                                data-bs-toggle="modal" data-bs-target="#delRow">
                                                <span data-bs-toggle="tooltip" data-bs-placement="top"
                                                    data-bs-title="Delete Patient Details">
                                                    <i class="ri-delete-bin-line"></i>
                                                </span>
                                            </button>
                                            <a href="edit-patient.html" class="btn btn-hover btn-sm rounded-5"
                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                data-bs-title="Edit Patient Details">
                                                <i class="ri-edit-box-line"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>002</td>
                                    <td>
                                        <img src="{{ asset('images/patient1.png') }}" class="img-2x rounded-5 me-1"
                                            alt="Doctors Admin Template">
                                        Adam Bradley
                                    </td>
                                    <td>36</td>
                                    <td>
                                        24/09/2002
                                    </td>
                                    <td>Diabetes</td>
                                    <td>
                                        <span class="badge bg-primary-subtle text-primary fs-6">Non
                                            Urgent</span>
                                    </td>
                                    <td>
                                        <div class="d-inline-flex gap-1">
                                            <button type="button" class="btn btn-hover btn-sm rounded-5"
                                                data-bs-toggle="modal" data-bs-target="#delRow">
                                                <span data-bs-toggle="tooltip" data-bs-placement="top"
                                                    data-bs-title="Delete Patient Details">
                                                    <i class="ri-delete-bin-line"></i>
                                                </span>
                                            </button>
                                            <a href="edit-patient.html" class="btn btn-hover btn-sm rounded-5"
                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                data-bs-title="Edit Patient Details">
                                                <i class="ri-edit-box-line"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>003</td>
                                    <td>
                                        <img src="{{ asset('images/patient2.png') }}" class="img-2x rounded-5 me-1"
                                            alt="Doctors Admin Template">
                                        Merle Daniel
                                    </td>
                                    <td>82</td>
                                    <td>
                                        22/02/2007
                                    </td>
                                    <td>Chancroid</td>
                                    <td>
                                        <span class="badge bg-warning-subtle text-warning fs-6">Out
                                            Patient</span>
                                    </td>
                                    <td>
                                        <div class="d-inline-flex gap-1">
                                            <button type="button" class="btn btn-hover btn-sm rounded-5"
                                                data-bs-toggle="modal" data-bs-target="#delRow">
                                                <span data-bs-toggle="tooltip" data-bs-placement="top"
                                                    data-bs-title="Delete Patient Details">
                                                    <i class="ri-delete-bin-line"></i>
                                                </span>
                                            </button>
                                            <a href="edit-patient.html" class="btn btn-hover btn-sm rounded-5"
                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                data-bs-title="Edit Patient Details">
                                                <i class="ri-edit-box-line"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>004</td>
                                    <td>
                                        <img src="{{ asset('images/patient3.png') }}" class="img-2x rounded-5 me-1"
                                            alt="Doctors Admin Template">
                                        Nicole Sellers
                                    </td>
                                    <td>29</td>
                                    <td>
                                        28/09/1996
                                    </td>
                                    <td>Pediatric</td>
                                    <td>
                                        <span class="badge bg-info-subtle text-info fs-6">Discharge</span>
                                    </td>
                                    <td>
                                        <div class="d-inline-flex gap-1">
                                            <button type="button" class="btn btn-hover btn-sm rounded-5"
                                                data-bs-toggle="modal" data-bs-target="#delRow">
                                                <span data-bs-toggle="tooltip" data-bs-placement="top"
                                                    data-bs-title="Delete Patient Details">
                                                    <i class="ri-delete-bin-line"></i>
                                                </span>
                                            </button>
                                            <a href="edit-patient.html" class="btn btn-hover btn-sm rounded-5"
                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                data-bs-title="Edit Patient Details">
                                                <i class="ri-edit-box-line"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>005</td>
                                    <td>
                                        <img src="{{ asset('images/patient4.png') }}" class="img-2x rounded-5 me-1"
                                            alt="Doctors Admin Template">
                                        Kathy Atkinson
                                    </td>
                                    <td>58</td>
                                    <td>
                                        30/03/1989
                                    </td>
                                    <td>Alphaviruses</td>
                                    <td>
                                        <span class="badge bg-danger-subtle text-danger fs-6">Urgent</span>
                                    </td>
                                    <td>
                                        <div class="d-inline-flex gap-1">
                                            <button type="button" class="btn btn-hover btn-sm rounded-5"
                                                data-bs-toggle="modal" data-bs-target="#delRow">
                                                <span data-bs-toggle="tooltip" data-bs-placement="top"
                                                    data-bs-title="Delete Patient Details">
                                                    <i class="ri-delete-bin-line"></i>
                                                </span>
                                            </button>
                                            <a href="edit-patient.html" class="btn btn-hover btn-sm rounded-5"
                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                data-bs-title="Edit Patient Details">
                                                <i class="ri-edit-box-line"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- Table ends -->

                    <!-- Modal Delete Row -->
                    <div class="modal fade" id="delRow" tabindex="-1" aria-labelledby="delRowLabel"
                        aria-hidden="true">
                        <div class="modal-dialog modal-sm">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="delRowLabel">
                                        Confirm
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    Are you sure you want to delete the patient details?
                                </div>
                                <div class="modal-footer">
                                    <div class="d-flex justify-content-end gap-2">
                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"
                                            aria-label="Close">No</button>
                                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal"
                                            aria-label="Close">Yes</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <!-- Row ends -->
@endsection
@push('scripts')
    <!-- *************
       ************ Vendor Js Files *************
      ************* -->

    <!-- Overlay Scroll JS -->
    <script src="{{ asset('vendor/overlay-scroll/jquery.overlayScrollbars.min.js') }}"></script>
    <script src="{{ asset('vendor/overlay-scroll/custom-scrollbar.js') }}"></script>

    <!-- Date Range JS -->
    <script src="{{ asset('vendor/daterange/daterange.js') }}"></script>
    <script src="{{ asset('vendor/daterange/custom-daterange.js') }}"></script>

    <!-- Apex Charts -->
    <script src="{{ asset('vendor/apex/apexcharts.min.js') }}"></script>
    <script src="{{ asset('vendor/apex/custom/home/patients.js') }}"></script>
    <script src="{{ asset('vendor/apex/custom/home/department-income.js') }}"></script>
    <script src="{{ asset('vendor/apex/custom/home/patients-age.js') }}"></script>
    <script src="{{ asset('vendor/apex/custom/home/sparklines.js') }}"></script>

    <!-- Data Tables -->
    <script src="{{ asset('vendor/datatables/dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/custom/custom-datatables.js') }}"></script>

    <!-- Custom JS files -->
    <script src="{{ asset('js/custom.js') }}"></script>
@endpush
