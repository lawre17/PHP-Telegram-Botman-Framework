<?php

namespace App\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use GuzzleHttp\Client;

class SearchConversation extends Conversation
{
    /**
     * Start the conversation.
     *
     * @return mixed
     */

    protected $help;
     protected $client;
     protected $url;
     protected $from;
     protected $to;
     protected $info;
     protected static $SearchInfo;

    public function __construct(){

        $this->help = new \Helpers();
        $this->client = new Client();
        $this->url = env('API');
        
        $this->from = 0;
        $this->to = 5;

    }
    public function run()
    {
        $this->info = $this->bot->getUser();
        if($this->help->IsLoggedIn($this->info->getId())){
            $this->Search();            
        }else{
            $message = "You were logged out due to inactivity,press the login button to continue";
            $this->help->Login($this->info->getId(),$message);
            return;
        }
    }

    public function Search(){

        $this->ask("Please eneter the name or admission number of the student", function (Answer $answer) {

            $value = $answer->getText();
            $params = ["apikey" => \Helpers::$Key, "value" => $value];
            $response = $this->client->post($this->url.'query',['json'=>$params]);
            $data = json_decode($response->getBody()->getContents());
            if(array_key_exists('errorcode',$data)){
                $this->help->MainMenu($this->info->getId(),$data["message"]);
            }else{
                SearchConversation::$SearchInfo = $data;
                for ($i=0; $i <5 ; $i++) { 
                    $infom = $data[$i];
                    $res = $infom->admno."-".$infom->name."\n".$infom->form." ".$infom->stream." ".$infom->year;
                    $results[$res] = $infom->admno;
                    $this->from++;
                }

                $message = "Search results has {count($data)} results. ";
                if(count($data)>5){
                    $message .="\n Use the <b>Next</b> and <b>Back</b> buttons to navigate through the results";
                }

                $button =[["Next",1]];

                $this->help->CustomButton($this->info->getId(), $message, $button);
                $this->ask("Click next to move to the next page", function (Answer $answer) {

                    if($answer->getText() =="Next"){
                        $this->Next();
                    }

                });

            }

        });

    }

    public function Next(){

        for ($i=$this->from; $i <$this->from+4 ; $i++) { 
            $infom = SearchConversation::$SearchInfo[$i];
            $res = $infom->admno."-".$infom->name."\n".$infom->form." ".$infom->stream." ".$infom->year;
            $results[$res] = $infom->admno;
            $this->from++;
        }

        if(count(SearchConversation::$SearchInfo)<=$this->from){

            $button =[["Back",0],["Next",0]];
            $this->help->CustomButton($this->info->getId(),"", $button);
            $this->ask("Click next to move to the next page", function (Answer $answer) {
    
                if($answer->getText() =="Next"){
                    $this->Next();
                }
    
                if($answer->getText() =="Back"){
                    $this->Back();
                }
    
            });
        }


    }

    public function Back(){

    }
}
