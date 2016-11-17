<?php
class StickerMessageControllor
{
  private $ReplyToken;
  private $bot;
  private $MessageStickerId;
  private $MessagePackageId;

  public function __construct($ReplyToken,$ReceiveData){
    $this->ReplyToken = $ReplyToken;
    $this->MessageStickerId = $ReceiveData->events[0]->message->stickerId;
    $this->MessagePackageId = $ReceiveData->events[0]->message->packageId;
    $httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient(ACCESS_TOKEN);
    $this->bot = new \LINE\LINEBot($httpClient, ['channelSecret' => SECRET_TOKEN]);
  }

  public function responseMessage(){
    $LineMessage = new LineMessageHandler($this->ReplyToken);
    
    $StickerMessage = new LINE\LINEBot\MessageBuilder\StickerMessageBuilder(1,2);
    $message = new LINE\LINEBot\MessageBuilder\MultiMessageBuilder();
    $message->add($StickerMessage);
    $response = $this->bot->replyMessage($this->ReplyToken, $message);
  }  
 
} 
?>