<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = [
        'tipo_documento',
        'nombres_apellidos',
        'cedula',
        'correo',
        'codigo_dactilar',
        'habilitado_cne',
        'telefono',
        'provincia_id',
        'canton_id',
        'zona_id',
        'cuenta_con',
        'provincias_movilizacion',
        'zonas_movilizacion',
        'disponibilidad_movilizacion',
        'reconocimiento_archivo',
        'exp_tecnico_superior',
        'exp_tecnologo_superior',
        'exp_tercer_nivel',
        'capacitacion_100h',
        'exp_gestion_educativa',
        'exp_docencia_investigacion', // si guardas el nombre del archivo
    ];
}
