<?php

namespace TeamsBot;

use TeamsBot\Exception\TeamsBotException;
use TeamsBot\Exception\TeamsBotTokenException;
use TeamsBot\Exception\TeamsBotMessageException;

class Bot
{
    /**
     * Bot ID (GUID)
     *
     * @var string
     */
    protected string $bot_id;
    /**
     * Bot password
     *
     * @var string
     */
    protected string $bot_password;
    /**
     * @var Context
     */
    public Context $context;
    /**
     * @var Token
     */
    public Token $token;


    /**
     * Bot constructor.
     *
     * @param string $bot_id
     * @param string $bot_password
     *
     * @param Context|null $context
     * @throws TeamsBotException
     * @throws TeamsBotTokenException
     */
    public function __construct(string $bot_id, string $bot_password, ?Context $context = null)
    {
        if (empty($bot_id)) {
            throw new TeamsBotException('Bot ID not defined!');
        }
        if (empty($bot_password)) {
            throw new TeamsBotException('Bot password not defined!');
        }
        $this->bot_id = $bot_id;
        $this->bot_password = $bot_password;

        $this->context = $context ?? new Context(file_get_contents('php://input'));
        $this->token = new Token($bot_id, $bot_password);
    }


    /**
     * @param callable $func
     */
    protected function runCallback(callable $func): void
    {
        $func($this);
    }


    /**
     * @return Message
     */
    public function createMessage(): Message
    {
        return new Message($this->context);
    }


    /**
     * @param Message $message
     * @return array
     * @throws TeamsBotException
     * @throws TeamsBotTokenException
     */
    public function postMessage(Message $message): array
    {
        $data = [
            'json' => $message->getData(),
            'headers' => [
                'Authorization' => $this->token->get()
            ]
        ];
        return HttpClient::process('POST', $message->getPostUrl(), $data);
    }


    /**
     * @param Message $message
     * @param string|null $activity_id
     * @return array
     * @throws TeamsBotException
     * @throws TeamsBotMessageException
     * @throws TeamsBotTokenException
     */
    public function updateMessage(Message $message, ?string $activity_id = null): array
    {
        $data = [
            'json' => $message->getData(),
            'headers' => [
                'Authorization' => $this->token->get()
            ]
        ];
        return HttpClient::process('PUT', $message->getUpdateUrl($activity_id), $data);
    }


    /**
     * @param string $text
     * @return array
     * @throws TeamsBotException
     * @throws TeamsBotTokenException
     */
    public function reply(string $text): array
    {
        $message = new Message($this->context);
        $message->setText($text);
        return $this->postMessage($message);
    }
}
