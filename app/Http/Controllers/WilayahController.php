<?php

namespace App\Http\Controllers;

use App\Repositories\WilayahRepository;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class WilayahController extends Controller
{
    public $wilayahRepository;
    public function __construct(WilayahRepository $wilayahRepository)
    {
        $this->wilayahRepository = $wilayahRepository;
    }

    public function getWilayah()
    {
        $wilayah = $this->wilayahRepository->getWilayah();

        return view('pages.wilayah.index', compact('wilayah'));
    }

    public function getProvinces()
    {
        return $this->wilayahRepository->getProvinces();
    }

    public function getCity($code)
    {
        return response()->json($this->wilayahRepository->getCity($code));
    }

    public function saveProvince()
    {
        $province = $this->wilayahRepository->saveProvince();

        if ($province == true) {
            Alert::success('Berhasil', 'Data Provinsi sudah di Update!');
        } else {
            Alert::error('Error', 'Data Provinsi Gagal Terupdate, silahkan coba secara berkala atau hubungi Developer');
        }

        return redirect()->route('wilayah.index');
    }

    public function saveCity($kode)
    {
        $kota = $this->wilayahRepository->saveKota($kode);
        if ($kota == true) {
            Alert::success('Berhasil', 'Data Kota sudah di Update!');
        } else {
            Alert::error('Error', 'Data Kota Gagal Terupdate, silahkan coba secara berkala atau hubungi Developer');
        }

        return redirect()->route('wilayah.index');
    }

    public function saveDistrict($code)
    {
        $kecamatan = $this->wilayahRepository->saveKecamatan($code);
        if ($kecamatan == true) {
            Alert::success('Berhasil', 'Data Kecamatan sudah di Update!');
        } else {
            Alert::error('Error', 'Data Kecamatan Gagal Terupdate, silahkan coba secara berkala atau hubungi Developer');
        }

        return redirect()->route('wilayah.index');
    }
}
