<?php


namespace TeamsBot;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use JsonException;
use TeamsBot\Exception\TeamsBotException;

class HttpClient
{
    private static ?Client $client;

    /**
     * @return Client
     */
    public static function getClient(): Client
    {
        if (empty(self::$client)) {
            self::setClient(new Client());
        }
        return self::$client;
    }

    /**
     * @param Client $client
     */
    public static function setClient(Client $client): void
    {
        self::$client = $client;
    }

    /**
     * @param string $method
     * @param string $url
     * @param array $data
     * @return array
     * @throws TeamsBotException
     */
    public static function process(string $method, string $url, array $data): array
    {
        try {
            $response = (self::getClient())->request($method, $url, $data);
            return json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);
        } catch (GuzzleException | JsonException $e) {
            throw new TeamsBotException('Error while sending message', $e->getCode());
        }
    }
}
