<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Encounter extends Model
{
    use HasUuids;
    protected $fillable = [
        'no_encounter',
        'rekam_medis',
        'name_pasien',
        'pasien_satusehat_id',
        'status',
        'type',
        'jenis_jaminan',
        'tujuan_kunjungan',
        'diskon_tindakan',
        'diskon_persen_tindakan',
        'total_tindakan',
        'total_bayar_tindakan',
        'diskon_resep',
        'diskon_persen_resep',
        'total_resep',
        'total_bayar_resep',
        'condition',
        'catatan',
        'status_bayar_resep',
        'metode_pembayaran_resep',
        'status_bayar_tindakan',
        'metode_pembayaran_tindakan',
        'clinic_id',
        'created_by'
    ];

    public function practitioner()
    {
        return $this->hasMany(Practitioner::class);
    }
    public function anamnesis()
    {
        return $this->hasOne(Anamnesis::class);
    }
    public function tandaVital()
    {
        return $this->hasOne(TandaVital::class);
    }
    public function pemeriksaanPenunjang()
    {
        return $this->hasMany(PemeriksaanPenunjang::class);
    }
    public function tindakan()
    {
        return $this->hasMany(TindakanEncounter::class);
    }
    public function requestBahan()
    {
        return $this->hasMany(RequestBahan::class);
    }
    public function diagnosis()
    {
        return $this->hasMany(Diagnosis::class);
    }
    public function resep()
    {
        return $this->hasOne(Resep::class);
    }
    // admission
    public function admission()
    {
        return $this->hasOne(InpatientAdmission::class);
    }

    public function patient()
    {
        return $this->belongsTo(Pasien::class, 'rekam_medis', 'rekam_medis');
    }

    /**
     * Mendapatkan URL pengingat WhatsApp untuk encounter.
     *
     * @return string
     */
    public function getWhatsappUrlAttribute(): string
    {
        // Mengambil no_hp dari properti encounter
        $noHp = preg_replace('/[^0-9]/', '', $this->no_hp);
        if (str_starts_with($noHp, '0')) {
            $noHp = '62' . substr($noHp, 1);
        }

        if (empty($noHp)) {
            return '#'; // Kembalikan link non-fungsional jika tidak ada nomor HP
        }

        $pesan = <<<PESAN
        *Bambu Dua Clinic* – *Pengingat Konsultasi*
        Halo {$this->name_pasien},
        Kami mengingatkan bahwa obat Anda kemungkinan akan habis dalam beberapa hari ke depan.

        Untuk menjaga kelangsungan pengobatan, kami sarankan Anda melakukan *kunjungan ulang sebelum obat habis*.

        *Jadwal kontrol ulang:* Senin - Sabtu Pukul 17.00 - 21.00 WIB
        *Lokasi:* Bambu Dua Clinic, Jl. Bambu II No.20
        *Konfirmasi kedatangan:* 0811-6311-378

        Kami siap membantu Anda menjaga kesehatan secara berkelanjutan.
        Terima kasih
        *Salam sehat*
        *Bambu Dua Clinic*
        PESAN;

        $pesanEncoded = urlencode($pesan);
        return "https://wa.me/{$noHp}?text={$pesanEncoded}";
    }
    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function nurses()
    {
        return $this->belongsToMany(User::class, 'encounter_nurse', 'encounter_id', 'user_id')->withTimestamps();
    }
    public function pasien()
    {
        return $this->belongsTo(Pasien::class, 'rekam_medis', 'rekam_medis');
    }
}
