<?php

/**
 * Partial implementation of a particle.io api client.
 */
class ParticleAPI
{
    private $access_token;
    
    public function __construct($accessToken)
    {
        $this->accessToken = $accessToken;
    }

    public function call($deviceId, $method, $args = array())
    {
        $url = "https://api.particle.io/v1/devices/{$deviceId}/{$method}?access_token={$this->accessToken}";

        $data = http_build_query($args);

        $opts = array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Content-type: application/x-www-form-urlencoded',
                'content' => $data
            )
        );

        $context = stream_context_create($opts);
        $result = file_get_contents($url, false, $context);
        if ($result === false) {
            return false;
        }

        $decoded = json_decode($result);
        if (is_null($decoded)) {
            return false;
        }

        return $decoded;
    }
}
