<?php

 class Helpers {

    protected $url;
    protected $telegram;

    public function __construct(){

        $this->url = "https://api.telegram.org/bot" . env('TELEGRAM_TOKEN')."/sendMessage";
        $this->telegram = new Telegraph();
    }
    
   private function MainMenu($chatID,$message){
    
        $menu = [
    
            ["Registry",0],
            ["Allocation",0],
            ["Communication",0],
            ["Administration",0],
            ["Profile",1]
    
        ];
    
        $content = $this->telegram ->Allign($chatID, $message, $menu);

        return $content;
    }

    public function SendMenu($chatID,$message){
        $this->telegram ->SendMessage($this->MainMenu($chatID,$message),$this->url);
    }


}


