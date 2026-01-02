<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class ApiPeruService
{
    protected $token;
    protected $urlDni;
    protected $urlRuc;

    public function __construct()
    {
        $this->token = config('services.apiperu.token');
        $this->urlDni = config('services.apiperu.url_dni');
        $this->urlRuc = config('services.apiperu.url_ruc');
    }

    /**
     * Consultar DNI en API Peru
     *
     * @param string $dni
     * @return array|null
     */
    public function consultarDni(string $dni): ?array
    {
        if (empty($this->token)) {
            Log::error('APIPERU_TOKEN no configurado');
            return null;
        }

        $params = json_encode(['dni' => $dni]);
        
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $this->urlDni,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_POSTFIELDS => $params,
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->token
            ],
        ]);
        
        $response = curl_exec($curl);
        $err = curl_error($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($err) {
            Log::error('Error cURL al consultar DNI: ' . $err);
            return null;
        }

        $data = json_decode($response, true);

        if ($httpCode !== 200 || !isset($data['success']) || !$data['success']) {
            Log::warning('Error al consultar DNI. HTTP Code: ' . $httpCode . ', Response: ' . $response);
            return null;
        }

        return $data['data'] ?? null;
    }

    /**
     * Consultar RUC en API Peru
     *
     * @param string $ruc
     * @return array|null
     */
    public function consultarRuc(string $ruc): ?array
    {
        if (empty($this->token)) {
            Log::error('APIPERU_TOKEN no configurado');
            return null;
        }

        $params = json_encode(['ruc' => $ruc]);
        
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $this->urlRuc,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_POSTFIELDS => $params,
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->token
            ],
        ]);
        
        $response = curl_exec($curl);
        $err = curl_error($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($err) {
            Log::error('Error cURL al consultar RUC: ' . $err);
            return null;
        }

        $data = json_decode($response, true);

        if ($httpCode !== 200 || !isset($data['success']) || !$data['success']) {
            Log::warning('Error al consultar RUC. HTTP Code: ' . $httpCode . ', Response: ' . $response);
            return null;
        }

        return $data['data'] ?? null;
    }
}

