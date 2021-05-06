<?php

namespace TeamsBot;

use PHPUnit\Framework\TestCase;
use TeamsBot\Exception\TeamsBotException;
use TeamsBot\Exception\TeamsBotTokenException;

class BotListenerTest extends TestCase
{
    /**
     * @var BotListener
     */
    private static BotListener $bot;

    /**
     * @throws TeamsBotException
     * @throws TeamsBotTokenException
     */
    protected function setUp(): void
    {
        $payload = file_get_contents(__DIR__ . '/payload.json');
        self::$bot = new BotListener('test','test', new Context($payload));
    }

    /**
     *
     */
    public function testOnStartPersonalChat(): void
    {
        self::assertFalse(self::$bot->onStartPersonalChat(static function ($bot) {}));
    }

    /**
     *
     */
    public function testOnText(): void
    {
        self::assertTrue(self::$bot->onText('tst', static function ($bot) {}));
    }

    /**
     *
     */
    public function testOnTextNotEqual(): void
    {
        self::assertFalse(self::$bot->onText('tst_false', static function ($bot) {}));
    }

    /**
     *
     */
    public function testOnSubmitForm(): void
    {
        self::assertFalse(self::$bot->onSubmitForm(static function ($bot) {}));
    }

    /**
     *
     */
    public function testOnAny(): void
    {
        self::assertTrue(self::$bot->onAny(static function ($bot) {}));
    }
}
