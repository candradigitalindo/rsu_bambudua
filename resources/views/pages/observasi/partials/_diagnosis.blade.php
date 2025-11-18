<div class="row gx-3">
    <div class="col-xxl-6 col-sm-6">
        <div class="card mb-1">
            <div class="card-header">
                <h5 class="card-title">Diagnosis Medis</h5>
                <hr class="mb-2">
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label" for="a2">Diagnosis (ICD10)</label>
                    <div class="input-group">
                        <select name="icd10_id" id="icd10_id" class="form-control">
                            <option value="">Pilih Jenis Diagnosis</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="a2">Tipe Diagnosis</label>
                    <div class="input-group">
                        <select name="diagnosis_type" id="diagnosis_type" class="form-control">
                            <option value="">Pilih Tipe Diagnosis</option>
                            <option value="Primer">Primer</option>
                            <option value="Sekunder">Sekunder</option>
                        </select>
                    </div>
                </div>
                <div class="d-flex gap-2 justify-content-end mt-4">
                    <button type="submit" class="btn btn-primary" id="btn-diagnosis-medis">
                        <span class="btn-txt" id="text-diagnosis-medis">Simpan</span>
                        <span class="spinner-border spinner-border-sm d-none" id="spinner-diagnosis-medis"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xxl-6 col-sm-12">
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title">Data Diagnosis</h5>
                <hr class="mb-2">
            </div>
            <div class="card-body">
                <div class="table-outer">
                    <div class="table-responsive">
                        <table class="table truncate m-0">
                            <thead>
                                <tr>
                                    <th class="text-center">Aksi</th>
                                    <th>Kode</th>
                                    <th>Diagnosa</th>
                                    <th>Type</th>
                                    <th>Dokter</th>
                                </tr>
                            </thead>
                            <tbody id="tbody-diagnosis"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        (function() {
            const ENCOUNTER_ID = @json($observasi);
            const listUrl = "{{ route('observasi.getDiagnosis', ':id') }}".replace(':id', ENCOUNTER_ID);
            const postUrl = "{{ route('observasi.postDiagnosis', ':id') }}".replace(':id', ENCOUNTER_ID);
            const deleteUrl = function(id) {
                return "{{ route('observasi.deleteDiagnosis', ':id') }}".replace(':id', id);
            };
            const icdAjaxUrl = "{{ route('observasi.getIcd10', $observasi) }}";

            function initIcdSelect() {
                if (!$.fn.select2) return;
                $('#icd10_id').select2({
                    placeholder: 'Cari kode atau nama diagnosis...',
                    allowClear: true,
                    width: '100%',
                    ajax: {
                        url: icdAjaxUrl,
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                search: params.term
                            };
                        },
                        processResults: function(data) {
                            // Support array or object with data property
                            const rows = Array.isArray(data) ? data : (Array.isArray(data.data) ? data
                                .data : []);
                            return {
                                results: rows.map(function(item) {
                                    if (item.code && item.description) {
                                        return {
                                            id: item.code,
                                            text: item.code + ' - ' + item.description
                                        };
                                    }
                                    // Fallback
                                    return {
                                        id: item.id || item.code || '',
                                        text: (item.description || item.text || '')
                                    };
                                })
                            };
                        },
                        cache: true
                    }
                });
            }

            function loadDiagnosis() {
                $.ajax({
                        url: listUrl,
                        type: 'GET',
                        data: {
                            _token: "{{ csrf_token() }}"
                        }
                    })
                    .done(function(data) {
                        const tbody = $('#tbody-diagnosis');
                        tbody.empty();
                        // Ensure data is an array
                        const items = Array.isArray(data) ? data : (data ? [data] : []);
                        items.forEach(function(item) {
                            tbody.append(`
            <tr>
              <td class="text-center">
                <button class="btn btn-danger btn-sm btn-hapus-diagnosis" data-id="${item.id}">
                  <i class="bi bi-trash"></i> Hapus
                </button>
              </td>
              <td>${item.diagnosis_code}</td>
              <td>${item.diagnosis_description}</td>
              <td>${item.diagnosis_type}</td>
              <td>${item.petugas_name}</td>
            </tr>
          `);
                        });
                    });
            }

            // On tab click, reset and load
            $(document).on('click', '#tab-diagnosis', function() {
                initIcdSelect();
                $('#icd10_id').val(null).trigger('change');
                $('#diagnosis_type').val('');
                loadDiagnosis();
            });

            // Save diagnosis
            $(document).on('click', '#btn-diagnosis-medis', function() {
                const icd10_id = $('#icd10_id').val();
                const diagnosis_type = $('#diagnosis_type').val();
                if (!icd10_id) {
                    alert('Jenis Diagnosis tidak boleh kosong');
                    return;
                }
                if (!diagnosis_type) {
                    alert('Tipe Diagnosis tidak boleh kosong');
                    return;
                }
                $.ajax({
                    url: postUrl,
                    type: 'POST',
                    data: {
                        icd10_id: icd10_id,
                        diagnosis_type: diagnosis_type,
                        _token: "{{ csrf_token() }}"
                    },
                    beforeSend: function() {
                        $('#spinner-diagnosis-medis').removeClass('d-none');
                        $('#text-diagnosis-medis').addClass('d-none');
                    }
                }).done(function(resp) {
                    if (resp.status == 200) {
                        swal(resp.message, {
                            icon: 'success'
                        });
                        loadDiagnosis();
                    } else {
                        swal('Terjadi kesalahan saat menyimpan data.', {
                            icon: 'error'
                        });
                    }
                }).always(function() {
                    $('#spinner-diagnosis-medis').addClass('d-none');
                    $('#text-diagnosis-medis').removeClass('d-none');
                });
            });

            // Delete diagnosis
            $(document).on('click', '#tbody-diagnosis .btn-hapus-diagnosis', function() {
                const id = $(this).data('id');
                swal({
                        title: 'Apakah Anda yakin?',
                        text: 'Data ini akan dihapus!',
                        icon: 'warning',
                        buttons: true,
                        dangerMode: true
                    })
                    .then((willDelete) => {
                        if (!willDelete) return;
                        $.ajax({
                                url: deleteUrl(id),
                                type: 'DELETE',
                                data: {
                                    _token: "{{ csrf_token() }}"
                                }
                            })
                            .done(function(data) {
                                swal(data.message || 'Berhasil dihapus.', {
                                    icon: (data.status == true ? 'success' : 'error')
                                });
                                loadDiagnosis();
                            }).fail(function() {
                                swal('Terjadi kesalahan saat menghapus data.', {
                                    icon: 'error'
                                });
                            });
                    });
            });
        })();
    </script>
@endpush
