<?php
namespace Saya\MessageControllor;

use Saya\MessageControllor;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\MessageBuilder\ImageMessageBuilder;
use LINE\LINEBot\MessageBuilder\MultiMessageBuilder;

class LocationMessageControllor
{
  private $EventData;
  private $Bot;
  private $DatabaseProvider;
  private $UserData;
  private $latitude;
  private $longitude;
  private $address;

  public function __construct($Bot, $EventData, $UserData){
    $this->EventData = $EventData;
    $this->Bot = $Bot;
    $this->UserData = $UserData;
    $this->latitude = $this->EventData->getLatitude();
    $this->longitude = $this->EventData->getLongitude();
    $this->address = $this->EventData->getAddress();
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
    $SendText = $LocationName."を代表する写真はこれかな！^o^";
    $TextMessageBuilder = new TextMessageBuilder($SendText);
    $OriginalContentSSLUrl = "https://tomo.syo.tokyo/openimg/shibuya.jpg";
    $PreviewImageSSLUrl = "https://tomo.syo.tokyo/openimg/shibuya.jpg";
    $ImageMessage = new ImageMessageBuilder($OriginalContentSSLUrl, $PreviewImageSSLUrl);

    $Message = new MultiMessageBuilder();
    $Message->add($TextMessageBuilder);
    $Message->add($ImageMessage);
    $response = $this->Bot->replyMessage($this->EventData->getReplyToken(), $Message);
  }

} 
?>