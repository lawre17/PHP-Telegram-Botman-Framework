<?php
use App\Botusers;

 class Helpers {

    protected $url;
    protected $telegram;

    public static $Key;
    public static $UserInfo;

    public function __construct(){

        $this->url = "https://api.telegram.org/bot" . env('TELEGRAM_TOKEN')."/sendMessage";
        $this->telegram = new Telegraph();
    }
    
   public function MainMenu($chatID,$message){
    
        $menu = [
            //["Registry",0],
            // ["Communication",0],
            // ["Administration",0],
            // ["Profile",1],
            ["Search Student",1],
            ["Allocations",1],
        ];
    
        $content = $this->telegram ->Allign($chatID, $message, $menu);
        $this->telegram ->SendMessage($content,$this->url);
        
    }

     public function CustomButton($chatID,$message,$menu){

        $content = $this->telegram ->Allign($chatID, $message, $menu);
        $this->telegram ->SendMessage($content,$this->url);
        
    }

    public function Login($chatID,$message){
    
        $menu = [ ["Login",1] ];
        $content = $this->telegram ->Allign($chatID, $message, $menu);
        $this->telegram ->SendMessage($content,$this->url);
    }

     public function YesNo($chatID,$message){
    
        $menu = [
            "Yes"=>'yes',
            "No"=>'no'    
        ];
    
        $content = $this->telegram ->make_inline($menu,1);
        $withMessage = $this->telegram->inline($chatID, $message, $content);
        $this->telegram ->SendMessage($withMessage,$this->url);
    }

    public function Inline($chatID,$message,$menu){
    
    
        $content = $this->telegram ->make_inline($menu,1);
        $withMessage = $this->telegram->inline($chatID, $message, $content);
        $this->telegram ->SendMessage($withMessage,$this->url);
    }
    public function IsLoggedIn($chatID){

        $data = Botusers::where('chatid', $chatID)->first();
        if(!empty($data)){

            $LastLogged_in = $data->updated_at;
            $interval = date_diff(date_create(date('Y-m-d')), date_create($LastLogged_in));
            if($interval->format("%a") >14){
                return false;
            }else{

                Helpers::$Key = $data->apikey;
                Helpers::$UserInfo = $data;
                $buser = Botusers::findOrFail($data->id);
                if($buser){
                    $buser->updated_at = date('Y-m-d H:i:s');
                    $buser->save();
                }
                return true;
            }

        }
        return false;
        
    }


}


