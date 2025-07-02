<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = [
        'tipo_documento', // Por defecto 'cedula'
        'nombres_apellidos',
        'cedula',
        'correo',
        'codigo_dactilar',
        'fecha_expiracion',
        'habilitado_cne',
    ];
}
