<?php
use App\Conversations\WelcomeConversation;
use App\Http\Controllers\BotManController;
use App\Helpers;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use GuzzleHttp\Client;

$botman = resolve('botman');

$botman->hears('/start', function ($bot) {
    $bot->startConversation(new WelcomeConversation());
});

$botman->hears('menu', function ($bot) {

 $jayParsedAry = [
    "reply_markup"=>[
            "keyboard" => [
                    [
                        [
                        "text" => "Add User", 
                        "request_contact" => false, 
                        "request_location" => false 
                        ], 
                        [
                            "text" => "Disable User", 
                            "request_contact" => false, 
                            "request_location" => false 
                        ] 
                    ], 
                    [
                                [
                                    "text" => "Key", 
                                    "request_contact" => false, 
                                    "request_location" => false 
                                ], 
                                [
                                    "text" => "Invoice", 
                                    "request_contact" => false, 
                                    "request_location" => false 
                                    ] 
                            ], 
                    [
                                        [
                                            "text" => "Receipt", 
                                            "request_contact" => false, 
                                            "request_location" => false 
                                        ], 
                                        [
                                                "text" => "Internet", 
                                                "request_contact" => false, 
                                                "request_location" => false 
                                            ] 
                                    ], 
                    [
                                                [
                                                    "text" => "Make Payment", 
                                                    "request_contact" => false, 
                                                    "request_location" => false 
                                                ] 
                                                ] 
                ], 
            "one_time_keyboard" => true, 
            "resize_keyboard" => true, 
            "selective" => true
            ],
            "text"=>"Message",
            "parse_mode"=>"HTML"
];

    $url = "https://api.telegram.org/bot" . env('TELEGRAM_TOKEN')."/sendMessage?chat_id=".$bot->getUser()->getId();
    $client = new Client();
    $client->post($url, ['json' => $jayParsedAry]);
    $bot->reply($url);

    // $quiz = Question::create("What animal person are you?")
    // ->addButtons([
    //         Button::create("I like cats")->value('cat')->additionalParameters(["resize_keyboard" => true])
    //     ]
    // );


    // $bot->ask($quiz,function($answer){

    //  });
});

$botman->hears('Profile', function ($bot) {

    $bot->reply("profile selected");

});
