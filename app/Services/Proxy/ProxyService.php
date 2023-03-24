<?php
namespace App\Services\Proxy;

use GuzzleHttp\Client;

class ProxyService {
    public function __construct(
        protected Client $client,
        protected ?string $baseUri
    ) { }

    public function getListOfProxies()
    {
        if (!$this->baseUri || !is_string($this->baseUri)) {
            throw new \Exception('No base URI configured for proxy list');
        }

        $response = $this->client->get($this->baseUri);

        return json_decode($response->getBody()->getContents());
    }
}
