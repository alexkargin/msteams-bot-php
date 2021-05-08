# PHP Teams Bot [![Build Status](https://travis-ci.com/alexkargin/msteams-bot-php.svg?branch=main)](https://travis-ci.com/github/alexkargin/msteams-bot-php)
A simple php library for creating bots for Microsoft Teams

## Requirements

- `PHP 7.4`
- Package uses `Guzzle Client` for API requests 

## Getting started

### Installation

Install using composer:

```bash
composer require alexkargin/msteams-bot-php
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

## Important! Validating incoming requests
The package does not contain ways to check the validity of incoming requests. 
You can implement this check according to the [documentation](https://docs.microsoft.com/en-us/azure/bot-service/rest-api/bot-framework-rest-connector-authentication?view=azure-bot-service-4.0), or use a different check method. For example, add a secret value to the handler address and check it.

## Token caching
By default, each bot instance receives a new token for sending messages. To speed up sending messages, the token can be cached for N seconds. For example, using the package [Stash](https://github.com/tedious/Stash)

Installing the package
```bash
composer require tedivm/stash
```

And use it
```php
    $bot = new TeamsBot\BotListener('bot_id', 'password');
    // use filesystem driver
    $pool = new Stash\Pool(new Stash\Driver\FileSystem([]));
    $item = $pool->getItem('token');
    $token = $item->get();
    if($item->isMiss())
    {
        // get new token
        $token = $bot->token->get();
        // Cache expires $token['expires_in']
        $expiration = new DateTime('@'.$token['expires_in']);
        $item->expiresAfter($expiration);
        $item->set($token);
        $pool->save($item);
    }

    // set token
    $bot->token->set($token);
```
