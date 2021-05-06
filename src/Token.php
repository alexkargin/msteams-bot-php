<?php

namespace TeamsBot;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use JsonException;
use TeamsBot\Exception\TeamsBotTokenException;

/**
 * Token for sending message to user
 *
 * @author Alexey Kargin <alexka@live.ru>
 * @package TeamsBot
 */
class Token
{
    private const URL = 'https://login.microsoftonline.com/botframework.com/oauth2/v2.0/token';
    /**
     * @var string|null
     */
    private ?string $token = null;
    /**
     * @var string
     */
    private string $bot_id;
    /**
     * @var string
     */
    private string $bot_password;
    /**
     * @var int|null
     */
    private ?int $expires_in = null;

    /**
     * Token constructor.
     * @param string $bot_id
     * @param string $bot_password
     * @throws TeamsBotTokenException
     */
    public function __construct(string $bot_id, string $bot_password)
    {
        if (empty($bot_id)) {
            throw new TeamsBotTokenException('Bot ID not defined!');
        }
        if (empty($bot_password)) {
            throw new TeamsBotTokenException('Bot password not defined!');
        }
        $this->bot_id = $bot_id;
        $this->bot_password = $bot_password;
    }

    /**
     * @param Client $client
     * @return object
     * @throws TeamsBotTokenException
     */
    private function request(Client $client): object
    {
        try {
            $response = $client->request('POST', self::URL, [
                'form_params' => [
                    'grant_type' => 'client_credentials',
                    'client_id' => $this->bot_id,
                    'client_secret' => $this->bot_password,
                    'scope' => 'https://api.botframework.com/.default'
                ]
            ]);
            $token_data = json_decode($response->getBody(), false, 512, JSON_THROW_ON_ERROR);
            if (empty($token_data->token_type) || empty($token_data->access_token) || empty($token_data->expires_in)) {
                throw new TeamsBotTokenException('Undefined response property');
            }
            return $token_data;
        } catch (GuzzleException | JsonException $e) {
            throw new TeamsBotTokenException('Unable to get token for sending message', $e->getCode());
        }
    }

    /**
     * The method returns a token for calling API
     * If not set, it requests a token from botframework
     *
     * @return string
     * @throws TeamsBotTokenException
     */
    public function get(): string
    {
        if (is_null($this->token)) {
            $time = time();
            $token_data = $this->request(HttpClient::getClient());
            $this->token = $token_data->token_type . ' ' . $token_data->access_token;
            $this->expires_in = $time + (int)$token_data->expires_in;
        }
        return $this->token;
    }

    /**
     * Set token
     *
     * @param string $token
     */
    public function set(string $token): void
    {
        $this->token = $token;
    }

    /**
     * If the token was received from botframework,
     * contains the timestamp of the expiration date of the token
     */
    public function getExpiresIn(): ?int
    {
        return $this->expires_in;
    }
}
