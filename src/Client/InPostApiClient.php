<?php

declare(strict_types=1);

namespace Azarniewicz\SyliusInPostPlugin\Client;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;

final readonly class InPostApiClient
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private string $apiBaseUrl,
    ) {
    }

    public function getPointByName(string $name): array
    {
        try {
            $response = $this->httpClient->request('GET', sprintf('%s/%s', $this->apiBaseUrl, rawurlencode($name)));

            if ($response->getStatusCode() !== 200) {
                throw new \RuntimeException('Unable to fetch InPost point details.');
            }

            return $response->toArray();
        } catch (ExceptionInterface | \RuntimeException $exception) {
            throw new \RuntimeException('Unable to fetch InPost point details.', 0, $exception);
        }
    }
}
