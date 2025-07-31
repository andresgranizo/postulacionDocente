<?php

namespace App\Services;

use SoapClient;
use Exception;

class CancilleriaSoapService
{
    private $client;

    public function __construct()
    {
        $wsdl = "https://serviciows.cancilleria.gob.ec:444/Mre.Servicios.Senescyt/ServiceSenescytDmz.svc?wsdl";

        $this->client = new SoapClient($wsdl, [
            'login'    => 'senescyt',
            'password' => '3xT3N4r0',
            'trace'    => true,
            'exceptions' => true,
            'cache_wsdl' => WSDL_CACHE_NONE,
        ]);
    }

    public function obtenerPaises()
    {
        try {
            $response = $this->client->GetNacionalidad();
            return $response->GetNacionalidadResult->PaisInfo ?? [];
        } catch (Exception $e) {
            logger()->error('WS Cancillería error (paises): ' . $e->getMessage());
            return [];
        }
    }

   public function obtenerVisaPorParametros($pasaporte, $nacionalidadId, $fechaNacimiento)
{
    try {
        $fecha = new \DateTime($fechaNacimiento);
        $xmlFecha = $fecha->format('Y-m-d\TH:i:s');

        $params = [
            'numeroPasaporte' => $pasaporte,
            'idNacionalidad' => (int) $nacionalidadId,
            'fechaNacimiento' => $xmlFecha,
        ];

        $response = $this->client->GetInformacionVisaPorParametrosSenescyt($params);
        $resultado = $response->GetInformacionVisaPorParametrosSenescytResult->VisasSenescytInfo ?? null;

        if ($resultado && isset($resultado->Nombres, $resultado->PrimerApellido, $resultado->SegundoApellido)) {
            $resultado->nombre = trim(
                "{$resultado->Nombres} {$resultado->PrimerApellido} {$resultado->SegundoApellido}"
            );
        } else {
            $resultado->nombre = null;
        }

        return $resultado;
    } catch (\Exception $e) {
        logger()->error('WS Cancillería error (visa): ' . $e->getMessage());
        return response()->json([
            'error' => 'No se pudo obtener información de visa.',
            'detalle' => $e->getMessage()
        ], 500);
    }
}

}
