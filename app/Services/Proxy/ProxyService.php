<?php
namespace App\Services\Proxy;

use GuzzleHttp\Client;

class ProxyService {
    public function __construct(
        protected Client $client
    ) { }

    public function getListOfProxies()
    {
        $response = $this->client->get('/v2/?request=displayproxies&protocol=http&timeout=10000&country=all&ssl=yes&anonymity=all');
        return json_decode($response->getBody()->getContents());
    }
}
