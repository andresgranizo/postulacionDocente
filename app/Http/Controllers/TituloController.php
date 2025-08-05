<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DinardapService;

class TituloController extends Controller
{
    public function consultar(Request $request, DinardapService $dinardapService)
    {
        $request->validate([
            'cedula' => 'required|string|size:10',
        ]);

        $titulos = $dinardapService->consultarTitulosPorCedula($request->cedula);

        if (empty($titulos)) {
            return response()->json([
                'message' => 'No se encontraron títulos para esta cédula.',
                'titulos' => [],
            ], 404);
        }

        return response()->json([
            'message' => 'Títulos encontrados',
            'titulos' => $titulos
        ]);
    }
}
