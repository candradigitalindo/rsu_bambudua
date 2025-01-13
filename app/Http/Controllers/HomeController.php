<?php

namespace App\Http\Controllers;

use App\Interfaces\HomeInterface;
use App\Models\User;
use App\Repositories\HomeRepository;
use App\Repositories\WilayahRepository;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public $homeRepository;
    public $wilayahRepository;
    public function __construct(HomeRepository $homeRepository, WilayahRepository $wilayahRepository)
    {
        $this->homeRepository = $homeRepository;
        $this->wilayahRepository = $wilayahRepository;
    }
    public function index()
    {
        return view('pages.dashboard.owner');
    }

    public function getProfile($id)
    {
        $user = $this->homeRepository->getProfile($id);
        $provinces = $this->wilayahRepository->getProvinces();
        return view('pages.dashboard.profile', compact('user', 'provinces'));
    }

    public function updateProfile(Request $request, $id)
    {
        $request->validate([
            'name'      => 'required|string',
            'nik'       => 'required|string',
            'tgl_lahir' => 'required|string',
            'gender'    => 'required|string',
            'email'     => 'required|string',
            'no_hp'     => 'required|string',
            'status_menikah'    => 'nullable|string',
            'gol_darah' => 'required|string',
            'alamat'    => 'nullable|string',
            'provinsi'  => 'nullable|string',
            'kota'      => 'nullable|string',
            'foto'      => 'nullable|file|mimes:jpeg,jpg,png',
            'username'  => 'required|string|unique:users,username,'.$id,
            'new_password'  => 'nullable|string'
        ],[
            'name.required'     => 'Kolom masih kosong',
            'nik.required'      => 'Kolom masih kosong',
            'tgl_lahir.required'=> 'Kolom masih kosong',
            'email.required'    => 'Kolom masih kosong',
            'gol_darah.required'=> 'Kolom masih kosong',
            'gender.required'   => 'Pilih jenis kelamin',
            'foto.mimes'        => 'File harus berupa jpeg, jpg, png',
            'no_hp.required'    => 'Kolom masih kosong'
        ]);
        $profile =  $this->homeRepository->updateProfile($request, $id);
        return redirect()->route('home.profile', $id);
    }
}
