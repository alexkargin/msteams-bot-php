<?php


namespace TeamsBot\Card;

use TeamsBot\Interfaces\AttachmentInterface;

class HeroCard extends Card implements AttachmentInterface
{
    public function __construct()
    {
        $this->content_type = 'application/vnd.microsoft.card.hero';
    }
}
