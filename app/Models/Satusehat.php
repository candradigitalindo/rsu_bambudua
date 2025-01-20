<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Satusehat extends Model
{
    use HasUuids;
    protected $fillable = [
        'organization_name', 'organization_id', 'client_id', 'client_secret', 'developer_email', 'access_token', 'expired_in', 'status'
    ];
}
