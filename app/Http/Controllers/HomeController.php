<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        return view('pages.dashboard.owner');
    }

    public function profile($id)
    {
        $user = User::findOrFail($id);
        return view('pages.dashboard.profile', compact('user'));
    }
}
