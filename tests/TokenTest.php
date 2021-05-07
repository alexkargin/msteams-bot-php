<?php

namespace TeamsBot;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use JsonException;
use PHPUnit\Framework\TestCase;
use TeamsBot\Exception\TeamsBotTokenException;

class TokenTest extends TestCase
{
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
     */
    protected function setUp(): void
    {
        self::$mock = new MockHandler([]);
        self::$handlerStack = HandlerStack::create(self::$mock);
        self::$client = new Client(['handler' => self::$handlerStack]);
        HttpClient::setClient(self::$client);
    }
    /**
     *
     */
    public function test__constructWhenBotIdIsEmpty(): void
    {
        $this->expectException(TeamsBotTokenException::class);
        $this->expectExceptionMessage('Bot ID not defined!');
        new Token('','');
    }

    /**
     *
     */
    public function test__constructWhenBotPasswordIsEmpty(): void
    {
        $this->expectException(TeamsBotTokenException::class);
        $this->expectExceptionMessage('Bot password not defined!');
        new Token('test','');
    }

    /**
     *
     */
    public function test__construct(): void
    {
        $token = new Token('test','test');
        self::assertInstanceOf(Token::class, $token);
    }

    /**
     * @throws TeamsBotTokenException
     */
    public function testSet(): void
    {
        $token = new Token('test','test');
        $token->set(['token' => 'test', 'expires_in' => time() + 1000]);
        self::assertEquals('test', $token->get()['token']);
    }

    /**
     * @throws TeamsBotTokenException
     */
    public function testGetWhenTokenIsSet(): void
    {
        $token = new Token('test','test');
        $token->set(['token' => 'test', 'expires_in' => time() + 1000]);
        self::assertEquals('test', $token->get()['token']);
    }


    /**
     * @throws JsonException
     * @throws TeamsBotTokenException
     */
    public function testGetWhenTokenIsNotSet(): void
    {
        self::$mock->reset();
        self::$mock->append(new Response(200, [], json_encode(['token_type' => 'Bearer', 'access_token' => 'test token', 'expires_in' => 1234567890], JSON_THROW_ON_ERROR)));

        $token = new Token('test','test');
        self::assertEquals('Bearer test token', $token->get()['token']); // use mock
    }

    /**
     * @throws TeamsBotTokenException
     */
    public function testGetWhenTokenIsNotSetEmptyResponse(): void
    {
        self::$mock->reset();
        self::$mock->append(new Response(200, [], ''));

        $this->expectException(TeamsBotTokenException::class);
        $this->expectExceptionMessage('Unable to get token for sending message');
        $token = new Token('test','test');
        $token->get();
    }

    /**
     * @throws TeamsBotTokenException|JsonException
     */
    public function testGetWhenTokenIsNotSetResponsePropertyNotExists(): void
    {
        self::$mock->reset();
        self::$mock->append(new Response(200, [], json_encode(['token_type' => 'Bearer'], JSON_THROW_ON_ERROR)));

        $this->expectException(TeamsBotTokenException::class);
        $this->expectExceptionMessage('Undefined response property');
        $token = new Token('test','test');
        $token->get();
    }

    /**
     * @throws TeamsBotTokenException
     */
    public function testGetWhenTokenIsNotSetHttpError(): void
    {
        self::$mock->reset();
        self::$mock->append(new Response(401, [], ''));

        $this->expectException(TeamsBotTokenException::class);
        $this->expectExceptionMessage('Unable to get token for sending message');
        $token = new Token('test','test');
        $token->get();
    }

}
