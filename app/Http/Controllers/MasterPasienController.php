<?php

namespace App\Http\Controllers;

use App\Models\Pasien;
use App\Models\Agama;
use App\Models\Pekerjaan;
use App\Models\Province;
use App\Models\City;
use App\Models\Encounter;
use App\Models\MedicalRecordFile;
use App\Models\ReminderLog;
use App\Models\RadiologyRequest;
use App\Models\InpatientAdmission;
use App\Models\RiwayatPenyakit;
use App\Models\PaketPasien;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MasterPasienController extends Controller
{
    public function index(Request $request)
    {
        $query = Pasien::query();

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($qb) use ($q) {
                $qb->where('name', 'like', "%{$q}%")
                   ->orWhere('rekam_medis', 'like', "%{$q}%")
                   ->orWhere('no_identitas', 'like', "%{$q}%")
                   ->orWhere('no_hp', 'like', "%{$q}%");
            });
        }

        $pasiens = $query->withCount('encounters')->orderBy('created_at', 'DESC')->paginate(15)->withQueryString();

        return view('pages.masterdata.pasien.index', compact('pasiens'));
    }

    public function create()
    {
        $agamas = Agama::orderBy('name')->get();
        $pekerjaans = Pekerjaan::orderBy('name')->get();
        $provinces = Province::orderBy('name')->get();

        return view('pages.masterdata.pasien.create', compact('agamas', 'pekerjaans', 'provinces'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'              => 'required|string|max:255',
            'jenis_kelamin'     => 'required|in:1,2',
            'tgl_lahir'         => 'required|date',
            'no_hp'             => 'required|string|max:20',
            'alamat'            => 'required|string|max:500',
            'jenis_identitas'   => 'nullable|string',
            'no_identitas'      => 'nullable|string|max:50',
            'golongan_darah'    => 'nullable|string',
            'email'             => 'nullable|email|max:255',
            'no_telepon'        => 'nullable|string|max:20',
            'status_menikah'    => 'nullable|string',
            'agama'             => 'nullable|string',
            'kewarganegaraan'   => 'nullable|in:1,2',
            'pendidikan'        => 'nullable|string',
            'pekerjaan'         => 'nullable|string',
            'province'          => 'nullable|string',
            'city'              => 'nullable|string',
        ], [
            'name.required'          => 'Nama pasien harus diisi',
            'jenis_kelamin.required' => 'Jenis kelamin harus dipilih',
            'tgl_lahir.required'     => 'Tanggal lahir harus diisi',
            'no_hp.required'         => 'No HP harus diisi',
            'alamat.required'        => 'Alamat harus diisi',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Auto-generate rekam_medis
        $lastRM = Pasien::max('rekam_medis');
        $newRM = $lastRM ? str_pad((int) $lastRM + 1, strlen($lastRM), '0', STR_PAD_LEFT) : '000001';

        // Get province & city names
        $provinceName = null;
        $provinceCode = $request->province;
        if ($provinceCode) {
            $prov = Province::where('code', $provinceCode)->first();
            $provinceName = $prov ? $prov->name : null;
        }

        $cityName = null;
        $cityCode = $request->city;
        if ($cityCode) {
            $city = City::where('code', $cityCode)->first();
            $cityName = $city ? $city->name : null;
        }

        Pasien::create([
            'rekam_medis'        => $newRM,
            'name'               => strtoupper($request->name),
            'is_identitas'       => $request->no_identitas ? 1 : 0,
            'jenis_identitas'    => $request->jenis_identitas,
            'no_identitas'       => $request->no_identitas,
            'tgl_lahir'          => $request->tgl_lahir,
            'golongan_darah'     => $request->golongan_darah,
            'jenis_kelamin'      => $request->jenis_kelamin,
            'email'              => $request->email,
            'no_telepon'         => $request->no_telepon,
            'no_hp'              => $request->no_hp,
            'status_menikah'     => $request->status_menikah,
            'agama'              => $request->agama,
            'kewarganegaraan'    => $request->kewarganegaraan ?? 1,
            'pendidikan'         => $request->pendidikan,
            'pekerjaan'          => $request->pekerjaan,
            'alamat'             => $request->alamat,
            'province_code'      => $provinceCode,
            'province'           => $provinceName,
            'city_code'          => $cityCode,
            'city'               => $cityName,
            'is_kerabat_dokter'  => $request->has('is_kerabat_dokter') ? 1 : 0,
            'is_kerabat_karyawan' => $request->has('is_kerabat_karyawan') ? 1 : 0,
            'is_kerabat_owner'   => $request->has('is_kerabat_owner') ? 1 : 0,
        ]);

        return redirect()->route('master.pasien.index')->with('success', 'Data pasien berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        $pasien = Pasien::findOrFail($id);
        $agamas = Agama::orderBy('name')->get();
        $pekerjaans = Pekerjaan::orderBy('name')->get();
        $provinces = Province::orderBy('name')->get();
        $cities = $pasien->province_code ? City::where('parent_code', $pasien->province_code)->orderBy('name')->get() : collect();

        return view('pages.masterdata.pasien.edit', compact('pasien', 'agamas', 'pekerjaans', 'provinces', 'cities'));
    }

    public function update(Request $request, string $id)
    {
        $pasien = Pasien::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name'              => 'required|string|max:255',
            'jenis_kelamin'     => 'required|in:1,2',
            'tgl_lahir'         => 'required|date',
            'no_hp'             => 'required|string|max:20',
            'alamat'            => 'required|string|max:500',
            'jenis_identitas'   => 'nullable|string',
            'no_identitas'      => 'nullable|string|max:50',
            'golongan_darah'    => 'nullable|string',
            'email'             => 'nullable|email|max:255',
            'no_telepon'        => 'nullable|string|max:20',
            'status_menikah'    => 'nullable|string',
            'agama'             => 'nullable|string',
            'kewarganegaraan'   => 'nullable|in:1,2',
            'pendidikan'        => 'nullable|string',
            'pekerjaan'         => 'nullable|string',
            'province'          => 'nullable|string',
            'city'              => 'nullable|string',
        ], [
            'name.required'          => 'Nama pasien harus diisi',
            'jenis_kelamin.required' => 'Jenis kelamin harus dipilih',
            'tgl_lahir.required'     => 'Tanggal lahir harus diisi',
            'no_hp.required'         => 'No HP harus diisi',
            'alamat.required'        => 'Alamat harus diisi',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $provinceName = null;
        $provinceCode = $request->province;
        if ($provinceCode) {
            $prov = Province::where('code', $provinceCode)->first();
            $provinceName = $prov ? $prov->name : null;
        }

        $cityName = null;
        $cityCode = $request->city;
        if ($cityCode) {
            $city = City::where('code', $cityCode)->first();
            $cityName = $city ? $city->name : null;
        }

        $pasien->update([
            'name'               => strtoupper($request->name),
            'is_identitas'       => $request->no_identitas ? 1 : 0,
            'jenis_identitas'    => $request->jenis_identitas,
            'no_identitas'       => $request->no_identitas,
            'tgl_lahir'          => $request->tgl_lahir,
            'golongan_darah'     => $request->golongan_darah,
            'jenis_kelamin'      => $request->jenis_kelamin,
            'email'              => $request->email,
            'no_telepon'         => $request->no_telepon,
            'no_hp'              => $request->no_hp,
            'status_menikah'     => $request->status_menikah,
            'agama'              => $request->agama,
            'kewarganegaraan'    => $request->kewarganegaraan ?? 1,
            'pendidikan'         => $request->pendidikan,
            'pekerjaan'          => $request->pekerjaan,
            'alamat'             => $request->alamat,
            'province_code'      => $provinceCode,
            'province'           => $provinceName,
            'city_code'          => $cityCode,
            'city'               => $cityName,
            'is_kerabat_dokter'  => $request->has('is_kerabat_dokter') ? 1 : 0,
            'is_kerabat_karyawan' => $request->has('is_kerabat_karyawan') ? 1 : 0,
            'is_kerabat_owner'   => $request->has('is_kerabat_owner') ? 1 : 0,
        ]);

        return redirect()->route('master.pasien.index')->with('success', 'Data pasien berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $pasien = Pasien::findOrFail($id);

        DB::transaction(function () use ($pasien) {
            // Nullify rekam_medis references
            Encounter::where('rekam_medis', $pasien->rekam_medis)->update(['rekam_medis' => null]);
            MedicalRecordFile::where('rekam_medis', $pasien->rekam_medis)->update(['rekam_medis' => null]);
            ReminderLog::where('rekam_medis', $pasien->rekam_medis)->update(['rekam_medis' => null]);

            // Nullify pasien_id references
            RadiologyRequest::where('pasien_id', $pasien->id)->update(['pasien_id' => null]);
            InpatientAdmission::where('pasien_id', $pasien->id)->update(['pasien_id' => null]);
            RiwayatPenyakit::where('pasien_id', $pasien->id)->update(['pasien_id' => null]);
            PaketPasien::where('pasien_id', $pasien->id)->update(['pasien_id' => null]);

            $pasien->delete();
        });

        return redirect()->route('master.pasien.index')->with('success', 'Data pasien berhasil dihapus.');
    }

    public function getCities($provinceCode)
    {
        $cities = City::where('parent_code', $provinceCode)->orderBy('name')->get();
        return response()->json($cities);
    }
}
