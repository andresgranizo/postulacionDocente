<?php

namespace App\Services;

use SoapClient;
use Exception;
use Illuminate\Support\Facades\Log;

class DinardapService
{
    protected string $wsdl = 'https://interoperabilidad.dinardap.gob.ec/interoperador-v2?wsdl';
    protected string $usuario = 'DINpINtOpIFth';
    protected string $clave = 'jlvsJlJrvH%ziO';

    public function consultarTitulosPorCedula(string $cedula): array
    {
        Log::info("Consultando títulos para cédula: {$cedula}");

        $resultado = $this->consultarPaquete($cedula, '1286'); // SOLO SENESCYT
        Log::info('Respuesta cruda: ' . json_encode($resultado));

        if (!empty($resultado)) {
            $filas = $this->extraerFilas($resultado);
            $titulos = [];

            foreach ($filas as $fila) {
                $titulo = $this->mapearTitulo($fila);
                if ($titulo) {
                    $titulos[] = $titulo;
                }
            }

            return $titulos;
        }

        return [];
    }

    private function consultarPaquete(string $identificacion, string $codigoPaquete): mixed
    {
        try {
            $client = new SoapClient($this->wsdl, [
                'login' => $this->usuario,
                'password' => $this->clave,
                'trace' => true,
                'exceptions' => true,
                'cache_wsdl' => WSDL_CACHE_NONE,
                'connection_timeout' => 60,
            ]);

            $params = [
                'parametros' => [
                    'parametro' => [
                        ['nombre' => 'codigoPaquete', 'valor' => $codigoPaquete],
                        ['nombre' => 'identificacion', 'valor' => $identificacion]
                    ]
                ]
            ];

            $response = $client->__soapCall('consultar', [$params]);
            return $response->paquete->entidades->entidad ?? null;
        } catch (Exception $e) {
            report($e);
            return null;
        }
    }

    private function extraerFilas($entidades): array
    {
        $filas = [];

        $entidades = is_array($entidades) ? $entidades : [$entidades];

        foreach ($entidades as $entidad) {
            if (!isset($entidad->filas->fila)) continue;
            $filaData = is_array($entidad->filas->fila) ? $entidad->filas->fila : [$entidad->filas->fila];
            $filas = array_merge($filas, $filaData);
        }

        return $filas;
    }

    private function mapearTitulo($fila): ?array
    {
        $col = function ($campo) use ($fila) {
            foreach ($fila->columnas->columna as $columna) {
                if ($columna->campo === $campo) {
                    return $columna->valor ?? null;
                }
            }
            return null;
        };

        $nombreTitulo = $col('nombreTitulo');
        $institucion = $col('ies');
        $fecha = $col('fechaRegistro');
        $estado = $col('estadoTitulo') ?? 'Desconocido';

        if ($nombreTitulo && $institucion) {
            return [
                'titulo' => $nombreTitulo,
                'institucion' => $institucion,
            ];
        }

        return null;
    }
}
