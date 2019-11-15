<?php
declare(strict_types=1);

namespace AppBundle\Service;

use GuzzleHttp\Client;

class GeoClient
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
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
