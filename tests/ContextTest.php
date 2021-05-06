<?php

namespace TeamsBot;

use JsonException;
use PHPUnit\Framework\TestCase;
use TeamsBot\Exception\TeamsBotException;

class ContextTest extends TestCase
{
    /**
     * @var false|string
     */
    private static $payload;
    /**
     * @var false|string
     */
    private static $payload_without_fields;
    /**
     * @var Context
     */
    private static Context $request;
    /**
     * @var mixed
     */
    private static $payload_array;

    /**
     * @throws TeamsBotException
     * @throws JsonException
     */
    protected function setUp(): void
    {
        self::$payload = file_get_contents(__DIR__ . '/payload.json');
        self::$payload_array = json_decode(self::$payload, true, 512, JSON_THROW_ON_ERROR);
        self::$payload_without_fields = json_encode(['dummy' => 'variable'], JSON_THROW_ON_ERROR);
        self::$request = new Context(self::$payload);
    }

    /**
     *
     */
    public function test__constructWhenPayloadIsEmpty(): void
    {
        $this->expectException(TeamsBotException::class);
        $this->expectExceptionMessage('Cannot initialize request');
        new Context('');
    }

    /**
     * @throws TeamsBotException
     */
    public function test__constructWhenJsonIsIncorrect(): void
    {
        $this->expectException(TeamsBotException::class);
        $this->expectExceptionMessage('Cannot initialize request');
        new Context(substr(self::$payload, 0, 10));
    }

    /**
     * @throws TeamsBotException
     */
    public function test__constructWhenFieldsNotExists(): void
    {
        $this->expectException(TeamsBotException::class);
        $this->expectExceptionMessage('Cannot initialize fields');
        new Context(self::$payload_without_fields);
    }

    /**
     * @throws TeamsBotException
     */
    public function test__construct(): void
    {
        $request = new Context(self::$payload);
        self::assertInstanceOf(Context::class, $request);
    }


    /**
     */
    public function testGetData(): void
    {
        self::assertEquals(self::$payload_array, self::$request->getData());
    }

    /**
     */
    public function testGetServiceUrl(): void
    {
        self::assertSame(self::$payload_array['serviceUrl'], self::$request->getServiceUrl());
    }

    /**
     */
    public function testGetConversationId(): void
    {
        self::assertSame(self::$payload_array['conversation']['id'], self::$request->getConversationId());
    }

    /**
     */
    public function testGetFromId(): void
    {
        self::assertSame(self::$payload_array['from']['id'], self::$request->getFromId());
    }

/*


    public function testGetId()
    {

    }

    public function testGetRecipientId()
    {

    }

    public function testGetFromName()
    {

    }

    public function testGetText()
    {

    }

    public function testGetRecipientName()
    {

    }*/
}
