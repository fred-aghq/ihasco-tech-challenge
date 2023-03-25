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

    public function getListOfProxies(): array
    {
        try {
            $response = $this->client->get($this->baseUri);
        }
        catch(TransferException $e) {
            // @TODO: Move to an exception handler so this pattern is more DRY
            Log::error('Problem requesting proxy list', [
                'message' => $e->getMessage(),
                'request' => Message::toString($e->getRequest()),
                'response' => Message::toString($e->getResponse()),
            ]);
        }

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
