<?php

namespace TeamsBot;

use JsonException;
use TeamsBot\Exception\TeamsBotException;

/**
 * Context is the request data from the application.
 * Used to generate a response from a bot.
 *
 * @author Alexey Kargin <alexka@live.ru>
 * @package TeamsBot
 */
class Context
{
    /**
     * @var array Context data
     */
    private array $data = [];
    /**
     * @var array The minimum set of required fields to create a context
     */
    private array $requirement_fields = [
        'id' => '',
        'from' => ['id' => '', 'name' => ''],
        'recipient' => ['id' => '', 'name' => ''],
        'conversation' => ['id' => ''],
        'serviceUrl' => ''
    ];


    /**
     * Context object constructor.
     *
     * @param string $payload Context data in JSON
     * @throws TeamsBotException
     */
    public function __construct(string $payload)
    {
        try {
            $data = json_decode($payload, true, 512, JSON_THROW_ON_ERROR);
            $check_errors = $this->checkFields($this->requirement_fields, $data);
            if (count($check_errors)) {
                throw new TeamsBotException('Cannot initialize fields: ' . implode(', ', $check_errors));
            }
            $this->data = $data;
        } catch (JsonException $e) {
            throw new TeamsBotException('Cannot initialize request');
        }
    }


    /**
     * Method for checking required fields
     *
     * @param array $expected
     * @param array $actual
     * @param string $history
     * @return array
     */
    private function checkFields(array $expected, array $actual, string $history = ''): array
    {
        $error_keys = [];
        foreach ($expected as $k => $v) {
            if (!isset($actual[$k])) {
                $error_keys[] = $history . $k;
            }
//            elseif(is_array($v)) {
//                $this->checkFields($v, (array)$actual[$k], $history . $k . '->');
//            }
        }
        return $error_keys;
    }

    /**
     * Return array of Context data
     *
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Get text sent by user
     *
     * @return string|null
     */
    public function getText(): ?string
    {
        return $this->data['text'] ?? null;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->data['id'];
    }

    /**
     * @return string
     */
    public function getRecipientId(): string
    {
        return $this->data['recipient']['id'];
    }

    /**
     * @return string
     */
    public function getRecipientName(): string
    {
        return $this->data['recipient']['name'];
    }

    /**
     * @return string
     */
    public function getFromId(): string
    {
        return $this->data['from']['id'];
    }

    /**
     * @return string
     */
    public function getFromName(): string
    {
        return $this->data['from']['name'];
    }

    /**
     * @return string
     */
    public function getConversationId(): string
    {
        return $this->data['conversation']['id'];
    }

    /**
     * @return string
     */
    public function getConversationType(): ?string
    {
        return $this->data['conversation']['conversationType'] ?? null;
    }

    /**
     * @return string
     */
    public function getServiceUrl(): string
    {
        return $this->data['serviceUrl'];
    }

    /**
     * @return array|null
     */
    public function getFormData(): ?array
    {
        if (!empty($this->data['value']) && is_array($this->data['value']) && count($this->data['value'])) {
            return $this->data['value'];
        }
        return null;
    }

    /**
     * @return string|null
     */
    public function getReplyToId(): ?string
    {
        return $this->data['replyToId'] ?? null;
    }

    /**
     * @return string|null
     */
    public function getMessageType(): ?string
    {
        return $this->data['type'] ?? null;
    }

    /**
     * @return string|null
     */
    public function getLocale(): ?string
    {
        return $this->data['entities'][0]['locale'] ?? null;
    }
}
