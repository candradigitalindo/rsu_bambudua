<?php

namespace App\Http\Controllers;

use App\Repositories\ObservasiRepository;
use Illuminate\Http\Request;

class ObservasiController extends Controller
{
    public $observasiRepository;
    public function __construct(ObservasiRepository $observasiRepository)
    {
        $this->observasiRepository = $observasiRepository;
    }

    public function index($id)
    {
        // Cek apakah encounter_id valid
        $encounter = $this->observasiRepository->getEncounterById($id);
        $observasi = $id;
        // Ambil data dokter yang menangani
        $dokters = $this->observasiRepository->getDokters($id);
        // Ambil data perawat yang menangani
        $perawats = $this->observasiRepository->getPerawats($id);
        return view('pages.observasi.index', compact('observasi', 'encounter', 'dokters', 'perawats'));
    }
    public function riwayatPenyakit($id)
    {
        $riwayatPenyakit = $this->observasiRepository->riwayatPenyakit($id);
        if ($riwayatPenyakit) {
            return response()->json($riwayatPenyakit);
        } else {
            return response()->json(['message' => 'Riwayat penyakit tidak ditemukan'], 404);
        }
    }
    public function postAnemnesis(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'dokter_id' => 'required|string|max:255',
            'keluhan_utama' => 'required|string|max:255',
            'riwayat_penyakit' => 'required|string|max:255',
            'riwayat_penyakit_keluarga' => 'required|string|max:255',
        ]);
        $result = $this->observasiRepository->postAnemnesis($request, $id);
        return response()->json([
            'status' => 200,
            'message' => 'Data Anamnesis berhasil disimpan.',
            'data' => $result
        ]);
    }
    // ambil tanda vital berdasarkan encounter_id
    public function tandaVital($id)
    {
        $tandaVital = $this->observasiRepository->tandaVital($id);
        if ($tandaVital) {
            return response()->json($tandaVital);
        } else {
            return response()->json(['message' => 'Tanda vital tidak ditemukan'], 404);
        }
    }
    public function postTandaVital(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'nadi' => 'nullable|string|max:255',
            'pernapasan' => 'nullable|string',
            'sistolik' => 'nullable|string',
            'diastolik' => 'nullable|string',
            'suhu' => 'nullable|string',
            'berat_badan' => 'nullable|string',
            'tinggi_badan' => 'nullable|string',
            'kesadaran' => 'nullable|string|max:255',
        ]);
        $result = $this->observasiRepository->postTandaVital($request, $id);
        return response()->json([
            'status' => 200,
            'message' => 'Data Tanda Vital berhasil disimpan.',
            'data' => $result
        ]);
    }
    public function pemeriksaanPenunjang($id)
    {
        $pemeriksaanPenunjang = $this->observasiRepository->pemeriksaanPenunjang($id);
        if ($pemeriksaanPenunjang) {
            return response()->json($pemeriksaanPenunjang);
        } else {
            return response()->json($pemeriksaanPenunjang);
        }
    }
    public function postPemeriksaanPenunjang(Request $request, $id)
    {
        // cek jika dokumen undifined
        if ($request->hasFile('dokumen_pemeriksaan')) {
            $request->validate([
                'jenis_pemeriksaan' => 'required|string',
                'hasil_pemeriksaan' => 'required|string',
                'dokumen_pemeriksaan' => 'nullable|file|mimes:pdf,jpg,jpeg,png',
            ]);
        } else {
            $request->validate([
                'jenis_pemeriksaan' => 'required|string',
                'hasil_pemeriksaan' => 'required|string',
            ]);
        }
        $result = $this->observasiRepository->postPemeriksaanPenunjang($request, $id);
        return response()->json([
            'status' => 200,
            'message' => 'Data Pemeriksaan Penunjang berhasil disimpan.',
            'data' => $result
        ]);
    }
    public function deletePemeriksaanPenunjang($id)
    {
        $result = $this->observasiRepository->deletePemeriksaanPenunjang($id);
        return response()->json([
            'status' => 200,
            'message' => 'Data Pemeriksaan Penunjang berhasil dihapus.',
            'data' => $result
        ]);
    }

    // ambil data tindakan
    public function getTindakan($id)
    {
        $tindakan = $this->observasiRepository->getTindakan($id);
        if ($tindakan) {
            return response()->json($tindakan);
        } else {
            return response()->json(['message' => 'Tindakan tidak ditemukan'], 404);
        }
    }
    // ambil data tindakan berdasarkan encounter_id
    public function getTindakanEncounter($id)
    {
        $tindakan = $this->observasiRepository->getTindakanEncounter($id);
        if ($tindakan) {
            return response()->json($tindakan);
        } else {
            return response()->json($tindakan);
        }
    }
    // post tindakan encounter
    public function postTindakanEncounter(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'jenis_tindakan' => 'required|string|max:255',
            'qty' => 'required|integer|max:255',
        ]);
        $result = $this->observasiRepository->postTindakanEncounter($request, $id);
        return response()->json([
            'status' => 200,
            'message' => 'Data Tindakan berhasil disimpan.',
            'data' => $result
        ]);
    }
    // hapus tindakan
    public function deleteTindakanEncounter($id)
    {
        $result = $this->observasiRepository->deleteTindakanEncounter($id);
        return response()->json([
            'status' => $result['success'],
            'message' => $result['message']
        ]);
    }
    // Ambil data icd10
    public function getIcd10($id)
    {
        $query = \App\Models\Icd10::query();

        $search = request('search');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        // Untuk kebutuhan Select2 AJAX, biasanya tidak perlu paginate
        return $query->limit(20)->get(['id', 'code', 'description']);
    }
    // Ambil semua data diagosis sesuai encounter_id
    public function getDiagnosis($id)
    {
        $diagnosis = $this->observasiRepository->getDiagnosis($id);
        return response()->json($diagnosis);
    }
    // post diagnosis
    public function postDiagnosis(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'icd10_id' => 'required|string|max:255',
            'diagnosis_type' => 'required|string|max:255',
        ]);
        $result = $this->observasiRepository->postDiagnosis($request, $id);
        return response()->json([
            'status' => 200,
            'message' => 'Data Diagnosis berhasil disimpan.',
            'data' => $result
        ]);
    }
    // hapus diagnosis
    public function deleteDiagnosis($id)
    {
        $result = $this->observasiRepository->deleteDiagnosis($id);
        return response()->json([
            'status' => $result['success'],
            'message' => $result['message']
        ]);
    }
    // ambil data resep
    public function getResep($id)
    {
        $resep = $this->observasiRepository->getResep($id);
        if ($resep) {
            return response()->json($resep);
        } else {
            return response()->json($resep);
        }
    }
    // post resep
    public function postResep(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'masa_pemakaian_hari' => 'required|string|max:255',
        ], [
            'masa_pemakaian_hari.required' => 'Masa pemakaian hari harus diisi.',
        ]);
        $result = $this->observasiRepository->postResep($request, $id);
        return response()->json([
            'status' => 200,
            'message' => 'Data Resep berhasil disimpan.',
            'data' => $result
        ]);
    }
    // getProduk apotek
    public function getProdukApotek($id)
    {
        return response()->json([
            'status' => 200,
            'message' => 'Data Produk Apotek berhasil diambil.',
            'data' => $this->observasiRepository->getProdukApotek($id)
        ]);
    }
    // postResepDetail
    public function postResepDetail(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'product_apotek_id' => 'required|string|max:255',
            'qty_obat' => 'required|integer|max:255',
            'aturan_pakai' => 'required|string|max:255',
        ]);
        $result = $this->observasiRepository->postResepDetail($request, $id);

        if ($result['success'] == false) {
            return response()->json([
                'status' => 400,
                'message' => $result['message']
            ], 400);
        } else {
            return response()->json([
                'status' => 200,
                'message' => 'Data Resep Detail berhasil disimpan.',
                'data' => $result
            ]);
        }
    }
    // hapus resep detail
    public function deleteResepDetail($id)
    {
        $result = $this->observasiRepository->deleteResepDetail($id);
        return response()->json([
            'status' => $result['success'],
            'message' => $result['message']
        ]);
    }
    // ambil data encounter
    public function getEncounterById($id)
    {
        $encounter = $this->observasiRepository->getEncounterById($id);
        if ($encounter) {
            return response()->json($encounter);
        } else {
            return response()->json($encounter);
        }
    }
    // Buat diskon tindakan
    public function postDiskonTindakan(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'diskon_tindakan' => 'required|numeric|min:0',
        ], [
            'diskon_tindakan.required' => 'Diskon tindakan harus diisi.',
            'diskon_tindakan.numeric' => 'Diskon tindakan harus berupa angka.',
            'diskon_tindakan.min' => 'Diskon tindakan minimal 0.',
            'diskon_tindakan.max' => 'Diskon tindakan maksimal 100.',
        ]);
        $result = $this->observasiRepository->postDiskonTindakan($request, $id);
        return response()->json($result);
    }
    // Buat diskon resep
    public function postDiskonResep(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'diskon_resep' => 'required|numeric|min:0',
        ], [
            'diskon_resep.required' => 'Diskon resep harus diisi.',
            'diskon_resep.numeric' => 'Diskon resep harus berupa angka.',
            'diskon_resep.min' => 'Diskon resep minimal 0.',
            'diskon_resep.max' => 'Diskon resep maksimal 100.',
        ]);
        $result = $this->observasiRepository->postDiskonResep($request, $id);
        return response()->json($result);
    }
    // post catatan encounter
    public function postCatatanEncounter(Request $request, $id)
    {
        // Ambil tipe encounter untuk validasi kondisional
        $encounter = \App\Models\Encounter::find($id);
        if (!$encounter) {
            return response()->json(['message' => 'Encounter tidak ditemukan.'], 404);
        }

        $request->validate([
            'catatan' => 'nullable|string|max:255',
            'status_pulang' => 'required|numeric',
            // Validasi perawat_ids, wajib jika tipe encounter adalah 1 (RJ) atau 3 (IGD)
            'perawat_ids' => 'required_if:encounter.type,1,3|array',
            'perawat_ids.*' => 'exists:users,id',
        ], [
            'perawat_ids.required_if' => 'Perawat harus dipilih untuk Rawat Jalan atau Rawat Darurat.',
            'perawat_ids.array' => 'Format data perawat tidak valid.'
        ]);
        $result = $this->observasiRepository->postCatatanEncounter($request, $id);
        return response()->json($result);
    }

    public function getInpatientAdmission($id)
    {
        $getInpatientAdmission = $this->observasiRepository->getInpatientAdmission($id);
        return view('pages.observasi.rinap', compact('getInpatientAdmission'));
    }
    public function postInpatientTreatment(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'type' => 'required|string|max:255',
            'treatment_date' => 'required|date',
            'tindakan_id' => 'required|string|max:255',
            'result' => 'nullable|string|max:255',
        ]);

        $result = $this->observasiRepository->postInpatientTreatment($request, $id);
        if ($result['success'] == false) {
            return response()->json([
                'status' => 404,
                'message' => $result['message']
            ], 404);
        } else {
            return response()->json([
                'status' => 200,
                'message' => 'Tindakan berhasil disimpan.',
                'data' => $result
            ]);
        }
    }
    //getInpatientTreatment
    public function getInpatientTreatment($id)
    {
        $inpatientTreatment = $this->observasiRepository->getInpatientTreatment($id);
        return response()->json($inpatientTreatment ?? []);
    }
    public function deleteInpatientTreatment($id)
    {
        $result = $this->observasiRepository->destroyInpatientTreatment($id);
        return response()->json([
            'status' => $result['success'],
            'message' => $result['message']
        ]);
    }
    //getInpatientDailyMedications
    public function getInpatientDailyMedications($id)
    {
        $inpatientDailyMedications = $this->observasiRepository->getInpatientDailyMedications($id);
        if ($inpatientDailyMedications) {
            return response()->json($inpatientDailyMedications);
        } else {
            return response()->json(['message' => 'Obat tidak ditemukan'], 404);
        }
    }
    public function updateInpatientDailyMedicationStatus($id)
    // Update status obat harian
    {
        $result = $this->observasiRepository->updateInpatientDailyMedicationStatus($id);
        if ($result['success'] == false) {
            return response()->json([
                'status' => 404,
                'message' => $result['message']
            ], 404);
        } else {
            return response()->json([
                'status' => 200,
                'message' => 'Status obat berhasil diperbarui.',
                'data' => $result
            ]);
        }
    }
    //postInpatientDailyMedication
    public function postInpatientDailyMedication(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'product_apotek_id' => 'required|string|max:255',
            'jumlah' => 'required|integer|max:255',
            'dosage_instructions' => 'required|string|max:255',
            'frequensi' => 'required|string|max:255',
            'route' => 'required|string|max:255',
            'notes' => 'nullable|string|max:255',
            'medicine_date' => 'required|date',
        ]);

        $result = $this->observasiRepository->postInpatientDailyMedication($request, $id);
        if ($result['success'] == false) {
            return response()->json([
                'status' => 404,
                'message' => $result['message']
            ], 404);
        } else {
            return response()->json([
                'status' => 200,
                'message' => 'Obat berhasil disimpan.',
                'data' => $result
            ]);
        }
    }
    //deleteInpatientDailyMedication
    public function deleteInpatientDailyMedication($id)
    {
        $result = $this->observasiRepository->deleteInpatientDailyMedication($id);
        if ($result['success'] == false) {
            return response()->json([
                'status' => 404,
                'message' => $result['message']
            ], 404);
        } else {
            return response()->json([
                'status' => 200,
                'message' => 'Obat berhasil dihapus.'
            ]);
        }
    }
}
