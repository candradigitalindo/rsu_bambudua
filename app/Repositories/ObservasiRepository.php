<?php

namespace App\Repositories;

use App\Models\Encounter;
use App\Models\InpatientAdmission;
use App\Models\IncentiveSetting;
use App\Models\InpatientDailyMedication;
use App\Models\InpatientTreatment;
use App\Models\Pasien;
use App\Models\Practitioner;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

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
    // Get doctor
    public function getDokters($id)
    {
        // [MODIFIED] Ambil SEMUA dokter yang sudah menangani encounter ini
        $practitioners = Practitioner::with('user')->where('encounter_id', $id)->get();
        $selected_doctor_ids = $practitioners->pluck('user.id')->filter()->toArray();

        // Ambil semua user role dokter
        $dokters = \App\Models\User::whereIn('role', [1, 2])->get(); // Hanya Owner dan Dokter

        return [
            'dokter_terpilih' => $selected_doctor_ids,
            'dokters' => $dokters
        ];
    }
    // Get Perawat
    public function getPerawats($id)
    {
        // Ambil encounter beserta relasi perawat (nurses)
        $encounter = \App\Models\Encounter::with('nurses')->find($id);

        // Ambil semua user role perawat
        $perawats = \App\Models\User::where('role', 3)->get();

        // Ambil array id perawat yang sudah menangani encounter ini
        $perawat_terpilih = ($encounter && $encounter->nurses)
            ? $encounter->nurses->pluck('id')->toArray()
            : [];

        return [
            'perawat_terpilih' => $perawat_terpilih,
            'perawats' => $perawats
        ];
    }
    public function postAnemnesis($request, $id)
    {
        // Update atau buat anamnesis
        $anamnesis = \App\Models\Anamnesis::updateOrCreate(
            ['encounter_id' => $id],
            ['keluhan_utama' => $request->keluhan_utama]
        );

        // Ambil encounter dan pasien
        $encounter = \App\Models\Encounter::find($id);
        $pasien = $encounter ? \App\Models\Pasien::where('rekam_medis', $encounter->rekam_medis)->first() : null;

        // Update atau buat riwayat penyakit jika pasien ditemukan
        if ($pasien) {
            \App\Models\RiwayatPenyakit::updateOrCreate(
                ['pasien_id' => $pasien->id],
                [
                    'riwayat_penyakit' => $request->riwayat_penyakit,
                    'riwayat_penyakit_keluarga' => $request->riwayat_penyakit_keluarga
                ]
            );
        }

        // [MODIFIED] Handle multiple doctors with proper UUID generation. Hapus yang lama, sisipkan yang baru.
        if ($request->has('dokter_ids')) {
            // Hapus semua practitioner yang ada untuk encounter ini
            \App\Models\Practitioner::where('encounter_id', $id)->delete();

            $doctorIds = $request->input('dokter_ids', []);
            $dokters = \App\Models\User::whereIn('id', $doctorIds)->get();
            foreach ($dokters as $dokter) {
                \App\Models\Practitioner::create([
                    'encounter_id' => $id,
                    'id_petugas'   => $dokter->id, // Use user.id for consistency
                    'satusehat_id' => $dokter->satusehat_id ?? '',
                    'name'         => $dokter->name,
                ]);
            }
        }

        // Ambil data riwayat penyakit terbaru (jika ada)
        $riwayatPenyakit = $pasien
            ? \App\Models\RiwayatPenyakit::where('pasien_id', $pasien->id)->first()
            : null;

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
        // Gunakan updateOrCreate untuk menyederhanakan logika
        $tandaVital = \App\Models\TandaVital::updateOrCreate(
            ['encounter_id' => $id],
            [
                'nadi' => $request->nadi,
                'pernapasan' => $request->pernapasan,
                'sistolik' => $request->sistolik,
                'diastolik' => $request->diastolik,
                'suhu' => $request->suhu,
                'berat_badan' => $request->berat_badan,
                'tinggi_badan' => $request->tinggi_badan,
                'kesadaran' => $request->kesadaran,
            ]
        );
        return $tandaVital;
    }
    public function pemeriksaanPenunjang($id)
    {
        $labRequests = \App\Models\LabRequest::with('items')
            ->where('encounter_id', $id)
            ->orderByDesc('created_at')
            ->get()
            ->flatMap(function ($request) {
                return $request->items->map(function ($item) use ($request) {
                    return [
                        'id' => $item->id,
                        'request_id' => $request->id,
                        'jenis_pemeriksaan' => $item->test_name,
                        'qty' => 1,
                        'harga' => (int) $item->price,
                        'total_harga' => (int) $item->price,
                        'status' => $request->status,
                        'type' => 'lab',
                        'created_at' => optional($request->created_at)->toDateTimeString(),
                    ];
                });
            });

        $radiologyRequests = \App\Models\RadiologyRequest::with('jenis')
            ->where('encounter_id', $id)
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($request) {
                return [
                    'id' => $request->id, // Menggunakan ID request radiologi sebagai ID item
                    'request_id' => $request->id,
                    'jenis_pemeriksaan' => optional($request->jenis)->name,
                    'qty' => 1,
                    'harga' => (int) $request->price,
                    'total_harga' => (int) $request->price,
                    'status' => $request->status,
                    'type' => 'radiologi',
                    'created_at' => optional($request->created_at)->toDateTimeString(),
                ];
            });

        return $labRequests->merge($radiologyRequests)->sortByDesc('created_at')->values();
    }
    public function postPemeriksaanPenunjang($request, $id)
    {
        // Logika ini sekarang ditangani oleh LabRequestController@store
        // Namun, kita bisa membuat LabRequest dari sini untuk menyatukan UI
        $encounter = Encounter::findOrFail($id);
        $test = \App\Models\JenisPemeriksaanPenunjang::findOrFail($request->jenis_pemeriksaan_id);

        // Buat LabRequest baru
        $labRequest = \App\Models\LabRequest::create([
            'encounter_id' => $encounter->id,
            'pasien_id' => $encounter->pasien->id,
            'dokter_id' => Auth::id(),
            'status' => 'requested',
            'notes' => 'Permintaan dari observasi',
        ]);

        // Buat LabRequestItem
        $labRequestItem = $labRequest->items()->create([
            'test_id' => $test->id,
            'test_name' => $test->name,
            'price' => $test->harga,
        ]);

        // [FIX] Panggil method untuk membuat insentif untuk dokter yang login
        $dokterPerujuk = User::find(Auth::id());
        $this->createPemeriksaanPenunjangIncentive($encounter, $dokterPerujuk, $test->name, (float)$test->harga);

        $this->updateEncounterTotalTindakan($encounter->id);

        return $labRequestItem;
    }
    public function deletePemeriksaanPenunjang($id)
    {
        // Coba hapus sebagai LabRequestItem: jika ditemukan, hanya boleh dihapus jika status request/canceled
        $item = \App\Models\LabRequestItem::with('request')->find($id);
        if ($item && $item->request) {
            $status = $item->request->status;
            if (in_array($status, ['requested', 'canceled'])) {
                // Hapus request parent (akan menghapus items juga, jika tidak cascade maka hapus items manual)
                $request = $item->request;
                $encounterId = $request->encounter_id;
                // Hapus semua item terlebih dulu (jaga-jaga jika constraint tidak cascade)
                foreach ($request->items as $it) {
                    $it->delete();
                }
                $request->delete();
                // Update total encounter
                $this->updateEncounterTotalTindakan($encounterId);
                return ['success' => true, 'message' => 'Permintaan laboratorium berhasil dihapus.'];
            }
            return ['success' => false, 'message' => 'Tidak bisa menghapus karena status bukan requested/canceled.'];
        }

        // Fallback ke model lokal jika memang masih ada data lama
        $pemeriksaanPenunjang = \App\Models\PemeriksaanPenunjang::find($id);
        if ($pemeriksaanPenunjang) {
            $encounterId = $pemeriksaanPenunjang->encounter_id;
            $pemeriksaanPenunjang->delete();
            $this->updateEncounterTotalTindakan($encounterId);
            return ['success' => true, 'message' => 'Data pemeriksaan penunjang berhasil dihapus.'];
        }
        return ['success' => false, 'message' => 'Data tidak ditemukan.'];
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
        // Ambil encounter
        $encounter = \App\Models\Encounter::find($id);
        if (!$encounter) {
            return null;
        }

        // Ambil data tindakan
        $tindakan = \App\Models\Tindakan::find($request->jenis_tindakan);
        if (!$tindakan) {
            return null;
        }

        // Cek apakah sudah ada TindakanEncounter
        $tindakanEncounter = \App\Models\TindakanEncounter::where('encounter_id', $id)
            ->where('tindakan_id', $tindakan->id)
            ->first();

        $qty = (int) $request->qty;
        $diskon = isset($request->diskon) ? floatval($request->diskon) : 0;

        if ($tindakanEncounter) {
            $tindakanEncounter->qty += $qty;
        } else {
            $tindakanEncounter = new \App\Models\TindakanEncounter();
            $tindakanEncounter->encounter_id = $id;
            $tindakanEncounter->tindakan_id = $tindakan->id;
            $tindakanEncounter->tindakan_name = $tindakan->name;
            $tindakanEncounter->tindakan_harga = $tindakan->harga;
            $tindakanEncounter->qty = $qty;
        }

        $subtotal = $tindakanEncounter->qty * $tindakan->harga;
        $tindakanEncounter->total_harga = max(0, $subtotal - $diskon);
        $tindakanEncounter->save();

        // Update total_tindakan encounter (gabungkan tindakan medis + pemeriksaan penunjang)
        $this->updateEncounterTotalTindakan($id);

        // Proses bahan (jika ada)
        $tindakanBahans = \App\Models\TindakanBahan::where('tindakan_id', $tindakan->id)->with('bahan')->get();
        foreach ($tindakanBahans as $item) {
            $requestBahan = \App\Models\RequestBahan::firstOrNew([
                'encounter_id' => $id,
                'bahan_id' => $item->bahan_id,
                'status' => 0
            ]);
            $requestBahan->qty = ($requestBahan->exists ? $requestBahan->qty : 0) + $item->quantity;
            $requestBahan->nama_bahan = $item->bahan->name;
            $requestBahan->keterangan = 'Request Bahan Tindakan';
            // Ambil stok bahan expired terdekat
            $stokBahanTerdekat = \App\Models\StokBahan::where('bahan_id', $item->bahan_id)
                ->where('is_available', true)
                ->orderBy('expired_at', 'asc')
                ->first();
            if ($stokBahanTerdekat) {
                $requestBahan->expired_at = $stokBahanTerdekat->expired_at;
            }
            $requestBahan->save();
        }

        return $tindakanEncounter;
    }
    public function deleteTindakanEncounter($id)
    {
        $tindakan = \App\Models\TindakanEncounter::find($id);
        if (!$tindakan) {
            return [
                'success' => false,
                'message' => 'Tindakan tidak ditemukan.'
            ];
        }

        // Cek jika ada RequestBahan status=1 untuk encounter & bahan terkait tindakan ini
        $bahanIds = \App\Models\TindakanBahan::where('tindakan_id', $tindakan->tindakan_id)
            ->pluck('bahan_id');
        $adaRequestBahanStatus1 = \App\Models\RequestBahan::where('encounter_id', $tindakan->encounter_id)
            ->whereIn('bahan_id', $bahanIds)
            ->where('status', 1)
            ->exists();

        if ($adaRequestBahanStatus1) {
            return [
                'success' => false,
                'message' => 'Tidak bisa menghapus tindakan karena ada bahan yang sudah diproses.'
            ];
        }

        // Hapus semua request bahan status 0 sekaligus
        \App\Models\RequestBahan::where('encounter_id', $tindakan->encounter_id)
            ->whereIn('bahan_id', $bahanIds)
            ->where('status', 0)
            ->delete();

        // Hapus tindakan
        $tindakan->delete();

        // Update total_tindakan di encounter (gabungkan tindakan medis + pemeriksaan penunjang)
        $this->updateEncounterTotalTindakan($tindakan->encounter_id);

        return [
            'success' => true,
            'message' => 'Tindakan berhasil dihapus.'
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
        return $diagnosis; // Selalu kembalikan koleksi, meskipun kosong
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
            $diagnosis->id_petugas = \Illuminate\Support\Facades\Auth::user()->id_petugas;
            $diagnosis->petugas_name = \Illuminate\Support\Facades\Auth::user()->name;
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
            $resep->dokter = \Illuminate\Support\Facades\Auth::user()->name;
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
        // Ambil resep berdasarkan encounter_id
        $resep = \App\Models\Resep::where('encounter_id', $id)->first();
        if (!$resep) {
            return null;
        }

        // Ambil stok terdekat yang belum expired dan status=0
        $stokTerdekat = \App\Models\ApotekStok::where('product_apotek_id', $request->product_apotek_id)
            ->where('status', 0)
            ->where(function ($q) {
                $q->whereNull('expired_at')
                    ->orWhere('expired_at', '>=', now()->toDateString());
            })
            ->orderBy('expired_at', 'asc')
            ->first();
        if (!$stokTerdekat) {
            return [
                'success' => false,
                'message' => 'Stok tidak tersedia atau sudah expired.'
            ];
        }

        // Ambil atau buat resep detail
        $resepDetail = \App\Models\ResepDetail::firstOrNew([
            'resep_id' => $resep->id,
            'product_apotek_id' => $request->product_apotek_id
        ]);



        // Jika sudah ada, tambahkan qty, jika belum set qty baru
        $resepDetail->qty = ($resepDetail->exists ? $resepDetail->qty : 0) + $request->qty_obat;
        $resepDetail->nama_obat = $stokTerdekat->productApotek->name;
        $resepDetail->aturan_pakai = $request->aturan_pakai;
        $resepDetail->expired_at = $stokTerdekat->expired_at;
        $resepDetail->harga = $stokTerdekat->productApotek->harga;
        $resepDetail->total_harga = $resepDetail->harga * $resepDetail->qty;
        $resepDetail->save();

        // Update total_resep encounter hanya sekali
        \App\Models\Encounter::where('id', $id)
            ->update([
                'total_resep' => \App\Models\ResepDetail::where('resep_id', $resep->id)->sum('total_harga'),
                'total_bayar_resep' => \App\Models\ResepDetail::where('resep_id', $resep->id)->sum('total_harga')
            ]);

        return [
            'success' => true,
            'data' => $resepDetail
        ];
    }
    // Hapus resep detail
    public function deleteResepDetail($id)
    {
        $resepDetail = \App\Models\ResepDetail::find($id);
        if ($resepDetail) {
            $resepDetail->delete();
            // Update total_resep encounter hanya sekali
            \App\Models\Encounter::where('id', $resepDetail->resep->encounter_id)
                ->update([
                    'total_resep' => \App\Models\ResepDetail::where('resep_id', $resepDetail->resep_id)->sum('total_harga'),
                    'total_bayar_resep' => \App\Models\ResepDetail::where('resep_id', $resepDetail->resep_id)->sum('total_harga')
                ]);
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
    // ambbil data encounter bedasarkan id beserta tindakan dan resep
    public function getEncounterById($id)
    {
        $encounter = \App\Models\Encounter::with(['tindakan', 'resep.details'])
            ->where('id', $id)
            ->first();
        if (!$encounter) {
            return null; // Jika tidak ada data encounter
        }

        // Gabungkan Pemeriksaan Penunjang dari dua sumber:
        // 1) Model lokal (jika masih ada data lama)
        // 2) LabRequest + LabRequestItem (alur baru request-only)
        $penunjang = [];
        // Lokal
        $localPenunjang = \App\Models\PemeriksaanPenunjang::where('encounter_id', $id)->get();
        foreach ($localPenunjang as $it) {
            $penunjang[] = [
                'jenis_pemeriksaan' => $it->jenis_pemeriksaan,
                'qty' => (int)($it->qty ?? 1),
                'harga' => (int)($it->harga ?? 0),
                'total_harga' => (int)($it->total_harga ?? 0),
            ];
        }
        // Lab requests
        $labRequests = \App\Models\LabRequest::with('items')
            ->where('encounter_id', $id)
            ->orderByDesc('created_at')
            ->get();
        foreach ($labRequests as $req) {
            foreach ($req->items as $it) {
                $penunjang[] = [
                    'jenis_pemeriksaan' => $it->test_name,
                    'qty' => 1,
                    'harga' => (int)($it->price ?? 0),
                    'total_harga' => (int)($it->price ?? 0),
                ];
            }
        }
        // Radiology requests
        $radiologyRequests = \App\Models\RadiologyRequest::with('jenis')
            ->where('encounter_id', $id)
            ->orderByDesc('created_at')
            ->get();
        foreach ($radiologyRequests as $rq) {
            $penunjang[] = [
                'jenis_pemeriksaan' => optional($rq->jenis)->name,
                'qty' => 1,
                'harga' => (int)($rq->price ?? 0),
                'total_harga' => (int)($rq->price ?? 0),
            ];
        }

        // Sisipkan pemeriksaan_penunjang sebagai attribute dinamis pada model Encounter
        $encounter->setAttribute('pemeriksaan_penunjang', $penunjang);
        return $encounter;
    }

    // Ringkasan encounter terakhir (sebelum encounter saat ini)
    public function getLastEncounterSummary($encounterId)
    {
        $current = \App\Models\Encounter::find($encounterId);
        if (!$current) {
            return null;
        }

        $prev = \App\Models\Encounter::where('rekam_medis', $current->rekam_medis)
            ->where('id', '!=', $encounterId)
            ->orderByDesc('created_at')
            ->first();
        if (!$prev) {
            return null;
        }

        // Diagnosis
        $diagnosis = $this->getDiagnosis($prev->id);
        // TTV
        $ttv = \App\Models\TandaVital::where('encounter_id', $prev->id)->first();
        // Resep ringkas
        $resep = \App\Models\Resep::with('details')->where('encounter_id', $prev->id)->latest()->first();
        $resepItems = [];
        if ($resep && $resep->details) {
            foreach ($resep->details as $d) {
                $resepItems[] = ['nama_obat' => $d->nama_obat, 'qty' => $d->qty, 'aturan_pakai' => $d->aturan_pakai];
            }
        }
        // Lab ringkas
        $lab = \App\Models\LabRequest::with('items')->where('encounter_id', $prev->id)->latest()->first();
        $labItems = [];
        if ($lab && $lab->items) {
            foreach ($lab->items as $it) {
                $labItems[] = ['test_name' => $it->test_name];
            }
        }

        return [
            'encounter_id' => $prev->id,
            'date' => optional($prev->created_at)->format('d M Y H:i'),
            'type' => $prev->type,
            'diagnosis' => $diagnosis ?? [],
            'ttv' => $ttv ? [
                'nadi' => $ttv->nadi,
                'pernapasan' => $ttv->pernapasan,
                'sistolik' => $ttv->sistolik,
                'diastolik' => $ttv->diastolik,
                'suhu' => $ttv->suhu,
            ] : null,
            'resep' => [
                'count' => $resep ? $resep->details->count() : 0,
                'items' => array_slice($resepItems, 0, 3),
            ],
            'lab' => [
                'status' => $lab ? $lab->status : null,
                'count' => $lab ? $lab->items->count() : 0,
                'items' => array_slice($labItems, 0, 3),
            ],
        ];
    }
    // Buat diskon tindakan
    public function postDiskonTindakan($request, $id)
    {
        $encounter = \App\Models\Encounter::find($id);
        if (!$encounter) {
            return [
                'success' => false,
                'message' => 'Encounter tidak ditemukan.'
            ];
        }

        $diskon = \App\Models\Discount::first();
        if (!$diskon) {
            return [
                'success' => false,
                'message' => 'Diskon tidak ditemukan.'
            ];
        }

        if ($request->diskon_tindakan > $diskon->diskon_tindakan) {
            return [
                'success' => false,
                'message' => 'Diskon tindakan tidak boleh melebihi ' . $diskon->diskon_tindakan . '%'
            ];
        }

        // nominal diskon dari request (hanya untuk tindakan medis)
        $diskonTindakan = $request->diskon_tindakan;
        $medisNominal = \App\Models\TindakanEncounter::where('encounter_id', $id)->sum('total_harga');
        $encounter->diskon_tindakan = $medisNominal * ($diskonTindakan / 100);
        $encounter->diskon_persen_tindakan = $diskonTindakan;

        // Total bayar tindakan medis saja = medisNominal - diskon
        $totalSetelahDiskon = $medisNominal - ($medisNominal * ($encounter->diskon_persen_tindakan / 100));
        $encounter->total_bayar_tindakan = $totalSetelahDiskon;
        $encounter->save();

        return [
            'success' => true,
            'message' => 'Diskon tindakan berhasil diterapkan.',
            'total_bayar_tindakan' => $encounter->total_bayar_tindakan
        ];
    }
    // Buat diskon resep
    public function postDiskonResep($request, $id)
    {
        $encounter = \App\Models\Encounter::find($id);
        if (!$encounter) {
            return [
                'success' => false,
                'message' => 'Encounter tidak ditemukan.'
            ];
        }

        $diskon = \App\Models\Discount::first();
        if (!$diskon) {
            return [
                'success' => false,
                'message' => 'Diskon tidak ditemukan.'
            ];
        }

        if ($request->diskon_resep > $diskon->diskon_resep) {
            return [
                'success' => false,
                'message' => 'Diskon resep tidak boleh melebihi ' . $diskon->diskon_resep . '%'
            ];
        }

        // nominal diskon dari request
        $diskonResep = $request->diskon_resep;
        $encounter->diskon_resep = $encounter->total_resep * ($diskonResep / 100);
        $encounter->diskon_persen_resep = $diskonResep;

        // Jika ingin return total setelah diskon:
        $totalSetelahDiskon = $encounter->total_resep - ($encounter->total_resep * ($encounter->diskon_persen_resep / 100));
        $encounter->total_bayar_resep = $totalSetelahDiskon;
        $encounter->save();

        return [
            'success' => true,
            'message' => 'Diskon resep berhasil diterapkan.',
            'total_bayar_resep' => $encounter->total_bayar_resep
        ];
    }
    // Post catatan encounter
    public function postCatatanEncounter($request, $id)
    {
        $encounter = Encounter::find($id);
        if (!$encounter) {
            return [
                'success' => false,
                'message' => 'Encounter tidak ditemukan.'
            ];
        }

        // Simpan status sebelum diupdate untuk perbandingan
        $wasRecentlyCreated = !$encounter->exists;
        $originalStatus = $encounter->status;

        if ($request->status_pulang == 3) {
            $this->handleRujukanRawatInap($encounter);
        } else {
            Pasien::where('rekam_medis', $encounter->rekam_medis)
                ->update(['status' => 0]);
        }

        // Update encounter utama
        $encounter->catatan = $request->catatan;
        $encounter->condition = $request->status_pulang;
        $encounter->status = 2; // Selesai
        $encounter->save();

        // Simpan perawat yang menangani
        $perawatIds = $request->input('perawat_ids', []);
        if ($request->has('perawat_ids')) {
            $encounter->nurses()->sync($perawatIds);
        }

        // --- LOGIKA INSENTIF (OPTIMIZED) ---
        // Hanya proses insentif jika status berubah menjadi 'Selesai' (2)
        if ($originalStatus != 2 && $encounter->status == 2) {
            $this->processIncentives($encounter, $perawatIds);
        }

        // Tentukan URL redirect sesuai type
        $routes = [
            1 => route('kunjungan.rawatJalan'),
            2 => route('kunjungan.rawatInap'),
            3 => route('kunjungan.rawatDarurat'),
        ];
        $url = $routes[$encounter->type] ?? '';

        if ($encounter->type == 2) {
            // Perbaikan: Nilai 'discharged' harus berupa string
            InpatientAdmission::where('encounter_id', $id)->update(['status' => 'discharged']);
        }


        return [
            'success' => true,
            'message' => 'Catatan encounter berhasil diperbarui.',
            'url'     => $url
        ];
    }

    private function handleRujukanRawatInap(Encounter $encounter)
    {
        $pasien = Pasien::where('rekam_medis', $encounter->rekam_medis)->first();
        if (!$pasien) {
            // Sebaiknya throw exception atau return error response
            return;
        }

        // Buat encounter baru untuk rawat inap
        $count = Encounter::whereDate('created_at', now()->toDateString())->count();
        $noEncounter = 'E-' . now()->format('ymd') . str_pad($count + 1, 2, '0', STR_PAD_LEFT);

        $newEncounter = Encounter::create([
            'no_encounter'        => $noEncounter,
            'rekam_medis'         => $encounter->rekam_medis,
            'name_pasien'         => $encounter->name_pasien,
            'pasien_satusehat_id' => $encounter->pasien_satusehat_id,
            'type'                => 2, // Rawat Inap
            'jenis_jaminan'       => $encounter->jenis_jaminan,
            'tujuan_kunjungan'    => $encounter->tujuan_kunjungan,
            'created_by'          => Auth::id()
        ]);

        // Buat data admisi rawat inap
        InpatientAdmission::create([
            'encounter_id'      => $newEncounter->id,
            'pasien_id'         => $pasien->id,
            'bed_number'        => 0,
            'admission_date'    => now(),
        ]);

        // Update status pasien
        $pasien->update(['status' => 2]); // Status Rawat Inap
    }

    private function processIncentives(Encounter $encounter, array $perawatIds)
    {
        $settings = IncentiveSetting::whereIn('setting_key', ['perawat_per_encounter', 'dokter_per_encounter'])
            ->pluck('setting_value', 'setting_key');

        $amountPerawat = $settings['perawat_per_encounter'] ?? 0;
        $amountDokter = $settings['dokter_per_encounter'] ?? 0;
        $now = now();
        $incentivesToCreate = [];

        // Insentif Perawat (Rawat Jalan/Darurat)
        if ($amountPerawat > 0 && in_array($encounter->type, [1, 3]) && !empty($perawatIds)) {
            foreach ($perawatIds as $perawatId) {
                $incentivesToCreate[] = $this->buildIncentiveData($perawatId, $amountPerawat, 'encounter', $encounter, $now);
            }
        }

        // Insentif Dokter (Rawat Jalan/Darurat)
        if ($amountDokter > 0 && in_array($encounter->type, [1, 3])) {
            $practitioner = $encounter->practitioner()->with('user')->first();
            if ($practitioner && $practitioner->user) {
                $incentivesToCreate[] = $this->buildIncentiveData($practitioner->user->id, $amountDokter, 'encounter', $encounter, $now);
            }
        }

        // Insentif Rawat Inap
        if ($encounter->type == 2) {
            $inpatientAdmission = InpatientAdmission::where('encounter_id', $encounter->id)->first();
            if ($inpatientAdmission) {
                // Insentif Perawat (Tindakan Rawat Inap)
                if ($amountPerawat > 0) {
                    $treatments = InpatientTreatment::where('admission_id', $inpatientAdmission->id)
                        ->whereHas('performedBy', fn($q) => $q->where('role', 3)) // Perawat
                        ->get();
                    foreach ($treatments as $treatment) {
                        $incentivesToCreate[] = $this->buildIncentiveData($treatment->performed_by, $amountPerawat, 'treatment_inap', $encounter, $now);
                    }
                }

                // Insentif Dokter (Visit Rawat Inap)
                if ($amountDokter > 0) {
                    $visits = InpatientTreatment::where('admission_id', $inpatientAdmission->id)
                        ->where('request_type', 'Visit')
                        ->whereHas('performedBy', function ($query) {
                            $query->where('role', '!=', 3); // Hanya untuk yang BUKAN Perawat (misal: Dokter)
                        })
                        ->get();
                    foreach ($visits as $visit) {
                        $incentivesToCreate[] = $this->buildIncentiveData($visit->performed_by, $amountDokter, 'visit_inap', $encounter, $now);
                    }
                }
            }
        }

        if (!empty($incentivesToCreate)) {
            \App\Models\Incentive::insert($incentivesToCreate);
        }
    }

    private function buildIncentiveData($userId, $amount, $type, Encounter $encounter, $timestamp)
    {
        $description = "Insentif " . str_replace('_', ' ', $type) . ": " . $encounter->name_pasien . ' (No. Encounter: ' . $encounter->no_encounter . ')';
        return [
            'id' => \Illuminate\Support\Str::uuid(),
            'user_id' => $userId,
            'year' => $timestamp->year,
            'month' => $timestamp->month,
            'amount' => $amount,
            'type' => $type,
            'description' => $description,
            'status' => 'pending',
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ];
    }

    // Show InpantientAdmission
    public function getInpatientAdmission($id)
    {
        // Ambil data rawat inap berdasarkan encounter_id
        $inpatientAdmission = InpatientAdmission::findOrFail($id);
        if (!$inpatientAdmission) {
            return null; // Jika tidak ada data rawat inap
        }
        return $inpatientAdmission;
    }
    public function getInpatientTreatment($id)
    {
        try {
            // Ambil data dengan eager loading
            $inpatientTreatments = InpatientTreatment::where('admission_id', $id)
                ->orderBy('treatment_date', 'desc')
                ->get();

            if ($inpatientTreatments->isEmpty()) {
                return null;
            }

            // Format data tanpa grouping
            $formattedTreatments = $inpatientTreatments->map(function ($treatment) {
                return [
                    'id' => $treatment->id,
                    'treatment_date_formatted' => \Carbon\Carbon::parse($treatment->treatment_date)->format('d/m/Y H:i'),
                    'tindakan_name' => $treatment->tindakan_name,
                    'quantity' => $treatment->quantity,
                    'harga_formatted' => 'Rp ' . number_format($treatment->harga, 0, ',', '.'),
                    'total_formatted' => 'Rp ' . number_format($treatment->total, 0, ',', '.'),
                    'result' => $treatment->result,
                    'document' => $treatment->document ? url('uploads/' . $treatment->document) : null,
                    'performed_by' => $treatment->performedBy->name ?? 'Unknown',
                    'request_type' => $treatment->request_type,
                ];
            });

            return $formattedTreatments;
        } catch (\Exception $e) {
            Log::error('Error in getInpatientTreatment', [
                'admission_id' => $id,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    public function postInpatientTreatment($request, $id)
    {
        // Ambil data rawat inap
        $inpatientAdmission = InpatientAdmission::find($id);
        if (!$inpatientAdmission) {
            return null; // Jika tidak ada data rawat inap
        }

        // Cek Tindakan_ID
        $tindakan = \App\Models\Tindakan::findOrFail($request->tindakan_id);
        if (!$tindakan) {
            return [
                'success' => false,
                'message' => 'Tindakan tidak ditemukan.'
            ];
        }
        $treatment_date = \Carbon\Carbon::parse(trim($request->treatment_date));

        // Selalu buat data baru setiap kali ada request, hapus logika pengecekan existingTreatment
        $inpatientTreatment = new InpatientTreatment();
        $inpatientTreatment->admission_id = $id;
        $inpatientTreatment->request_type = $request->type; // Simpan tipe tindakan
        $inpatientTreatment->tindakan_id = $tindakan->id; // Simpan ID tindakan
        $inpatientTreatment->tindakan_name = $tindakan->name;
        $inpatientTreatment->harga = $tindakan->harga;
        $inpatientTreatment->total = $tindakan->harga; // Total adalah harga per tindakan (quantity selalu 1)
        $inpatientTreatment->quantity = 1; // Quantity selalu 1 untuk setiap tindakan baru
        $inpatientTreatment->result = $request->result;
        $inpatientTreatment->performed_by = \Illuminate\Support\Facades\Auth::user()->id;
        $inpatientTreatment->treatment_date = $treatment_date->format('Y-m-d H:i:s'); // Simpan tanggal tindakan
        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads'), $filename);
            $inpatientTreatment->document = $filename;
        }
        $inpatientTreatment->save();


        return [
            'success' => true,
            'message' => 'Tindakan berhasil ditambahkan.',
        ];
    }

    // Destroy Inpatient Treatment
    public function destroyInpatientTreatment($id)
    {
        $inpatientTreatment = InpatientTreatment::find($id);
        if (!$inpatientTreatment) {
            return [
                'success' => false,
                'message' => 'Tindakan tidak ditemukan.'
            ];
        }
        if ($inpatientTreatment->document) {
            $filePath = public_path('uploads/' . $inpatientTreatment->document);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
        // Hapus tindakan
        $inpatientTreatment->delete();

        return [
            'success' => true,
            'message' => 'Tindakan berhasil dihapus.'
        ];
    }
    // Ambil data inpatient daily medicine
    public function getInpatientDailyMedications($admissionId)
    {
        return InpatientDailyMedication::where('inpatient_admission_id', $admissionId)->get()
            ->map(function ($medication) {
                // Format tanggal medicine_date
                $medication->medicine_date = \Carbon\Carbon::parse($medication->medicine_date)->format('d-m-Y');
                $medication->administered_at = \Carbon\Carbon::parse($medication->administered_at)->format('d-m-Y H:i:s');
                // Nama Dokter dan perawat
                $medication->authorized_name = $medication->authorized_name ?? '-';
                $medication->administered_name = $medication->administered_name ?? '-';

                return $medication;
            });
    }
    // update status obart harian
    public function updateInpatientDailyMedicationStatus($id)
    {
        $inpatientDailyMedication = InpatientDailyMedication::find($id);
        if (!$inpatientDailyMedication) {
            return [
                'success' => false,
                'message' => 'Obat harian tidak ditemukan.'
            ];
        }
        $inpatientDailyMedication->status = "Diberikan";
        $inpatientDailyMedication->administered_by = \Illuminate\Support\Facades\Auth::user()->id;
        $inpatientDailyMedication->administered_name = \Illuminate\Support\Facades\Auth::user()->name;
        $inpatientDailyMedication->administered_at = now();
        $inpatientDailyMedication->is_billing = 'Ya'; // Tandai untuk penagihan
        $inpatientDailyMedication->save();

        // Jika diberikan, catat insentif farmasi (opsional, biasanya saat input sudah dibuat)
        $this->createPharmacyIncentiveForInpatient($inpatientDailyMedication);

        return [
            'success' => true,
            'message' => 'Status obat berhasil diperbarui.'
        ];
    }

    // Post Inpatient Daily Medication
    public function postInpatientDailyMedication($request, $admissionId)
    {

        // Ambil stok terdekat yang belum expired dan status=0
        $stokTerdekat = \App\Models\ApotekStok::where('product_apotek_id', $request->product_apotek_id)
            ->where('status', 0)
            ->where(function ($q) {
                $q->whereNull('expired_at')
                    ->orWhere('expired_at', '>=', now()->toDateString());
            })
            ->orderBy('expired_at', 'asc')
            ->first();

        if (!$stokTerdekat) {
            return [
                'success' => false,
                'message' => 'Stok tidak tersedia atau sudah expired.'
            ];
        }
        // Simpan data
        $inpatientDailyMedication = new InpatientDailyMedication();
        $inpatientDailyMedication->inpatient_admission_id = $admissionId;
        $inpatientDailyMedication->medication_code = $stokTerdekat->productApotek->code;
        $inpatientDailyMedication->medication_name = $stokTerdekat->productApotek->name;
        $inpatientDailyMedication->harga = $stokTerdekat->productApotek->harga;
        $inpatientDailyMedication->jumlah = $request->jumlah;
        $inpatientDailyMedication->total = $stokTerdekat->productApotek->harga * $request->jumlah;
        $inpatientDailyMedication->dosage_instructions = $request->dosage_instructions;
        $inpatientDailyMedication->satuan = $stokTerdekat->productApotek->satuan;
        $inpatientDailyMedication->route = $request->route;
        $inpatientDailyMedication->frequency = $request->frequensi;
        $inpatientDailyMedication->expiration_date = $stokTerdekat->expired_at;
        $inpatientDailyMedication->notes = $request->notes;
        $inpatientDailyMedication->authorized_by = \Illuminate\Support\Facades\Auth::user()->id;
        $inpatientDailyMedication->authorized_name = \Illuminate\Support\Facades\Auth::user()->name;
        $inpatientDailyMedication->medicine_date = $request->medicine_date ? \Carbon\Carbon::parse($request->medicine_date)->format('Y-m-d') : now()->format('Y-m-d');
        $inpatientDailyMedication->save();

        // Buat insentif obat (farmasi) berdasarkan setting
        $this->createPharmacyIncentiveForInpatient($inpatientDailyMedication);

        return [
            'success' => true,
            'message' => 'Obat berhasil ditambahkan.',
        ];
    }
    // Hapus Inpatient Daily Medication
    public function deleteInpatientDailyMedication($id)
    {
        $inpatientDailyMedication = InpatientDailyMedication::find($id);
        if (!$inpatientDailyMedication) {
            return [
                'success' => false,
                'message' => 'Obat tidak ditemukan.'
            ];
        }
        // Cek apakah obat sudah diberikan (status=1)
        if ($inpatientDailyMedication->status == 1) {
            return [
                'success' => false,
                'message' => 'Tidak bisa menghapus obat yang sudah diberikan.'
            ];
        }
        $inpatientDailyMedication->delete();

        return [
            'success' => true,
            'message' => 'Obat berhasil dihapus.'
        ];
    }

    /**
     * Update total_tindakan encounter dengan menggabungkan tindakan medis + pemeriksaan penunjang
     * Method ini memastikan konsistensi kalkulasi di seluruh aplikasi
     */
    public function updateEncounterTotalTindakan($encounterId)
    {
        $encounter = \App\Models\Encounter::find($encounterId);
        if (!$encounter) {
            return;
        }

        // Hitung total tindakan medis
        $totalTindakanMedis = \App\Models\TindakanEncounter::where('encounter_id', $encounterId)
            ->sum('total_harga');

        // Hitung total pemeriksaan penunjang (lokal)
        $totalPemeriksaanPenunjangLokal = \App\Models\PemeriksaanPenunjang::where('encounter_id', $encounterId)
            ->sum('total_harga');

        // Hitung total permintaan lab (LabRequestItem) berdasarkan encounter
        $totalLabRequests = \App\Models\LabRequestItem::whereHas('request', function ($q) use ($encounterId) {
            $q->where('encounter_id', $encounterId);
        })
            ->sum('price');

        // Hitung total permintaan radiologi
        $totalRadiologyRequests = \App\Models\RadiologyRequest::where('encounter_id', $encounterId)
            ->sum('price');

        // Total gabungan (medis + penunjang lokal + lab requests + radiology requests)
        $totalKeseluruhan = ($totalTindakanMedis ?? 0)
            + ($totalPemeriksaanPenunjangLokal ?? 0)
            + ($totalLabRequests ?? 0)
            + ($totalRadiologyRequests ?? 0);
        // Update encounter dengan total yang benar
        $encounter->total_tindakan = $totalKeseluruhan;

        // Jika belum ada diskon, total_bayar_tindakan sama dengan total_tindakan
        if (is_null($encounter->diskon_persen_tindakan) || $encounter->diskon_persen_tindakan == 0) {
            $encounter->total_bayar_tindakan = $totalKeseluruhan;
        } else {
            // Jika ada diskon, hitung ulang total bayar
            $diskonAmount = $totalKeseluruhan * ($encounter->diskon_persen_tindakan / 100);
            $encounter->diskon_tindakan = $diskonAmount;
            $encounter->total_bayar_tindakan = $totalKeseluruhan - $diskonAmount;
        }

        $encounter->save();
    }

    /**
     * Membuat insentif untuk dokter yang meminta pemeriksaan penunjang.
     *
     * @param \App\Models\Encounter $encounter
     * @param \App\Models\User $dokter
     * @param string $namaPemeriksaan
     * @param float $hargaPemeriksaan
     * @return void
     */
    private function computeFeeAmount(float $base, int $mode, float $value): float
    {
        if ($base <= 0 || $value <= 0) return 0.0;
        return $mode === 1 ? ($base * ($value / 100.0)) : $value;
    }

    public function createPemeriksaanPenunjangIncentive(\App\Models\Encounter $encounter, \App\Models\User $dokter, string $namaPemeriksaan, float $hargaPemeriksaan, string $tipe = 'lab'): void
    {
        $tipe = strtolower($tipe);
        $modeKey = $tipe === 'radiologi' ? 'fee_radiologi_mode' : 'fee_lab_mode';
        $valKey  = $tipe === 'radiologi' ? 'fee_radiologi_value' : 'fee_lab_value';
        $mode = (int) (\App\Models\IncentiveSetting::where('setting_key', $modeKey)->value('setting_value') ?? 1);
        $val  = (float)(\App\Models\IncentiveSetting::where('setting_key', $valKey)->value('setting_value') ?? 0);

        $amount = $this->computeFeeAmount($hargaPemeriksaan, $mode, $val);
        if ($amount <= 0) {
            return;
        }

        $description = "Fee Penunjang ($tipe: $namaPemeriksaan) untuk " . $encounter->name_pasien;

        \App\Models\Incentive::create([
            'id' => \Illuminate\Support\Str::uuid(),
            'user_id' => $dokter->id,
            'amount' => $amount,
            'type' => 'fee_penunjang',
            'description' => $description,
            'year' => now()->year,
            'month' => now()->month,
            'status' => 'pending',
        ]);
    }

    private function createPharmacyIncentiveForInpatient(\App\Models\InpatientDailyMedication $medication): void
    {
        try {
            $mode = (int)(\App\Models\IncentiveSetting::where('setting_key','fee_obat_mode')->value('setting_value') ?? 1);
            $val  = (float)(\App\Models\IncentiveSetting::where('setting_key','fee_obat_value')->value('setting_value') ?? 0);
            $target= (int)(\App\Models\IncentiveSetting::where('setting_key','fee_obat_target_mode')->value('setting_value') ?? 0);
            $base = (float)($medication->total ?? 0);
            if ($base <= 0 || $val <= 0) return;

            $amount = $this->computeFeeAmount($base, $mode, $val);
            if ($amount <= 0) return;

            // Tentukan penerima: 0=DPJP (practitioner pertama), 1=prescriber (authorized_by)
            $encounter = optional($medication->admission)->encounter;
            $userId = null;
            if ($target === 1 && $medication->authorized_by) {
                $userId = $medication->authorized_by;
            } else if ($encounter) {
                $pr = $encounter->practitioner()->with('user')->first();
                $userId = optional(optional($pr)->user)->id;
            }
            if (!$userId) return;

            \App\Models\Incentive::create([
                'id' => \Illuminate\Support\Str::uuid(),
                'user_id' => $userId,
                'amount' => $amount,
                'type' => 'fee_obat_inap',
                'description' => 'Fee Obat (Inap) pasien ' . (optional($encounter)->name_pasien ?? ''),
                'year' => now()->year,
                'month' => now()->month,
                'status' => 'pending',
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Gagal membuat insentif obat inap: '.$e->getMessage());
        }
    }
}
