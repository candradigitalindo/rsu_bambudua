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
            }else {
                foreach ($tindakanBahan as $item) {
                    // Cek apakah sudah ada request bahan
                    $requestBahan = \App\Models\RequestBahan::where('encounter_id', $id)->where('bahan_id', $item->bahan_id)->where('status', 0)->first();
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
                    // Cek apakah sudah ada request bahan
                    $requestBahan = \App\Models\RequestBahan::where('encounter_id', $id)->where('bahan_id', $item->bahan_id)->where('status', 0)->first();
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
                        $requestBahan->save();
                    }
                }
            }
            return $tindakanEncounter; // Kembalikan data tindakan yang sudah ada
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
}
