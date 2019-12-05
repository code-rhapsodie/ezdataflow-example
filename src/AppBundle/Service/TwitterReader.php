<?php


namespace AppBundle\Service;


use TwitterAPIExchange;

class TwitterReader
{
    /**
     * @var TwitterAPIExchange
     */
    private $exchange;

    public function __construct(TwitterAPIExchange $exchange)
    {
        $this->exchange = $exchange;
    }

    public function read(string $account, int $limit = 10)
    {
        $url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
        $getfield = '?screen_name=' . $account;
        $requestMethod = 'GET';


        $result = $this->exchange->setGetfield($getfield)
            ->buildOauth($url, $requestMethod)
            ->performRequest();

        $twits = json_decode($result, true);
        if (false === $twits) {
            throw  new \Exception('Invalid Json from Twitter');
        }
        $twits = array_slice($twits, 0, $limit);
        foreach ($twits as $twit) {
            yield $twit;
        }
    }
}