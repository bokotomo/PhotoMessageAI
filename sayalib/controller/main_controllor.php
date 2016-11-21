<?php
namespace Saya;

use Saya;
use Saya\MessageControllor\TextMessageControllor;
use Saya\MessageControllor\StickerMessageControllor;
use Saya\MessageControllor\ImageMessageControllor;
use Saya\MessageControllor\LocationMessageControllor;
use TomoLib\DatabaseProvider;
use \LINE\LINEBot\HTTPClient\CurlHTTPClient;
use \LINE\LINEBot;
use TomoLib\DataLogger;

class MainControllor
{
  private $EventData;
  private $Bot;
  private $DatabaseProvider;

  public function __construct($Bot, $EventData){
    $this->EventData = $EventData;
    $this->Bot = $Bot;
    if(empty( $this->EventData->getUserId() )){
      $this->UserId = "1";
    }
    $this->DatabaseProvider = new DatabaseProvider("sqlite3", ROOT_DIR_PATH."/sayalib/database/sayadb.sqlite3");
    if(!$this->checkUserLoginDone()){
      $this->addUser();
    }
  }

  private function checkUserLoginDone(){
    $stmt = $this->DatabaseProvider->setSql("select * from user_info where user_id = :user_id");
    $stmt->bindValue(':user_id', $this->UserId, \PDO::PARAM_STR);
    $stmt->execute();
    while($row = $stmt -> fetch(\PDO::FETCH_ASSOC)) {
      return true;
    }
    return false;
  }

  private function addUser(){
    $stmt = $this->DatabaseProvider->setSql("insert into user_info(user_id,date) values(:id, :date)");
    $stmt->bindValue(':id', $this->UserId, \PDO::PARAM_STR);
    $stmt->bindValue(':date', date("Y-m-d H:i:s"), \PDO::PARAM_STR);
    $stmt->execute();
  }

  public function responseMessage(){
    if($this->EventData instanceof \LINE\LINEBot\Event\MessageEvent\TextMessage){
      $TextMessageControllor = new TextMessageControllor($this->Bot, $this->EventData);
      $TextMessageControllor->responseMessage();
    }else if($this->EventData instanceof \LINE\LINEBot\Event\MessageEvent\StickerMessage){
      $StickerMessageControllor = new StickerMessageControllor($this->Bot, $this->EventData);
      $StickerMessageControllor->responseMessage();
    }else if($this->EventData instanceof \LINE\LINEBot\Event\MessageEvent\ImageMessage){
      $ImageMessageControllor = new ImageMessageControllor($this->Bot, $this->EventData);
      $ImageMessageControllor->responseMessage();
    }else if($this->EventData instanceof \LINE\LINEBot\Event\MessageEvent\LocationMessage){
      $LocationMessageControllor = new LocationMessageControllor($this->Bot, $this->EventData);
      $LocationMessageControllor->responseMessage();
    }
  }
}
?>