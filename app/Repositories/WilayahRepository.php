<?php

namespace App\Repositories;

use App\Models\City;
use App\Models\District;
use App\Models\Province;
use App\Models\Satusehat;
use App\Models\Subdistrict;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Promise\Promise;


class WilayahRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct(SatusehatRepository $satusehatRepository)
    {
        $satusehat = Satusehat::first();
        if ($satusehat->expired_in <= date('Y-m-d H:i:s')) {
            return $satusehatRepository->accesstoken();
        }
    }

    /**
     * Fungsi untuk menyimpan / update data Wialayah
     */

    public function saveProvince()
    {
        ini_set('max_execution_time', 10000);
        $satusehat = Satusehat::first();
        $url       = $satusehat->status == 1 ? env("URL_SANDBOX") . "/masterdata/v1/provinces" : env("URL_PRODUCTION") . "/masterdata/v1/provinces";
        try {
            $reqProvinsi = Http::withHeaders([
                'Authorization'     => 'Bearer ' . $satusehat->access_token
            ])->get($url);
            $resProvinsi = json_decode($reqProvinsi, true)['data'];
            foreach ($resProvinsi as $provinsi) {
                $cekProvinsi = Province::where('code', $provinsi['code'])->first();
                if (!$cekProvinsi) {
                    Province::create([
                        'code'          => $provinsi['code'],
                        'parent_code'   => $provinsi['parent_code'],
                        'bps_code'      => $provinsi['bps_code'],
                        'name'          => $provinsi['name']
                    ]);
                }
            }
            return true;
        } catch (\Throwable $th) {
            //throw $th;
            return false;
        }
    }

    public function saveKota($code)
    {
        ini_set('max_execution_time', 10000);
        $satusehat = Satusehat::first();
        $url       = $satusehat->status == 1 ? env("URL_SANDBOX") . "/masterdata/v1/cities?province_codes" : env("URL_PRODUCTION") . "/masterdata/v1/cities?province_codes";
        try {
            $province = Province::where('code', $code)->first();

                $reqCity = Http::withHeaders([
                    'Authorization'     => 'Bearer ' . $satusehat->access_token
                ])->get($url, ['province_codes' => $province->code]);
                $resCities = json_decode($reqCity, true)['data'];
                foreach ($resCities as $city) {
                    $cekCity = City::where('code', $city['code'])->first();
                    if (!$cekCity) {
                        City::create([
                            'code'          => $city['code'],
                            'parent_code'   => $city['parent_code'],
                            'bps_code'      => $city['bps_code'],
                            'name'          => $city['name']
                        ]);
                    }
                }

            return true;
        } catch (\Throwable $th) {
            //throw $th;
            return false;
        }
    }

    public function saveKecamatan($code)
    {
        ini_set('max_execution_time', 20000);
        $satusehat = Satusehat::first();
        $url       = $satusehat->status == 1 ? env("URL_SANDBOX") . "/masterdata/v1/districts?city_codes" : env("URL_PRODUCTION") . "/masterdata/v1/districts?city_codes";
        try {
            $kotas = City::where('parent_code', $code)->get();
            foreach ($kotas as $kota) {
                $reqDistrict = Http::withHeaders([
                    'Authorization'     => 'Bearer ' . $satusehat->access_token
                ])->get($url, ['city_codes' => $kota->code]);
                $resDistrict = json_decode($reqDistrict, true);
                if ($resDistrict['data']) {
                    foreach ($resDistrict['data'] as $district) {
                        $cekDistrict = District::where('code', $district['code'])->first();
                        if (!$cekDistrict) {
                            District::create([
                                'province_code' => $code,
                                'code'          => $district['code'],
                                'parent_code'   => $district['parent_code'],
                                'bps_code'      => $district['bps_code'],
                                'name'          => $district['name']
                            ]);
                        }
                    }
                }
            }
            return true;
        } catch (\Throwable $th) {
            //throw $th;
            return false;
        }
    }

    public function saveDesa($code)
    {
        ini_set('max_execution_time', 20000);
        $satusehat = Satusehat::first();
        $url       = $satusehat->status == 1 ? env("URL_SANDBOX") . "/masterdata/v1/sub-districts?district_codes" : env("URL_PRODUCTION") . "/masterdata/v1/sub-districts?district_codes";
        try {
            $kecamatans = District::where('province_code', $code)->get();
            foreach ($kecamatans as $kecamatan) {
                $reqDesa = Http::withHeaders([
                    'Authorization'     => 'Bearer ' . $satusehat->access_token
                ])->get($url, ['district_codes' => $kecamatan->code]);
                $resDesa = json_decode($reqDesa, true);
                if ($resDesa['data']) {
                    foreach ($resDesa['data'] as $desa) {
                        $cekDesa = Subdistrict::where('code', $desa['code'])->first();
                        if (!$cekDesa) {
                            Subdistrict::create([
                                'province_code' => $code,
                                'code'          => $desa['code'],
                                'parent_code'   => $desa['parent_code'],
                                'bps_code'      => $desa['bps_code'],
                                'name'          => $desa['name']
                            ]);
                        }
                    }
                }

            }
            return true;
        } catch (\Throwable $th) {
            //throw $th;
            dd($th);
        }
    }

    public function getWilayah()
    {
        $province       = Province::count();
        $city           = City::count();
        $district       = District::count();
        $subdistrict    = Subdistrict::count();
        $dataProvince   = Province::orderBy('name', 'ASC')->get();
        $dataProvince->map(function ($provinsi) {
            $provinsi['kota']       = City::where('parent_code', $provinsi->code)->count();
            $provinsi['kecamatan']  = District::where('province_code', $provinsi->code)->count();
            $provinsi['desa']       = Subdistrict::where('province_code', $provinsi->code)->count();
        });


        return ['provinsi' => $province, 'kota' => $city, 'kecamatan' => $district, 'desa' => $subdistrict, 'dataProvinces' => $dataProvince];
    }

    public function getProvinces()
    {
        return Province::orderBy('name', 'ASC')->get();
    }

    public function getCity($code)
    {
        return City::where('parent_code', $code)->orderBy('name', 'ASC')->get();
    }
}
