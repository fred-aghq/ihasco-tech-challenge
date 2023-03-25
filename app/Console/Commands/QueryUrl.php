<?php

namespace App\Console\Commands;

use App\Services\Proxy\ProxyService;
use App\Services\UrlQuery\UrlQueryService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class QueryUrl extends Command
{
    protected $signature = 'query:url {url?} --retries=5';

    protected $description = 'N/A';

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
            $this->line($name . ': ' . implode(', ', $values));
        }
    }

    private function logRequest(string $url) {
        $now = Carbon::now()->format('Y-m-d H:i:s');

        // @TODO: use LOGGER
        Log::channel('queryUrl')->info($url);
    }

    public function handle()
    {
        $url = $this->argument('url');


        while (empty($url)) {
            $url = $this->ask('Enter URL to query');
        }

        // @TODO: validate URL

        // Find a proxy.
        $proxyList = $this->getProxyList();

        // Make request.

        // TODO: decide whether to bail immediately on failure, define number of proxies to retry with, etc.
        $headers = $this->urlQueryService->query($url, $proxyList[0]);

        $this->writeHeaders($headers);

        // Log this request.
        $this->logRequest($url);

        // @TODO: use constants for exit codes
        return self::SUCCESS;
    }
}
