<?php

namespace TeamsBot;

use TeamsBot\Exception\TeamsBotMessageException;
use TeamsBot\Interfaces\AttachmentInterface;

/**
 * Activity object
 * https://docs.microsoft.com/en-us/azure/bot-service/rest-api/bot-framework-rest-connector-api-reference?view=azure-bot-service-4.0#activity-object
 *
 * @author Alexey Kargin <alexka@live.ru>
 * @package TeamsBot
 */
class Message
{
    public const CONST_TYPE_MESSAGE = 'message';
    public const CONST_TEXT_FORMAT_PLAIN = 'plain';
    public const LOCALE_RU_RU = 'ru-RU';

    /**
     * @var array Array of Attachment objects
     */
    private array $attachments = [];
    /**
     * @var array
     */
    public array $data;
    /**
     * @var Context
     */
    private Context $context;

    /**
     * Activity constructor.
     * The constructor creates from Context the underlying data array to send.
     * You can pass the Context from the incoming request or create a new one.
     *
     * @param Context $request
     */
    public function __construct(Context $request)
    {
        $this->context = $request;
        $this->data = [
            'type' => self::CONST_TYPE_MESSAGE,
            'text' => '',
            'textFormat' => self::CONST_TEXT_FORMAT_PLAIN,
            'locale' => $request->getLocale() ?? self::LOCALE_RU_RU,
            'replyToId' => $request->getId(),
            'from' => [
                'id' => $request->getRecipientId(),
                'name' => $request->getRecipientName()
            ],
            'recipient' => [
                'id' => $request->getFromId(),
                'name' => $request->getFromName()
            ],
            'conversation' => [
                'id' => $request->getConversationId()
            ]
        ];
    }

    /**
     * Set text field
     *
     * @param string $text
     */
    public function setText(string $text): void
    {
        $this->data['text'] = $text;
    }

    /**
     * Get Activity data array with Attachments
     *
     * @return array
     */
    public function getData(): array
    {
        $message_data = $this->data;
        if (count($this->attachments)) {
            $message_data['attachments'] = [];
            foreach ($this->attachments as $attachment) {
                $message_data['attachments'][] = $attachment->toAttachment();
            }
        }
        return $message_data;
    }

    /**
     * Add a new Attachment to Activity
     *
     * @param AttachmentInterface $attachment
     */
    public function addAttachment(AttachmentInterface $attachment): void
    {
        $this->attachments[] = $attachment;
    }

    /**
     * Get URL for POST Activity
     *
     * @return string
     */
    public function getPostUrl(): string
    {
        return
            rtrim($this->context->getServiceUrl(), '/') .
            '/v3/conversations/' .
            $this->context->getConversationId() .
            '/activities/' .
            urlencode($this->context->getId());
    }

    /**
     * Get URL for UPDATE Activity
     *
     * @param string|null $activity_id
     * @return string
     * @throws TeamsBotMessageException
     */
    public function getUpdateUrl(?string $activity_id = null): string
    {
        if (empty($activity_id)) {
            $activity_id = $this->context->getReplyToId();
        }
        if (empty($activity_id)) {
            throw new TeamsBotMessageException('Cannot get parent activity id');
        }
        return
            rtrim($this->context->getServiceUrl(), '/') .
            '/v3/conversations/' .
            $this->context->getConversationId() .
            '/activities/' .
            urlencode($activity_id);
    }
}
