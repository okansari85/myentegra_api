<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PazaramaToken extends Model
{
    use HasFactory;


    protected $table = 'pazarama_tokens';

    protected $fillable = ['token', 'expires_at'];
}
