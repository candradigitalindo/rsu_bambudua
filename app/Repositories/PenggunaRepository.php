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

        $users = $query->with(['profile', 'clinics'])->orderByDesc('updated_at')->paginate(20);

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
            $user->role = $roleMap[$user->role] ?? $user->role;
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

        return $user;
    }

    public function edit($id)
    {
        $user = User::with('profile')->findOrFail($id);
        $spesialis = Spesialis::orderBy('kode', 'asc')->get();
        return compact('user', 'spesialis');
    }

    public function update($request, $id)
    {
        $user = User::with('profile')->findOrFail($id);

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
}
