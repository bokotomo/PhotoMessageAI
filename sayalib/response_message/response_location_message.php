<?php
namespace Saya\MessageControllor;

use Saya\MessageControllor;
use \LINE\LINEBot\HTTPClient\CurlHTTPClient;
use \LINE\LINEBot;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\MessageBuilder\ImageMessageBuilder;
use LINE\LINEBot\MessageBuilder\MultiMessageBuilder;

class LocationMessageControllor
{
  private $ReplyToken;
  private $Bot;
  private $latitude;
  private $longitude;
  private $address;

  public function __construct($ReplyToken, $ReceiveData){
    $this->ReplyToken = $ReplyToken;
    $httpClient = new CurlHTTPClient(ACCESS_TOKEN);
    $this->Bot = new LINEBot($httpClient, ['channelSecret' => SECRET_TOKEN]);
    $this->latitude = $ReceiveData->events[0]->message->latitude;
    $this->longitude = $ReceiveData->events[0]->message->longitude;
    $this->address = $ReceiveData->events[0]->message->address;
  }

  public function responseMessage(){

    $LocationName = $this->address;
    $LocationName = mb_convert_kana($LocationName, "na");
    $texttable = [
      '-'=>'',
      '〒'=>'',
      '丁目'=>'',
      ' '=>''
    ];
    $LocationName = str_replace(array_keys($texttable), array_values($texttable), $LocationName);
    $LocationName = preg_replace("/[0-9]/", "", $LocationName);
  
    $sendtext = $LocationName."を代表する写真はこれかな！^o^";
    $TextMessageBuilder = new TextMessageBuilder($sendtext);
    
    $OriginalContentSSLUrl = "https://tomo.syo.tokyo/openimg/shibuya.jpg";
    $PreviewImageSSLUrl = "https://tomo.syo.tokyo/openimg/shibuya.jpg";
    $ImageMessage = new ImageMessageBuilder($OriginalContentSSLUrl, $PreviewImageSSLUrl);
    
    $message = new MultiMessageBuilder();
    $message->add($TextMessageBuilder);
    $message->add($ImageMessage);
    $response = $this->Bot->replyMessage($this->ReplyToken, $message);
  }  
 
} 
?>