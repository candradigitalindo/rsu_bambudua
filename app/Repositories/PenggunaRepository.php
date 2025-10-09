<?php

namespace App\Repositories;

use App\Models\Clinic;
use App\Models\Profile;
use App\Models\Spesialis;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class PenggunaRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function index()
    {
        $query = User::query();

        if (request()->filled('q')) {
            $query->where('name', 'like', '%' . request()->q . '%');
        }

        $users = $query->with(['profile', 'clinics', 'professionalLicenses'])->orderByDesc('updated_at')->paginate(20);

        $roleMap = [
            1 => "Owner",
            2 => "Dokter",
            3 => "Perawat",
            4 => "Admin",
            5 => "Pendaftaran",
            6 => "Kasir",
            7 => "Apotek",
            8 => "Gudang",
            9 => "Teknisi"
        ];

        $users->getCollection()->transform(function ($user) use ($roleMap) {
            $spesialis = Spesialis::where('kode', $user->profile->spesialis ?? null)->first();
            $user->spesialis = $spesialis ? ucwords($spesialis->name) : null;
            // Jangan ubah nilai role asli, buat atribut baru untuk label
            $user->role_label = $roleMap[$user->role] ?? $user->role;
            // Ambil license terakhir
            $lic = $user->professionalLicenses->sortByDesc('sip_expiry_date')->first();
            if ($lic) {
                $user->sip_number = $lic->sip_number;
                $user->sip_expiry = optional($lic->sip_expiry_date)->format('Y-m-d');
                $user->str_number = $lic->str_number;
                $user->str_expiry = optional($lic->str_expiry_date)->format('Y-m-d');
            }
            return $user;
        });

        $users->appends(request()->query());
        return $users;
    }

    public function create()
    {
        return Spesialis::orderBy('kode', 'asc')->get();
    }

    public function store($request)
    {
        $cek = User::max('id_petugas') ?? 0;
        $user = User::create([
            'name'      => ucfirst($request->name),
            'username'  => $request->username,
            'role'      => $request->role,
            'id_petugas' => $cek + 1,
            'password'  => Hash::make($request->password),
        ]);

        Profile::create([
            'user_id'   => $user->id,
            'spesialis' => $request->spesialis
        ]);

        // Simpan poliklinik
        if ($request->has('poliklinik')) {
            $user->clinics()->sync($request->poliklinik);
        }

        // Simpan SIP/STR jika diisi
        if ($request->filled('profession') || $request->filled('sip_number') || $request->filled('sip_expiry_date') || $request->filled('str_number') || $request->filled('str_expiry_date')) {
            $sipPath = null; $strPath = null;
            if ($request->file('sip_file')) {
                $sipPath = $request->file('sip_file')->store('licenses', 'public');
            }
            if ($request->file('str_file')) {
                $strPath = $request->file('str_file')->store('licenses', 'public');
            }
            \App\Models\ProfessionalLicense::create([
                'user_id' => $user->id,
                'profession' => $request->input('profession') ?: ($this->mapRoleToProfession($user->role)),
                'sip_number' => $request->input('sip_number'),
                'sip_expiry_date' => $request->input('sip_expiry_date') ?: now()->addYear()->toDateString(),
                'str_number' => $request->input('str_number'),
'str_expiry_date' => $request->input('str_expiry_date'),
                'sip_file_path' => $sipPath,
                'str_file_path' => $strPath,
            ]);
        }

        return $user;
    }

    public function edit($id)
    {
        $user = User::with('profile')->findOrFail($id);
        $spesialis = Spesialis::orderBy('kode', 'asc')->get();
        $license = $user->professionalLicenses()->latest()->first();
        return compact('user', 'spesialis', 'license');
    }

    public function update($request, $id)
    {
        $user = User::with(['profile','professionalLicenses'])->findOrFail($id);

        $data = [
            'name'      => ucfirst($request->name),
            'username'  => $request->username,
            'role'      => $request->role,
        ];
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }
        $user->update($data);

        if ($user->profile) {
            $user->profile->update(['spesialis' => $request->spesialis]);
        } else {
            Profile::create(['user_id' => $user->id, 'spesialis' => $request->spesialis]);
        }

        // Update/Create SIP/STR
        if ($request->filled('profession') || $request->filled('sip_number') || $request->filled('sip_expiry_date') || $request->filled('str_number') || $request->filled('str_expiry_date')) {
            $lic = $user->professionalLicenses()->latest()->first();
            if (!$lic) {
                $lic = new \App\Models\ProfessionalLicense();
                $lic->user_id = $user->id;
            }
            $lic->profession = $request->input('profession') ?: ($this->mapRoleToProfession($user->role));
            $lic->sip_number = $request->input('sip_number');
            if ($request->filled('sip_expiry_date')) {
                $lic->sip_expiry_date = $request->input('sip_expiry_date');
            } elseif (!$lic->sip_expiry_date) {
                $lic->sip_expiry_date = now()->addYear()->toDateString();
            }
            $lic->str_number = $request->input('str_number');
            if ($request->file('sip_file')) {
                $lic->sip_file_path = $request->file('sip_file')->store('licenses', 'public');
            }
            if ($request->file('str_file')) {
                $lic->str_file_path = $request->file('str_file')->store('licenses', 'public');
            }
            $lic->str_expiry_date = $request->input('str_expiry_date');
            $lic->save();
        }

        // Update poliklinik
        if ($request->has('poliklinik')) {
            $user->clinics()->sync($request->poliklinik);
        }

        return $user;
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return $user;
    }
    public function getClinics()
    {
        return Clinic::orderBy('nama', 'asc')->get();
    }
    private function mapRoleToProfession($role): ?string
    {
        return match ((int)$role) {
            2 => 'dokter',
            3 => 'perawat',
            7 => 'apoteker',
            default => null,
        };
    }
}
