<?php

use App\Http\Controllers\AgamaController;
use App\Http\Controllers\AntrianController;
use App\Http\Controllers\BahanController;
use App\Http\Controllers\CategoryRuanganController;
use App\Http\Controllers\EncounterController;
use App\Http\Controllers\EtnisController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JaminanController;
use App\Http\Controllers\LokasiloketController;
use App\Http\Controllers\LoketController;
use App\Http\Controllers\ObservasiController;
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
        Route::resource('lokasiloket', LokasiloketController::class)->only(['index','create','store','edit', 'update', 'destroy']);
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
        Route::resource('jenisjaminan', JaminanController::class)->only(['index', 'store','edit', 'destroy']);
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

    });

    Route::prefix('pendaftaran')->group(function () {
        Route::get('/', [PendaftaranController::class, 'index'])->name('pendaftaran.index');
        Route::get('/rawatJalan', [PendaftaranController::class, 'showRawatJalan'])->name('pendaftaran.showRawatJalan');
        Route::get('/caripasien', [PendaftaranController::class, 'cariPasien'])->name('pendaftaran.caripasien');
        Route::post('/antrian', [PendaftaranController::class, 'update_antrian'])->name('pendaftaran.update_antrian');
        Route::post('/pasien', [PendaftaranController::class, 'store_pasien'])->name('pendaftaran.store_pasien');
        Route::get('/pasien/{id}/edit', [PendaftaranController::class, 'editPasien'])->name('pendaftaran.editPasien');
        Route::post('/pasien/{id}/update', [PendaftaranController::class, 'updatePasien'])->name('pendaftaran.updatePasien');
        Route::get('/pasien/{id}/show', [PendaftaranController::class, 'showPasien'])->name('pendaftaran.showPasien');
        Route::post('/pasien/{id}/rawatJalan', [PendaftaranController::class, 'postRawatJalan'])->name('pendaftaran.postRawatJalan');
        Route::get('/pasien/{id}/editEncounter', [PendaftaranController::class, 'editEncounterRajal'])->name('pendaftaran.editEncounterRajal');
        Route::post('/pasien/{id}/updateRawatJalan', [PendaftaranController::class, 'updateRawatJalan'])->name('pendaftaran.updateRawatJalan');
        Route::delete('/pasien/{id}/destroyEncounter', [PendaftaranController::class, 'destroyEncounterRajal'])->name('pendaftaran.destroyEncounterRajal');
    });
    Route::resource('bahans', BahanController::class)->only(['index','create','store','edit', 'update', 'destroy']);
    Route::get('/bahans/{id}/input', [BahanController::class, 'getBahan'])->name('bahan.getBahan');
    Route::post('/bahans/{id}/stok', [BahanController::class, 'stokBahan'])->name('bahan.stokBahan');
    Route::get('/bahans/{id}/getBahanKeluar', [BahanController::class, 'getBahanKeluar'])->name('bahan.getBahanKeluar');
    Route::post('/bahans/{id}/stokKeluar', [BahanController::class, 'stokKeluar'])->name('bahan.stokKeluar');
    Route::get('/bahans/getAllHistori', [BahanController::class, 'getAllHistori'])->name('bahan.getAllHistori');
    Route::resource('pengguna', PenggunaController::class)->only(['index','create','store','edit', 'update', 'destroy']);
    // route frefix kunjungan
    Route::prefix('kunjungan')->group(function () {
        Route::get('/rawatJalan', [EncounterController::class, 'getAllRawatJalan'])->name('kunjungan.rawatJalan');
        Route::get('/rawatInap', [EncounterController::class, 'getAllRawatInap'])->name('kunjungan.rawatInap');
        Route::get('/rawatDarurat', [EncounterController::class, 'getAllRawatDarurat'])->name('kunjungan.rawatDarurat');
        Route::get('/observasi/{id}', [ObservasiController::class, 'index'])->name('observasi.index');
        Route::get('/observasi/{id}/riwayatPenyakit', [ObservasiController::class, 'riwayatPenyakit'])->name('observasi.riwayatPenyakit');
        Route::post('/observasi/{id}/postAnemnesis', [ObservasiController::class, 'postAnemnesis'])->name('observasi.postAnemnesis');
        Route::get('/observasi/{id}/tandaVital', [ObservasiController::class, 'tandaVital'])->name('observasi.tandaVital');
        Route::post('/observasi/{id}/postTandaVital', [ObservasiController::class, 'postTandaVital'])->name('observasi.postTandaVital');
    });
});
