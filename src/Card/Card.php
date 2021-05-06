<?php

namespace TeamsBot\Card;

use JsonException;
use TeamsBot\Exception\TeamsBotAttachmentException;

abstract class Card
{
    /**
     * @var string
     */
    protected string $content_type;
    /**
     * @var array
     */
    public array $content = [];

    public function toAttachment(): array
    {
        return [
            'contentType' => $this->content_type,
            'content' => $this->content
        ];
    }

    /**
     * @param string $content
     * @throws TeamsBotAttachmentException
     */
    public function setContentFromJson(string $content): void
    {
        try {
            $this->content = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new TeamsBotAttachmentException('Error while decode Attachment content');
        }
    }
}
