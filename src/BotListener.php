<?php

namespace TeamsBot;

/**
 * BotListener for run callbacks on some types of requests
 *
 * @author Alexey Kargin <alexka@live.ru>
 * @package TeamsBot
 */
class BotListener extends Bot
{
    /**
     * Run callback function on every request
     *
     * @param callable $func Function for run
     * @return bool Always true
     */
    public function onAny(callable $func): bool
    {
        $this->runCallback($func);
        return true;
    }

    /**
     * Run callback function when user submits some text to bot
     *
     * @param string $text Text for compare
     * @param callable $func Function for run
     * @return bool True when callback run, false otherwise
     */
    public function onText(string $text, callable $func): bool
    {
        if ($this->context->getText() === $text) {
            $this->runCallback($func);
            return true;
        }
        return false;
    }

    /**
     * Run callback function when user submit form
     * https://docs.microsoft.com/ru-ru/microsoftteams/platform/task-modules-and-cards/cards/cards-actions
     *
     * @param callable $func Function for run
     * @return bool True when callback run, false otherwise
     */
    public function onSubmitForm(callable $func): bool
    {
        if (!empty($this->context->getFormData())) {
            $this->runCallback($func);
            return true;
        }
        return false;
    }

    /**
     * Run callback function when user start conversation with bot
     *
     * @param callable $func Function for run
     * @return bool True when callback run, false otherwise
     */
    public function onStartPersonalChat(callable $func): bool
    {
        if ($this->context->getMessageType() === 'conversationUpdate'
            && $this->context->getConversationType() === 'personal'
            && !empty($this->context->getData()['membersAdded'])
            && is_array($this->context->getData()['membersAdded'])
        ) {
            $this->runCallback($func);
            return true;
        }
        return false;
    }
}
