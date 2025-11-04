@extends('layouts.app')

@section('title', 'Input Hasil Radiologi')
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
    <div class="row">
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Input Hasil Radiologi</h5>
                    <a href="{{ route('radiologi.requests.show', $req->id) }}" class="btn btn-light btn-sm">Kembali</a>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="m-0">
                                @foreach ($errors->all() as $e)
                                    <li>{{ $e }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="mb-3">
                        <div class="text-muted small">Pasien</div>
                        <div class="fw-semibold">{{ $req->pasien->rekam_medis ?? '-' }} — {{ $req->pasien->name ?? '-' }}
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Pemeriksaan</div>
                        <div class="fw-semibold">{{ $req->jenis->name ?? '-' }}</div>
                    </div>

                    <form method="POST" action="{{ route('radiologi.requests.results.store', $req->id) }}"
                        enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Dokter Spesialis Radiologi <span class="text-danger">*</span></label>
                            <select name="radiologist_id" class="form-select" required>
                                <option value="">-- Pilih Radiolog --</option>
                                @php
                                    $radiologists = \App\Models\User::where('role', 2)
                                        ->where('is_active', 1)
                                        ->orderBy('name')
                                        ->get();
                                @endphp
                                @foreach ($radiologists as $radiolog)
                                    <option value="{{ $radiolog->id }}"
                                        {{ old('radiologist_id') == $radiolog->id ? 'selected' : '' }}>
                                        {{ $radiolog->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">Pilih dokter spesialis radiologi yang melakukan pemeriksaan</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Perawat / Nurse <span class="text-danger">*</span></label>
                            <select name="reporter_id" class="form-select" required>
                                <option value="">-- Pilih Perawat --</option>
                                @php
                                    $nurses = \App\Models\User::where('role', 3)
                                        ->where('is_active', 1)
                                        ->orderBy('name')
                                        ->get();
                                @endphp
                                @foreach ($nurses as $nurse)
                                    <option value="{{ $nurse->id }}"
                                        {{ old('reporter_id') == $nurse->id ? 'selected' : '' }}>
                                        {{ $nurse->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">Pilih perawat yang membantu pemeriksaan</div>
                        </div>

                        @if (
                            $req->jenis &&
                                (stripos($req->jenis->name, 'ECHOCARDIOGRAPHY') !== false || stripos($req->jenis->name, 'ECHO') !== false))
                            {{-- ECHOCARDIOGRAPHY Structured Form --}}
                            <div class="card bg-light mb-3">
                                <div class="card-body">
                                    <h6 class="mb-3">Pengukuran / Measurement</h6>

                                    <style>
                                        .measurement-table {
                                            width: 100%;
                                            border-collapse: collapse;
                                            margin-bottom: 1rem;
                                            background-color: white;
                                        }

                                        .measurement-table th {
                                            background-color: #198754;
                                            color: white;
                                            padding: 12px;
                                            text-align: center;
                                            font-weight: 600;
                                            border: 1px solid #198754;
                                        }

                                        .measurement-table td {
                                            padding: 10px 12px;
                                            border: 1px solid #dee2e6;
                                        }

                                        .measurement-table td:first-child {
                                            background-color: #f8f9fa;
                                            font-weight: 600;
                                            text-align: center;
                                            vertical-align: middle;
                                            width: 20%;
                                        }

                                        .measurement-table tbody td:nth-child(2) {
                                            background-color: #f8f9fa;
                                            width: 30%;
                                            font-weight: 500;
                                            text-align: left !important;
                                            padding-left: 15px;
                                        }

                                        .measurement-table td:nth-child(3) {
                                            background-color: #ffffff;
                                            width: 180px;
                                            min-width: 180px;
                                            max-width: 180px;
                                            padding: 8px;
                                            text-align: center;
                                            vertical-align: middle;
                                        }

                                        .measurement-table td:nth-child(4) {
                                            background-color: #fffbf0;
                                            color: #6c757d;
                                            width: 200px;
                                            min-width: 200px;
                                            font-size: 13px;
                                            font-style: italic;
                                            text-align: center;
                                            vertical-align: middle;
                                        }

                                        .measurement-table input[type="number"] {
                                            width: 160px;
                                            padding: 10px 12px;
                                            border: 1px solid #ced4da;
                                            border-radius: 4px;
                                            box-sizing: border-box;
                                            height: 40px;
                                            display: block;
                                            margin: 0 auto;
                                            font-size: 15px;
                                            text-align: center;
                                            font-weight: 500;
                                        }

                                        .measurement-table tbody tr td:nth-child(1) {
                                            text-align: left !important;
                                        }

                                        .measurement-table input[type="number"]:focus {
                                            outline: none;
                                            border-color: #198754;
                                            box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25);
                                        }

                                        .measurement-table input[type="number"]::placeholder {
                                            text-align: center;
                                            color: #adb5bd;
                                            font-style: italic;
                                        }
                                    </style>

                                    <table class="measurement-table">
                                        <thead>
                                            <tr>
                                                <th>Bagian</th>
                                                <th>Parameter</th>
                                                <th>Nilai</th>
                                                <th>Normal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td rowspan="1">Aorta</td>
                                                <td>Root diam</td>
                                                <td><input type="number" step="0.1" name="payload[Root diam]"
                                                        value="{{ old('payload.Root diam') }}" placeholder="mm"></td>
                                                <td>20–37 mm</td>
                                            </tr>

                                            <tr>
                                                <td rowspan="9">Ventrikel Kiri<br>(Left Ventricle)</td>
                                                <td>EDD</td>
                                                <td><input type="number" step="0.1" name="payload[EDD]"
                                                        value="{{ old('payload.EDD') }}" placeholder="mm"></td>
                                                <td>35–52 mm</td>
                                            </tr>
                                            <tr>
                                                <td>ESD</td>
                                                <td><input type="number" step="0.1" name="payload[ESD]"
                                                        value="{{ old('payload.ESD') }}" placeholder="mm"></td>
                                                <td>26–36 mm</td>
                                            </tr>
                                            <tr>
                                                <td>IVS Diastole</td>
                                                <td><input type="number" step="0.1" name="payload[IVS Diastole]"
                                                        value="{{ old('payload.IVS Diastole') }}" placeholder="mm"></td>
                                                <td>7–11 mm</td>
                                            </tr>
                                            <tr>
                                                <td>IVS Systole</td>
                                                <td><input type="number" step="0.1" name="payload[IVS Systole]"
                                                        value="{{ old('payload.IVS Systole') }}" placeholder="mm"></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td>PW Diastole</td>
                                                <td><input type="number" step="0.1" name="payload[PW Diastole]"
                                                        value="{{ old('payload.PW Diastole') }}" placeholder="mm"></td>
                                                <td>7–11 mm</td>
                                            </tr>
                                            <tr>
                                                <td>PW Systole</td>
                                                <td><input type="number" step="0.1" name="payload[PW Systole]"
                                                        value="{{ old('payload.PW Systole') }}" placeholder="mm"></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td>EF</td>
                                                <td><input type="number" step="0.1" name="payload[EF]"
                                                        value="{{ old('payload.EF') }}" placeholder="%"></td>
                                                <td>52–77%</td>
                                            </tr>
                                            <tr>
                                                <td>FS</td>
                                                <td><input type="number" step="0.1" name="payload[FS]"
                                                        value="{{ old('payload.FS') }}" placeholder="%"></td>
                                                <td>&gt; 25%</td>
                                            </tr>
                                            <tr>
                                                <td>EPSS</td>
                                                <td><input type="number" step="0.1" name="payload[EPSS]"
                                                        value="{{ old('payload.EPSS') }}" placeholder="mm"></td>
                                                <td>&lt; 10 mm</td>
                                            </tr>

                                            <tr>
                                                <td rowspan="2">Atrium Kiri<br>(Left Atrium)</td>
                                                <td>Dimension</td>
                                                <td><input type="number" step="0.1" name="payload[LA Dimension]"
                                                        value="{{ old('payload.LA Dimension') }}" placeholder="mm"></td>
                                                <td>15–40 mm</td>
                                            </tr>
                                            <tr>
                                                <td>LA/Ao ratio</td>
                                                <td><input type="number" step="0.01" name="payload[LA/Ao ratio]"
                                                        value="{{ old('payload.LA/Ao ratio') }}" placeholder="ratio">
                                                </td>
                                                <td>&lt; 1.33</td>
                                            </tr>

                                            <tr>
                                                <td rowspan="5">Ventrikel Kanan<br>(Right Ventricle)</td>
                                                <td>Dimension</td>
                                                <td><input type="number" step="0.1" name="payload[RV Dimension]"
                                                        value="{{ old('payload.RV Dimension') }}" placeholder="mm"></td>
                                                <td>&lt; 43 mm</td>
                                            </tr>
                                            <tr>
                                                <td>M.V.A</td>
                                                <td><input type="number" step="0.1" name="payload[M.V.A]"
                                                        value="{{ old('payload.M.V.A') }}" placeholder="cm²"></td>
                                                <td>&gt; 3 cm²</td>
                                            </tr>
                                            <tr>
                                                <td>TAPSE</td>
                                                <td><input type="number" step="0.1" name="payload[TAPSE]"
                                                        value="{{ old('payload.TAPSE') }}" placeholder="mm"></td>
                                                <td>≥ 16 mm</td>
                                            </tr>
                                            <tr>
                                                <td>RA mayor</td>
                                                <td><input type="number" step="0.1" name="payload[RA mayor]"
                                                        value="{{ old('payload.RA mayor') }}" placeholder="mm"></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td>RA minor</td>
                                                <td><input type="number" step="0.1" name="payload[RA minor]"
                                                        value="{{ old('payload.RA minor') }}" placeholder="mm"></td>
                                                <td></td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <h6 class="mb-3 mt-4">Penilaian Katup & Gerakan Otot</h6>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Gerakan Otot / Wall Motion</label>
                                            <textarea name="payload[Gerakan Otot / Wall Motion]" class="form-control" rows="2"
                                                placeholder="Contoh: Normokinetik">{{ old('payload.Gerakan Otot / Wall Motion') }}</textarea>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Katup Mitral / Mitral Valve</label>
                                            <textarea name="payload[Katup Mitral / Mitral Valve]" class="form-control" rows="2"
                                                placeholder="Contoh: MR Mild, E/A > 1">{{ old('payload.Katup Mitral / Mitral Valve') }}</textarea>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Katup Trikuspid / Tricuspid Valve</label>
                                            <textarea name="payload[Katup Trikuspid / Tricuspid Valve]" class="form-control" rows="2"
                                                placeholder="Contoh: TR Mild">{{ old('payload.Katup Trikuspid / Tricuspid Valve') }}</textarea>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Katup Aorta / Aortic Valve</label>
                                            <textarea name="payload[Katup Aorta / Aortic Valve]" class="form-control" rows="2" placeholder="Contoh: Baik">{{ old('payload.Katup Aorta / Aortic Valve') }}</textarea>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Katup Pulmonal / Pulmonal Valve</label>
                                            <textarea name="payload[Katup Pulmonal / Pulmonal Valve]" class="form-control" rows="2"
                                                placeholder="Contoh: Baik">{{ old('payload.Katup Pulmonal / Pulmonal Valve') }}</textarea>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Fungsi Sistolik LV</label>
                                            <textarea name="payload[Fungsi Sistolik LV]" class="form-control" rows="2"
                                                placeholder="Contoh: Baik (EF: 66%)">{{ old('payload.Fungsi Sistolik LV') }}</textarea>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Dimensi Ruang Jantung</label>
                                            <textarea name="payload[Dimensi Ruang Jantung]" class="form-control" rows="2" placeholder="Contoh: LVH (+)">{{ old('payload.Dimensi Ruang Jantung') }}</textarea>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Lain-lain (PH, Efusi Perikard, dll)</label>
                                            <textarea name="payload[Lain-lain]" class="form-control" rows="2"
                                                placeholder="Contoh: PH (-), Efusi Perikard (-)">{{ old('payload.Lain-lain') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @elseif ($req->jenis && $req->jenis->templateFields && $req->jenis->templateFields->isNotEmpty())
                            {{-- Generic Template Fields for Non-ECHO Examinations --}}
                            <div class="card bg-light mb-3">
                                <div class="card-header">
                                    <h6 class="mb-0">Data Pemeriksaan {{ $req->jenis->name }}</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        @foreach ($req->jenis->templateFields as $field)
                                            @if ($field->field_type === 'group')
                                                {{-- Group field dengan pemeriksaan yang sudah ditentukan admin --}}
                                                <div class="col-12">
                                                    <div class="card examination-group-card">
                                                        <div class="card-header bg-success text-white">
                                                            <h6 class="mb-0">
                                                                <i class="bi bi-folder me-1"></i>{{ $field->field_label }}
                                                            </h6>
                                                        </div>
                                                        <div class="card-body">
                                                            @if ($field->fieldItems->isNotEmpty())
                                                                <div class="table-responsive">
                                                                    <table class="table table-sm table-bordered">
                                                                        <thead class="table-light">
                                                                            <tr>
                                                                                <th>Pemeriksaan</th>
                                                                                <th width="150">Hasil</th>
                                                                                <th width="100">Satuan</th>
                                                                                <th width="200">Nilai Normal</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @foreach ($field->fieldItems as $item)
                                                                                <tr>
                                                                                    <td class="fw-semibold">
                                                                                        {{ $item->examination_name }}</td>
                                                                                    <td>
                                                                                        <input type="text"
                                                                                            name="payload[{{ $field->field_name }}][{{ $item->item_name }}]"
                                                                                            class="form-control form-control-sm"
                                                                                            value="{{ old('payload.' . $field->field_name . '.' . $item->item_name) }}"
                                                                                            placeholder="Masukkan hasil">
                                                                                    </td>
                                                                                    <td class="text-center text-muted">
                                                                                        {{ $item->unit }}</td>
                                                                                    <td class="text-muted">
                                                                                        {{ $item->normal_range }}</td>
                                                                                </tr>
                                                                            @endforeach
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            @else
                                                                <div class="alert alert-warning">
                                                                    <i class="bi bi-exclamation-triangle me-1"></i>
                                                                    Belum ada pemeriksaan yang dikonfigurasi untuk grup ini.
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                {{-- Regular field --}}
                                                <div class="col-md-6">
                                                    <label class="form-label">{{ $field->field_label }}</label>
                                                    @if ($field->field_type === 'textarea')
                                                        <textarea name="payload[{{ $field->field_name }}]" class="form-control" rows="3"
                                                            placeholder="{{ $field->placeholder }}">{{ old('payload.' . $field->field_name) }}</textarea>
                                                    @elseif($field->field_type === 'number')
                                                        <input type="number" step="0.01"
                                                            name="payload[{{ $field->field_name }}]" class="form-control"
                                                            value="{{ old('payload.' . $field->field_name) }}"
                                                            placeholder="{{ $field->placeholder }}">
                                                    @elseif($field->field_type === 'select')
                                                        <select name="payload[{{ $field->field_name }}]"
                                                            class="form-select">
                                                            <option value="">-- Pilih --</option>
                                                            @if ($field->placeholder)
                                                                @foreach (explode('|', $field->placeholder) as $option)
                                                                    <option value="{{ $option }}"
                                                                        {{ old('payload.' . $field->field_name) == $option ? 'selected' : '' }}>
                                                                        {{ $option }}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    @else
                                                        <input type="text" name="payload[{{ $field->field_name }}]"
                                                            class="form-control"
                                                            value="{{ old('payload.' . $field->field_name) }}"
                                                            placeholder="{{ $field->placeholder }}">
                                                    @endif
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if (
                            $req->jenis &&
                                (stripos($req->jenis->name, 'ECHOCARDIOGRAPHY') !== false || stripos($req->jenis->name, 'ECHO') !== false))
                            {{-- ECHO specific fields --}}
                            <div class="mb-3">
                                <label class="form-label">Temuan / Findings</label>
                                <textarea name="findings" class="form-control" rows="4">{{ old('findings') }}</textarea>
                                <div class="form-text">Temuan dan analisis hasil pemeriksaan echocardiography (opsional)
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Saran / Recommendation<span class="text-danger">*</span></label>
                                <textarea name="impression" class="form-control" rows="4" required>{{ old('impression') }}</textarea>
                                <div class="form-text">Saran dan rekomendasi dari hasil pemeriksaan echocardiography</div>
                            </div>
                        @else
                            {{-- Generic for other examinations --}}
                            <div class="mb-3">
                                <label class="form-label">Saran / Recommendation <span
                                        class="text-danger">*</span></label>
                                <textarea name="findings" class="form-control" rows="6" required>{{ old('findings') }}</textarea>
                                <div class="form-text">Saran dan rekomendasi dari hasil pemeriksaan radiologi</div>
                            </div>
                        @endif

                        <div class="d-flex gap-2">
                            <a href="{{ route('radiologi.requests.show', $req->id) }}" class="btn btn-light">Batal</a>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle me-1"></i>Simpan Hasil & Selesaikan
                            </button>
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
