<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Depos extends Model
{
    use HasFactory;

    protected $table = 'depos';

    protected $fillable = ['name', 'parent_id'];

    public function children()
    {
        return $this->hasMany(Depos::class, 'parent_id');
    }

    public function parent() {
        return $this->belongsTo(Depos::class, 'parent_id');
    }

    public function descendants()
    {
        return $this->parent()->with('descendants');
    }
}
