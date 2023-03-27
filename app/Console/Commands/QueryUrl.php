<?php

namespace App\Console\Commands;

use App\Services\Proxy\ProxyService;
use App\Services\UrlQuery\UrlQueryService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class QueryUrl extends Command
{
    protected $signature = 'query:url {url?} --retries=5';

    protected $description = 'Uses the first available proxy to query a URL and return the headers';

    public function __construct(
        private ProxyService $proxyService,
        private UrlQueryService $urlQueryService
    ) {
        parent::__construct();
    }

    private function getProxyList(): array
    {
        return $this->proxyService->getProxyList();
    }

    private function writeHeaders(array $headers): void {

        foreach($headers as $name => $values) {
            $line = $name . ': ' . implode(', ', $values);
            $this->line($line);
        }
    }

    private function logRequest(string $url) {
        $now = Carbon::now()->format('Y-m-d H:i:s');
        Log::channel('queryUrl')->info($url);
    }

    public function handle()
    {
        $url = $this->argument('url') ?? $this->ask('Enter URL to query');

        $validator = Validator::make([
            'url' => $url,
        ],
        [
            'url' => 'required|url',
        ]);

        if (empty($url) || $validator->fails()) {
            $this->error('Invalid URL');
            return self::FAILURE;
        }

        // Find a proxy.
        $proxyList = $this->getProxyList();

        $headers = $this->urlQueryService->query($url, $proxyList[0]);

        $this->writeHeaders($headers);

        $this->logRequest($url);

        return self::SUCCESS;
    }
}
