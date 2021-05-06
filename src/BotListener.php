<?php

namespace TeamsBot;

class BotListener extends Bot
{
    /**
     * @param callable $func
     * @return bool
     */
    public function onAny(callable $func): bool
    {
        $this->runCallback($func);
        return true;
    }

    /**
     * @param string $text
     * @param callable $func
     * @return bool
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
     * @param callable $func
     * @return bool
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
     * @param callable $func
     * @return bool
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
