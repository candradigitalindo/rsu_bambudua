<?php

namespace App\Interfaces;

interface WilayahInterface
{
    /** Menyimpan Data Wilayah */
    public function saveWilayah();

    /** Mengambil semua data provinsi */
    public function getProvinces();

    /**
     * Mengambil data city sesuai code province
     */
    public function getCity($code);
}
