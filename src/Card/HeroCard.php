<?php

namespace TeamsBot\Card;

use TeamsBot\Interfaces\AttachmentInterface;

/**
 * Hero card object
 * https://docs.microsoft.com/en-us/microsoftteams/platform/task-modules-and-cards/cards/cards-reference#hero-card
 *
 * @author Alexey Kargin <alexka@live.ru>
 * @package TeamsBot
 */
class HeroCard extends Card implements AttachmentInterface
{
    public function __construct()
    {
        $this->content_type = 'application/vnd.microsoft.card.hero';
    }
}
