<?php

namespace App\Services\Msisdn;

use GuzzleHttp\Client;
use Exception;

class Name
{
    private $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function search(string $msisdn, string $language): ?string
    {
        $token = $this->getToken();

        if (!$token) {
            return null;
        }

        $name = $this->getName($msisdn, $token, $language);

        return $name;
    }

    private function getName(string $msisdn, string $token, string $language = null): ?string
    {
        if (!$language) {
            $language = app()->getLocale();
        }

        $nameResponse = $this->curl("/customer/api/customerManagement/v3/customer/{$msisdn}?profile=NAME", [
            "Authorization: Bearer {$token}",
            'Content-Type: application/json',
            "Accept-Language: $language"
        ], [], 'GET');

        try {

            return $nameResponse['characteristic'][0]['value'];

        } catch (Exception $exception) {

            return null;

        }
    }

    private function getToken(): ?string
    {
        $login = config('msisdn.middleware.login');
        $password = config('msisdn.middleware.password');

        $auth = "Basic " . base64_encode("{$login}:{$password}");

        $result = $this->curl('/uaa/oauth/token?grant_type=client_credentials', [
            "Authorization: {$auth}"
        ]);

        try {

            return $result['access_token'];

        } catch (Exception $exception) {

            return null;

        }
    }

    private function curl($uri, $headers = [], $data = [], $method = 'POST')
    {
        $host = config('msisdn.middleware.host');

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL            => $host . $uri,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => "",
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => $method,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_POSTFIELDS     => $data
        ]);

        $response = curl_exec($curl);

        curl_close($curl);

        try {

            return json_decode($response, true);

        } catch (Exception $exception) {

            return [];

        }
    }

}
