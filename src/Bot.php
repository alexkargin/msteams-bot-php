<?php

namespace TeamsBot;

use TeamsBot\Exception\TeamsBotException;
use TeamsBot\Exception\TeamsBotTokenException;
use TeamsBot\Exception\TeamsBotMessageException;

/**
 * Main bot class.
 *
 * @author Alexey Kargin <alexka@live.ru>
 * @package TeamsBot
 */
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
     * By default, the context is automatically created from the php://input
     *
     * @param string $bot_id
     * @param string $bot_password
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
     * Method to run the callback function
     *
     * @param callable $func
     */
    protected function runCallback(callable $func): void
    {
        $func($this);
    }


    /**
     * Method for creating an Activity object from the current bot Context
     *
     * @return Message
     */
    public function createMessage(): Message
    {
        return new Message($this->context);
    }


    /**
     * Send a new Activity
     *
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
     * Updating an existing Activity
     * If activity_id is not specified, the Activity id for updating is taken from the Activity context
     * This is only possible in card actions (parameter replyToId)
     * https://docs.microsoft.com/en-us/microsoftteams/platform/task-modules-and-cards/cards/cards-actions
     *
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
     * Simple method for text reply
     *
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
