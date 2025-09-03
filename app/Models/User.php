<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'role',
        'id_petugas',
        'id_satusehat',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }
    public function clinics()
    {
        return $this->belongsToMany(Clinic::class);
    }
    public function nurseEncounters()
    {
        return $this->belongsToMany(Encounter::class, 'encounter_nurse', 'user_id', 'encounter_id')->withTimestamps();
    }
    public function visitsDokter()
    {
        return $this->hasMany(InpatientVisit::class, 'dokter_id');
    }

    public function visitsPerawat()
    {
        return $this->hasMany(InpatientVisit::class, 'perawat_id');
    }
}
