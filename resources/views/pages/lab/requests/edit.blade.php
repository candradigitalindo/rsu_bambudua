@extends('layouts.app')
@section('title', 'Hasil & Status Permintaan Lab')
@push('style')
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
    <style>
        .examination-group-card {
            border: 2px solid #dee2e6 !important;
            border-radius: 8px !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1) !important;
            margin-bottom: 1rem !important;
        }

        .examination-group-card .card-header {
            background: linear-gradient(135deg, #198754 0%, #146c43 100%) !important;
            border-bottom: 2px solid #146c43 !important;
            padding: 12px 16px !important;
        }

        .examination-group-card .card-body {
            padding: 16px !important;
            background-color: #ffffff !important;
        }

        .examination-group-card .table-bordered {
            border: 1px solid #dee2e6 !important;
        }

        .examination-group-card .table-bordered th,
        .examination-group-card .table-bordered td {
            border: 1px solid #dee2e6 !important;
        }

        .examination-group-card .table thead th {
            background-color: #f8f9fa !important;
            font-weight: 600 !important;
            color: #495057 !important;
        }

        .examination-group-card input.form-control-sm {
            border: 1px solid #ced4da !important;
            border-radius: 4px !important;
        }

        .examination-group-card input.form-control-sm:focus {
            border-color: #198754 !important;
            box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25) !important;
        }
    </style>
@endpush
@section('content')
    <div class="row gx-3">
        <div class="col-12 col-lg-10">
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Hasil & Status Permintaan Lab</h5>
                    <a href="{{ route('lab.requests.show', $req->id) }}" class="btn btn-sm btn-secondary">Kembali</a>
                </div>
                <div class="card-body">
                    <form action="{{ route('lab.requests.update', $req->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <small class="text-muted">RM</small>
                            <div><strong>{{ $req->encounter->rekam_medis ?? '-' }}</strong> —
                                {{ $req->encounter->name_pasien ?? '-' }}</div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm align-middle">
                                <thead>
                                    <tr>
                                        <th style="width:20%">Pemeriksaan</th>
                                        <th>Hasil</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($req->items as $it)
                                        <tr>
                                            <td>
                                                <div class="fw-semibold">{{ $it->test_name }}</div>
                                                <div class="small text-muted">{{ $it->jenisPemeriksaan?->name }}</div>
                                            </td>
                                            <td>
                                                <input type="hidden" name="items[{{ $loop->index }}][id]"
                                                    value="{{ $it->id }}" />
                                                <div class="dynamic-fields" data-index="{{ $loop->index }}"
                                                    data-test-id="{{ $it->test_id }}"
                                                    data-payload='@json($it->result_payload)'>
                                                    @if (!$it->test_id)
                                                        <div class="row g-2">
                                                            <div class="col-md-4"><input type="text"
                                                                    name="items[{{ $loop->index }}][result_value]"
                                                                    class="form-control" placeholder="Nilai"
                                                                    value="{{ old('items.' . $loop->index . '.result_value', $it->result_value) }}">
                                                            </div>
                                                            <div class="col-md-3"><input type="text"
                                                                    name="items[{{ $loop->index }}][result_unit]"
                                                                    class="form-control" placeholder="Satuan"
                                                                    value="{{ old('items.' . $loop->index . '.result_unit', $it->result_unit) }}">
                                                            </div>
                                                            <div class="col-md-5"><input type="text"
                                                                    name="items[{{ $loop->index }}][result_reference]"
                                                                    class="form-control" placeholder="Rujukan"
                                                                    value="{{ old('items.' . $loop->index . '.result_reference', $it->result_reference) }}">
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="row g-2 mt-2">
                            <div class="col-md-4">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select" required>
                                    @foreach (['requested', 'collected', 'processing', 'completed', 'cancelled'] as $st)
                                        <option value="{{ $st }}"
                                            {{ old('status', $req->status) === $st ? 'selected' : '' }}>
                                            {{ ucfirst($st) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end gap-2 mt-3">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="{{ asset('vendor/overlay-scroll/jquery.overlayScrollbars.min.js') }}"></script>
    <script src="{{ asset('vendor/overlay-scroll/custom-scrollbar.js') }}"></script>
    <script src="{{ asset('js/custom.js') }}"></script>
@endpush
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.dynamic-fields').forEach(function(container) {
                const testId = container.dataset.testId;
                const idx = container.dataset.index;
                if (!testId) return;
                fetch('{{ route('observasi.getTemplateFields', ['id' => 'ID_PLACEHOLDER']) }}'.replace(
                        'ID_PLACEHOLDER', testId))
                    .then(r => r.json())
                    .then(fields => {
                        const payload = (() => {
                            try {
                                return JSON.parse(container.dataset.payload || '{}')
                            } catch (e) {
                                return {}
                            }
                        })();

                        (fields || []).sort((a, b) => (a.order || 0) - (b.order || 0)).forEach(function(
                            f) {
                            if (f.field_type === 'group' && f.field_items && f.field_items
                                .length > 0) {
                                // Buat grup dengan tabel seperti di radiologi
                                const groupCard = document.createElement('div');
                                groupCard.className = 'card examination-group-card';

                                const cardHeader = document.createElement('div');
                                cardHeader.className = 'card-header bg-success text-white';
                                cardHeader.innerHTML =
                                    `<h6 class="mb-0"><i class="bi bi-folder me-1"></i>${f.field_label}</h6>`;

                                const cardBody = document.createElement('div');
                                cardBody.className = 'card-body';

                                const table = document.createElement('table');
                                table.className = 'table table-sm table-bordered';

                                // Header tabel
                                const thead = document.createElement('thead');
                                thead.className = 'table-light';
                                thead.innerHTML = `
                <tr>
                  <th>Pemeriksaan</th>
                  <th width="150">Hasil</th>
                  <th width="100">Satuan</th>
                  <th width="200">Nilai Normal</th>
                </tr>
              `;

                                const tbody = document.createElement('tbody');

                                // Buat baris untuk setiap pemeriksaan dalam grup
                                f.field_items.forEach(function(item) {
                                    const row = document.createElement('tr');

                                    const examCell = document.createElement('td');
                                    examCell.className = 'fw-semibold';
                                    examCell.textContent = item.examination_name || item
                                        .item_label;

                                    const resultCell = document.createElement('td');
                                    const resultInput = document.createElement('input');
                                    resultInput.type = 'text';
                                    resultInput.name =
                                        `items[${idx}][payload][${f.field_name}][${item.item_name}]`;
                                    resultInput.className =
                                        'form-control form-control-sm';
                                    resultInput.placeholder = 'Masukkan hasil';
                                    const currentValue = payload && payload[f
                                        .field_name] && payload[f.field_name][item
                                        .item_name
                                    ] ? payload[f.field_name][item.item_name] : '';
                                    resultInput.value = currentValue;
                                    resultCell.appendChild(resultInput);

                                    const unitCell = document.createElement('td');
                                    unitCell.className = 'text-center text-muted';
                                    unitCell.textContent = item.unit || '-';

                                    const normalCell = document.createElement('td');
                                    normalCell.className = 'text-muted';
                                    normalCell.textContent = item.normal_range || '-';

                                    row.appendChild(examCell);
                                    row.appendChild(resultCell);
                                    row.appendChild(unitCell);
                                    row.appendChild(normalCell);
                                    tbody.appendChild(row);
                                });

                                table.appendChild(thead);
                                table.appendChild(tbody);
                                cardBody.appendChild(table);
                                groupCard.appendChild(cardHeader);
                                groupCard.appendChild(cardBody);
                                container.appendChild(groupCard);

                            } else if (f.field_type !== 'group') {
                                // Field biasa seperti sebelumnya
                                const wrapper = document.createElement('div');
                                wrapper.className = 'row g-2 mb-3';

                                const col = document.createElement('div');
                                col.className = 'col-md-4';
                                const label = document.createElement('label');
                                label.className = 'form-label small';
                                label.textContent = f.field_label;
                                let input;
                                if (f.field_type === 'textarea') {
                                    input = document.createElement('textarea');
                                    input.className = 'form-control';
                                    input.rows = 2;
                                } else {
                                    input = document.createElement('input');
                                    input.type = f.field_type === 'number' ? 'number' : 'text';
                                    input.className = 'form-control';
                                }
                                input.name = `items[${idx}][payload][${f.field_name}]`;
                                input.placeholder = f.placeholder || '';
                                const cur = payload ? payload[f.field_name] : '';
                                if (cur !== undefined && cur !== null) input.value = cur;
                                col.appendChild(label);
                                col.appendChild(input);
                                wrapper.appendChild(col);
                                container.appendChild(wrapper);
                            }
                        });
                    })
                    .catch(() => {});
            });
        });
    </script>
@endpush
