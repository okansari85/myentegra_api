<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Buyers extends Model
{
    use HasFactory;

    protected $table = 'buyers';

    protected $fillable = [
        'created_at',
        'updated_at',
        'buyer_id',
        'fullName',
        'taxId',
        'taxOffice',
        'email',
        'tcId'
    ];
}

