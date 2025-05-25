<?php

namespace App\Repositories;

use App\Models\Pasien;

class ObservasiRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }
    public function riwayatPenyakit($id)
    {
        // Ambil riwayat penyakit berdasarkan rekam_medis di encounter
        $encounter = \App\Models\Encounter::find($id);
        if ($encounter) {
            $pasien = Pasien::where('rekam_medis', $encounter->rekam_medis)->first();
            if ($pasien) {
                $riwayatPenyakit = \App\Models\RiwayatPenyakit::where('pasien_id', $pasien->id)->first();
                if ($riwayatPenyakit) {
                    // ambil anamnesis berdasarkan encounter_id
                    $anamnesis = \App\Models\Anamnesis::where('encounter_id', $id)->first();
                    if ($anamnesis) {
                        return ['anamnesis' => $anamnesis, 'riwayatPenyakit' => $riwayatPenyakit];
                    } else {
                        return ['anamnesis' => null, 'riwayatPenyakit' => $riwayatPenyakit]; // Anamnesis tidak ditemukan
                    }
                } else {
                    return null; // Riwayat penyakit tidak ditemukan
                }
            } else {
                return null; // Pasien tidak ditemukan
            }
        } else {
            return null; // Encounter tidak ditemukan
        }
    }
    public function postAnemnesis($request, $id)
    {
        // Cek apakah anamnesis sudah ada
        $anamnesis = \App\Models\Anamnesis::where('encounter_id', $id)->first();
        if ($anamnesis) {
            // Jika sudah ada, update data
            $anamnesis->keluhan_utama = $request->keluhan_utama;
            $anamnesis->save();
        } else {
            // Jika belum ada, buat data baru
            $anamnesis = new \App\Models\Anamnesis();
            $anamnesis->encounter_id = $id;
            $anamnesis->keluhan_utama = $request->keluhan_utama;
            $anamnesis->save();
        }
        // Ambil data pasien berdasarkan rekam_medis di encounter
        $encounter = \App\Models\Encounter::find($id);
        if ($encounter) {
            $pasien = \App\Models\Pasien::where('rekam_medis', $encounter->rekam_medis)->first();
            // Cek apakah riwayat penyakit sudah ada
            $riwayatPenyakit = \App\Models\RiwayatPenyakit::where('pasien_id', $pasien->id)->first();
            if ($riwayatPenyakit) {
                // Jika sudah ada, update data
                $riwayatPenyakit->riwayat_penyakit = $request->riwayat_penyakit;
                $riwayatPenyakit->riwayat_penyakit_keluarga = $request->riwayat_penyakit_keluarga;
                $riwayatPenyakit->save();
            } else {
                // Jika belum ada, buat data baru
                $riwayatPenyakit = new \App\Models\RiwayatPenyakit();
                $riwayatPenyakit->pasien_id = $pasien->id;
                $riwayatPenyakit->riwayat_penyakit = $request->riwayat_penyakit;
                $riwayatPenyakit->riwayat_penyakit_keluarga = $request->riwayat_penyakit_keluarga;
                $riwayatPenyakit->save();
            }
        }
        return [$anamnesis, $riwayatPenyakit];
    }
    public function tandaVital($id)
    {
        $tandaVital = \App\Models\TandaVital::where('encounter_id', $id)->first();
        return $tandaVital ? $tandaVital : null;
    }
    // post tanda vital
    public function postTandaVital($request, $id)
    {
        // Cek apakah tanda vital sudah ada
        $tandaVital = \App\Models\TandaVital::where('encounter_id', $id)->first();
        if ($tandaVital) {
            // Jika sudah ada, update data
            $tandaVital->nadi = $request->nadi;
            $tandaVital->pernapasan = $request->pernapasan;
            $tandaVital->sistolik = $request->sistolik;
            $tandaVital->diastolik = $request->diastolik;
            $tandaVital->suhu = $request->suhu;
            $tandaVital->berat_badan = $request->berat_badan;
            $tandaVital->tinggi_badan = $request->tinggi_badan;
            $tandaVital->kesadaran = $request->kesadaran;
            $tandaVital->save();
        } else {
            // Jika belum ada, buat data baru
            $tandaVital = new \App\Models\TandaVital();
            $tandaVital->encounter_id = $id;
            $tandaVital->nadi = $request->nadi;
            $tandaVital->pernapasan = $request->pernapasan;
            $tandaVital->sistolik = $request->sistolik;
            $tandaVital->diastolik = $request->diastolik;
            $tandaVital->suhu = $request->suhu;
            $tandaVital->berat_badan = $request->berat_badan;
            $tandaVital->tinggi_badan = $request->tinggi_badan;
            $tandaVital->kesadaran = $request->kesadaran;
            $tandaVital->save();
        }
        return $tandaVital;
    }
    public function pemeriksaanPenunjang($id)
    {
        $pemeriksaanPenunjang = \App\Models\PemeriksaanPenunjang::where('encounter_id', $id)->get();
        if ($pemeriksaanPenunjang->isEmpty()) {
            return null; // Jika tidak ada data pemeriksaan penunjang
        }
        // Jika ada dokumen pemeriksaan, ambil nama file
        foreach ($pemeriksaanPenunjang as $item) {
            if ($item->dokumen_pemeriksaan) {
                $item->dokumen_pemeriksaan = url('uploads/' . $item->dokumen_pemeriksaan);
            }
        }
        return $pemeriksaanPenunjang;
    }
    public function postPemeriksaanPenunjang($request, $id)
    {
        // Jika belum ada, buat data baru
        $pemeriksaanPenunjang = new \App\Models\PemeriksaanPenunjang();
        $pemeriksaanPenunjang->encounter_id = $id;
        $pemeriksaanPenunjang->jenis_pemeriksaan = $request->jenis_pemeriksaan;
        $pemeriksaanPenunjang->hasil_pemeriksaan = $request->hasil_pemeriksaan;
        if ($request->hasFile('dokumen_pemeriksaan')) {
            $file = $request->file('dokumen_pemeriksaan');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads'), $filename);
            $pemeriksaanPenunjang->dokumen_pemeriksaan = $filename;
        }
        $pemeriksaanPenunjang->save();

        return $pemeriksaanPenunjang;
    }
    public function deletePemeriksaanPenunjang($id)
    {
        $pemeriksaanPenunjang = \App\Models\PemeriksaanPenunjang::find($id);
        if ($pemeriksaanPenunjang) {
            // Hapus file dokumen jika ada
            if ($pemeriksaanPenunjang->dokumen_pemeriksaan) {
                $filePath = public_path('uploads/' . $pemeriksaanPenunjang->dokumen_pemeriksaan);
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
            $pemeriksaanPenunjang->delete();
            return true;
        }
        return false;
    }
    // Ambil data tindakan
    public function getTindakan($id)
    {
        $tindakan = \App\Models\Tindakan::all();
        if ($tindakan->isEmpty()) {
            return null; // Jika tidak ada data tindakan
        }
        return $tindakan;
    }
    // Ambil data tindakan_ecounter berdasarkan encounter_id
    public function getTindakanEncounter($id)
    {
        $tindakan = \App\Models\TindakanEncounter::where('encounter_id', $id)->get();
        if ($tindakan->isEmpty()) {
            return null; // Jika tidak ada data tindakan
        }
        // Tambahkan grand total_harga
        $grandTotal = 0;
        foreach ($tindakan as $item) {
            $grandTotal += $item->total_harga;
        }

        return $tindakan;
    }
    // Post tindakan encounter
    public function postTindakanEncounter($request, $id)
    {
        // Cek apakah tindakan sudah ada
        $tindakan = \App\Models\TindakanEncounter::where('encounter_id', $id)->where('tindakan_id', $request->jenis_tindakan)->first();
        if ($tindakan) {
            // Jika ada jumlahkan qty
            $tindakan->qty += $request->qty;
            $tindakan->total_harga = $tindakan->qty * $tindakan->tindakan_harga;
            $tindakan->save();
            // Ambil tindakan bahan
            $tindakanBahan = \App\Models\TindakanBahan::where('tindakan_id', $request->jenis_tindakan)->with('bahan')->get();
            if ($tindakanBahan->isEmpty()) {
                return null; // Jika tidak ada data tindakan bahan
            } else {
                foreach ($tindakanBahan as $item) {
                    // Cek apakah sudah ada request bahan
                    // Ambil stokbahan dengan expired terdekat dan is_available = 1
                    $stokBahanTerdekat = \App\Models\StokBahan::where('bahan_id', $item->bahan_id)
                        ->where('is_available', true)
                        ->orderBy('expired_at', 'asc')
                        ->first();

                    // Jika stok ditemukan, gunakan expired_at dari stok tersebut
                    $expiredAt = $stokBahanTerdekat ? $stokBahanTerdekat->expired_at : null;

                    // Cek apakah sudah ada request bahan
                    $requestBahan = \App\Models\RequestBahan::where('encounter_id', $id)
                        ->where('bahan_id', $item->bahan_id)
                        ->where('status', 0)
                        ->first();

                    if ($requestBahan) {
                        // Jika ada, jumlahkan qty
                        $requestBahan->qty += $item->quantity;
                        if ($expiredAt) {
                            $requestBahan->expired_at = $expiredAt;
                        }
                        $requestBahan->save();
                    } else {
                        // Jika belum ada, buat data baru
                        $requestBahan = new \App\Models\RequestBahan();
                        $requestBahan->encounter_id = $id;
                        $requestBahan->bahan_id = $item->bahan_id;
                        $requestBahan->qty = $item->quantity;
                        $requestBahan->nama_bahan = $item->bahan->name;
                        $requestBahan->status = 0;
                        $requestBahan->keterangan = 'Request Bahan Tindakan';
                        if ($expiredAt) {
                            $requestBahan->expired_at = $expiredAt;
                        }
                        $requestBahan->save();
                    }
                }
            }
            return $tindakan; // Kembalikan data tindakan yang sudah ada
        } else {
            // ambil data tindakan
            $tindakan = \App\Models\Tindakan::find($request->jenis_tindakan);
            // Jika tidak ada data tindakan
            if (!$tindakan) {
                return null; // Jika tidak ada data tindakan
            }
            // Jika belum ada, buat data baru
            $tindakanEncounter = new \App\Models\TindakanEncounter();
            $tindakanEncounter->encounter_id = $id;
            $tindakanEncounter->tindakan_id = $request->jenis_tindakan;
            $tindakanEncounter->tindakan_name = $tindakan->name;
            $tindakanEncounter->tindakan_harga = $tindakan->harga;
            $tindakanEncounter->qty = $request->qty;
            $tindakanEncounter->total_harga = $request->qty * $tindakan->harga;
            $tindakanEncounter->save();

            // Ambil tindakan bahan
            $tindakanBahan = \App\Models\TindakanBahan::where('tindakan_id', $request->jenis_tindakan)->with('bahan')->get();

            if ($tindakanBahan->isEmpty()) {
                return null; // Jika tidak ada data tindakan bahan
            } else {
                foreach ($tindakanBahan as $item) {
                    // Ambil stokbahan dengan expired terdekat dan is_available = 1
                    $stokBahanTerdekat = \App\Models\StokBahan::where('bahan_id', $item->bahan_id)
                        ->where('is_available', true)
                        ->orderBy('expired_at', 'asc')
                        ->first();

                    $expiredAt = $stokBahanTerdekat ? $stokBahanTerdekat->expired_at : null;

                    // Cek apakah sudah ada request bahan
                    $requestBahan = \App\Models\RequestBahan::where('encounter_id', $id)
                        ->where('bahan_id', $item->bahan_id)
                        ->where('status', 0)
                        ->first();

                    if ($requestBahan) {
                        // Jika ada, jumlahkan qty
                        $requestBahan->qty += $item->quantity;
                        $requestBahan->save();
                    } else {
                        // Jika belum ada, buat data baru
                        $requestBahan = new \App\Models\RequestBahan();
                        $requestBahan->encounter_id = $id;
                        $requestBahan->bahan_id = $item->bahan_id;
                        $requestBahan->qty = $item->quantity;
                        $requestBahan->nama_bahan = $item->bahan->name;
                        $requestBahan->status = 0;
                        $requestBahan->keterangan = 'Request Bahan Tindakan';
                        if ($expiredAt) {
                            $requestBahan->expired_at = $expiredAt;
                        }
                        $requestBahan->save();
                    }
                }
            }
            return $tindakanEncounter; // Kembalikan data tindakan encounter yang baru
        }
    }
    public function deleteTindakanEncounter($id)
    {
        $tindakan = \App\Models\TindakanEncounter::find($id);
        if ($tindakan) {
            // Ambil semua bahan terkait tindakan ini
            $tindakanBahans = \App\Models\TindakanBahan::where('tindakan_id', $tindakan->tindakan_id)->get();

            // Cek jika ada RequestBahan status = 1
            foreach ($tindakanBahans as $bahan) {
                $adaRequestBahanStatus1 = \App\Models\RequestBahan::where('encounter_id', $tindakan->encounter_id)
                    ->where('bahan_id', $bahan->bahan_id)
                    ->where('status', 1)
                    ->exists();
                if ($adaRequestBahanStatus1) {
                    // Tidak boleh hapus, return false atau pesan error
                    return [
                        'success' => false,
                        'message' => 'Tidak bisa menghapus tindakan karena ada bahan yang sudah diproses.'
                    ];
                }
            }

            // Jika aman, hapus semua request bahan status 0
            foreach ($tindakanBahans as $bahan) {
                \App\Models\RequestBahan::where('encounter_id', $tindakan->encounter_id)
                    ->where('bahan_id', $bahan->bahan_id)
                    ->where('status', 0)
                    ->delete();
            }
            // Hapus data tindakan
            $tindakan->delete();

            return [
                'success' => true,
                'message' => 'Tindakan berhasil dihapus.'
            ];
        }
        return [
            'success' => false,
            'message' => 'Tindakan tidak ditemukan.'
        ];
    }
    // Ambil data icd10
    public function getIcd10($id)
    {
        $icd10 = \App\Models\Icd10::all();
        return $icd10;
    }
    // Ambil semua data diagosis sesuai encounter_id
    public function getDiagnosis($id)
    {
        $diagnosis = \App\Models\Diagnosis::where('encounter_id', $id)->get();
        if ($diagnosis->isEmpty()) {
            return null; // Jika tidak ada data diagnosis
        }
        return $diagnosis;
    }
    // Post diagnosis
    public function postDiagnosis($request, $id)
    {
        // Cek apakah diagnosis sudah ada
        $diagnosis = \App\Models\Diagnosis::where('encounter_id', $id)->where('diagnosis_code', $request->icd10_id)->first();
        if (!$diagnosis) {
            // Cari data icd10
            $icd10 = \App\Models\Icd10::where('code', $request->icd10_id)->first();
            // Jika belum ada, buat data baru
            $diagnosis = new \App\Models\Diagnosis();
            $diagnosis->encounter_id = $id;
            $diagnosis->diagnosis_code = $icd10->code;
            $diagnosis->diagnosis_description = $icd10->description;
            $diagnosis->diagnosis_type = $request->diagnosis_type;
            // Petugas harus mempunyai role dokter
            $diagnosis->id_petugas = auth()->user()->id_petugas;
            $diagnosis->petugas_name = auth()->user()->name;
            $diagnosis->save();
        }
        return $diagnosis;
    }
    // Hapus diagnosis
    public function deleteDiagnosis($id)
    {
        $diagnosis = \App\Models\Diagnosis::find($id);
        if ($diagnosis) {
            $diagnosis->delete();
            return [
                'success' => true,
                'message' => 'Diagnosis berhasil dihapus.'
            ];
        }
        return [
            'success' => false,
            'message' => 'Diagnosis tidak ditemukan.'
        ];
    }

    // Ambil data resep
    public function getResep($id)
    {
        $resep = \App\Models\Resep::where('encounter_id', $id)->with('details')->first();
        if (!$resep) {
            return null; // Jika tidak ada data resep
        }
        return $resep;
    }
    // Post resep
    public function postResep($request, $id)
    {
        // Cek apakah resep sudah ada
        $resep = \App\Models\Resep::where('encounter_id', $id)->first();
        if ($resep) {
            // Jika sudah ada, update data
            $resep->masa_pemakaian_hari = $request->masa_pemakaian_hari;
            $resep->save();
        } else {
            // Ambil kode resep terbesar dari seluruh tabel
            $lastKodeResep = \App\Models\Resep::max('kode_resep');
            if ($lastKodeResep) {
                $lastNumber = (int) substr($lastKodeResep, 3);
                $kodeResep = 'RSP' . str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);
            } else {
                $kodeResep = 'RSP00001';
            }
            // Jika belum ada, buat data baru
            $resep = new \App\Models\Resep();
            $resep->encounter_id = $id;
            $resep->kode_resep = $kodeResep;
            $resep->masa_pemakaian_hari = $request->masa_pemakaian_hari;
            $resep->dokter = auth()->user()->name;
            $resep->save();
        }
        return $resep;
    }
    // get peoduk apotek
    public function getProdukApotek($id)
    {
        $query = \App\Models\ProductApotek::query();

        $search = request('search');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('code', 'like', '%' . $search . '%');
            });
        }
        $query->where('type', 0);


        // Untuk kebutuhan Select2 AJAX, biasanya tidak perlu paginate
        return $query->limit(20)->get(['id', 'code', 'name', 'satuan', 'harga', 'stok']);
    }
    // Post resep detail ambil  dari encounter_id
    public function postResepDetail($request, $id)
    {
        // Cek apakah resep sudah ada
        $resep = \App\Models\Resep::where('encounter_id', $id)->first();
        if ($resep) {
            // Cek stok apotek: ambil stok dengan expired terdekat, status=0, dan expired_at >= hari ini
            $stokTerdekat = \App\Models\ApotekStok::where('product_apotek_id', $request->product_apotek_id)
                ->where('status', 0)
                ->where(function($q) {
                    $q->whereNull('expired_at')
                      ->orWhere('expired_at', '>=', now()->toDateString());
                })
                ->orderBy('expired_at', 'asc')
                ->first();

            // Tidak ada stok yang valid
            if (!$stokTerdekat) {
                return [
                    'success' => false,
                    'message' => 'Stok tidak tersedia atau sudah expired.'
                ];
            }
            // Cek apakah sudah ada resep detail
            $resepDetail = \App\Models\ResepDetail::where('resep_id', $resep->id)
                ->where('product_apotek_id', $request->product_apotek_id)
                ->first();
            if ($resepDetail) {
                // Jika ada, jumlahkan qty
                $resepDetail->qty += $request->qty;
                $resepDetail->total_harga = $stokTerdekat->productApotek->harga * $resepDetail->qty; // simpan total harga
                $resepDetail->save();
                return $resepDetail; // Kembalikan data resep detail yang sudah ada
            }
            // Jika belum ada, buat data baru
            // Buat data resep detail
            $resepDetail = new \App\Models\ResepDetail();
            $resepDetail->resep_id = $resep->id;
            $resepDetail->product_apotek_id = $request->product_apotek_id;
            $resepDetail->nama_obat = $stokTerdekat->productApotek->name;
            $resepDetail->qty = $request->qty;
            $resepDetail->aturan_pakai = $request->aturan_pakai;
            $resepDetail->expired_at = $stokTerdekat->expired_at; // simpan expired yang diambil
            $resepDetail->harga = $stokTerdekat->productApotek->harga; // simpan harga
            $resepDetail->total_harga = $stokTerdekat->productApotek->harga * $request->qty; // simpan total harga
            $resepDetail->save();

            return $resepDetail;
        }
        return null;
    }
    // Hapus resep detail
    public function deleteResepDetail($id)
    {
        $resepDetail = \App\Models\ResepDetail::find($id);
        if ($resepDetail) {
            $resepDetail->delete();
            return [
                'success' => true,
                'message' => 'Resep detail berhasil dihapus.'
            ];
        }
        return [
            'success' => false,
            'message' => 'Resep detail tidak ditemukan.'
        ];
    }
}
