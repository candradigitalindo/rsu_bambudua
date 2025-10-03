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
    case KASIR = 10;

    public function label(): string
    {
        return match($this) {
            self::OWNER => 'Owner',
            self::DOKTER => 'Dokter',
            self::PERAWAT => 'Perawat',
            self::ADMIN => 'Admin',
            self::PENDAFTARAN => 'Pendaftaran',
            self::KEUANGAN => 'Keuangan',
            self::APOTEK => 'Apotek',
            self::KASIR => 'Kasir',
        };
    }

    public function dashboardRoute(): string
    {
        return match($this) {
            self::OWNER, self::ADMIN => 'home',
            self::DOKTER, self::PERAWAT => 'dokter.index',
            self::PENDAFTARAN => 'loket.dashboard',
            self::KEUANGAN => 'keuangan.index',
            self::APOTEK => 'apotek.dashboard',
            self::KASIR => 'kasir.index',
        };
    }

    public static function fromValue(int $value): ?self
    {
        return match($value) {
            1 => self::OWNER,
            2 => self::DOKTER,
            3 => self::PERAWAT,
            4 => self::ADMIN,
            5 => self::PENDAFTARAN,
            6 => self::KEUANGAN,
            7 => self::APOTEK,
            10 => self::KASIR,
            default => null,
        };
    }
}