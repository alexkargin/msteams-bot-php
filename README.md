# PHP Teams Bot

## Requirements

- `PHP 7.4`
- Package uses `Guzzle Client` for API requests 

## Getting started

### Installation

Install using composer:

```bash
composer require "alexkargin/msteams-bot-php"
```
### Create a bot

Create a new bot in [Teams App Studio](https://docs.microsoft.com/en-us/microsoftteams/platform/concepts/build-and-test/app-studio-overview).

You should get Bot ID and Password at this step.

### Basic Usage

```php
include __DIR__ . '/vendor/autoload.php';

use TeamsBot\Bot;
use TeamsBot\Exception\TeamsBotException;
use TeamsBot\Exception\TeamsBotTokenException;

try {
    $bot = new TeamsBot\BotListener('bot_id', 'password');

    // Handled on any request
    $bot->onAny(static function (Bot $bot) {
        // Sends a simple text message
        if(!empty($bot->context->getText())) {
            $bot->reply('You send ' . $bot->context->getText());
        }                     
    });

    // Handled when user add bot
    $bot->onStartPersonalChat(static function (Bot $bot) {
        // Sends a simple text message
        $bot->reply('Hi, ' . $bot->context->getFromName());                    
    });

    // Handled when user sends 'test' to bot
    $bot->onText('test', function (Bot $bot) {
        // create Activity
        $message = $bot->createMessage();
        // add Hero Card
        $att = new TeamsBot\Card\HeroCard();
        $att->setContentFromJson('
{
"buttons": [
    {
        "type": "messageBack",
        "text": "Send request to bot",
        "value": "{\"property\": \"propertyValue\" }"
   }
]
}
        ');
        $message->addAttachment($att);
        // send new message
        $bot->postMessage($message);
    });

    // Handled when user send form to bot
    // for example, Hero Card from previous handler
    $bot->onSubmitForm(function (Bot $bot) {
        $message = $bot->createMessage();
        $message->setText('Received data: ' . json_encode($bot->context->getFormData(), JSON_THROW_ON_ERROR));
        // update Activity with form
        $bot->updateMessage($message);
    });

} catch (TeamsBotException $e) {
} catch (TeamsBotTokenException $e) {

}
```