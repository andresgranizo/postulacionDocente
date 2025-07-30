<?php

namespace App\Http\Controllers;

use App\Models\Provincia;
use App\Models\Canton;
use App\Models\Zona;

class CatalogoController extends Controller
{
    public function provincias()
    {
        return response()->json(Provincia::orderBy('nombre')->get());
    }

    public function cantones($provincia_id)
    {
        return response()->json(Canton::where('provincia_id', $provincia_id)->orderBy('nombre')->get());
    }

    public function zonas()
    {
        return response()->json(Zona::orderBy('nombre')->get());
    }
}
