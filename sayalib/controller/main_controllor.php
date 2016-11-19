<?php
namespace Saya;

use Saya;
use Saya\MessageControllor\TextMessageControllor;
use Saya\MessageControllor\StickerMessageControllor;
use Saya\MessageControllor\ImageMessageControllor;
use Saya\MessageControllor\LocationMessageControllor;
use TomoLib\DatabaseProvider;
use TomoLib\UploadProvider;
use \LINE\LINEBot\HTTPClient\CurlHTTPClient;
use \LINE\LINEBot;
use TomoLib\DataLogger;

class MainControllor
{
  private $ReceiveData;
  private $ReplyToken;
  private $MessageType;
  private $Bot;
  private $DatabaseProvider;
  private $UserId;

  public function __construct($ReceiveData){
    $httpClient = new CurlHTTPClient(ACCESS_TOKEN);
    $this->Bot = new LINEBot($httpClient, ['channelSecret' => SECRET_TOKEN]);
    $this->ReceiveData = $ReceiveData;
    $this->ReplyToken = $ReceiveData->events[0]->replyToken;
    $this->MessageType = $ReceiveData->events[0]->message->type;
    $this->UserId = $ReceiveData->events[0]->source->userId;
    if(empty($this->UserId )){
      $this->UserId = "1";
    }
    $this->DatabaseProvider = new DatabaseProvider("sqlite3", __DIR__."/../database/sayadb.sqlite3");
    if(!$this->checkUserLoginDone()){
      $this->addUser();
    }
    $this->api_get_message_content_request($ReceiveData->events[0]->message->id);
  }

  private function checkUserLoginDone(){
    $stmt = $this->DatabaseProvider->runQuery("select * from user_info where user_id = '".$this->UserId."'");
    $stmt->execute();
    while($row = $stmt -> fetch(\PDO::FETCH_ASSOC)) {
      return true;
    }
    return false;
  }

  private function addUser(){
    $stmt = $this->DatabaseProvider->runQuery("insert into user_info(user_id,date) values(:id, :date)");
    $stmt->bindValue(':id', $this->UserId, \PDO::PARAM_STR);
    $stmt->bindValue(':date', date("Y-m-d H:i:s"), \PDO::PARAM_STR);
    $stmt->execute();
  }

  public function api_get_message_content_request($message_id) {
    $response = $this->Bot->getMessageContent($message_id);
    file_put_contents("./tomo.jpg",$response->getRawBody());
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