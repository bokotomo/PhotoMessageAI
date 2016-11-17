<?php
namespace Saya;

use Saya;
use Saya\MessageControllor\TextMessageControllor;
use Saya\MessageControllor\StickerMessageControllor;
use Saya\MessageControllor\ImageMessageControllor;
use Saya\MessageControllor\LocationMessageControllor;

class MainControllor
{
  private $ReceiveData;
  private $ReplyToken;
  private $MessageType;

  public function __construct($ReceiveData){
    $this->ReceiveData = $ReceiveData;
    $this->ReplyToken = $ReceiveData->events[0]->replyToken;
    $this->MessageType = $ReceiveData->events[0]->message->type;   
  }
  
  public function responseMessage(){
    if($this->MessageType == "text"){
      $TextMessageControllor = new TextMessageControllor($this->ReplyToken, $this->ReceiveData);
      $TextMessageControllor->responseMessage();
    }else if($this->MessageType == "sticker"){
      $StickerMessageControllor = new StickerMessageControllor($this->ReplyToken, $this->ReceiveData);
      $StickerMessageControllor->responseMessage();
    }else if($this->MessageType == "image"){
      $ImageMessageControllor = new ImageMessageControllor($this->ReplyToken, $this->ReceiveData);
      $ImageMessageControllor->responseMessage();
    }else if($this->MessageType == "location"){
      $LocationMessageControllor = new LocationMessageControllor($this->ReplyToken, $this->ReceiveData);
      $LocationMessageControllor->responseMessage();
    }
  }
} 
?>