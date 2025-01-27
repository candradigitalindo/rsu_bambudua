<?php

namespace App\Repositories;

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
        $users = User::when(request()->q, function($user) {
            $user = $user->where('name', 'like', '%'. request()->q . '%');
        })->orderBy('updated_at', 'DESC')->paginate(20);
        $users->map(function ($user) {
            $spesialis = Spesialis::where('kode', $user->role)->first();
            $user['role'] =  $spesialis->name;
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

        $cek    = User::max('id_petugas');
        $user = User::create([
            'name'      => ucfirst($request->name),
            'username'  => $request->username,
            'role'      => $request->role,
            'id_petugas'=> $cek + 1,
            'password'  => Hash::make($request->password)
        ]);

        Profile::create(['user_id' => $user->id]);
        return $user;
    }

    public function edit($id)
    {
        $user       = User::findOrFail($id);
        $spesialis  = Spesialis::orderBy('kode', 'asc')->get();

        return ['user' => $user,'spesialis' => $spesialis];
    }

    public function update($request, $id)
    {
        $user       = User::findOrFail($id);
        if ($request->password) {
            $user->update([
                'name'      => ucfirst($request->name),
                'username'  => $request->username,
                'role'      => $request->role,
                'password'  => Hash::make($request->password)
            ]);
        }else {
            $user->update([
                'name'      => ucfirst($request->name),
                'username'  => $request->username,
                'role'      => $request->role,
            ]);
        }

        return $user;
    }

    public function destroy($id)
    {
        $user       = User::findOrFail($id);
        $user->delete();
        return $user;
    }
}
