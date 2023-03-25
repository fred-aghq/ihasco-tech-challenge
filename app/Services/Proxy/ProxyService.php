<?php
namespace App\Services\Proxy;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Psr7\Message;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ProxyService {
    public function __construct(
        protected Client $client,
        protected string $baseUri
    ) { }

    public function getProxyList(): array
    {
        $response = $this->client->get($this->baseUri);

        if ($response->getStatusCode() === Response::HTTP_OK) {
            $content = $response->getBody()->getContents();

            if (strlen($content) > 0) {
                return explode("\n", $content);
            }

            return [];
        }

        throw new \Exception('Problem requesting proxy list, check logs for more info');
    }
}
