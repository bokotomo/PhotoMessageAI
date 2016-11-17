<?php

/**
 * 2016 Fukumoto
 * mail : bokotomo@me.com
 */

class LineMessageHandler
{
  private $replyToken;
  private $bot;

  public function __construct($replyToken){
    $this->replyToken = $replyToken;
    $httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient(ACCESS_TOKEN);
    $this->bot = new \LINE\LINEBot($httpClient, ['channelSecret' => SECRET_TOKEN]);
  }

  public function ImageMessage($OriginalContentSSLUrl, $PreviewImageSSLUrl){
    $ImageMessage = new LINE\LINEBot\MessageBuilder\ImageMessageBuilder($OriginalContentSSLUrl, $PreviewImageSSLUrl);
    $message = new LINE\LINEBot\MessageBuilder\MultiMessageBuilder();
    $message->add($ImageMessage);
    $response = $this->bot->replyMessage($this->replyToken, $message);
  }

  public function StickerMessage($PackageId, $StickerId){
    $StickerMessage = new LINE\LINEBot\MessageBuilder\StickerMessageBuilder($PackageId, $StickerId);
    $message = new LINE\LINEBot\MessageBuilder\MultiMessageBuilder();
    $message->add($StickerMessage);
    $response = $this->bot->replyMessage($this->replyToken, $message);
  }

  public function TextMessage($text){
    $TextMessageBuilder = new LINE\LINEBot\MessageBuilder\TextMessageBuilder($text);
    $message = new LINE\LINEBot\MessageBuilder\MultiMessageBuilder();
    $message->add($TextMessageBuilder);
    $response = $this->bot->replyMessage($this->replyToken, $message);
  }

  public function ConfirmTemplateVoteMessage(){
    $MessageYesButton = new LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder("はい","はい");
    $MessageNoButton = new LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder("いいえ","いいえ");
    $ConfirmTemplate = new LINE\LINEBot\MessageBuilder\TemplateBuilder\ConfirmTemplateBuilder("写真を投稿しますか？", [$MessageYesButton, $MessageNoButton]);
    $TemplateMessage = new LINE\LINEBot\MessageBuilder\TemplateMessageBuilder("写真を投稿しますか？(はい/いいえ)", $ConfirmTemplate);
  
    $message = new LINE\LINEBot\MessageBuilder\MultiMessageBuilder();
    $message->add($TemplateMessage);
    $response = $this->bot->replyMessage($this->replyToken, $message);
  }

  public function uploadImage($messageId){
    $ImageData = $this->bot->getMessageContent($messageId);
    $uploaddir = './';
    $uploadfile = "tomo.jpg";
    move_uploaded_file($ImageData, $ImageData.$uploadfile);
  }

  public function ImageVoteConfirmMessage(){
    
    
    $OriginalContentSSLUrl = "https://iso.500px.com/wp-content/uploads/2016/06/stock-photo-142869191-1-1500x1000.jpg";
    $PreviewImageSSLUrl = "https://iso.500px.com/wp-content/uploads/2016/06/stock-photo-142869191-1-1500x1000.jpg";
    $ImageMessage = new LINE\LINEBot\MessageBuilder\ImageMessageBuilder($OriginalContentSSLUrl, $PreviewImageSSLUrl);
  
    $MessageYesButton = new LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder("はい","はい");
    $MessageNoButton = new LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder("いいえ","いいえ");
    $ConfirmTemplate = new LINE\LINEBot\MessageBuilder\TemplateBuilder\ConfirmTemplateBuilder("この写真を投稿してよろしいですか？", [$MessageYesButton, $MessageNoButton]);
    $TemplateMessage = new LINE\LINEBot\MessageBuilder\TemplateMessageBuilder("この写真を投稿してよろしいですか？(はい/いいえ)", $ConfirmTemplate);
    
    $message = new LINE\LINEBot\MessageBuilder\MultiMessageBuilder();  
    $message->add($ImageMessage);
    $message->add($TemplateMessage);
    $response = $this->bot->replyMessage($this->replyToken, $message);
  }
}

?>