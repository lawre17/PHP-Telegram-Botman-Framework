<?php
use GuzzleHttp\Client;

class Telegraph
{
    private $inline = array();
    private function Button($value, $contact = false, $location = false)
    {
        $keyboard = "";
        if(is_array($value)) {
            $keys = array();
            foreach ($value as $key) {
                $keys[] = [
                'text'         => $key,
                'request_contact'  => $contact,
                'request_location' =>$location
                 ];
            }
            $keyboard = $keys;
        } else {
            $keyboard = [
            'text'         => $value,
            'request_contact'  => $contact,
            'request_location' =>$location
            ];

        }

        return $keyboard;
    }

    public function Allign($chatId, $message, $option = [], $Url_link = "", $caption = "")
    {
        $combined = array();
        $single = array();
        $multiple = array();
        $rows = array();
        $row = array();
        if(is_array($option) && ! empty($option)) {
            foreach ($option as $key) {
                if($key[1] == 0) {
                    $multiple[] = $key[0];
                } else {
                    $single[] =  $this->Button($key[0]);
                }
            }

            if(!empty($multiple)) {
                //$combined[] = $multiple;
                $count = count($multiple);
                $i=1;
                if($count>1) {
                    foreach ($multiple as $key) {
                        $rows[] = $key;
                        if($i % 2==0) {
                            $combined[] = $this->Button($rows);
                            $rows = [];
                        }
                        $i++;
                    }
                } else {
                    $combined[] = [$this->Button($multiple[0])];
                }
            }
            if(!empty($single)) {

                foreach ($single as $key) {

                    $combined[] = [$key];

                }
            }
        }
        $json = $this->BuildKeyboard($combined);
        //file_put_contents('sample.txt', $json);
        if($Url_link =="") {
            $content = $this->Content($chatId, $json, $message, $Url_link, $caption);
        } else {
            $content = $this->File($chatId, $json, $Url_link, $caption);
        }

        return $content;

    }

    private function BuildKeyboard($made, $onetime = true, $resize = true, $selective = true)
    {
        $replyMarkup= [
          'keyboard'=> $made,
          'one_time_keyboard'=> $onetime,
          'resize_keyboard'=>$resize,
          'selective'=> $selective
        ];
        $encodedKeyboard = json_encode($replyMarkup, true);
        return $encodedKeyboard;
    }

    private function Content($chatId, $json, $message, $Url_link, $caption)
    {
        $content = array(
          'chat_id'=> $chatId,
          'reply_markup'=> $json,
          'text'=> $message,
          'parse_mode'=>"HTML"
        );

        if($Url_link !="") {
            $content+= ["document"=>$Url_link,"caption"=>$caption];
        }
        return $content;

    }

    private function File($chatId, $json, $Url_link, $caption)
    {
        $content = array(
          'chat_id'=> $chatId,
          'document'=> $Url_link,
          'caption'=> $caption,
          'parse_mode'=>"HTML",
          'reply_markup'=> $json
        );
        return $content;
    }

    public function SendMessage($content, $url)
    {
         //file_put_contents("content.json", json_encode($content));
        if (isset($content['chat_id'])) {
            $url = $url.'?chat_id='.$content['chat_id'];
            unset($content['chat_id']);
        }

        //file_put_contents("url.json", $url);

        $client = new Client();
        $client->post($url, ['json' => $content]);
    }

    public function SendFile($chatId, $url, $filename, $message)
    {
        file_get_contents($url.'?chat_id='.$chatId.'&document='.$filename.'&caption='.$message);
    }

    public function make_inline($array,$type = 0)
  {
    if($type==1)
    {
      $arr = array();
      foreach ($array as $key => $value) {
        $arr[] = ['text'=>$key,'callback_data'=>$value];
      }
      $inline[] = $arr;
        //file_put_contents("sample1.txt",json_encode($inline));
    }
    else
    {
      $j = 1;
      $k = 0;
      $k = array();
      foreach ($array as $key) {
        if($key[1]==0)
        {
          if($j<=2)
          {
            $k[] = ['text'=>$key[0],'callback_data'=>$key[0]];
            if($j==2)
            {
              $inline[] = $k;
              $j=1;
              $k++;
              $k = array();
            }
            else
            {
              $j++;
            }
          }
        }
        else
        {
          if($j<=1)
          {
            $k[] = ['text'=>$key[0],'callback_data'=>$key[0]];
            $inline[] = $k;
            $k++;
            $k = array();
          }
        }
      }
    }
    //file_put_contents("inline.txt",json_encode($inline));
    return $inline;
  }

  public function inline($chatId,$message,$inline)
  {
    $inline_keyboard =['inline_keyboard'=>$inline];
    $keyboard = json_encode($inline_keyboard);
    return $this->Content($chatId,$keyboard,$message,"","");
  }
}