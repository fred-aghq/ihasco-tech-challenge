<?php

namespace App\Services\UrlQuery;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Validator;

class UrlQueryService
{

    public function __construct(
        protected Client $client
    ) {}

    public function query(string $url, string $proxy = ''): array
    {
        // ValidationException thrown if invalid, to be handled by the caller.
        $this->validateUrl($url);

        $options = [];

        if (!empty($$proxy)) {
            $options['proxy'] = $proxy;
        }

        $response = $this->client->get($url, $options);

        return $response->getHeaders();
    }

    /**
     * @param string $url
     * @return void
     * @throws \Illuminate\Validation\ValidationException
     */
    private function validateUrl(string $url): void
    {
        $input = [
            'url' => $url,
        ];

        // I don't like static calls but I gotta demonstrate *some* Laravel knowledge :)
        $validator = Validator::make(
            data: $input,
            rules: [
                'url' => 'required|url',
            ],
            messages: [
                'url.required' => 'URL is required',
                'url.url' => 'URL is invalid format',
            ]
        );

        $validator->validate();
    }
}
