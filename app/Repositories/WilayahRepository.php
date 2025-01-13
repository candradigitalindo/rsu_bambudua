<?php

namespace App\Repositories;

use App\Interfaces\WilayahInterface;
use App\Models\City;
use App\Models\District;
use App\Models\Province;
use App\Models\Subdistrict;
use Illuminate\Support\Facades\Http;

class WilayahRepository implements WilayahInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Fungsi untuk menyimpan / update data Wialayah
     */

    public function saveWilayah()
    {
        ini_set('max_execution_time', 10000);
        // try {
        //     $reqProvinsi = Http::withHeaders([
        //         'Authorization'     => 'Bearer Ok0HbRti60BoG1uqvV3OZTc54He7'
        //     ])->get('https://api-satusehat-stg.dto.kemkes.go.id/masterdata/v1/provinces');
        //     $resProvinsi = json_decode($reqProvinsi, true)['data'];
        //     foreach ($resProvinsi as $provinsi) {
        //         $cekProvinsi = Province::where('code', $provinsi['code'])->first();
        //         if (!$cekProvinsi) {
        //             Province::create([
        //                 'code'          => $provinsi['code'],
        //                 'parent_code'   => $provinsi['parent_code'],
        //                 'bps_code'      => $provinsi['bps_code'],
        //                 'name'          => $provinsi['name']
        //             ]);
        //         }
        //     }
        // } catch (\Throwable $th) {
        //     //throw $th;
        // }

        // $provinces = Province::all();
        // foreach ($provinces as $province) {
        //     try {
        //         $reqCity = Http::withHeaders([
        //             'Authorization'     => 'Bearer Ok0HbRti60BoG1uqvV3OZTc54He7'
        //         ])->get('https://api-satusehat-stg.dto.kemkes.go.id/masterdata/v1/cities?province_codes',['province_codes' => $province->code]);
        //         $resCities = json_decode($reqCity, true)['data'];
        //         foreach ($resCities as $city) {
        //             $cekCity = City::where('code', $city['code'])->first();
        //             if (!$cekCity) {
        //                 City::create([
        //                     'code'          => $city['code'],
        //                     'parent_code'   => $city['parent_code'],
        //                     'bps_code'      => $city['bps_code'],
        //                     'name'          => $city['name']
        //                 ]);
        //             }
        //         }
        //     } catch (\Throwable $th) {
        //         //throw $th;
        //     }
        // }

        // $kotas = City::all();
        // foreach ($kotas as $kota) {
        //     try {
        //         $reqDistrict = Http::withHeaders([
        //             'Authorization'     => 'Bearer Ok0HbRti60BoG1uqvV3OZTc54He7'
        //         ])->get('https://api-satusehat-stg.dto.kemkes.go.id/masterdata/v1/districts?city_codes',['city_codes' => $kota->code]);
        //         $resDistrict = json_decode($reqDistrict, true)['data'];
        //         foreach ($resDistrict as $district) {
        //             $cekDistrict = District::where('code', $district['code'])->first();
        //             if (!$cekDistrict) {
        //                 District::create([
        //                     'code'          => $district['code'],
        //                     'parent_code'   => $district['parent_code'],
        //                     'bps_code'      => $district['bps_code'],
        //                     'name'          => $district['name']
        //                 ]);
        //             }
        //         }
        //     } catch (\Throwable $th) {
        //         //throw $th;
        //     }
        // }

        // $kecamatans = District::all();
        // foreach ($kecamatans as $kecamatan) {
        //     try {
        //         $reqSub = Http::withHeaders([
        //             'Authorization'     => 'Bearer Ok0HbRti60BoG1uqvV3OZTc54He7'
        //         ])->get('https://api-satusehat-stg.dto.kemkes.go.id/masterdata/v1/sub-districts?district_codes', ['district_codes' => $kecamatan->code]);
        //         $resSub = json_decode($reqSub, true)['data'];
        //         foreach ($resSub as $subdis) {
        //             $cekSub = Subdistrict::where('code', $subdis['code'])->first();
        //             if (!$cekSub) {
        //                 Subdistrict::create([
        //                     'code'          => $subdis['code'],
        //                     'parent_code'   => $subdis['parent_code'],
        //                     'bps_code'      => $subdis['bps_code'],
        //                     'name'          => $subdis['name']
        //                 ]);
        //             }
        //         }
        //     } catch (\Throwable $th) {
        //         //throw $th;
        //     }
        // }

        $desa = District::all();
        return $desa;
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
