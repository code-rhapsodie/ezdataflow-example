<?php
declare(strict_types=1);

namespace App\Service;

use GuzzleHttp\Client;

class GeoClient
{
    public function __construct(private readonly Client $client)
    {
    }

    public function read($url): iterable
    {
        $response = $this->client->get($url);

        $result = json_decode($response->getBody()->getContents(), true);

        if (false === $result) {
            return [];
        }

        foreach ($result as $row) {
            yield $row;
        }
    }
}
