<?php

namespace App\Repositories;

use App\Interfaces\HomeInterface;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class HomeRepository implements HomeInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function getProfile($id)
    {
        return User::findOrFail($id);
    }

    public function updateProfile($request, $id)
    {
        $user = User::findOrFail($id);
        
        if ($request->new_password == null) {
            $user->update(['name' => $request->name, 'username' => $request->username]);
        } else {
            $user->update(['name' => $request->name, 'username' => $request->username, 'password' => Hash::make($request->new_password)]);
        }

        $profile = Profile::where('user_id', $user->id)->first();
        if ($profile) {
            if ($request->foto) {
                $file = $request->file('foto');
                $file->storeAs('public/profile', $file->hashName());
                $profile->update([
                    'nik'   => $request->nik,
                    'tgl_lahir' => $request->tgl_lahir,
                    'gender'    => $request->gender,
                    'email'     => $request->email,
                    'no_hp'     => $request->no_hp,
                    'status_menikah'    => $request->status_menikah,
                    'gol_darah' => $request->gol_darah,
                    'alamat'    => $request->alamat,
                    'kode_provinsi' => $request->provinsi,
                    'kode_kota' => $request->kota,
                    'foto'      => $file->hashName()
                ]);
            } else {
                $profile->update([
                    'nik'   => $request->nik,
                    'tgl_lahir' => $request->tgl_lahir,
                    'gender'    => $request->gender,
                    'email'     => $request->email,
                    'no_hp'     => $request->no_hp,
                    'status_menikah'    => $request->status_menikah,
                    'gol_darah' => $request->gol_darah,
                    'alamat'    => $request->alamat,
                    'kode_provinsi' => $request->provinsi,
                    'kode_kota' => $request->kota,
                ]);
            }
        } else {
            if ($request->foto) {
                $file = $request->file('foto');
                $file->storeAs('public/profile', $file->hashName());
                Profile::create([
                    'user_id'   => $user->id,
                    'nik'       => $request->nik,
                    'tgl_lahir' => $request->tgl_lahir,
                    'gender'    => $request->gender,
                    'email'     => $request->email,
                    'no_hp'     => $request->no_hp,
                    'status_menikah'    => $request->status_menikah,
                    'gol_darah' => $request->gol_darah,
                    'alamat'    => $request->alamat,
                    'kode_provinsi' => $request->provinsi,
                    'kode_kota' => $request->kota,
                    'foto'      => $file->hashName()
                ]);
            } else {
                Profile::create([
                    'user_id'   => $user->id,
                    'nik'       => $request->nik,
                    'tgl_lahir' => $request->tgl_lahir,
                    'gender'    => $request->gender,
                    'email'     => $request->email,
                    'no_hp'     => $request->no_hp,
                    'status_menikah'    => $request->status_menikah,
                    'gol_darah' => $request->gol_darah,
                    'alamat'    => $request->alamat,
                    'kode_provinsi' => $request->provinsi,
                    'kode_kota' => $request->kota,
                ]);
            }
        }

        return $user;
    }
}
