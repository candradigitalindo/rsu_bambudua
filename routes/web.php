<?php

use App\Http\Controllers\AgamaController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\AntrianController;
use App\Http\Controllers\ApotekController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\SpecialistConsultationController;
use App\Http\Controllers\NursingCareController;
use App\Http\Controllers\BahanController;
use App\Http\Controllers\BeritaController;
use App\Http\Controllers\CategoryRuanganController;
use App\Http\Controllers\DokterController;
use App\Http\Controllers\EncounterController;
use App\Http\Controllers\EtnisController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JaminanController;
use App\Http\Controllers\LokasiloketController;
use App\Http\Controllers\LoketController;
use App\Http\Controllers\ObservasiController;
use App\Http\Controllers\OtherIncomeController;
use App\Http\Controllers\OperationalExpenseController;
use App\Http\Controllers\PekerjaanController;
use App\Http\Controllers\PendaftaranController;
use App\Http\Controllers\PendidikanController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\RuanganController;
use App\Http\Controllers\SatusehatController;
use App\Http\Controllers\SpesialisController;
use App\Http\Controllers\StorageController;
use App\Http\Controllers\TindakanController;
use App\Http\Controllers\WilayahController;
use App\Http\Controllers\LabRequestController;
use App\Http\Controllers\LabReagentController;
use App\Http\Controllers\LabDashboardController;
use App\Http\Controllers\LabResultController;
use App\Http\Controllers\FinanceReportController;
use App\Http\Controllers\JenisPemeriksaanPenunjangController;
use App\Http\Controllers\Icd10Controller;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\ClinicController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CostCenterController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\RadiologiController;
use App\Http\Controllers\RadiologySupplyController;
use App\Http\Controllers\MedicalRecordsController;
use App\Http\Controllers\IncentiveController;
use App\Http\Controllers\MedicalEquipmentController;
use App\Models\Subdistrict;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});
Route::get('/tes', function () {
    $apotekStok = \App\Models\ApotekStok::where('status', 1)->delete();
});

Route::get('/antrian', [AntrianController::class, 'index'])->name('antrian.index');
Route::get('/antrian/{id}', [AntrianController::class, 'show'])->name('antrian.show');
Route::get('/antrian/{id}/cetak', [AntrianController::class, 'store'])->name('antrian.store');
Route::get('/antrian/{id}/monitor', [AntrianController::class, 'edit'])->name('antrian.monitor');

// [FIX] AJAX route untuk dokter-by-clinic - perlu accessible tanpa full auth
Route::get('/pendaftaran/ajax/dokter-by-clinic/{clinic}', [PendaftaranController::class, 'getDokterByClinic'])->name('ajax.dokterByClinic');

Route::middleware(['auth'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/home/realtime-data', [HomeController::class, 'getRealTimeData'])->name('owner.realtime-data');
    Route::get('/home/{id}/profile', [HomeController::class, 'getProfile'])->name('home.profile');
    // Admin Dashboard Route
    Route::get('admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

    Route::post('/home/{id}/profile', [HomeController::class, 'updateProfile'])->name('home.profile.update');
    Route::get('/public/profile/{filename}', [StorageController::class, 'profile'])->name('home.profile.filename');

    Route::prefix('setting')->group(function () {
        // SATUSEHAT
        Route::get('/satusehat', [SatusehatController::class, 'getSatusehat'])->name('satusehat.index');
        Route::post('/satusehat', [SatusehatController::class, 'saveSatusehat'])->name('satusehat.store');
        Route::resource('lokasiloket', LokasiloketController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
        Route::resource('loket', LoketController::class)->only(['index', 'store', 'destroy']);
    });

    Route::prefix('masterdata')->group(function () {
        //Master Wilayah
        Route::get('/wilayah', [WilayahController::class, 'getWilayah'])->name('wilayah.index');
        Route::get('/wilayah/province', [WilayahController::class, 'getProvinces'])->name('wilayah.province');
        Route::get('/wilayah/city/{code}', [WilayahController::class, 'getCity'])->name('wilayah.city');

        Route::get('/wilayah/province/save', [WilayahController::class, 'saveProvince'])->name('wilayah.saveProvince');
        Route::get('/wilayah/kota/save/{code}', [WilayahController::class, 'saveCity'])->name('wilayah.saveCity');
        Route::get('/wilayah/kecamatan/save/{code}', [WilayahController::class, 'saveDistrict'])->name('wilayah.saveDistrict');
        Route::resource('jenisjaminan', JaminanController::class)->except(['show', 'create', 'update']);
        Route::resource('etnis', EtnisController::class)->only(['index', 'store', 'destroy']);
        Route::resource('pendidikan', PendidikanController::class)->only(['index', 'store', 'destroy']);
        Route::resource('agama', AgamaController::class)->only(['index', 'store', 'destroy']);
        Route::resource('pekerjaan', PekerjaanController::class)->only(['index', 'store', 'destroy']);
        Route::resource('spesialis', SpesialisController::class)->only(['index', 'store', 'destroy']);
        Route::resource('ruangan', RuanganController::class)->except(['show']);
        // Bed Availability routes
        Route::get('ruangan/bed-availability/dashboard', [RuanganController::class, 'bedAvailabilityDashboard'])->name('ruangan.bed-availability.dashboard');
        Route::get('ruangan/bed-availability/api', [RuanganController::class, 'getBedAvailability'])->name('ruangan.bed-availability.api');
        Route::get('ruangan/bed-availability/summary', [RuanganController::class, 'getBedAvailabilitySummary'])->name('ruangan.bed-availability.summary');
        Route::get('ruangan/occupied-patients', [RuanganController::class, 'getOccupiedPatients'])->name('ruangan.occupied-patients');
        Route::get('ruangan/test-bed-logic', [RuanganController::class, 'testBedLogic'])->name('ruangan.test-bed-logic');
        Route::post('ruangan/discharge-patient/{admissionId}', [RuanganController::class, 'dischargePatient'])->name('ruangan.discharge-patient');
        Route::post('ruangan/readmit-patient/{admissionId}', [RuanganController::class, 'readmitPatient'])->name('ruangan.readmit-patient');
        Route::post('ruangan/cleanup-admission-data', [RuanganController::class, 'cleanupAdmissionData'])->name('ruangan.cleanup-admission-data');
        Route::get('ruangan/test-kpi-accuracy', [RuanganController::class, 'testKPIAccuracy'])->name('ruangan.test-kpi-accuracy');
        Route::resource('category', CategoryRuanganController::class)->except(['show']);
        Route::resource('tindakan', TindakanController::class)->except(['show']);
        Route::get('/tindakan/getBahan/{id}', [TindakanController::class, 'getBahan'])->name('tindakan.getBahan');
        Route::post('/tindakan/storeBahan/{id}', [TindakanController::class, 'storeBahan'])->name('tindakan.storeBahan');
        Route::delete('/tindakan/destroyBahan/{id}', [TindakanController::class, 'destroyBahan'])->name('tindakan.destroyBahan');
        // Route CRUD Jenis Pemeriksaan Penunjang
        Route::get('jenis-pemeriksaan/{id}/fields', [JenisPemeriksaanPenunjangController::class, 'showFields'])->name('jenis-pemeriksaan.fields.index');
        Route::post('jenis-pemeriksaan/{id}/fields', [JenisPemeriksaanPenunjangController::class, 'storeField'])->name('jenis-pemeriksaan.fields.store');
        Route::delete('jenis-pemeriksaan/fields/{field_id}', [JenisPemeriksaanPenunjangController::class, 'destroyField'])->name('jenis-pemeriksaan.fields.destroy');
        Route::post('jenis-pemeriksaan/fields/{field_id}/examinations', [JenisPemeriksaanPenunjangController::class, 'storeExamination'])->name('jenis-pemeriksaan.examinations.store');
        Route::delete('jenis-pemeriksaan/examinations/{item_id}', [JenisPemeriksaanPenunjangController::class, 'destroyExamination'])->name('jenis-pemeriksaan.examinations.destroy');
        Route::resource('jenis-pemeriksaan', JenisPemeriksaanPenunjangController::class)->except(['show']);
        Route::resource('icd10', Icd10Controller::class);
        Route::post('icd10/import', [\App\Http\Controllers\Icd10Controller::class, 'import'])->name('icd10.import');
        // route discount
        Route::get('/discount', [DiscountController::class, 'index'])->name('discounts.index');
        Route::post('/discount', [DiscountController::class, 'update'])->name('discounts.update');
        Route::resource('clinics', ClinicController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
        // Master baru
        Route::resource('payment-methods', PaymentMethodController::class)->except(['show']);
        Route::resource('units', UnitController::class)->except(['show']);
        Route::resource('suppliers', SupplierController::class)->except(['show']);
        // Master Cost Center & Expense Category
        Route::resource('cost-centers', CostCenterController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy'])->names('master.cost-centers');
        Route::resource('expense-categories', ExpenseCategoryController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy'])->names('master.expense-categories');
        // SIP / Professional Licenses
        Route::resource('professional-licenses', \App\Http\Controllers\ProfessionalLicenseController::class)->except(['show'])->names('professional-licenses');

        // Reminder Settings
        Route::resource('reminder-settings', \App\Http\Controllers\ReminderSettingController::class)->except(['show']);
        Route::post('reminder-settings/{reminder_setting}/toggle-status', [\App\Http\Controllers\ReminderSettingController::class, 'toggleStatus'])->name('reminder-settings.toggle-status');
    });

    Route::prefix('pendaftaran')->group(function () {
        Route::get('/', [PendaftaranController::class, 'index'])->name('pendaftaran.index');
        Route::get('/caripasien', [PendaftaranController::class, 'cariPasien'])->name('pendaftaran.caripasien');
        Route::get('/caripasien-json', [PendaftaranController::class, 'cariPasienJson'])->name('pendaftaran.caripasien.json');
        Route::post('/antrian', [PendaftaranController::class, 'update_antrian'])->name('pendaftaran.update_antrian');

        // Patient routes
        Route::post('/pasien', [PendaftaranController::class, 'store_pasien'])->name('pendaftaran.store_pasien');
        Route::get('/pasien/{id}/edit', [PendaftaranController::class, 'editPasien'])->name('pendaftaran.editPasien');
        Route::post('/pasien/{id}/update', [PendaftaranController::class, 'updatePasien'])->name('pendaftaran.updatePasien');
        Route::get('/pasien/{id}/show', [PendaftaranController::class, 'showPasien'])->name('pendaftaran.showPasien');

        // Rawat Jalan
        Route::get('/rawatJalan', [PendaftaranController::class, 'showRawatJalan'])->name('pendaftaran.showRawatJalan');
        Route::post('/pasien/{id}/rawatJalan', [PendaftaranController::class, 'postRawatJalan'])->name('pendaftaran.postRawatJalan');
        Route::get('/pasien/{id}/editEncounter', [PendaftaranController::class, 'editEncounterRajal'])->name('pendaftaran.editEncounterRajal');
        Route::post('/pasien/{id}/updateRawatJalan', [PendaftaranController::class, 'updateRawatJalan'])->name('pendaftaran.updateRawatJalan');
        Route::delete('/pasien/{id}/destroyEncounter', [PendaftaranController::class, 'destroyEncounterRajal'])->name('pendaftaran.destroyEncounterRajal');

        // Rawat Inap - Tambahkan routes yang hilang
        Route::get('/rawatInap', [PendaftaranController::class, 'showRawatInap'])->name('pendaftaran.showRawatInap');
        Route::post('/rawatInap/{id}/postRawatInap', [PendaftaranController::class, 'postRawatInap'])->name('pendaftaran.postRawatInap');
        Route::post('/rawatInap/{id}/update', [PendaftaranController::class, 'updateRawatInap'])->name('pendaftaran.updateRawatInap');
        Route::get('/rawatInap/{id}/editEncounter', [PendaftaranController::class, 'editEncounterRinap'])->name('pendaftaran.editEncounterRinap');
        Route::delete('/rawatInap/{id}/destroy', [PendaftaranController::class, 'destroyRawatInap'])->name('pendaftaran.destroyEncounterRinap');

        // Rawat Darurat
        Route::get('/rawatDarurat', [PendaftaranController::class, 'showRawatDarurat'])->name('pendaftaran.showRawatDarurat');
        Route::get('/rawatDarurat/doctors', [PendaftaranController::class, 'getAllDoctors'])->name('pendaftaran.getAllDoctors');
        Route::post('/rawatDarurat/{id}/postRawatDarurat', [PendaftaranController::class, 'postRawatDarurat'])->name('pendaftaran.postRawatDarurat');
        Route::post('/rawatDarurat/{id}/update', [PendaftaranController::class, 'updateRawatDarurat'])->name('pendaftaran.updateRawatDarurat');
        Route::get('/rawatDarurat/{id}/editEncounter', [PendaftaranController::class, 'editEncounterRdarurat'])->name('pendaftaran.editEncounterRdarurat');
        Route::delete('/rawatDarurat/{id}/destroy', [PendaftaranController::class, 'destroyEncounterRdarurat'])->name('pendaftaran.destroyEncounterRdarurat');

        // Export routes
        Route::get('/rawatJalan/export', [PendaftaranController::class, 'exportRawatJalan'])->name('pendaftaran.exportRawatJalan');
        Route::get('/rawatDarurat/export', [PendaftaranController::class, 'exportRawatDarurat'])->name('pendaftaran.exportRawatDarurat');
        Route::get('/rawatInap/export', [PendaftaranController::class, 'exportRawatInap'])->name('pendaftaran.exportRawatInap');

        // Route moved outside auth middleware - see line 67
    });
    Route::resource('bahans', BahanController::class)->except(['show']);
    // AJAX tindakan per bahan
    Route::get('/bahans/{id}/tindakan-json', [BahanController::class, 'tindakanJson'])->name('bahans.tindakan.json');

    Route::get('/bahans/{id}/input', [BahanController::class, 'getBahan'])->name('bahan.getBahan');
    Route::post('/bahans/{id}/stok', [BahanController::class, 'stokBahan'])->name('bahan.stokBahan');
    Route::get('/bahans/{id}/getBahanKeluar', [BahanController::class, 'getBahanKeluar'])->name('bahan.getBahanKeluar');
    Route::post('/bahans/{id}/stokKeluar', [BahanController::class, 'stokKeluar'])->name('bahan.stokKeluar');
    Route::get('/bahans/getAllHistori', [BahanController::class, 'getAllHistori'])->name('bahan.getAllHistori');
    Route::get('/bahans/getRequestBahan', [BahanController::class, 'getRequestBahan'])->name('bahan.getRequestBahan');
    // bahan diserahkan
    Route::post('/bahans/diserahkan/{id}', [BahanController::class, 'bahanDiserahkan'])->name('bahan.bahanDiserahkan');
    Route::resource('pengguna', PenggunaController::class)->except(['show']);
    // Aktivitas Pengguna
    Route::get('/pengguna/aktivitas', [\App\Http\Controllers\PenggunaController::class, 'activityIndex'])->name('pengguna.activity.index');
    Route::get('/pengguna/aktivitas/{log}', [\App\Http\Controllers\PenggunaController::class, 'activityShow'])->name('pengguna.activity.show');
    // route frefix kunjungan
    Route::get('/pengguna/{user}/gaji', [\App\Http\Controllers\PenggunaController::class, 'aturGaji'])->name('pengguna.gaji.atur');
    Route::post('/pengguna/{user}/gaji', [\App\Http\Controllers\PenggunaController::class, 'simpanGaji'])->name('pengguna.gaji.simpan');
    Route::prefix('kunjungan')->group(function () {
        Route::get('/rawatJalan', [EncounterController::class, 'getAllRawatJalan'])->name('kunjungan.rawatJalan');
        Route::get('/rawatInap', [EncounterController::class, 'getAllRawatInap'])->name('kunjungan.rawatInap');
        Route::get('/rawatDarurat', [EncounterController::class, 'getAllRawatDarurat'])->name('kunjungan.rawatDarurat');

        // Dashboard Bed untuk Perawat - moved from masterdata to kunjungan
        Route::get('/dashboard-bed-perawat', [RuanganController::class, 'nurseBedDashboard'])->name('kunjungan.nurse-bed-dashboard');

        // API endpoints untuk nurse dashboard
        Route::get('/nurse-dashboard/refresh', [RuanganController::class, 'refreshNurseDashboard'])->name('api.nurse-dashboard.refresh');
        Route::get('/nurse-dashboard/admission/{admissionId}', [RuanganController::class, 'getAdmissionData'])->name('api.nurse-dashboard.admission-data');
        Route::get('/nurse-dashboard/room-patients/{roomId}', [RuanganController::class, 'getRoomPatients'])->name('api.nurse-dashboard.room-patients');
        Route::post('/nurse-dashboard/assign-room', [RuanganController::class, 'assignRoomToAdmission'])->name('api.nurse-dashboard.assign-room');
        Route::get('/nurse-dashboard/patient/{roomNumber}/{patientId?}', [RuanganController::class, 'getPatientDetail'])->name('api.nurse-dashboard.patient-detail');
        Route::post('/nurse-dashboard/nursing-note', [RuanganController::class, 'addNursingNote'])->name('api.nurse-dashboard.add-nursing-note');
        Route::post('/nurse-dashboard/emergency-call', [RuanganController::class, 'emergencyCall'])->name('api.nurse-dashboard.emergency-call');
        Route::post('/nurse-dashboard/vital-signs', [RuanganController::class, 'recordVitalSigns'])->name('api.nurse-dashboard.vital-signs');
        Route::post('/nurse-dashboard/store-vital-signs', [RuanganController::class, 'storeVitalSigns'])->name('kunjungan.nurse-dashboard.store-vital-signs');
        Route::get('/nurse-dashboard/vital-signs-history/{admissionId}', [RuanganController::class, 'getVitalSignsHistory'])->name('kunjungan.nurse-dashboard.vital-signs-history');
        Route::get('/nurse-dashboard/all-inpatients', [RuanganController::class, 'getAllInpatients'])->name('kunjungan.nurse-dashboard.all-inpatients');
        Route::post('/nurse-dashboard/complete-task', [RuanganController::class, 'completeTask'])->name('api.nurse-dashboard.complete-task');
        Route::get('/nurse-dashboard/occupied-rooms', [RuanganController::class, 'getOccupiedRoomsForTransfer'])->name('api.nurse-dashboard.occupied-rooms');
        Route::post('/nurse-dashboard/transfer-patient', [RuanganController::class, 'transferPatient'])->name('api.nurse-dashboard.transfer-patient');
        Route::get('/nurse-dashboard/nursing-notes', [RuanganController::class, 'getTodayNursingNotes'])->name('api.nurse-dashboard.nursing-notes');

        // Prescription Orders Routes
        Route::post('/prescription-orders', [RuanganController::class, 'storePrescriptionOrder'])->name('kunjungan.prescription-orders.store');
        Route::get('/prescription-orders/patient/{encounterId}', [RuanganController::class, 'getPatientPrescriptions'])->name('kunjungan.prescription-orders.patient');
        Route::get('/prescription-orders/schedule/{encounterId}', [RuanganController::class, 'getMedicationSchedule'])->name('kunjungan.prescription-orders.schedule');
        Route::patch('/prescription-orders/{id}/status', [RuanganController::class, 'updatePrescriptionStatus'])->name('kunjungan.prescription-orders.update-status');
        Route::delete('/prescription-orders/{id}', [RuanganController::class, 'deletePrescriptionOrder'])->name('kunjungan.prescription-orders.delete');

        // Medication Administration Routes
        Route::post('/medication-administration', [RuanganController::class, 'recordAdministration'])->name('kunjungan.medication-administration.record');
        Route::get('/medication-administration/history/{medicationId}', [RuanganController::class, 'getMedicationHistory'])->name('kunjungan.medication-administration.history');

        // Helper Routes
        Route::get('/api/doctors', [RuanganController::class, 'getDoctorsList'])->name('api.doctors');
        Route::get('/api/medications', [RuanganController::class, 'getMedicationsList'])->name('api.medications');
        Route::get('/api/product-name/{id}', [RuanganController::class, 'getProductName'])->name('api.product-name');

        // Medication Management Routes
        Route::get('/nurse-dashboard/medication-schedule', [RuanganController::class, 'getMedicationScheduleAll'])->name('kunjungan.nurse-dashboard.medication-schedule');
        Route::get('/nurse-dashboard/prescription-order/{prescriptionOrderId}', [RuanganController::class, 'getPrescriptionOrder'])->name('kunjungan.nurse-dashboard.prescription-order');
        Route::post('/nurse-dashboard/record-medication-administration', [RuanganController::class, 'recordMedicationAdministration'])->name('kunjungan.nurse-dashboard.record-medication-administration');
        Route::get('/nurse-dashboard/patient-pending-medications/{admissionId}', [RuanganController::class, 'getPatientPendingMedications'])->name('kunjungan.nurse-dashboard.patient-pending-medications');

        // Legacy endpoints (keep for compatibility)
        Route::get('/nurse-assignments', [RuanganController::class, 'getNurseAssignments'])->name('kunjungan.nurse-assignments');
        Route::get('/urgent-tasks', [RuanganController::class, 'getUrgentTasks'])->name('kunjungan.urgent-tasks');
        Route::post('/complete-task/{taskId}', [RuanganController::class, 'completeTask'])->name('kunjungan.complete-task');
        Route::get('/observasi/{id}', [ObservasiController::class, 'index'])->name('observasi.index');
        Route::get('/observasi/{id}/riwayatPenyakit', [ObservasiController::class, 'riwayatPenyakit'])->name('observasi.riwayatPenyakit');
        Route::post('/observasi/{id}/postAnemnesis', [ObservasiController::class, 'postAnemnesis'])->name('observasi.postAnemnesis');
        Route::get('/observasi/{id}/tandaVital', [ObservasiController::class, 'tandaVital'])->name('observasi.tandaVital');
        Route::get('/observasi/{id}/lastEncounterSummary', [ObservasiController::class, 'lastEncounterSummary'])->name('observasi.lastEncounterSummary');
        Route::get('/observasi/{id}/lastEncounterFull', [ObservasiController::class, 'lastEncounterFull'])->name('observasi.lastEncounterFull');
        Route::post('/observasi/{id}/copyLastEncounter', [ObservasiController::class, 'copyLastEncounter'])->name('observasi.copyLastEncounter');
        Route::post('/observasi/{id}/postTandaVital', [ObservasiController::class, 'postTandaVital'])->name('observasi.postTandaVital');
        Route::get('/observasi/{id}/pemeriksaanPenunjang', [ObservasiController::class, 'pemeriksaanPenunjang'])->name('observasi.pemeriksaanPenunjang');
        Route::post('/observasi/{id}/postPemeriksaanPenunjang', [ObservasiController::class, 'postPemeriksaanPenunjang'])->name('observasi.postPemeriksaanPenunjang');
        // delete pemeriksaan penunjang
        // [FIX] Route untuk mengambil template field dinamis
        Route::get('/pemeriksaan-penunjang/templates/{id}', [ObservasiController::class, 'getTemplateFields'])->name('observasi.getTemplateFields');
        Route::delete('/observasi/{id}/destroyPemeriksaanPenunjang', [ObservasiController::class, 'deletePemeriksaanPenunjang'])->name('observasi.deletePemeriksaanPenunjang');
        // [FIX] Pindahkan rute cetak ke dalam grup kunjungan
        Route::get('/observasi/pemeriksaan-penunjang/print/{id}', [ObservasiController::class, 'printPemeriksaanPenunjang'])->name('observasi.printPemeriksaanPenunjang');
        Route::get('/observasi/pemeriksaan-penunjang/download/{id}', [ObservasiController::class, 'downloadPemeriksaanPenunjang'])->name('observasi.downloadPemeriksaanPenunjang');
        // AJAX Hasil Lab (untuk realtime render)
        Route::get('/observasi/{id}/labRequests', [ObservasiController::class, 'labRequests'])->name('observasi.labRequests');
        Route::get('/observasi/lab/{id}/print', [ObservasiController::class, 'printLabRequest'])->name('observasi.lab.print');
        Route::get('/observasi/{id}/radiologyRequests', [ObservasiController::class, 'radiologyRequests'])->name('observasi.radiologyRequests');
        // Get detailed lab and radiologi results for modal display
        Route::get('/lab/{id}/hasil', [ObservasiController::class, 'getLabResults'])->name('kunjungan.lab.hasil');
        Route::get('/radiologi/{id}/hasil', [ObservasiController::class, 'getRadiologyResults'])->name('kunjungan.radiologi.hasil');
        Route::post('/observasi/radiologi/{id}/cancel', [ObservasiController::class, 'cancelRadiologyRequest'])->name('observasi.radiologi.cancel');
        Route::delete('/observasi/radiologi/{id}', [ObservasiController::class, 'destroyRadiologyRequest'])->name('observasi.radiologi.destroy');

        // route untuk tindakan counter
        Route::get('/observasi/getTindakan/{id}', [ObservasiController::class, 'getTindakan'])->name('observasi.getTindakan');
        Route::get('/observasi/TindakanEncounter/{id}', [ObservasiController::class, 'getTindakanEncounter'])->name('observasi.getTindakanEncounter');
        Route::post('/observasi/{id}/postTindakanEncounter', [ObservasiController::class, 'postTindakanEncounter'])->name('observasi.postTindakanEncounter');
        // delete tindakan encounter
        Route::delete('/observasi/{id}/deleteTindakanEncounter', [ObservasiController::class, 'deleteTindakanEncounter'])->name('observasi.deleteTindakanEncounter');

        // route untuk icd10
        Route::get('/observasi/getIcd10/{id}', [ObservasiController::class, 'getIcd10'])->name('observasi.getIcd10');
        Route::get('/observasi/getDiagnosis/{id}', [ObservasiController::class, 'getDiagnosis'])->name('observasi.getDiagnosis');
        Route::post('/observasi/{id}/postDiagnosis', [ObservasiController::class, 'postDiagnosis'])->name('observasi.postDiagnosis');
        // delete diagnosis
        Route::delete('/observasi/{id}/deleteDiagnosis', [ObservasiController::class, 'deleteDiagnosis'])->name('observasi.deleteDiagnosis');

        // route untuk resep
        Route::get('/observasi/getResep/{id}', [ObservasiController::class, 'getResep'])->name('observasi.getResep');
        Route::post('/observasi/{id}/postResep', [ObservasiController::class, 'postResep'])->name('observasi.postResep');

        // getProduk
        Route::get('/observasi/getProdukApotek/{id}', [ObservasiController::class, 'getProdukApotek'])->name('observasi.getProdukApotek');
        Route::post('/observasi/{id}/postResepDetail', [ObservasiController::class, 'postResepDetail'])->name('observasi.postResepDetail');
        // delete resep detail
        Route::delete('/observasi/{id}/deleteResepDetail', [ObservasiController::class, 'deleteResepDetail'])->name('observasi.deleteResepDetail');
        // get encounter
        Route::get('/observasi/getEncounter/{id}', [ObservasiController::class, 'getEncounterById'])->name('observasi.getEncounter');
        // diskon_tindakan
        Route::post('/observasi/{id}/postDiskonTindakan', [ObservasiController::class, 'postDiskonTindakan'])->name('observasi.postDiskonTindakan');
        // diskon_resep
        Route::post('/observasi/{id}/postDiskonResep', [ObservasiController::class, 'postDiskonResep'])->name('observasi.postDiskonResep');
        // post catatan encounter
        Route::post('/observasi/{id}/postCatatanEncounter', [ObservasiController::class, 'postCatatanEncounter'])->name('observasi.postCatatanEncounter');
        // Cetak Encounter
        Route::get('/observasi/{id}/cetak', [EncounterController::class, 'cetakEncounter'])->name('observasi.cetakEncounter');
        // getInpatientAdmission
        Route::get('/observasi/{id}/getInpatientAdmission', [ObservasiController::class, 'getInpatientAdmission'])->name('observasi.getInpatientAdmission');
        // getInpatientTreatment
        Route::get('/observasi/{id}/getInpatientTreatment', [ObservasiController::class, 'getInpatientTreatment'])->name('observasi.getInpatientTreatment');
        // postInpatientTreatment
        Route::post('/observasi/{id}/postInpatientTreatment', [ObservasiController::class, 'postInpatientTreatment'])->name('observasi.postInpatientTreatment');
        // deleteInpatientTreatment
        Route::delete('/observasi/{id}/deleteInpatientTreatment', [ObservasiController::class, 'deleteInpatientTreatment'])->name('observasi.deleteInpatientTreatment');
        //getInpatientDailyMedications
        Route::get('/observasi/{id}/getInpatientDailyMedications', [ObservasiController::class, 'getInpatientDailyMedications'])->name('observasi.getInpatientDailyMedications');
        //postInpatientDailyMedication
        Route::post('/observasi/{id}/postInpatientDailyMedication', [ObservasiController::class, 'postInpatientDailyMedication'])->name('observasi.postInpatientDailyMedication');
        // deleteInpatientDailyMedication
        Route::delete('/observasi/{id}/deleteInpatientDailyMedication', [ObservasiController::class, 'deleteInpatientDailyMedication'])->name('observasi.deleteInpatientDailyMedication');
        //updateInpatientDailyMedicationStatus
        Route::post('/observasi/{id}/updateInpatientDailyMedicationStatus', [ObservasiController::class, 'updateInpatientDailyMedicationStatus'])->name('observasi.updateInpatientDailyMedicationStatus');

        // Prescription Orders - New Medication Management System
        Route::get('/observasi/{encounterId}/prescription-orders', [ObservasiController::class, 'getPrescriptionOrders'])->name('observasi.getPrescriptionOrders');
        Route::post('/observasi/{encounterId}/prescription-orders', [ObservasiController::class, 'createPrescriptionOrder'])->name('observasi.createPrescriptionOrder');
        Route::get('/observasi/prescription-orders/{orderId}', [ObservasiController::class, 'getPrescriptionOrderDetail'])->name('observasi.getPrescriptionOrderDetail');
        Route::post('/observasi/prescription-orders/{orderId}/medications', [ObservasiController::class, 'addMedicationToPrescription'])->name('observasi.addMedicationToPrescription');
        Route::delete('/observasi/prescription-medications/{medicationId}', [ObservasiController::class, 'removeMedicationFromPrescription'])->name('observasi.removeMedicationFromPrescription');
        Route::patch('/observasi/prescription-orders/{orderId}/status', [ObservasiController::class, 'updatePrescriptionOrderStatus'])->name('observasi.updatePrescriptionOrderStatus');

        Route::get('/dashboard-dokter', [DokterController::class, 'index'])->name('dokter.index');
        Route::get('/histori-pasien', [DokterController::class, 'historiPasien'])->name('dokter.histori-pasien');
        Route::get('/histori-pendapatan', [DokterController::class, 'historiPendapatan'])->name('dokter.histori-pendapatan');

        // Konsultasi Spesialis
        Route::resource('konsultasi', SpecialistConsultationController::class)->only(['index', 'create', 'store', 'edit', 'update', 'show']);
        Route::get('konsultasi/{id}/cetak', [SpecialistConsultationController::class, 'print'])->name('konsultasi.print');
        Route::get('konsultasi/encounters/search', [SpecialistConsultationController::class, 'searchEncounters'])->name('konsultasi.encounters.search');

        // Asuhan Keperawatan
        Route::resource('keperawatan', NursingCareController::class)->only(['index', 'create', 'store', 'edit', 'update', 'show']);
        Route::get('keperawatan/{id}/cetak', [NursingCareController::class, 'print'])->name('keperawatan.print');
    });

    // Inventory - Alat Medis
    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('/alat-medis', [MedicalEquipmentController::class, 'index'])->name('equipment.index');
        Route::get('/alat-medis/create', [MedicalEquipmentController::class, 'create'])->name('equipment.create');
        Route::post('/alat-medis', [MedicalEquipmentController::class, 'store'])->name('equipment.store');
        Route::get('/alat-medis/{id}', [MedicalEquipmentController::class, 'show'])->name('equipment.show');
        Route::get('/alat-medis/{id}/edit', [MedicalEquipmentController::class, 'edit'])->name('equipment.edit');
        Route::put('/alat-medis/{id}', [MedicalEquipmentController::class, 'update'])->name('equipment.update');
        Route::delete('/alat-medis/{id}', [MedicalEquipmentController::class, 'destroy'])->name('equipment.destroy');
        // Maintenance log (gabungan - legacy)
        Route::get('/alat-medis/{id}/maintenance', [MedicalEquipmentController::class, 'maintenance'])->name('equipment.maintenance');
        Route::post('/alat-medis/{id}/maintenance', [MedicalEquipmentController::class, 'maintenanceStore'])->name('equipment.maintenance.store');
        // Perawatan terpisah
        Route::get('/alat-medis/{id}/perawatan', [MedicalEquipmentController::class, 'perawatan'])->name('equipment.perawatan');
        Route::post('/alat-medis/{id}/perawatan', [MedicalEquipmentController::class, 'perawatanStore'])->name('equipment.perawatan.store');
        // Kalibrasi terpisah
        Route::get('/alat-medis/{id}/kalibrasi', [MedicalEquipmentController::class, 'kalibrasi'])->name('equipment.kalibrasi');
        Route::post('/alat-medis/{id}/kalibrasi', [MedicalEquipmentController::class, 'kalibrasiStore'])->name('equipment.kalibrasi.store');
        // Download lampiran
        Route::get('/alat-medis/maintenance/{log}/download', [MedicalEquipmentController::class, 'maintenanceDownload'])->name('equipment.maintenance.download');
        // Edit/Update/Delete maintenance
        Route::get('/alat-medis/maintenance/{log}/edit', [MedicalEquipmentController::class, 'maintenanceEdit'])->name('equipment.maintenance.edit');
        Route::put('/alat-medis/maintenance/{log}', [MedicalEquipmentController::class, 'maintenanceUpdate'])->name('equipment.maintenance.update');
        Route::delete('/alat-medis/maintenance/{log}', [MedicalEquipmentController::class, 'maintenanceDestroy'])->name('equipment.maintenance.destroy');
        // Exports
        Route::get('/alat-medis/export/pdf', [MedicalEquipmentController::class, 'exportPdf'])->name('equipment.export.pdf');
        Route::get('/alat-medis/export/excel', [MedicalEquipmentController::class, 'exportExcel'])->name('equipment.export.excel');
    });

    Route::prefix('apotek')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\ApotekController::class, 'dashboard'])->name('apotek.dashboard');
        Route::resource('categories', CategoryController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
        Route::resource('products', ProductController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
        Route::get('/products/{id}/stok', [ProductController::class, 'addStock'])->name('product.addStock');
        Route::post('/products/{id}/stok', [ProductController::class, 'storeStock'])->name('product.storeStock');
        Route::get('/products/getAllHistori', [ProductController::class, 'getHistori'])->name('product.getAllHistori');
        // Permintaan Obat Inap
        Route::get('/permintaan-inap', [ApotekController::class, 'permintaanObatInap'])->name('apotek.permintaan-inap');
        Route::get('/permintaan-inap/detail/{id}', [ApotekController::class, 'permintaanObatInapDetail'])->name('apotek.permintaan-inap.detail');
        Route::post('/permintaan-inap/update-status/{id}', [ApotekController::class, 'updatePharmacyStatus'])->name('apotek.permintaan-inap.update-status');
        // Route untuk Penyiapan Resep (Rawat Jalan & Pulang)
        Route::get('/penyiapan-resep', [ApotekController::class, 'penyiapanResepIndex'])->name('apotek.penyiapan-resep');
        Route::get('/penyiapan-resep/detail/{id}', [ApotekController::class, 'penyiapanResepDetail'])->name('apotek.penyiapan-resep.detail');
        Route::post('/penyiapan-resep/siapkan/{id}', [ApotekController::class, 'siapkanResep'])->name('apotek.penyiapan-resep.siapkan');
        Route::post('/penyiapan-resep/siapkan-item/{id}', [ApotekController::class, 'siapkanItemResep'])->name('apotek.penyiapan-resep.siapkan-item');
        Route::get('/penyiapan-resep/reorder-list', [ApotekController::class, 'reorderList'])->name('apotek.penyiapan-resep.reorder.list');
        Route::post('/penyiapan-resep/{id}/reorder', [ApotekController::class, 'reorder'])->name('apotek.penyiapan-resep.reorder.action');
        Route::post('/penyiapan-resep/batalkan/{id}', [ApotekController::class, 'batalkanResep'])->name('apotek.penyiapan-resep.batalkan');

        // Detail Resep untuk Reminder (AJAX)
        Route::get('/resep-detail/{encounterId}', [ApotekController::class, 'resepDetail'])->name('apotek.resep-detail');

        Route::get('transaksi-resep/pdf', [ApotekController::class, 'exportPdf'])->name('apotek.transaksi-resep.pdf');
        Route::get('transaksi-resep/excel', [ApotekController::class, 'exportExcel'])->name('apotek.transaksi-resep.excel');
    });
    Route::prefix('loket')->group(function () {
        Route::get('/dashboard', [LoketController::class, 'dashboard'])->name('loket.dashboard');
        Route::get('/encounter', [LoketController::class, 'getEncounter'])->name('loket.getEncounter');
        Route::get('tindakan-detail/{id}', [LoketController::class, 'tindakanAjax']);
        Route::get('/encounter/{id}/cetak', [LoketController::class, 'cetakEncounter'])->name('loket.cetakEncounter');
        // PDF
        Route::get('transaksi-tindakan/pdf', [LoketController::class, 'exportPdf'])->name('loket.transaksi-tindakan.pdf');
        Route::get('transaksi-tindakan/excel', [LoketController::class, 'exportExcel'])->name('loket.transaksi-tindakan.excel');

        // getReminderEncounter
        Route::get('/reminder/getReminderEncounter', [\App\Http\Controllers\LoketController::class, 'getReminderEncounter'])->name('loket.getReminderEncounter');
        Route::post('/reminder/{logId}/mark-clicked', [\App\Http\Controllers\LoketController::class, 'markReminderClicked'])->name('loket.reminder.markClicked');
    });

    // Radiologi
    Route::prefix('radiologi')->as('radiologi.')->group(function () {
        Route::get('/dashboard', [RadiologiController::class, 'dashboard'])->name('dashboard');
        Route::get('/permintaan', [RadiologiController::class, 'requestsIndex'])->name('requests.index');
        Route::get('/permintaan/create', [RadiologiController::class, 'requestsCreate'])->name('requests.create');
        Route::post('/permintaan', [RadiologiController::class, 'requestsStore'])->name('requests.store');
        Route::get('/permintaan/{id}', [RadiologiController::class, 'requestsShow'])->name('requests.show');
        Route::get('/permintaan/{id}/print', [RadiologiController::class, 'print'])->name('requests.print');
        Route::get('/permintaan/{id}/hasil', [RadiologiController::class, 'resultsEdit'])->name('requests.results.edit');
        Route::post('/permintaan/{id}/hasil', [RadiologiController::class, 'resultsStore'])->name('requests.results.store');
        Route::post('/permintaan/{id}/status', [RadiologiController::class, 'requestsUpdateStatus'])->name('requests.status');
        Route::get('/hasil', [RadiologiController::class, 'resultsIndex'])->name('results.index');
        // AJAX search pasien untuk Radiologi
        Route::get('/pasien/search', [RadiologiController::class, 'searchPatients'])->name('patients.search');
        // AJAX search dokter pengirim
        Route::get('/dokter/search', [RadiologiController::class, 'searchDoctors'])->name('doctors.search');

        // Inventory/Supplies Management
        Route::resource('supplies', RadiologySupplyController::class)->except(['show']);
        Route::post('/supplies/{supply}/stock', [RadiologySupplyController::class, 'updateStock'])->name('supplies.stock');
        Route::get('/supplies-history', [RadiologySupplyController::class, 'history'])->name('supplies.history');
    });

    Route::prefix('kasir')->group(function () {
        Route::get('/', [KasirController::class, 'index'])->name('kasir.index');
        Route::get('/pembayaran/{pasien_id}', [KasirController::class, 'show'])->name('kasir.show');
        Route::post('/pembayaran/{pasien_id}', [KasirController::class, 'processPayment'])->name('kasir.processPayment');
        Route::get('/cetak-terakhir', [KasirController::class, 'cetakStrukTerakhir'])->name('kasir.cetakStrukTerakhir');
        Route::get('/histori', [KasirController::class, 'histori'])->name('kasir.histori');
        Route::get('/laporan', [KasirController::class, 'laporan'])->name('kasir.laporan');
        Route::get('/cetak-struk/{encounter_id}', [KasirController::class, 'cetakStruk'])->name('kasir.cetakStruk');
    });

    Route::prefix('laboratorium')->as('lab.')->group(function () {
        // Dashboard
        Route::get('/dashboard', [LabDashboardController::class, 'index'])->name('dashboard');

        // Search endpoints
        Route::get('/tests/search', [LabRequestController::class, 'searchTests'])->name('tests.search');
        Route::get('/encounters/search', [LabRequestController::class, 'searchEncounters'])->name('encounters.search');
        // Results (input hasil)
        Route::get('/results', [LabResultController::class, 'index'])->name('results.index');

        // Requests
        Route::resource('requests', LabRequestController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update']);
        Route::get('requests/{id}/print', [LabRequestController::class, 'print'])->name('requests.print');
        Route::get('requests/{id}/print-medical', [LabRequestController::class, 'printMedical'])->name('requests.print.medical');
        Route::get('requests/{id}/print-pdf', [LabRequestController::class, 'printPdf'])->name('requests.print.pdf');
        // Reagents
        Route::resource('reagents', LabReagentController::class)->except(['show', 'destroy']);
        Route::get('reagents/history', [LabReagentController::class, 'history'])->name('reagents.history');
        Route::get('reagents/{reagent}/stock', [LabReagentController::class, 'showStockForm'])->name('reagents.stock.form');
        Route::post('reagents/{reagent}/stock', [LabReagentController::class, 'storeStock'])->name('reagents.stock.store');
    });

    Route::prefix('medical-records')->as('medical-records.')->group(function () {
        Route::get('/dashboard', [MedicalRecordsController::class, 'dashboard'])->name('dashboard');
        Route::get('/riwayat', [MedicalRecordsController::class, 'riwayat'])->name('riwayat');
        Route::get('/riwayat/data', [MedicalRecordsController::class, 'riwayatData'])->name('riwayat.data');
        Route::get('/riwayat/pasien/{rekam_medis}', [MedicalRecordsController::class, 'riwayatPasien'])->name('riwayat.pasien');
        Route::get('/statistik', [MedicalRecordsController::class, 'statistik'])->name('statistik');
        Route::get('/arsip', [MedicalRecordsController::class, 'arsip'])->name('arsip');
        Route::post('/arsip/upload', [MedicalRecordsController::class, 'arsipUpload'])->name('arsip.upload');
        Route::get('/arsip/list', [MedicalRecordsController::class, 'arsipList'])->name('arsip.list');
        Route::delete('/arsip/{id}', [MedicalRecordsController::class, 'arsipDelete'])->name('arsip.delete');
    });

    Route::prefix('keuangan')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\KeuanganController::class, 'index'])->name('keuangan.index');
        Route::get('/detail-pendapatan', [\App\Http\Controllers\KeuanganController::class, 'getDetailPendapatan'])->name('keuangan.detail.pendapatan');
        Route::get('/gaji', [\App\Http\Controllers\KeuanganController::class, 'gaji'])->name('keuangan.gaji');
        Route::get('/pengaturan-insentif', [\App\Http\Controllers\KeuanganController::class, 'pengaturanIncentive'])->name('keuangan.incentive.settings');
        Route::post('/pengaturan-insentif', [\App\Http\Controllers\KeuanganController::class, 'simpanPengaturanIncentive'])->name('keuangan.incentive.settings.simpan');
        Route::post('/gaji/{user}/bayar', [\App\Http\Controllers\KeuanganController::class, 'paySalary'])->name('keuangan.gaji.bayar');
        Route::get('/gaji/{user}/detail', [\App\Http\Controllers\KeuanganController::class, 'gajiDetail'])->name('keuangan.gaji.detail');
        // Route untuk menyimpan penyesuaian gaji (bonus/potongan)
        Route::post('/gaji/{user}/penyesuaian', [\App\Http\Controllers\KeuanganController::class, 'storeSalaryAdjustment'])->name('keuangan.gaji.penyesuaian.simpan');
        // Route untuk Pengeluaran Operasional
        Route::resource('operasional', OperationalExpenseController::class)->except(['show']);
        // Route untuk Pendapatan Lainnya
        Route::resource('pendapatan-lain', OtherIncomeController::class)->except(['show']);
        // Route untuk Manajemen Insentif Manual
        Route::resource('insentif-manual', IncentiveController::class)
            ->except(['show'])->names('keuangan.insentif');

        // Laporan Keuangan (placeholder)
        Route::prefix('laporan')->name('keuangan.laporan.')->group(function () {
            Route::get('/kas-bank', [FinanceReportController::class, 'kasBank'])->name('kas-bank');
            Route::get('/kas-bank/export/pdf', [FinanceReportController::class, 'kasBankExportPdf'])->name('kas-bank.pdf');
            Route::get('/kas-bank/export/excel', [FinanceReportController::class, 'kasBankExportExcel'])->name('kas-bank.excel');

            Route::get('/ar-claim-bpjs', [FinanceReportController::class, 'arBpjs'])->name('ar-bpjs');
            Route::get('/ar-claim-bpjs/export/pdf', [FinanceReportController::class, 'arBpjsExportPdf'])->name('ar-bpjs.pdf');
            Route::get('/ar-claim-bpjs/export/excel', [FinanceReportController::class, 'arBpjsExportExcel'])->name('ar-bpjs.excel');

            Route::get('/ap-supplier', [FinanceReportController::class, 'apSupplier'])->name('ap-supplier');
            Route::get('/ap-supplier/export/pdf', [FinanceReportController::class, 'apSupplierExportPdf'])->name('ap-supplier.pdf');
            Route::get('/ap-supplier/export/excel', [FinanceReportController::class, 'apSupplierExportExcel'])->name('ap-supplier.excel');

            Route::get('/laba-rugi', [FinanceReportController::class, 'labaRugi'])->name('laba-rugi');
            Route::get('/laba-rugi/export/pdf', [FinanceReportController::class, 'labaRugiExportPdf'])->name('laba-rugi.pdf');
            Route::get('/laba-rugi/export/excel', [FinanceReportController::class, 'labaRugiExportExcel'])->name('laba-rugi.excel');

            // Tambahan sesuai standar RS
            Route::get('/ar-aging', [FinanceReportController::class, 'arAging'])->name('ar-aging');
            Route::get('/ar-aging/export/pdf', [FinanceReportController::class, 'arAgingExportPdf'])->name('ar-aging.pdf');
            Route::get('/ar-aging/export/excel', [FinanceReportController::class, 'arAgingExportExcel'])->name('ar-aging.excel');

            Route::get('/ap-aging', [FinanceReportController::class, 'apAging'])->name('ap-aging');
            Route::get('/ap-aging/export/pdf', [FinanceReportController::class, 'apAgingExportPdf'])->name('ap-aging.pdf');
            Route::get('/ap-aging/export/excel', [FinanceReportController::class, 'apAgingExportExcel'])->name('ap-aging.excel');

            Route::get('/payor-mix', [FinanceReportController::class, 'payorMix'])->name('payor-mix');
            Route::get('/payor-mix/export/pdf', [FinanceReportController::class, 'payorMixExportPdf'])->name('payor-mix.pdf');
            Route::get('/payor-mix/export/excel', [FinanceReportController::class, 'payorMixExportExcel'])->name('payor-mix.excel');

            Route::get('/pnl-cost-center', [FinanceReportController::class, 'pnlCostCenter'])->name('pnl-cost-center');
            Route::get('/pnl-cost-center/export/pdf', [FinanceReportController::class, 'pnlCostCenterExportPdf'])->name('pnl-cost-center.pdf');
            Route::get('/pnl-cost-center/export/excel', [FinanceReportController::class, 'pnlCostCenterExportExcel'])->name('pnl-cost-center.excel');

            Route::get('/cash-flow', [FinanceReportController::class, 'cashFlow'])->name('cash-flow');
            Route::get('/cash-flow/export/pdf', [FinanceReportController::class, 'cashFlowExportPdf'])->name('cash-flow.pdf');
            Route::get('/cash-flow/export/excel', [FinanceReportController::class, 'cashFlowExportExcel'])->name('cash-flow.excel');

            Route::get('/inventory', [FinanceReportController::class, 'inventory'])->name('inventory');
            Route::get('/inventory/export/pdf', [FinanceReportController::class, 'inventoryExportPdf'])->name('inventory.pdf');
            Route::get('/inventory/export/excel', [FinanceReportController::class, 'inventoryExportExcel'])->name('inventory.excel');
        });
    });

    // Route untuk Berita
    Route::resource('berita', BeritaController::class);

    // Development Testing Routes (REMOVE IN PRODUCTION)
    if (config('app.debug')) {
        Route::get('/test-components', function () {
            return view('test-components');
        })->name('test.components');
    }
});
