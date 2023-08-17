<?php

namespace App\Conversations;

use App\Botusers;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use GuzzleHttp\Client;

//require('app/Helpers.php');

class WelcomeConversation extends Conversation
{
    /**
     * Start the conversation.
     *
     * @return mixed
     */
    protected $url;
    protected $help;

    public function __construct(){
        $this->help = new \Helpers;
    }

     public function Welcome(){

        $this->url = env('API');
        $info = $this->bot->getUser();
        $name = $info->getFirstName();
        $message = 'Hello <b>'.$name.'!</b> \n Welcome to B School Management system bot.';
        $this->say($message,['parse_mode'=>'HTML']);
        $this->GetCode();  
    }

    public function GetCode() {

        $this->ask("Please enter you *school code* to  continue", function (Answer $answer) {

            $params = ['code' => $answer->getText()];
            $client = new Client();
            $response = $client->post($this->url.'authbot',['json'=>$params]);
            $data = json_decode($response->getBody()->getContents(),true);

            if(array_key_exists("error",$data)){
                $this->say($data['message']);
                $this->repeat();
            }else{
                $this->say("*" . strtoupper($data['name']) . "*", ['parse_mode' => 'Markdown']);
                
                $chatID = $this->bot->getUser()->getId();
                $buser = Botusers::where("chatid",$chatID)->first();
                if(!empty($buser)){
                    $buser->findOrFail($buser->id);
                    $buser->chatid = $chatID;
                    $buser->code = $data['code'];
                    $buser->save();
                    $this->Login();

                }else{
                    $buser = new Botusers();
                    $buser->chatid = $chatID;
                    $buser->code = $data['code'];
                    $buser->save();
                    $this->Login();
                }
                
            }


        } ,['parse_mode'=>'Markdown']);
    }

    public function Login(){
        $this->ask("Please enter your email address", function (Answer $answer) {
            
            if(!filter_var($answer->getText(),FILTER_VALIDATE_EMAIL)){
                $this->say("You have entered an invalid email address!");
                $this->repeat();
            }else{
                $mail = $answer->getText();
                $chatID = $this->bot->getUser()->getId();
                $buser = Botusers::where("chatid", $chatID)->first();
                $buser->findOrFail($buser->id);
                $buser->email = $mail;
                $buser->save();
                $this->AskPwd();
                
            }
        });
    }

    public function AskPwd(){
        $this->ask("Please eneter your password", function (Answer $answer) {

            $pwd = $answer->getText();
            $chatID = $this->bot->getUser()->getId();
            $buser = Botusers::where("chatid", $chatID)->first();
            $buser->findOrFail($buser->id);
            $buser->pwd = $pwd;
            $buser->updated_at = date('Y-m-d H:i:s');
            $buser->save();
            //retrieve save user info for authentication
            $data  = Botusers::where("chatid",$chatID)->first();

            if(!empty($data)){

                $params = ['code' => $data->code,'email'=>$data->email,'pwd'=>$data->pwd];
                $cli =  new Client();
                $response = $cli->post($this->url.'authuser',['json'=>$params]);
                $data = json_decode($response->getBody()->getContents(),true);
                $this->say($response->getBody()->getContents());
                if(array_key_exists("errorcode",$data)){
                    if($data["errorcode"] =="1"){
                        $this->say($data["message"], ['parse_mode' => 'Markdown']);
                        $buser = Botusers::where("chatid", $chatID)->first();
                        $buser->find($buser->id);
                        $buser->delete();
                        $this->GetCode();
                    }else if($data["errorcode"] =="0"){
                        $this->say($data["message"], ['parse_mode' => 'Markdown']);
                        $this->AskPwd();
                    }
                }
                else
                {
                    $buser = Botusers::where("chatid",$chatID)->first();
                    $buser->findOrFail($buser->id);
                    $buser->apikey = $data["apikey"];
                    $buser->uname = $data["name"];
                    $buser->save();

                    

                    $message = "Welcome <b>{$data['name']}</b>. What do you want to do today?";
                    $this->help->MainMenu($chatID,$message);
                    return;
                }
                

            }
        });
    }

    public function run()
    {
        $info = $this->bot->getUser();
        if($this->help->IsLoggedIn($info->getId())){
            $message = "Welcome <b>".\Helpers::$UserInfo->uname."</b>. What do you want to do today?";
            $this->help->MainMenu($info->getId(),$message);
            return;

        }else{

            $message = "You were logged out due to inactivity,press the login button to continue";
            $this->help->Login($info->getId(),$message);
            return;
        }
    }
}
