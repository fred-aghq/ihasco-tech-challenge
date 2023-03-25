<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class QueryUrl extends Command
{
    protected $signature = 'query:url {url}';

    protected $description = 'N/A';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Find a proxy.

        // @FIXME: rewrite as ProxyService and accept dependencies via constructor (maybe even service provider)
        $curl = curl_init('https://api.proxyscrape.com/v2/?request=displayproxies&protocol=http&timeout=10000&country=all&ssl=yes&anonymity=all');

        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($curl);

        curl_close($curl);

        $proxies = explode("\n", $response);

        // Make request.

        // @FIXME: validate url argument - use query service to do this so validation will be consistent if this is
        // reused on an HTTP route.
        $url = $this->argument('url');

        // @TODO: QueryService
        // TODO: decide whether to bail immediately on failure, define number of proxies to retry with, etc.
        foreach ($proxies as $proxy) {
            $curl = curl_init($url);

            curl_setopt($curl, CURLOPT_HEADER, 1);
            curl_setopt($curl, CURLOPT_PROXY, $proxy);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($curl);

            curl_close($curl);

            if (!$response) {
                continue;
            }
        }

        // Output HTTP headers.
        // @TODO: should be able to use a PSR-4(?) request obj for this
        $parts = explode("\r\n\r\n", $response, 2);

        $header = $parts[0];

        $this->line($header);

        // Log this request.

        $now = date('d/m/Y H:i:s');

        // @TODO: use LOGGER
        file_put_contents(storage_path() . '/logs/results.log', "{$now}: {$url}\r\n", FILE_APPEND);

        // @TODO: use constants for exit codes
        return 0;
    }
}
