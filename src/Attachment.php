<?php

namespace TeamsBot;

use TeamsBot\Interfaces\AttachmentInterface;

/**
 * Basic Attachment
 *
 *
 * @author Alexey Kargin <alexka@live.ru>
 * @package TeamsBot
 */
class Attachment implements AttachmentInterface
{
    /**
     * @var string
     */
    private string $content_type;
    /**
     * @var string
     */
    private string $content_url;
    /**
     * @var string
     */
    private string $name;

    public function __construct(string $name, string $content_type, string $content_url)
    {
        $this->content_type = $content_type;
        $this->name = $name;
        $this->content_url = $content_url;
    }

    /**
     * @return array
     */
    public function toAttachment(): array
    {
        return [
            'name' => $this->name,
            'contentType' => $this->content_type,
            'contentUrl' => $this->content_url
        ];
    }
}
