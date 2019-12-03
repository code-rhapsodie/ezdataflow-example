<?php
declare(strict_types=1);

namespace AppBundle\Service;

use GuzzleHttp\Client;

class GeoClient
{
    private $client;
    /**
     * @var string
     */
    private $token;

    public function __construct(Client $client, string $token)
    {
        $this->client = $client;
        $this->token = $token;
    }

    public function read($url): iterable
    {
        $response = $this->client->get($url);

        $result = json_decode($response->getBody()->getContents(), true);

        if (false === $result) {
            return [];
        }


        $size = "600x600";

        $url = "https://api.mapbox.com/styles/v1/mapbox/streets-v11/static/";
        $endurl = ",14.25,0,0/" . $size . "?access_token=" . $this->token;

        foreach ($result as $row) {
            $path = tempnam(sys_get_temp_dir(), 'ezdemo');
            $realURL = $url . implode(',', $row['centre']['coordinates']) . $endurl;
            file_put_contents($path, file_get_contents($realURL));
            $row['image'] = $path;

            yield $row;
        }
    }
}
