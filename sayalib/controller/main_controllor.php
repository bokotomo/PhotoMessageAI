<?php
namespace Saya;

use Saya;
use Saya\MessageControllor\TextMessageControllor;
use Saya\MessageControllor\StickerMessageControllor;
use Saya\MessageControllor\ImageMessageControllor;
use Saya\MessageControllor\LocationMessageControllor;
use \LINE\LINEBot\HTTPClient\CurlHTTPClient;
use \LINE\LINEBot;
use TomoLib\DatabaseProvider;
use TomoLib\DataLogger;

class MainControllor
{
  private $EventData;
  private $Bot;
  private $DatabaseProvider;
  private $UserName;
  private $UserImageUrl;
  private $UserText;

  public function __construct($Bot, $EventData){
    $this->EventData = $EventData;
    $this->Bot = $Bot;
    if(empty( $this->EventData->getUserId() )){
      $this->UserId = "1";
    }else{
      $this->UserId = $this->EventData->getUserId();
    }
    $this->DatabaseProvider = new DatabaseProvider("sqlite3", ROOT_DIR_PATH."/sayalib/database/sayadb.sqlite3");
    if(!$this->checkUserLoginDone()){
      $this->setUserData();
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

  private function setUserData(){
    $Response = $this->Bot->getProfile($this->EventData->getUserId());
    if ($Response->isSucceeded()) {
        $Profile = $Response->getJSONDecodedBody();
        $this->UserName = $Profile['displayName'];
        $this->UserImageUrl = $Profile['pictureUrl'];
        $this->UserText = $Profile['statusMessage'];
    }
  }

  private function addUser(){
    $stmt = $this->DatabaseProvider->setSql("insert into user_info(user_id,date,user_name,user_image,user_text) values(:id, :date, :username, :userimage, :usertext)");
    $stmt->bindValue(':id', $this->UserId, \PDO::PARAM_STR);
    $stmt->bindValue(':date', date("Y-m-d H:i:s"), \PDO::PARAM_STR);
    $stmt->bindValue(':username', $this->UserName, \PDO::PARAM_STR);
    $stmt->bindValue(':userimage', $this->UserImageUrl, \PDO::PARAM_STR);
    $stmt->bindValue(':usertext', $this->UserText, \PDO::PARAM_STR);
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