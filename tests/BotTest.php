<?php

namespace TeamsBot\Test;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use JsonException;
use PHPUnit\Framework\TestCase;
use TeamsBot\Bot;
use TeamsBot\Context;
use TeamsBot\Exception\TeamsBotException;
use TeamsBot\Exception\TeamsBotMessageException;
use TeamsBot\Exception\TeamsBotTokenException;
use TeamsBot\HttpClient;

class BotTest extends TestCase
{
    /**
     * @var false|string
     */
    private static $payload;
    /**
     * @var Bot
     */
    private static Bot $bot;
    /**
     * @var MockHandler
     */
    private static MockHandler $mock;
    /**
     * @var HandlerStack
     */
    private static HandlerStack $handlerStack;
    /**
     * @var Client
     */
    private static Client $client;

    /**
     * @throws TeamsBotException
     * @throws TeamsBotTokenException
     */
    protected function setUp(): void
    {
        self::$payload = file_get_contents(__DIR__ . '/payload.json');
        self::$bot = new Bot('test','test', new Context(self::$payload));

        self::$mock = new MockHandler([]);
        self::$handlerStack = HandlerStack::create(self::$mock);
        self::$client = new Client(['handler' => self::$handlerStack]);
        HttpClient::setClient(self::$client);
    }

    public function test__constructWhenBotIdIsEmpty(): void
    {
        $this->expectException(TeamsBotException::class);
        $this->expectExceptionMessage('Bot ID not defined!');
        new Bot('','');
    }

    public function test__constructWhenBotPasswordIsEmpty(): void
    {
        $this->expectException(TeamsBotException::class);
        $this->expectExceptionMessage('Bot password not defined!');
        new Bot('test','');
    }

    /**
     * @throws TeamsBotException
     * @throws TeamsBotTokenException
     */
    public function test__construct(): void
    {
        $bot = new Bot('test','test', new Context(self::$payload));
        self::assertInstanceOf(Bot::class, $bot);
    }


    /**
     * @throws TeamsBotException
     * @throws TeamsBotTokenException|JsonException
     */
    public function testReply(): void
    {
        self::$mock->reset();
        self::$mock->append(new Response(200, [], json_encode(['id' => '1234567890'], JSON_THROW_ON_ERROR)));
        self::$bot->token->set('testToken');

        self::assertSame(['id' => '1234567890'], self::$bot->reply('test'));
    }


    /**
     * @throws TeamsBotException
     * @throws TeamsBotTokenException|JsonException
     */
    public function testSendMessage(): void
    {
        self::$mock->reset();
        self::$mock->append(new Response(200, [], json_encode(['id' => '1234567890'], JSON_THROW_ON_ERROR)));
        self::$bot->token->set('testToken');

        self::assertSame(['id' => '1234567890'], self::$bot->postMessage(self::$bot->createMessage()));
    }


    /**
     * @throws TeamsBotException
     * @throws TeamsBotTokenException|JsonException
     */
    public function testSendMessageWhenJsonIsIncorrect(): void
    {
        self::$mock->reset();
        self::$mock->append(new Response(200, [], substr(json_encode(['id' => '1234567890'], JSON_THROW_ON_ERROR), 0, 5)));
        self::$bot->token->set('testToken');

        $this->expectException(TeamsBotException::class);
        $this->expectExceptionMessage('Error while sending message');
        self::$bot->postMessage(self::$bot->createMessage());
    }


    /**
     * @throws TeamsBotException
     * @throws TeamsBotTokenException|JsonException
     */
    public function testSendMessageWhenUnAuthorized(): void
    {
        self::$mock->reset();
        self::$mock->append(new Response(401, [], json_encode(['id' => '1234567890'], JSON_THROW_ON_ERROR), 0, 5));
        self::$bot->token->set('testToken');

        $this->expectException(TeamsBotException::class);
        $this->expectExceptionMessage('Error while sending message');
        self::$bot->postMessage(self::$bot->createMessage());
    }

    /**
     * @throws TeamsBotException
     * @throws TeamsBotTokenException
     * @throws JsonException
     * @throws TeamsBotMessageException
     */
    public function testUpdateMessage(): void
    {
        self::$mock->reset();
        self::$mock->append(new Response(200, [], json_encode(['id' => '1234567890'], JSON_THROW_ON_ERROR)));
        self::$bot->token->set('testToken');

        self::assertSame(['id' => '1234567890'], self::$bot->updateMessage(self::$bot->createMessage(), 'test_activity_id'));
    }


    /**
     * @throws TeamsBotException
     * @throws TeamsBotTokenException|JsonException|TeamsBotMessageException
     */
    public function testUpdateMessageWhenJsonIsIncorrect(): void
    {
        self::$mock->reset();
        self::$mock->append(new Response(200, [], substr(json_encode(['id' => '1234567890'], JSON_THROW_ON_ERROR), 0, 5)));
        self::$bot->token->set('testToken');

        $this->expectException(TeamsBotException::class);
        $this->expectExceptionMessage('Error while sending message');
        self::$bot->updateMessage(self::$bot->createMessage(), 'test_activity_id');
    }


    /**
     * @throws TeamsBotException
     * @throws TeamsBotTokenException|JsonException|TeamsBotMessageException
     */
    public function testUpdateMessageWhenUnAuthorized(): void
    {
        self::$mock->reset();
        self::$mock->append(new Response(401, [], json_encode(['id' => '1234567890'], JSON_THROW_ON_ERROR), 0, 5));
        self::$bot->token->set('testToken');

        $this->expectException(TeamsBotException::class);
        $this->expectExceptionMessage('Error while sending message');
        self::$bot->updateMessage(self::$bot->createMessage(), 'test_activity_id');
    }

}
