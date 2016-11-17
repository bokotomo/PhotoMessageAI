<?php
namespace Saya\MessageControllor;

use Saya\MessageControllor;
use \LINE\LINEBot\HTTPClient\CurlHTTPClient;
use \LINE\LINEBot;
use LINE\LINEBot\MessageBuilder\StickerMessageBuilder;
use LINE\LINEBot\MessageBuilder\MultiMessageBuilder;

class StickerMessageControllor
{
  private $ReplyToken;
  private $Bot;
  private $MessageStickerId;
  private $MessagePackageId;

  public function __construct($ReplyToken, $ReceiveData){
    $httpClient = new CurlHTTPClient(ACCESS_TOKEN);
    $this->Bot = new LINEBot($httpClient, ['channelSecret' => SECRET_TOKEN]);
    $this->ReplyToken = $ReplyToken;
    $this->MessageStickerId = $ReceiveData->events[0]->message->stickerId;
    $this->MessagePackageId = $ReceiveData->events[0]->message->packageId;
  }

  public function responseMessage(){
    
    $StickerMessage = new StickerMessageBuilder(1, 2);
    
    $message = new MultiMessageBuilder();
    $message->add($StickerMessage);
    $response = $this->Bot->replyMessage($this->ReplyToken, $message);
  }  
 
} 
?>