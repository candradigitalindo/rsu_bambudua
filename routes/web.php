<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\WilayahController;
use App\Models\City;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/home/{id}/profile', [HomeController::class, 'getProfile'])->name('home.profile');
    Route::post('/home/{id}/profile', [HomeController::class, 'updateProfile'])->name('home.profile.update');
    Route::get('/provinsi', function () {
        // $request = Http::withHeaders([
        //     'Authorization'     => 'Bearer Ok0HbRti60BoG1uqvV3OZTc54He7'
        // ])->get('https://api-satusehat-stg.dto.kemkes.go.id/masterdata/v1/sub-districts?district_codes',[
        //     'district_codes'    => '127505'
        // ]);

        // $response = json_decode($request, true)['data'];
        // return $response;
        City::truncate();
    });

    //Master Wilayah
    Route::get('/wilayah', [WilayahController::class, 'saveWilayah'])->name('wilayah.index');
    Route::get('/wilayah/province', [WilayahController::class, 'getProvinces'])->name('wilayah.province');
    Route::get('/wilayah/city/{code}', [WilayahController::class, 'getCity'])->name('wilayah.city');
});
