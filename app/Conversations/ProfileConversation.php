<?php

namespace App\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;
use GuzzleHttp\Client;

class ProfileConversation extends Conversation
{
    /**
     * Start the conversation.
     *
     * @return mixed
     */

     protected $help;
     protected $client;
     protected $url;
     protected $info;

     public function __construct(){
        $this->help = new \Helpers();
        $this->client = new Client();
        $this->url = env('API');
        $this->info = $this->bot->getUser();
     }
    public function run()
    {
        
        if($this->help->IsLoggedIn($this->info->getId())){

            $message = "You were logged out due to inactivity,press the login button to continue";
            $this->help->Login($this->info->getId(),$message);
            return;
            
        }else{
            $this->ViewProfile();
        }
    }

    public function ViewProfile()  {

        $params = ["email" => \Helpers::$UserInfo->email, "apikey" => \Helpers::$UserInfo->apikey];
        $response = $this->client->post($this->url . 'profile', ['json' => $params]);

        $data = json_decode($response->getBody()->getContents());

        if(array_key_exists('errorcode',$data)){

            $this->help->MainMenu($this->info->getId(),$data["message"]);

        }else{

            /// I stopped at this place because I found not neccessary to put all the work of the system into the bot but only the
               // main and repetitive tasks. How ever if need be I will:
            //show user profile with edit pic,change password and other user infomation

        }

        
    }
}
