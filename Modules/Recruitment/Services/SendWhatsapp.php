<?php

namespace Modules\Recruitment\Services;

use Exception;

class SendWhatsapp
{
    private $apiKey;
    private $instanceName;

    public function __construct()
    {
        $this->apiKey = env('WAPP_KEY');
        $this->instanceName = env('INSTANCE_WAPP');
    }

    public function sendText(string $text, string $toNumber)
    {
        $url = 'https://wpp.studiopro.com.br/message/sendText/' . $this->instanceName;

        $data = json_encode([
            "number" => '+55'.$toNumber,
            "textMessage" => ["text" => $text],
        ]);
        
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'accept: application/json',
            'apiKey: ' . $this->apiKey,
        ]);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            throw new Exception('Curl Error: ' . curl_error($curl));
        }

        curl_close($curl);
      
        return json_decode($response);
    }
}
