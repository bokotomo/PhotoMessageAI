<?php
class LocationMessageControllor
{
  private $ReplyToken;
  private $bot;
  private $latitude;
  private $longitude;
  private $address;

  public function __construct($ReplyToken,$ReceiveData){
    $this->ReplyToken = $ReplyToken;
    $httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient(ACCESS_TOKEN);
    $this->bot = new \LINE\LINEBot($httpClient, ['channelSecret' => SECRET_TOKEN]);
    $this->latitude = $ReceiveData->events[0]->message->latitude;
    $this->longitude = $ReceiveData->events[0]->message->longitude;
    $this->address = $ReceiveData->events[0]->message->address;
  }

  public function responseMessage(){
    $LineMessage = new LineMessageHandler($this->ReplyToken);

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
    $TextMessageBuilder = new LINE\LINEBot\MessageBuilder\TextMessageBuilder($sendtext);
    
    $OriginalContentSSLUrl = "https://tomo.syo.tokyo/openimg/shibuya.jpg";
    $PreviewImageSSLUrl = "https://tomo.syo.tokyo/openimg/shibuya.jpg";
    $ImageMessage = new LINE\LINEBot\MessageBuilder\ImageMessageBuilder($OriginalContentSSLUrl, $PreviewImageSSLUrl);
    
    $message = new LINE\LINEBot\MessageBuilder\MultiMessageBuilder();
    $message->add($TextMessageBuilder);
    $message->add($ImageMessage);
    $response = $this->bot->replyMessage($this->ReplyToken, $message);
  }  
 
} 
?>