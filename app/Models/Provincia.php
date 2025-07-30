<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Provincia extends Model
{
    use HasFactory;

    public function cantones()
    {
        return $this->hasMany(Canton::class, 'provincia_id');
    }

    public function zona()
    {
        return $this->belongsTo(Zona::class, 'zona', 'id');
    }
}
