<?php

namespace TeamsBot\Card;

use TeamsBot\Interfaces\AttachmentInterface;

/**
 * Adaptive card object
 * https://docs.microsoft.com/en-us/microsoftteams/platform/task-modules-and-cards/cards/cards-reference#adaptive-card
 *
 * @author Alexey Kargin <alexka@live.ru>
 * @package TeamsBot
 */
class AdaptiveCard extends Card implements AttachmentInterface
{
    public function __construct()
    {
        $this->content_type = 'application/vnd.microsoft.card.adaptive';
    }
}
