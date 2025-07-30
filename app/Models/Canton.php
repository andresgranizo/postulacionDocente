<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Canton extends Model
{
    use HasFactory;
    protected $table = 'cantones';


    public function provincia()
    {
        return $this->belongsTo(Provincia::class, 'provincia_id');
    }
}
