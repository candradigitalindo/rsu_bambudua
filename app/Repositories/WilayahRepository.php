<?php

namespace App\Repositories;

use App\Models\City;
use App\Models\District;
use App\Models\Province;
use App\Models\Satusehat;
use App\Models\Subdistrict;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Promise\Promise;


class WilayahRepository
{
    private $satusehatRepository;

    /**
     * Create a new class instance.
     */
    public function __construct(SatusehatRepository $satusehatRepository)
    {
        $this->satusehatRepository = $satusehatRepository;
    }

    /**
     * Helper method untuk request ke Satu Sehat API dengan error handling
     */
    private function satusehatRequest($endpoint, $params = [])
    {
        try {
            $satusehat = Satusehat::first();

            if (!$satusehat) {
                Log::warning('WilayahRepository: Konfigurasi Satu Sehat tidak tersedia');
                return null;
            }

            // Auto refresh token jika expired atau tidak ada
            if (!$satusehat->access_token || $satusehat->expired_in <= date('Y-m-d H:i:s')) {
                Log::info('WilayahRepository: Token expired, melakukan refresh...');
                $this->satusehatRepository->accesstoken();
                $satusehat = Satusehat::first(); // Reload data setelah refresh

                if (!$satusehat || !$satusehat->access_token) {
                    Log::error('WilayahRepository: Gagal refresh token Satu Sehat');
                    return null;
                }
            }

            $baseUrl = $satusehat->status == 1 ? env("URL_SANDBOX") : env("URL_PRODUCTION");
            $url = $baseUrl . $endpoint;

            $response = Http::timeout(30)
                ->retry(2, 1000)
                ->withHeaders(['Authorization' => 'Bearer ' . $satusehat->access_token])
                ->get($url, $params);

            if (!$response->successful()) {
                Log::error("WilayahRepository: API request gagal - {$endpoint}", [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return null;
            }

            $data = $response->json();

            if (!isset($data['data']) || $data['data'] === null) {
                Log::warning("WilayahRepository: Tidak ada data dari API - {$endpoint}", [
                    'message' => $data['message'] ?? 'Unknown',
                    'params' => $params
                ]);
                return null;
            }

            return $data['data'];
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error("WilayahRepository: Koneksi timeout - {$endpoint}", [
                'error' => $e->getMessage()
            ]);
            return null;
        } catch (\Throwable $th) {
            Log::error("WilayahRepository: Error tidak terduga - {$endpoint}", [
                'error' => $th->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Fungsi untuk menyimpan / update data Wilayah
     */

    public function saveProvince()
    {
        ini_set('max_execution_time', 10000);

        try {
            $resProvinsi = $this->satusehatRequest("/masterdata/v1/provinces");

            if ($resProvinsi === null) {
                return false;
            }

            // Hapus semua data provinsi lama beserta relasi city dan district
            Log::info('WilayahRepository: Menghapus semua data wilayah lama...');
            District::truncate();
            City::truncate();
            Province::truncate();

            // Insert data provinsi baru dari Satu Sehat
            foreach ($resProvinsi as $provinsi) {
                Province::create([
                    'code'        => $provinsi['code'],
                    'parent_code' => $provinsi['parent_code'],
                    'bps_code'    => $provinsi['bps_code'],
                    'name'        => $provinsi['name']
                ]);
            }

            Log::info('WilayahRepository: Berhasil sync ' . count($resProvinsi) . ' provinsi');
            return true;
        } catch (\Throwable $th) {
            Log::error('WilayahRepository: Error saat sync provinsi', [
                'error' => $th->getMessage()
            ]);
            return false;
        }
    }

    public function saveKota($code)
    {
        ini_set('max_execution_time', 10000);

        try {
            $province = Province::where('code', $code)->first();

            if (!$province) {
                Log::warning("WilayahRepository: Provinsi tidak ditemukan - {$code}");
                return false;
            }

            $resCities = $this->satusehatRequest("/masterdata/v1/cities", [
                'province_codes' => $province->bps_code
            ]);

            if ($resCities === null) {
                return false;
            }

            // Hapus semua kota/kabupaten di provinsi ini beserta district
            Log::info("WilayahRepository: Menghapus data kota lama di provinsi {$province->name}...");

            // Hapus district di provinsi ini
            District::where('province_code', $code)->delete();
            // Hapus city di provinsi ini
            City::where('parent_code', $code)->delete();

            // Insert data kota baru dari Satu Sehat
            foreach ($resCities as $city) {
                City::create([
                    'code'        => $city['code'],
                    'parent_code' => $city['parent_code'],
                    'bps_code'    => $city['bps_code'],
                    'name'        => $city['name']
                ]);
            }

            Log::info("WilayahRepository: Berhasil sync " . count($resCities) . " kota/kabupaten untuk provinsi {$province->name}");
            return true;
        } catch (\Throwable $th) {
            Log::error('WilayahRepository: Error saat sync kota', [
                'error' => $th->getMessage(),
                'code' => $code
            ]);
            return false;
        }
    }

    public function saveKecamatan($code)
    {
        ini_set('max_execution_time', 20000);

        try {
            $kotas = City::where('parent_code', $code)->get();

            if ($kotas->isEmpty()) {
                Log::warning("WilayahRepository: Tidak ada kota untuk provinsi - {$code}");
                return false;
            }

            // Hapus semua kecamatan di provinsi ini
            Log::info("WilayahRepository: Menghapus data kecamatan lama di provinsi {$code}...");
            District::where('province_code', $code)->delete();

            $totalSynced = 0;

            foreach ($kotas as $kota) {
                $resDistricts = $this->satusehatRequest("/masterdata/v1/districts", [
                    'city_codes' => $kota->bps_code
                ]);

                if ($resDistricts === null) {
                    continue; // Skip jika request gagal
                }

                foreach ($resDistricts as $district) {
                    District::create([
                        'code'          => $district['code'],
                        'province_code' => $code,
                        'parent_code'   => $district['parent_code'],
                        'bps_code'      => $district['bps_code'],
                        'name'          => $district['name']
                    ]);
                    $totalSynced++;
                }
            }

            Log::info("WilayahRepository: Berhasil sync {$totalSynced} kecamatan");
            return true;
        } catch (\Throwable $th) {
            Log::error('WilayahRepository: Error saat sync kecamatan', [
                'error' => $th->getMessage(),
                'code' => $code
            ]);
            return false;
        }
    }

    /**
     * Method saveDesa dihapus karena data desa/kelurahan sulit didapatkan dari Satu Sehat API
     * Data wilayah hanya sampai tingkat Kecamatan
     */
    // public function saveDesa($code)
    // {
    //     ini_set('max_execution_time', 20000);
    //
    //     try {
    //         $kecamatans = District::where('province_code', $code)->get();
    //
    //         if ($kecamatans->isEmpty()) {
    //             Log::warning("WilayahRepository: Tidak ada kecamatan untuk provinsi - {$code}");
    //             return false;
    //         }
    //
    //         // Hapus semua desa/kelurahan di provinsi ini
    //         Log::info("WilayahRepository: Menghapus data desa lama di provinsi {$code}...");
    //         Subdistrict::where('province_code', $code)->delete();
    //
    //         $totalSynced = 0;
    //
    //         foreach ($kecamatans as $kecamatan) {
    //             $resDesa = $this->satusehatRequest("/masterdata/v1/sub-districts", [
    //                 'district_codes' => $kecamatan->bps_code
    //             ]);
    //
    //             if ($resDesa === null) {
    //                 continue; // Skip jika request gagal
    //             }
    //
    //             foreach ($resDesa as $desa) {
    //                 Subdistrict::create([
    //                     'code'          => $desa['code'],
    //                     'province_code' => $code,
    //                     'parent_code'   => $desa['parent_code'],
    //                     'bps_code'      => $desa['bps_code'],
    //                     'name'          => $desa['name']
    //                 ]);
    //                 $totalSynced++;
    //             }
    //         }
    //
    //         Log::info("WilayahRepository: Berhasil sync {$totalSynced} desa/kelurahan");
    //         return true;
    //     } catch (\Throwable $th) {
    //         Log::error('WilayahRepository: Error saat sync desa/kelurahan', [
    //             'error' => $th->getMessage(),
    //             'code' => $code
    //         ]);
    //         return false;
    //     }
    // }

    public function getWilayah()
    {
        $province       = Province::count();
        $city           = City::count();
        $district       = District::count();
        $dataProvince   = Province::orderBy('name', 'ASC')->get();
        $dataProvince->map(function ($provinsi) {
            $provinsi['kota']       = City::where('parent_code', $provinsi->code)->count();
            $provinsi['kecamatan']  = District::where('province_code', $provinsi->code)->count();
        });

        return ['provinsi' => $province, 'kota' => $city, 'kecamatan' => $district, 'dataProvinces' => $dataProvince];
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
