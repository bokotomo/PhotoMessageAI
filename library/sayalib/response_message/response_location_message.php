<?php
namespace Saya\MessageControllor;

use Saya\MessageControllor;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\MessageBuilder\ImageMessageBuilder;
use LINE\LINEBot\MessageBuilder\MultiMessageBuilder;

class LocationMessageControllor
{
  private $eventData;
  private $bot;
  private $databaseProvider;
  private $userData;
  private $latitude;
  private $longitude;
  private $address;

  public function __construct($bot, $eventData, $userData){
    $this->eventData = $eventData;
    $this->bot = $bot;
    $this->userData = $userData;
    $this->latitude = $this->eventData->getLatitude();
    $this->longitude = $this->eventData->getLongitude();
    $this->address = $this->eventData->getAddress();
  }

  public function responseMessage(){
    $locationName = $this->address;
    $locationName = mb_convert_kana($locationName, "na");
    $texttable = [
      '-'=>'',
      '〒'=>'',
      '丁目'=>'',
      ' '=>''
    ];
    $locationName = str_replace(array_keys($texttable), array_values($texttable), $locationName);
    $locationName = preg_replace("/[0-9]/", "", $locationName);
    $sendText = $locationName."を代表する写真はこれかな！^o^";
    $textMessageBuilder = new TextMessageBuilder($sendText);
    $originalContentSSLUrl = "https://tomo.syo.tokyo/openimg/shibuya.jpg";
    $previewImageSSLUrl = "https://tomo.syo.tokyo/openimg/shibuya.jpg";
    $imageMessage = new ImageMessageBuilder($originalContentSSLUrl, $previewImageSSLUrl);

    $message = new MultiMessageBuilder();
    $message->add($textMessageBuilder);
    $message->add($imageMessage);
    $response = $this->bot->replyMessage($this->eventData->getReplyToken(), $message);
  }

}