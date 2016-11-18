<?php
namespace Saya\MessageControllor;

use Saya\MessageControllor;
use \LINE\LINEBot\HTTPClient\CurlHTTPClient;
use \LINE\LINEBot;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\MessageBuilder\MultiMessageBuilder;

class ImageMessageControllor
{
  private $ReplyToken;
  private $Bot;

  public function __construct($ReplyToken, $ReceiveData){
    $httpClient = new CurlHTTPClient(ACCESS_TOKEN);
    $this->Bot = new LINEBot($httpClient, ['channelSecret' => SECRET_TOKEN]);
    $this->ReplyToken = $ReplyToken;
  }

  public function responseMessage(){
    
//  $LineMessage->uploadImage($MessageId);
//  $LineMessage->ImageVoteConfirmMessage();

    $TextMessageBuilder = new TextMessageBuilder("ありがと！☆^^ なかなかいいね！参考になる！");
    $message = new MultiMessageBuilder();
    $message->add($TextMessageBuilder);
    $response = $this->Bot->replyMessage($this->ReplyToken, $message);

  }  
 
} 
?>