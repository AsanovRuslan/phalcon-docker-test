<?php


namespace Site\App;

class Request
{
    public static function jsonRPC(string $url, string $controller, string $action, array $params)
    {
        $result = static::postJSON($url, [
            'params'   => $params,
            'jsonrpc' => '2.0',
            'method'   => $controller . '.' . $action,
            'id'       => random_int(1, 10000),
        ]);

        return json_decode($result, true);
    }

    private static function postJSON(string $url, array $params)
    {
        $curl        = curl_init();
        $data_string = json_encode($params);

        curl_setopt_array($curl, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => ['Content-Type:application/json', /*'Content-Length: ' . strlen($data_string)*/],
            CURLOPT_POSTFIELDS     => $data_string,
        ]);
        $response = curl_exec($curl);
        curl_close($curl);

        return $response;
    }
}