<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    use HasFactory;

    protected $fillable = [
        'contact_id',
        'tipo',
        'ruta',
        'nombre_original',
    ];

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }
}
