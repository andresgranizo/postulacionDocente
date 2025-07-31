<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CancilleriaSoapService;

class CancilleriaController extends Controller
{
    public function consultarVisaPorParametros(Request $request, CancilleriaSoapService $cancilleria)
    {
        $request->validate([
            'pasaporte' => 'required|string',
            'idNacionalidad' => 'required|integer',
            'fechaNacimiento' => 'required|date',
        ]);

        $pasaporte = $request->input('pasaporte');
        $idNacionalidad = $request->input('idNacionalidad');
        $fechaNacimiento = $request->input('fechaNacimiento');

        $visaData = $cancilleria->obtenerVisaPorParametros($pasaporte, $idNacionalidad, $fechaNacimiento);

        if (is_array($visaData) && collect($visaData)->filter()->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => '❌ No se encontraron datos. Por favor verifique la información ingresada.',
            ], 422);
        }

        return response()->json($visaData);
    }

    public function listarPaises(CancilleriaSoapService $cancilleria)
    {
        $paises = $cancilleria->obtenerPaises();
        return response()->json($paises);
    }
}
