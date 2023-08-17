<?php
use App\Conversations\ProfileConversation;
use App\Conversations\SearchConversation;
use App\Conversations\WelcomeConversation;
use App\Http\Controllers\BotManController;
use App\Helpers;
use BotMan\BotMan\Messages\Attachments\Image;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use GuzzleHttp\Client;

$botman = resolve('botman');

$botman->hears('/start', function ($bot) {
    $bot->startConversation(new WelcomeConversation());
});

$botman->hears('search student', function ($bot) {
    $bot->startConversation(new SearchConversation());
});

// $botman->hears('profile', function ($bot) {
//     $bot->startConversation(new ProfileConversation());
// });

$botman->hears('test', function ($bot) {

    $message = OutgoingMessage::create("TestImage")->withAttachment(

        new Image('http://73f3-102-219-210-77.ngrok-free.app/passports/64dde394332e9.jpg')
    );
    $bot->reply($message );
});



