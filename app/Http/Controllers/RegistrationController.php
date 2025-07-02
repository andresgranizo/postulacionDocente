<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;
use RicorocksDigitalAgency\Soap\Facades\Soap;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class RegistrationController extends Controller
{
    public function create()
    {
        return view('create');
    }

    public function store(Request $request)
    {

        $request->validate([
            'tipo_documento'    => 'required|string',
            'cedula'            => 'required',
            'nombres_apellidos' => 'required|string',
            'codigo_dactilar'   => 'required|string',
            'correo' => [
                'required',
                'email',
                'regex:/^[^\s,]+@[^\s,]+\.[^\s,]+$/'
            ],
            'fecha_expiracion'  => 'required|date',
        ], [
            'correo.regex'    => 'El correo no debe contener espacios ni comas.',
            'correo.email'    => 'El correo debe tener un formato válido.',
            'correo.required' => 'El campo correo electrónico es obligatorio.',
            'codigo_dactilar' => 'El campo codigo dactilar es obligatorio.',
        ], [
            'tipo_documento'    => 'tipo de documento',
            'cedula'            => 'cédula',
            'nombres_apellidos' => 'apellidos y nombres',
            'codigo_dactilar'   => 'código dactilar',
            'correo'            => 'correo electrónico',
            'fecha_expiracion'  => 'fecha de expiración',
            'fecha_nacimiento'  => 'fecha de nacimiento',

        ]);

        if ($request->tipo_documento === 'cedula') {
            $codigoDinardap = session('dinardap_codigo_dactilar');
            $fechaDinardap  = session('dinardap_fecha_expiracion');
            $fechaNacimiento = session('dinardap_fecha_nacimiento');
            print_r($fechaNacimiento);


            if (!empty($fechaDinardap) && !empty($codigoDinardap)) {
                $fechaDinardapFormateada = Carbon::createFromFormat('d/m/Y', $fechaDinardap)->startOfDay();
                $fechaFormulario = Carbon::parse($request->fecha_expiracion)->startOfDay();

                if (
                    $codigoDinardap !== strtoupper($request->codigo_dactilar) ||
                    !$fechaDinardapFormateada->equalTo($fechaFormulario)
                ) {
                    return redirect()->back()
                        ->with('error', 'El código dactilar o la fecha de expiración no coinciden con los datos del Registro Civil.')
                        ->withInput();
                }
            } else {
                Log::info("🟡 No se realizó validación Dinardap para la cédula: " . $request->cedula);
            }

            if ($fechaNacimiento) {
                $fechaNacimientoCarbon = Carbon::createFromFormat('d/m/Y', $fechaNacimiento);
                $edad = $fechaNacimientoCarbon->age;

                if ($edad < 18) {
                    return redirect()->back()
                        ->with('error', 'El ciudadano debe ser mayor de edad.')
                        ->withInput();
                }
            } else {
                Log::warning("⚠️ No se obtuvo la fecha de nacimiento de Dinardap para la cédula: " . $request->cedula);
            }

            // Validación contra CNE
            $cneResult = $this->consultarCneInterno($request->cedula);
            if ($cneResult !== 'SI') {
                return redirect()->back()
                    ->with('error', '🚫 Para continuar con el proceso, el ciudadano debe estar habilitado para trámite público. Si considera que esta información es incorrecta, por favor verifique sus datos o contacte a las autoridades correspondientes.')
                    ->withInput();
            }
        }

        if (Contact::where('cedula', $request->cedula)->exists()) {
            return redirect()->back()
                ->with('error', 'Este número de identificación ya fue registrado anteriormente.')
                ->withInput();
        }

        if (Contact::where('correo', $request->correo)->exists()) {
            return redirect()->back()
                ->with('error', 'Este correo ya está registrado con otro número de identificación.')
                ->withInput();
        }

        $habilitadoCne = $this->consultarCneInterno($request->cedula);
        if (!$habilitadoCne) {
            $habilitadoCne = 'NO';
        }

        Contact::create([
            'tipo_documento'    => $request->tipo_documento,
            'cedula'            => $request->cedula,
            'nombres_apellidos' => $request->nombres_apellidos,
            'codigo_dactilar'   => $request->codigo_dactilar,
            'correo'            => $request->correo,
            'fecha_expiracion'  => $request->fecha_expiracion,
            'habilitado_cne'    => $habilitadoCne,
        ]);

        session()->forget(['dinardap_codigo_dactilar', 'dinardap_fecha_expiracion', 'dinardap_fecha_nacimiento']);

        return redirect()->back()->with('success', '✅ Datos guardados exitosamente.');
    }

    public function consultarCedula(Request $request)
    {
        $request->validate([
            'cedula' => ['required', 'digits:10']
        ]);

        $cedula = $request->cedula;

        try {
            $context = stream_context_create([
                'ssl' => [
                    'verify_peer'       => false,
                    'verify_peer_name'  => false,
                    'allow_self_signed' => true,
                ],
            ]);

            $response = Soap::to(config('soap.dinardap_wsdl'))
                ->withBasicAuth(
                    config('soap.dinardap_user'),
                    config('soap.dinardap_pass')
                )
                ->withOptions(['stream_context' => $context])
                ->call('getFichaGeneral', [
                    'numeroIdentificacion' => $cedula,
                    'codigoPaquete'        => '471',
                ]);

            $body = $response->response;
            $registros = $body->return->instituciones->datosPrincipales->registros ?? [];

            $nombre = collect($registros)->firstWhere('campo', 'nombre')->valor ?? null;
            $codigoDactilar = collect($registros)->firstWhere('campo', 'individualDactilar')->valor ?? null;
            $fechaExpiracion = collect($registros)->firstWhere('campo', 'fechaExpiracion')->valor ?? null;
            $fechaNacimiento = collect($registros)->firstWhere('campo', 'fechaNacimiento')->valor ?? null;

            Log::info('🔍 Respuesta Dinardap:', [
                'cedula'            => $cedula,
                'nombre'            => $nombre,
                'codigo_dactilar'   => $codigoDactilar,
                'fecha_expiracion'  => $fechaExpiracion,
                'fecha_nacimiento'  => $fechaNacimiento,
            ]);
            session([
                'dinardap_codigo_dactilar'  => $codigoDactilar,
                'dinardap_fecha_expiracion' => $fechaExpiracion,
                'dinardap_fecha_nacimiento' => $fechaNacimiento,
            ]);

            if (!$nombre) {
                return response()->json([
                    'error'   => 'no_encontrado',
                    'message' => 'No se encontraron datos para esta cédula'
                ], 200);
            }

            return response()->json([
                'nombre' => $nombre,
                'cedula' => $cedula
            ], 200);
        } catch (\Throwable $e) {
            Log::error("❌ Dinardap getFichaGeneral error: " . $e->getMessage());
            return response()->json([
                'error'   => 'sin_servicio',
                'message' => 'No se pudo conectar al servicio Dinardap'
            ], 200);
        }
    }

    public function consultarCne(Request $request)
    {
        $request->validate([
            'cedula' => ['required', 'digits:10']
        ]);

        $cedula = $request->cedula;

        try {
            $context = stream_context_create([
                'ssl' => [
                    'verify_peer'       => false,
                    'verify_peer_name'  => false,
                    'allow_self_signed' => true,
                ],
            ]);

            $response = Soap::to('https://interoperabilidad.dinardap.gob.ec/interoperador-v2?wsdl')
                ->withBasicAuth(
                    'DINpINtOpIFth',
                    'jlvsJlJrvH%ziO'
                )
                ->withOptions(['stream_context' => $context])
                ->call('consultar', [
                    'parametros' => [
                        'parametro' => [
                            [
                                'nombre' => 'codigoPaquete',
                                'valor'  => '1284'
                            ],
                            [
                                'nombre' => 'identificacion',
                                'valor'  => $cedula
                            ],
                        ],
                    ],
                ]);

            Log::info('🔍 Respuesta CNE cruda:', json_decode(json_encode($response), true));

            // 🚨 Recorrer automáticamente buscando 'paquete'
            $paquete = null;

            if (isset($response->response->stdClass->paquete)) {
                $paquete = $response->response->stdClass->paquete;
            } elseif (isset($response->response->paquete)) {
                $paquete = $response->response->paquete;
            } elseif (isset($response->return->paquete)) {
                $paquete = $response->return->paquete;
            }

            if (!$paquete) {
                Log::error('❌ No se encontró el paquete en ninguna ruta.');
                return response()->json([
                    'error' => 'no_encontrado',
                    'message' => 'No se encontró el paquete en la respuesta.'
                ], 200);
            }

            $entidad = $paquete->entidades->entidad ?? null;
            $fila = $entidad ? ($entidad->filas->fila ?? null) : null;
            $columnas = $fila ? ($fila->columnas->columna ?? []) : [];

            if (!is_array($columnas)) {
                $columnas = [$columnas];
            }

            $columnas_array = json_decode(json_encode($columnas), true);
            Log::info('🔍 Columnas finales:', ['columnas' => $columnas_array]);

            $habilitado = null;

            foreach ($columnas as $columna) {
                if (isset($columna->campo) && stripos($columna->campo, 'habilitado') !== false) {
                    $habilitado = $columna->valor;
                    break;
                }
            }

            if ($habilitado === null) {
                return response()->json([
                    'error'   => 'no_encontrado',
                    'message' => 'No se encontró el campo habilitadoTPublico en CNE.'
                ], 200);
            }

            return response()->json([
                'cedula'     => $cedula,
                'habilitado' => $habilitado
            ], 200);
        } catch (\Throwable $e) {
            Log::error("❌ Error consultando CNE: " . $e->getMessage());
            return response()->json([
                'error'   => 'sin_servicio',
                'message' => 'No se pudo conectar al servicio CNE.'
            ], 200);
        }
    }




    private function consultarCneInterno($cedula)
    {
        try {
            $context = stream_context_create([
                'ssl' => [
                    'verify_peer'       => false,
                    'verify_peer_name'  => false,
                    'allow_self_signed' => true,
                ],
            ]);

            $response = Soap::to('https://interoperabilidad.dinardap.gob.ec/interoperador-v2?wsdl')
                ->withBasicAuth(
                    'DINpINtOpIFth',
                    'jlvsJlJrvH%ziO'
                )
                ->withOptions(['stream_context' => $context])
                ->call('consultar', [
                    'parametros' => [
                        'parametro' => [
                            [
                                'nombre' => 'codigoPaquete',
                                'valor'  => '1284'
                            ],
                            [
                                'nombre' => 'identificacion',
                                'valor'  => $cedula
                            ],
                        ],
                    ],
                ]);

            $entidades = collect($response->return->entidades->entidad ?? []);

            foreach ($entidades as $entidad) {
                $filas = collect($entidad->filas->fila ?? []);
                foreach ($filas as $fila) {
                    $columnas = collect($fila->columnas->columna ?? []);
                    foreach ($columnas as $columna) {
                        if ($columna->campo === 'habilitadoTPublico') {
                            return $columna->valor;
                        }
                    }
                }
            }

            return null;
        } catch (\Throwable $e) {
            Log::error("❌ Error consultando CNE interno: " . $e->getMessage());
            return null;
        }
    }
}
