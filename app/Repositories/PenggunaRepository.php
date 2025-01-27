<?php

namespace App\Repositories;

use App\Models\User;

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
        $users->appends(request()->query());
        return $users;
    }
}
