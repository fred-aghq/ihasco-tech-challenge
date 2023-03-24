<?php
namespace App\Services\Proxy;

use GuzzleHttp\Client;

class ProxyService {
    public function __construct(
        protected Client $client,
        protected string $baseUri
    ) { }

    public function getListOfProxies()
    {
        $response = $this->client->get($this->baseUri);

        return json_decode($response->getBody()->getContents());
    }
}
