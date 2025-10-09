<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Province;
use App\Models\City;

class WilayahSeeder extends Seeder
{
    public function run(): void
    {
        $provinces = [
            ['code' => 'ID-AC',  'bps_code' => '11', 'name' => 'Aceh'],
            ['code' => 'ID-SU',  'bps_code' => '12', 'name' => 'Sumatera Utara'],
            ['code' => 'ID-SB',  'bps_code' => '13', 'name' => 'Sumatera Barat'],
            ['code' => 'ID-RI',  'bps_code' => '14', 'name' => 'Riau'],
            ['code' => 'ID-JK',  'bps_code' => '31', 'name' => 'DKI Jakarta'],
            ['code' => 'ID-JB',  'bps_code' => '32', 'name' => 'Jawa Barat'],
            ['code' => 'ID-JT',  'bps_code' => '33', 'name' => 'Jawa Tengah'],
            ['code' => 'ID-YO',  'bps_code' => '34', 'name' => 'DI Yogyakarta'],
            ['code' => 'ID-JI',  'bps_code' => '35', 'name' => 'Jawa Timur'],
            ['code' => 'ID-BA',  'bps_code' => '51', 'name' => 'Bali'],
            ['code' => 'ID-NTB', 'bps_code' => '52', 'name' => 'Nusa Tenggara Barat'],
            ['code' => 'ID-NTT', 'bps_code' => '53', 'name' => 'Nusa Tenggara Timur'],
        ];

        $citiesByProvince = [
            'ID-AC'  => [
                ['code' => 'ID-AC-01', 'bps_code' => '1171', 'name' => 'Banda Aceh'],
                ['code' => 'ID-AC-02', 'bps_code' => '1173', 'name' => 'Lhokseumawe'],
            ],
            'ID-SU'  => [
                ['code' => 'ID-SU-01', 'bps_code' => '1271', 'name' => 'Medan'],
                ['code' => 'ID-SU-02', 'bps_code' => '1275', 'name' => 'Binjai'],
            ],
            'ID-SB'  => [
                ['code' => 'ID-SB-01', 'bps_code' => '1371', 'name' => 'Padang'],
                ['code' => 'ID-SB-02', 'bps_code' => '1372', 'name' => 'Solok'],
                ['code' => 'ID-SB-03', 'bps_code' => '1375', 'name' => 'Bukittinggi'],
            ],
            'ID-RI'  => [
                ['code' => 'ID-RI-01', 'bps_code' => '1471', 'name' => 'Pekanbaru'],
                ['code' => 'ID-RI-02', 'bps_code' => '1472', 'name' => 'Dumai'],
            ],
            'ID-JK'  => [
                ['code' => 'ID-JK-01', 'bps_code' => '3171', 'name' => 'Jakarta Pusat'],
                ['code' => 'ID-JK-02', 'bps_code' => '3172', 'name' => 'Jakarta Utara'],
                ['code' => 'ID-JK-03', 'bps_code' => '3173', 'name' => 'Jakarta Barat'],
                ['code' => 'ID-JK-04', 'bps_code' => '3174', 'name' => 'Jakarta Selatan'],
                ['code' => 'ID-JK-05', 'bps_code' => '3175', 'name' => 'Jakarta Timur'],
            ],
            'ID-JB'  => [
                ['code' => 'ID-JB-01', 'bps_code' => '3273', 'name' => 'Bandung'],
                ['code' => 'ID-JB-02', 'bps_code' => '3271', 'name' => 'Bogor'],
                ['code' => 'ID-JB-03', 'bps_code' => '3275', 'name' => 'Bekasi'],
                ['code' => 'ID-JB-04', 'bps_code' => '3276', 'name' => 'Depok'],
            ],
            'ID-JT'  => [
                ['code' => 'ID-JT-01', 'bps_code' => '3374', 'name' => 'Semarang'],
                ['code' => 'ID-JT-02', 'bps_code' => '3372', 'name' => 'Surakarta'],
                ['code' => 'ID-JT-03', 'bps_code' => '3371', 'name' => 'Magelang'],
            ],
            'ID-YO'  => [
                ['code' => 'ID-YO-01', 'bps_code' => '3471', 'name' => 'Yogyakarta'],
            ],
            'ID-JI'  => [
                ['code' => 'ID-JI-01', 'bps_code' => '3578', 'name' => 'Surabaya'],
                ['code' => 'ID-JI-02', 'bps_code' => '3573', 'name' => 'Malang'],
                ['code' => 'ID-JI-03', 'bps_code' => '3571', 'name' => 'Kediri'],
            ],
            'ID-BA'  => [
                ['code' => 'ID-BA-01', 'bps_code' => '5171', 'name' => 'Denpasar'],
                ['code' => 'ID-BA-02', 'bps_code' => '5103', 'name' => 'Badung'],
            ],
            'ID-NTB' => [
                ['code' => 'ID-NTB-01', 'bps_code' => '5271', 'name' => 'Mataram'],
                ['code' => 'ID-NTB-02', 'bps_code' => '5272', 'name' => 'Bima'],
            ],
            'ID-NTT' => [
                ['code' => 'ID-NTT-01', 'bps_code' => '5371', 'name' => 'Kupang'],
                ['code' => 'ID-NTT-02', 'bps_code' => '5308', 'name' => 'Ende'],
            ],
        ];

        foreach ($provinces as $prov) {
            $province = Province::updateOrCreate(
                ['code' => $prov['code']],
                [
                    'parent_code' => 'ID',
                    'bps_code'    => $prov['bps_code'],
                    'name'        => $prov['name'],
                ]
            );
            // set status manually (not in fillable)
            if ((int) ($province->status ?? 0) !== 1) {
                $province->status = 1;
                $province->save();
            }

            $cities = $citiesByProvince[$prov['code']] ?? [];
            foreach ($cities as $city) {
                City::updateOrCreate(
                    ['code' => $city['code']],
                    [
                        'parent_code' => $prov['code'],
                        'bps_code'    => $city['bps_code'],
                        'name'        => $city['name'],
                    ]
                );
            }
        }
    }
}
