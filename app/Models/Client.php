<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $primaryKey = 'client_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'client_id',
        'client_name',
        'ic_number',
        'installation_address',
        'phone_number',
        'email_address',
    ];
}

