<?php
class ImageMessageControllor
{
  private $ReplyToken;
  private $bot;

  public function __construct($ReplyToken,$ReceiveData){
    $this->ReplyToken = $ReplyToken;
  }

  public function responseMessage(){
    $LineMessage = new LineMessageHandler($this->ReplyToken);
    
//  $LineMessage->uploadImage($MessageId);
//  $LineMessage->ImageVoteConfirmMessage();
    $LineMessage->TextMessage("ありがと！☆^^ なかなかいいね！参考になる！");

  }  
 
} 
?>