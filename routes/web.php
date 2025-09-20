<?php

use App\Http\Controllers\AgamaController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\AntrianController;
use App\Http\Controllers\ApotekController;
use App\Http\Controllers\BahanController;
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

Route::middleware(['auth'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/home/{id}/profile', [HomeController::class, 'getProfile'])->name('home.profile');
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
        Route::get('/wilayah/desa/save/{code}', [WilayahController::class, 'saveDesa'])->name('wilayah.saveDesa');
        Route::resource('jenisjaminan', JaminanController::class)->only(['index', 'store', 'edit', 'destroy']);
        Route::resource('etnis', EtnisController::class)->only(['index', 'store', 'destroy']);
        Route::resource('pendidikan', PendidikanController::class)->only(['index', 'store', 'destroy']);
        Route::resource('agama', AgamaController::class)->only(['index', 'store', 'destroy']);
        Route::resource('pekerjaan', PekerjaanController::class)->only(['index', 'store', 'destroy']);
        Route::resource('spesialis', SpesialisController::class)->only(['index', 'store', 'destroy']);
        Route::resource('ruangan', RuanganController::class)->only(['index', 'create', 'edit', 'store', 'update', 'destroy']);
        Route::resource('category', CategoryRuanganController::class)->only(['index', 'create', 'edit', 'store', 'update', 'destroy']);
        Route::resource('tindakan', TindakanController::class)->only(['index', 'create', 'edit', 'store', 'update', 'destroy']);
        Route::get('/tindakan/getBahan/{id}', [TindakanController::class, 'getBahan'])->name('tindakan.getBahan');
        Route::post('/tindakan/storeBahan/{id}', [TindakanController::class, 'storeBahan'])->name('tindakan.storeBahan');
        Route::delete('/tindakan/destroyBahan/{id}', [TindakanController::class, 'destroyBahan'])->name('tindakan.destroyBahan');
        // Route CRUD Jenis Pemeriksaan Penunjang
        Route::get('jenis-pemeriksaan/{id}/fields', [\App\Http\Controllers\JenisPemeriksaanPenunjangController::class, 'showFields'])->name('jenis-pemeriksaan.fields.index');
        Route::post('jenis-pemeriksaan/{id}/fields', [\App\Http\Controllers\JenisPemeriksaanPenunjangController::class, 'storeField'])->name('jenis-pemeriksaan.fields.store');
        Route::delete('jenis-pemeriksaan/fields/{field_id}', [\App\Http\Controllers\JenisPemeriksaanPenunjangController::class, 'destroyField'])->name('jenis-pemeriksaan.fields.destroy');
        Route::resource('jenis-pemeriksaan', \App\Http\Controllers\JenisPemeriksaanPenunjangController::class)->except(['show']);
        Route::resource('icd10', \App\Http\Controllers\Icd10Controller::class);
        Route::post('icd10/import', [\App\Http\Controllers\Icd10Controller::class, 'import'])->name('icd10.import');
        // route discount
        Route::get('/discount', [\App\Http\Controllers\DiscountController::class, 'index'])->name('discounts.index');
        Route::post('/discount', [\App\Http\Controllers\DiscountController::class, 'update'])->name('discounts.update');
        Route::resource('clinics', \App\Http\Controllers\ClinicController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
    });

    Route::prefix('pendaftaran')->group(function () {
        Route::get('/', [PendaftaranController::class, 'index'])->name('pendaftaran.index');
        Route::get('/caripasien', [PendaftaranController::class, 'cariPasien'])->name('pendaftaran.caripasien');
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
        Route::post('/rawatDarurat/{id}/postRawatDarurat', [PendaftaranController::class, 'postRawatDarurat'])->name('pendaftaran.postRawatDarurat');
        Route::post('/rawatDarurat/{id}/update', [PendaftaranController::class, 'updateRawatDarurat'])->name('pendaftaran.updateRawatDarurat');
        Route::get('/rawatDarurat/{id}/editEncounter', [PendaftaranController::class, 'editEncounterRdarurat'])->name('pendaftaran.editEncounterRdarurat');
        Route::delete('/rawatDarurat/{id}/destroy', [PendaftaranController::class, 'destroyEncounterRdarurat'])->name('pendaftaran.destroyEncounterRdarurat');

        Route::get('/ajax/dokter-by-clinic/{clinic}', [PendaftaranController::class, 'getDokterByClinic'])->name('ajax.dokterByClinic');
    });
    Route::resource('bahans', BahanController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    Route::get('/bahans/{id}/input', [BahanController::class, 'getBahan'])->name('bahan.getBahan');
    Route::post('/bahans/{id}/stok', [BahanController::class, 'stokBahan'])->name('bahan.stokBahan');
    Route::get('/bahans/{id}/getBahanKeluar', [BahanController::class, 'getBahanKeluar'])->name('bahan.getBahanKeluar');
    Route::post('/bahans/{id}/stokKeluar', [BahanController::class, 'stokKeluar'])->name('bahan.stokKeluar');
    Route::get('/bahans/getAllHistori', [BahanController::class, 'getAllHistori'])->name('bahan.getAllHistori');
    Route::get('/bahans/getRequestBahan', [BahanController::class, 'getRequestBahan'])->name('bahan.getRequestBahan');
    // bahan diserahkan
    Route::post('/bahans/diserahkan/{id}', [BahanController::class, 'bahanDiserahkan'])->name('bahan.bahanDiserahkan');
    Route::resource('pengguna', PenggunaController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    // route frefix kunjungan
    Route::get('/pengguna/{user}/gaji', [\App\Http\Controllers\PenggunaController::class, 'aturGaji'])->name('pengguna.gaji.atur');
    Route::post('/pengguna/{user}/gaji', [\App\Http\Controllers\PenggunaController::class, 'simpanGaji'])->name('pengguna.gaji.simpan');
    Route::prefix('kunjungan')->group(function () {
        Route::get('/rawatJalan', [EncounterController::class, 'getAllRawatJalan'])->name('kunjungan.rawatJalan');
        Route::get('/rawatInap', [EncounterController::class, 'getAllRawatInap'])->name('kunjungan.rawatInap');
        Route::get('/rawatDarurat', [EncounterController::class, 'getAllRawatDarurat'])->name('kunjungan.rawatDarurat');
        Route::get('/observasi/{id}', [ObservasiController::class, 'index'])->name('observasi.index');
        Route::get('/observasi/{id}/riwayatPenyakit', [ObservasiController::class, 'riwayatPenyakit'])->name('observasi.riwayatPenyakit');
        Route::post('/observasi/{id}/postAnemnesis', [ObservasiController::class, 'postAnemnesis'])->name('observasi.postAnemnesis');
        Route::get('/observasi/{id}/tandaVital', [ObservasiController::class, 'tandaVital'])->name('observasi.tandaVital');
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

        Route::get('/dashboard-dokter', [DokterController::class, 'index'])->name('dokter.index');
    });

    Route::prefix('apotek')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\ApotekController::class, 'dashboard'])->name('apotek.dashboard');
        Route::resource('categories', \App\Http\Controllers\CategoryController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
        Route::resource('products', \App\Http\Controllers\ProductController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
        Route::get('/products/{id}/stok', [\App\Http\Controllers\ProductController::class, 'addStock'])->name('product.addStock');
        Route::post('/products/{id}/stok', [\App\Http\Controllers\ProductController::class, 'storeStock'])->name('product.storeStock');
        Route::get('/products/getAllHistori', [\App\Http\Controllers\ProductController::class, 'getHistori'])->name('product.getAllHistori');
        // Permintaan Obat Inap
        Route::get('/permintaan-inap', [ApotekController::class, 'permintaanObatInap'])->name('apotek.permintaan-inap');
        Route::get('/permintaan-inap/detail/{id}', [ApotekController::class, 'permintaanObatInapDetail'])->name('apotek.permintaan-inap.detail');
        Route::get('/permintaan-inap/detail-grouped/{admissionId}', [ApotekController::class, 'permintaanObatInapDetailGrouped'])->name('apotek.permintaan-inap.detail-grouped');
        Route::post('/permintaan-inap/siapkan/{id}', [ApotekController::class, 'siapkanObatInap'])->name('apotek.siapkan-inap');
        // Route untuk Penyiapan Resep (Rawat Jalan & Pulang)
        Route::get('/penyiapan-resep', [ApotekController::class, 'penyiapanResepIndex'])->name('apotek.penyiapan-resep');
        Route::get('/penyiapan-resep/detail/{id}', [ApotekController::class, 'penyiapanResepDetail'])->name('apotek.penyiapan-resep.detail');
        Route::post('/penyiapan-resep/siapkan/{id}', [ApotekController::class, 'siapkanResep'])->name('apotek.penyiapan-resep.siapkan');
        Route::post('/penyiapan-resep/siapkan-item/{id}', [ApotekController::class, 'siapkanItemResep'])->name('apotek.penyiapan-resep.siapkan-item');
        Route::post('/penyiapan-resep/batalkan/{id}', [ApotekController::class, 'batalkanResep'])->name('apotek.penyiapan-resep.batalkan');


        Route::get('transaksi-resep/pdf', [\App\Http\Controllers\ApotekController::class, 'exportPdf'])->name('apotek.transaksi-resep.pdf');
        Route::get('transaksi-resep/excel', [\App\Http\Controllers\ApotekController::class, 'exportExcel'])->name('apotek.transaksi-resep.excel');
    });
    Route::prefix('loket')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\LoketController::class, 'dashboard'])->name('loket.dashboard');
        Route::get('/encounter', [\App\Http\Controllers\LoketController::class, 'getEncounter'])->name('loket.getEncounter');
        Route::get('tindakan-detail/{id}', [\App\Http\Controllers\LoketController::class, 'tindakanAjax']);
        Route::get('/encounter/{id}/cetak', [\App\Http\Controllers\LoketController::class, 'cetakEncounter'])->name('loket.cetakEncounter');
        // PDF
        Route::get('transaksi-tindakan/pdf', [\App\Http\Controllers\LoketController::class, 'exportPdf'])->name('loket.transaksi-tindakan.pdf');
        Route::get('transaksi-tindakan/excel', [\App\Http\Controllers\LoketController::class, 'exportExcel'])->name('loket.transaksi-tindakan.excel');

        // getReminderEncounter
        Route::get('/reminder/getReminderEncounter', [\App\Http\Controllers\LoketController::class, 'getReminderEncounter'])->name('loket.getReminderEncounter');
    });

    Route::prefix('kasir')->group(function () {
        Route::get('/', [KasirController::class, 'index'])->name('kasir.index');
        Route::get('/pembayaran/{pasien_id}', [KasirController::class, 'show'])->name('kasir.show');
        Route::post('/pembayaran/{pasien_id}', [KasirController::class, 'processPayment'])->name('kasir.processPayment');
    });

    Route::prefix('keuangan')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\KeuanganController::class, 'index'])->name('keuangan.index');
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
        Route::resource('insentif-manual', \App\Http\Controllers\IncentiveController::class)
            ->except(['show'])->names('keuangan.insentif');
    });
});
