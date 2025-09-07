@extends('layouts.app')
@section('title')
    Manajemen Gaji & Insentif
@endsection
@push('style')
    <!-- Data Tables -->
    <link rel="stylesheet" href="{{ asset('vendor/datatables/dataTables.bs5.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/datatables/dataTables.bs5-custom.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/datatables/buttons/dataTables.bs5-custom.css') }}">

    <!-- Scrollbar CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
@endpush

@section('content')
    <!-- Row starts -->
    <div class="row gx-3">
        <div class="col-xxl-12 col-sm-12">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title">Daftar Gaji & Insentif Karyawan - {{ $current_month_name }}</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="gajiTable" class="table table-bordered table-striped m-0">
                            <thead>
                                <tr>
                                    <th>Nama Karyawan</th>
                                    <th>Jabatan</th>
                                    <th>Gaji Pokok</th>
                                    <th>Total Insentif</th>
                                    <th>Bonus</th>
                                    <th>Potongan</th>
                                    <th>Total Gaji</th>
                                    <th>Status Pembayaran</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($employees as $employee)
                                    @php
                                        $gajiPokok = $employee->salary->base_salary ?? 0;
                                        $totalGaji =
                                            $gajiPokok +
                                            $employee->total_incentive +
                                            $employee->bonus -
                                            $employee->deduction;
                                        $payment = $employee->salaryPayments->first();
                                    @endphp
                                    <tr>
                                        <td>{{ $employee->name }}</td>
                                        <td>{{ $employee->role == 2 ? 'Dokter' : ($employee->role == 3 ? 'Perawat' : 'Staf') }}
                                        </td>
                                        <td>{{ formatPrice($gajiPokok) }}</td>
                                        <td>{{ formatPrice($employee->total_incentive) }}</td>
                                        <td class="text-success">{{ formatPrice($employee->bonus) }}</td>
                                        <td class="text-danger">{{ formatPrice($employee->deduction) }}</td>
                                        <td>{{ formatPrice($totalGaji) }}</td>
                                        <td>
                                            @if ($payment && $payment->status == 'paid')
                                                <span class="badge bg-success">Sudah Dibayar</span>
                                            @else
                                                <span class="badge bg-warning">Belum Dibayar</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-inline-flex gap-1">
                                                @if (!$payment || $payment->status != 'paid')
                                                    <button class="btn btn-sm btn-primary btn-pay"
                                                        data-id="{{ $employee->id }}" data-name="{{ $employee->name }}"
                                                        data-amount="{{ formatPrice($totalGaji) }}">
                                                        <i class="ri-wallet-3-line"></i> Bayar
                                                    </button>
                                                @endif
                                                <a href="{{ route('keuangan.gaji.detail', $employee->id) }}"
                                                    class="btn btn-sm btn-info">
                                                    <i class="ri-eye-line"></i> Detail
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Row ends -->

    <!-- Modal Konfirmasi Pembayaran -->
    <div class="modal fade" id="payConfirmationModal" tabindex="-1" aria-labelledby="payConfirmationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="payConfirmationModalLabel">Konfirmasi Pembayaran Gaji</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Anda akan memproses pembayaran untuk:</p>
                    <p><strong>Nama:</strong> <span id="employeeName"></span></p>
                    <p><strong>Total Gaji:</strong> <span id="totalAmount"></span></p>
                    <p>Apakah Anda yakin ingin melanjutkan?</p>
                </div>
                <div class="modal-footer">
                    <form id="payForm" method="POST">
                        @csrf
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="confirmPayButton">
                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            Ya, Bayar Sekarang
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Data Tables -->
    <script src="{{ asset('vendor/datatables/dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/custom/custom-datatables.js') }}"></script>
    <script src="{{ asset('vendor/datatables/buttons/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/buttons/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/buttons/buttons.print.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/buttons/buttons.colVis.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/buttons/jszip.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/buttons/pdfmake.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/buttons/vfs_fonts.js') }}"></script>
    <!-- Overlay Scroll JS -->
    <script src="{{ asset('vendor/overlay-scroll/jquery.overlayScrollbars.min.js') }}"></script>
    <script src="{{ asset('vendor/overlay-scroll/custom-scrollbar.js') }}"></script>
    <!-- Custom JS files -->
    <script src="{{ asset('js/custom.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('#gajiTable').DataTable({
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'excelHtml5',
                        title: 'Data Gaji & Insentif - {{ $current_month_name }}',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7]
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        title: 'Data Gaji & Insentif - {{ $current_month_name }}',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7]
                        },
                        orientation: 'landscape'
                    },
                    'colvis'
                ]
            });
        });

        // Logika untuk tombol Bayar
        $('.btn-pay').on('click', function() {
            const employeeId = $(this).data('id');
            const employeeName = $(this).data('name');
            const totalAmount = $(this).data('amount');

            // Isi data modal
            $('#employeeName').text(employeeName);
            $('#totalAmount').text(totalAmount);

            // Set action form
            let url = "{{ route('keuangan.gaji.bayar', ':id') }}";
            url = url.replace(':id', employeeId);
            $('#payForm').attr('action', url);

            // Tampilkan modal
            const payModal = new bootstrap.Modal(document.getElementById('payConfirmationModal'));
            payModal.show();
        });

        // Handle submit form pembayaran
        $('#payForm').on('submit', function(e) {
            e.preventDefault();
            const form = $(this);
            const url = form.attr('action');
            const button = $('#confirmPayButton');
            const spinner = button.find('.spinner-border');

            button.prop('disabled', true);
            spinner.removeClass('d-none');

            $.ajax({
                url: url,
                method: 'POST',
                data: form.serialize(),
                success: function(response) {
                    button.prop('disabled', false);
                    spinner.addClass('d-none');
                    $('#payConfirmationModal').modal('hide');
                    alert(response.message);
                    location.reload(); // Muat ulang halaman untuk melihat perubahan status
                },
                error: function(xhr) {
                    button.prop('disabled', false);
                    spinner.addClass('d-none');
                    const error = xhr.responseJSON;
                    alert(error.message || 'Terjadi kesalahan saat memproses pembayaran.');
                }
            });
        });
    </script>
@endpush
