<?php

namespace App\Enums;

enum UserRole: int
{
    case OWNER = 1;
    case DOKTER = 2;
    case PERAWAT = 3;
    case ADMIN = 4;
    case PENDAFTARAN = 5;
    case KEUANGAN = 6;
    case APOTEK = 7;
    case LABORATORIUM = 8;
    case RADIOLOGI = 9;
    case KASIR = 10;

    public function label(): string
    {
        return match ($this) {
            self::OWNER => 'Owner',
            self::DOKTER => 'Dokter',
            self::PERAWAT => 'Perawat',
            self::ADMIN => 'Admin',
            self::PENDAFTARAN => 'Pendaftaran',
            self::KEUANGAN => 'Keuangan',
            self::APOTEK => 'Apotek',
            self::LABORATORIUM => 'Laboratorium',
            self::RADIOLOGI => 'Radiologi',
            self::KASIR => 'Kasir',
        };
    }

    public function dashboardRoute(): string
    {
        return match ($this) {
            self::OWNER => 'home',
            self::ADMIN => 'admin.dashboard',
            self::DOKTER, self::PERAWAT => 'dokter.index',
            self::PENDAFTARAN => 'loket.dashboard',
            self::KEUANGAN => 'keuangan.index',
            self::APOTEK => 'apotek.dashboard',
            self::LABORATORIUM => 'lab.dashboard',
            self::RADIOLOGI => 'radiologi.dashboard',
            self::KASIR => 'kasir.index',
        };
    }
}
