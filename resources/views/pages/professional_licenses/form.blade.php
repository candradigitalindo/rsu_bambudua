@extends('layouts.app')

@section('title', $mode === 'create' ? 'Tambah SIP' : 'Edit SIP')

@section('content')
<div class="row">
  <div class="col-12 col-lg-8">
    <div class="card">
      <div class="card-header">
        <h5 class="card-title">{{ $mode === 'create' ? 'Tambah SIP' : 'Edit SIP' }}</h5>
      </div>
      <div class="card-body">
        @if ($errors->any())
          <div class="alert alert-danger">
            <ul class="mb-0">
              @foreach($errors->all() as $e)
                <li>{{ $e }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form method="POST" action="{{ $mode==='create' ? route('professional-licenses.store') : route('professional-licenses.update', $license->id) }}">
          @csrf
          @if($mode==='edit')
            @method('PUT')
          @endif

          <div class="mb-3">
            <label class="form-label">Tenaga Kesehatan</label>
            <select name="user_id" class="form-select" required>
              <option value="">-- Pilih --</option>
              @foreach($users as $u)
                <option value="{{ $u->id }}" @selected(old('user_id', $license->user_id)===$u->id)>{{ $u->name }} @if($u->username) ({{ $u->username }}) @endif</option>
              @endforeach
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Profesi</label>
            <select name="profession" class="form-select" required>
              <option value="">-- Pilih Profesi --</option>
              @foreach($professions as $p)
                <option value="{{ $p }}" @selected(old('profession', $license->profession)===$p)>{{ ucfirst(str_replace('_',' ',$p)) }}</option>
              @endforeach
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Nomor SIP</label>
            <input type="text" name="sip_number" class="form-control" value="{{ old('sip_number', $license->sip_number) }}" placeholder="Contoh: SIP-123/ABC">
          </div>

          <div class="mb-3">
            <label class="form-label">Tanggal Kadaluarsa</label>
            <input type="date" name="sip_expiry_date" class="form-control" required value="{{ old('sip_expiry_date', optional($license->sip_expiry_date)->format('Y-m-d')) }}">
          </div>

          <div class="d-flex gap-2">
            <a href="{{ route('professional-licenses.index') }}" class="btn btn-light">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection