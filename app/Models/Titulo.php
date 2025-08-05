<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Titulo extends Model
{
    protected $fillable = [
        'contact_id',
        'titulo',
        'institucion',
    ];

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }
}
