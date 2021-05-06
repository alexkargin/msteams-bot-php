<?php

namespace TeamsBot;

use JsonException;
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
     * @var mixed
     */
    private static array $payload_array;

    /**
     * @throws TeamsBotException
     * @throws TeamsBotTokenException|JsonException
     */
    protected function setUp(): void
    {
        $payload = file_get_contents(__DIR__ . '/payload.json');
        self::$payload_array = json_decode($payload, true, 512, JSON_THROW_ON_ERROR);
        self::$bot = new BotListener('test','test', new Context($payload));
    }

    /**
     *
     * @throws JsonException
     * @throws TeamsBotException
     * @throws TeamsBotTokenException
     */
    public function testOnStartPersonalChat(): void
    {
        self::$payload_array['type'] = 'conversationUpdate';
        self::$payload_array['conversation']['conversationType'] = 'personal';
        self::$payload_array['membersAdded'] = ['a' => 'b'];
        $bot = new BotListener('test', 'test', new Context(json_encode(self::$payload_array, JSON_THROW_ON_ERROR)));
        self::assertTrue($bot->onStartPersonalChat(static function ($bot) {}));
    }

    /**
     *
     */
    public function testOnStartPersonalChatWhenNotEqual(): void
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
    public function testOnTextWhenNotEqual(): void
    {
        self::assertFalse(self::$bot->onText('tst_false', static function ($bot) {}));
    }

    /**
     *
     * @throws JsonException
     * @throws TeamsBotException
     * @throws TeamsBotTokenException
     */
    public function testOnSubmitForm(): void
    {
        self::$payload_array['value'] = ['a' => 'b'];
        $bot = new BotListener('test', 'test', new Context(json_encode(self::$payload_array, JSON_THROW_ON_ERROR)));
        self::assertTrue($bot->onSubmitForm(static function ($bot) {}));
    }

    /**
     *
     */
    public function testOnSubmitFormNotEqual(): void
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
