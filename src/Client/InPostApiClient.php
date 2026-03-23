<?php

declare(strict_types=1);

namespace Azarniewicz\SyliusInPostPlugin\Client;

use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class InPostApiClient
{
    private const API_BASE_URL = 'https://api-pl-points.easypack24.net/v1/points';

    public function __construct(
        private HttpClientInterface $httpClient,
    ) {
    }

    public function getPointByName(string $name): array
    {
        $response = $this->httpClient->request('GET', sprintf('%s/%s', self::API_BASE_URL, $name));

        if ($response->getStatusCode() !== 200) {
            throw new \RuntimeException(sprintf('Failed to fetch InPost point: %s', $name));
        }

        return $response->toArray();
    }
}
