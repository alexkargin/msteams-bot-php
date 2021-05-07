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
     * @var array|null
     */
    private ?array $token = null;
    /**
     * @var string
     */
    private string $bot_id;
    /**
     * @var string
     */
    private string $bot_password;

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
     * @return array
     * @throws TeamsBotTokenException
     */
    private function request(Client $client): array
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
            $token_data = json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);
            if (
                empty($token_data['token_type']) ||
                empty($token_data['access_token']) ||
                empty($token_data['expires_in'])
            ) {
                throw new TeamsBotTokenException('Undefined response property');
            }
            return $token_data;
        } catch (GuzzleException | JsonException $e) {
            throw new TeamsBotTokenException('Unable to get token for sending message', $e->getCode());
        }
    }

    /**
     * The method returns a token for calling API
     * If not set or expired, it requests a token from botframework
     *
     * @return array
     * @throws TeamsBotTokenException
     */
    public function get(): array
    {
        if (
            is_null($this->token) ||
            (!empty($this->token['expires_in']) && (int)$this->token['expires_in'] < time())
        ) {
            $time = time();
            $token_data = $this->request(HttpClient::getClient());
            $this->token = [
                'token' => $token_data['token_type'] . ' ' . $token_data['access_token'],
                'expires_in' => $time + (int)$token_data['expires_in']
            ];
        }
        return $this->token;
    }

    /**
     * Set token
     *
     * @param array $token
     * @throws TeamsBotTokenException
     */
    public function set(array $token): void
    {
        if (empty($token['token']) || empty($token['expires_in'])) {
            throw new TeamsBotTokenException('Undefined response property');
        }
        $this->token = [
            'token' => $token['token'],
            'expires_in' => (int)$token['expires_in']
        ];
    }
}
