<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Provincia;
use App\Models\Documento;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use RicorocksDigitalAgency\Soap\Facades\Soap;
use Illuminate\Validation\Rule;

class RegistrationController extends Controller
{
    public function create()
    {
        $contacts = Contact::orderBy('nombres_apellidos')->get();
        return view('create', compact('contacts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipo_documento'    => 'required|string',
            'cedula' => Rule::requiredIf($request->tipo_documento === 'cedula'),
            'codigo_dactilar' => Rule::requiredIf($request->tipo_documento === 'cedula'),
            'fecha_nacimiento' => Rule::requiredIf($request->tipo_documento === 'pasaporte'),
            'pais_nacionalidad' => Rule::requiredIf($request->tipo_documento === 'pasaporte'),
            'correo'            => [
                'required',
                'email',
                'regex:/^[^\s,]+@[^\s,]+\.[^\s,]+$/'
            ],
            'telefono'          => 'required|string',
            'provincia_id'      => 'required|integer',
            'canton_id'         => 'required|integer',
            'zona_id'           => 'required|integer',
            'disponibilidad_movilizacion' => 'required|in:si,no',
            'provincias_movilizacion'     => 'nullable|array',
            'provincias_movilizacion.*'   => 'integer',
            'zonas_movilizacion'          => 'nullable|string',
            'exp_tecnico_superior' => 'required|in:SI,NO,NO_APLICA',
            'exp_tecnologo_superior' => 'required|in:SI,NO,NO_APLICA',
            'exp_tercer_nivel' => 'required|in:SI,NO,NO_APLICA',
            'capacitacion_100h' => 'required|in:SI,NO',
            'exp_gestion_educativa' => 'required|in:SI,NO,NO_APLICA',
            'exp_docencia_investigacion' => 'required|in:SI,NO,NO_APLICA',

        ], [
            'correo.regex' => 'El correo no debe contener espacios ni comas.',
        ]);

        // Validar Dinardap
        if ($request->tipo_documento === 'cedula') {
            $codigoDinardap = session('dinardap_codigo_dactilar');
            $fechaNacimiento = session('dinardap_fecha_nacimiento');
            $habilitadoCne = session('cne_habilitado');

            if (!$codigoDinardap) {
                Log::info("🟡 No se realizó validación Dinardap para la cédula: {$request->cedula}");
            } elseif ($codigoDinardap !== strtoupper($request->codigo_dactilar)) {
                return back()->withInput()->with('error', 'El código dactilar no coincide con los datos del Registro Civil.');
            }

            if ($fechaNacimiento) {
                $edad = Carbon::createFromFormat('d/m/Y', $fechaNacimiento)->age;
                if ($edad < 18) {
                    return back()->withInput()->with('error', 'El ciudadano debe ser mayor de edad.');
                }
            } else {
                Log::warning("⚠️ No se obtuvo la fecha de nacimiento de Dinardap para la cédula: {$request->cedula}");
            }

            if (!$habilitadoCne) {
                return back()->withInput()->with('error', 'Por favor realice la búsqueda de su cédula antes de guardar.');
            }
            if ($habilitadoCne !== 'SI') {
                return back()->withInput()->with('error', '🚫 Para continuar, el ciudadano debe estar habilitado para trámite público.');
            }
        }

        // Validar duplicados
        if (Contact::where('cedula', $request->cedula)->exists()) {
            return back()->withInput()->with('error', 'Este número de identificación ya fue registrado anteriormente.');
        }
        if (Contact::where('correo', $request->correo)->exists()) {
            return back()->withInput()->with('error', 'Este correo ya está registrado con otro número de identificación.');
        }

        Log::info('🟢 Datos recibidos Request:', $request->all());




        $contact = Contact::create([
            'tipo_documento' => $request->tipo_documento,
            'cedula' => $request->cedula,
            'nombres_apellidos' => $request->nombres_apellidos,
            'codigo_dactilar' => $request->codigo_dactilar,
            'correo' => $request->correo,
            'habilitado_cne' => session('cne_habilitado') ?? 'NO',
            'telefono' => $request->telefono,
            'provincia_id' => $request->provincia_id,
            'canton_id' => $request->canton_id,
            'zona_id' => $request->zona_id,
            'disponibilidad_movilizacion' => $request->disponibilidad_movilizacion,
            'provincias_movilizacion' => $request->provincias_movilizacion ? json_encode($request->provincias_movilizacion) : null,
            'zonas_movilizacion' => $request->zonas_movilizacion,
            'cuenta_con' => $request->cuenta_con ? json_encode($request->cuenta_con) : null,

            // Aquí los nuevos campos mapeados directamente
            'exp_tecnico_superior' => $request->exp_tecnico_superior,
            'exp_tecnologo_superior' => $request->exp_tecnologo_superior,
            'exp_tercer_nivel' => $request->exp_tercer_nivel,
            'capacitacion_100h' => $request->capacitacion_100h,
            'exp_gestion_educativa' => $request->exp_gestion_educativa,
            'exp_docencia_investigacion' => $request->exp_docencia_investigacion,

        ]);

        if ($request->hasFile('archivo')) {
            $nombreOriginal = $request->file('archivo')->getClientOriginalName();
            $rutaArchivo = $request->file('archivo')->storeAs('documentos_cv', $nombreOriginal, 'public');


            Documento::create([
                'contact_id' => $contact->id,
                'tipo' => 'cv',
                'ruta' => $rutaArchivo,
                'nombre_original' => $request->file('archivo')->getClientOriginalName(),
                'created_at' => now(),
            ]);
        }

        session()->forget([
            'dinardap_codigo_dactilar',
            'dinardap_fecha_nacimiento',
            'cne_habilitado',
        ]);

        session(['cedula' => $request->cedula]);

        return back()->with('success', '✅ Datos guardados exitosamente.');
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
            $fechaNacimiento = collect($registros)->firstWhere('campo', 'fechaNacimiento')->valor ?? null;

            Log::info('🔍 Respuesta Dinardap:', [
                'cedula'            => $cedula,
                'nombre'            => $nombre,
                'codigo_dactilar'   => $codigoDactilar,
                'fecha_nacimiento'  => $fechaNacimiento,
            ]);

            session([
                'dinardap_codigo_dactilar'  => $codigoDactilar,
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
                'cedula' => $cedula,
                'fecha_nacimiento' => $fechaNacimiento,
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

            //    Log::info('🔍 Respuesta CNE cruda:', json_decode(json_encode($response), true));

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

            session(['cne_habilitado' => $habilitado]);

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

            // Igual que el otro método
            $entidad = $response->return->entidades->entidad ?? null;
            $fila = $entidad ? ($entidad->filas->fila ?? null) : null;
            $columnas = $fila ? ($fila->columnas->columna ?? []) : [];

            if (!is_array($columnas)) {
                $columnas = [$columnas];
            }

            foreach ($columnas as $columna) {
                if (isset($columna->campo) && stripos($columna->campo, 'habilitado') !== false) {
                    return $columna->valor;
                }
            }

            return null;
        } catch (\Throwable $e) {
            Log::error("❌ Error consultando CNE interno: " . $e->getMessage());
            return null;
        }
    }

    public function getProvincias()
    {
        return \App\Models\Provincia::select('id', 'nombre', 'zona')
            ->orderBy('nombre')
            ->get();
    }

    public function validarCedula(Request $request)
    {
        $cedula = $request->cedula;

        // 1. ¿Ya existe en la base de datos?
        $existe = Contact::where('cedula', $cedula)->exists();
        if ($existe) {
            return response()->json([
                'valida' => false,
                'existe' => true,
                'message' => 'Esta cédula ya tiene un registro previo. No se puede volver a registrar.',
            ], 409);
        }

        // 2. Consultar a Dinardap
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

            if ($nombre) {
                return response()->json([
                    'valida' => true,
                    'existe' => false,
                    'message' => '✅ Cédula válida y encontrada en Registro Civil.',
                ]);
            } else {
                return response()->json([
                    'valida' => false,
                    'existe' => false,
                    'message' => '⚠️ Cédula no encontrada en el Registro Civil.',
                ], 404);
            }
        } catch (\Throwable $e) {
            Log::error("❌ Error consultando Dinardap: " . $e->getMessage());
            return response()->json([
                'valida' => false,
                'existe' => false,
                'message' => '⚠️ Error al consultar el Registro Civil, cédula no válida.',
            ], 500);
        }
    }

    public function validarPasaporte(Request $request)
    {
        $request->validate([
            'pasaporte' => ['required', 'string', 'max:20'],
        ]);

        $pasaporte = $request->pasaporte;

        if (preg_match('/^\d{10}$/', $pasaporte)) {
            return response()->json([
                'valido' => false,
                'existe' => false,
                'message' => '⚠️ Este número parece una cédula ecuatoriana. Por favor, verifique.',
            ], 422);
        }

        $existe = Contact::where('cedula', $pasaporte)->exists();

        if ($existe) {
            return response()->json([
                'valido' => false,
                'existe' => true,
                'message' => 'Este pasaporte ya tiene un registro previo.',
            ], 409);
        }

        return response()->json([
            'valido' => true,
            'existe' => false,
            'message' => '✅ Pasaporte válido. No se encontró en la base de datos.',
        ]);
    }
}
