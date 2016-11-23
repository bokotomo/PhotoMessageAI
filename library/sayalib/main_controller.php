<?php
namespace Saya;

require_once(__DIR__."/response_message/response_text_message.php");
require_once(__DIR__."/response_message/response_image_message.php");
require_once(__DIR__."/response_message/response_sticker_message.php");
require_once(__DIR__."/response_message/response_location_message.php");

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
  private $UserData;

  public function __construct($Bot, $EventData){
    $this->outputLog();
    $this->EventData = $EventData;
    $this->Bot = $Bot;
    if(empty( $this->EventData->getUserId() )){
      $this->UserData["user_id"] = "1";
    }else{
      $this->UserData["user_id"] = $this->EventData->getUserId();
    }
    $this->DatabaseProvider = new DatabaseProvider(SQL_TYPE, LOCAL_DATABASE_PATH."/sayadb.sqlite3");
    if(!$this->checkUserLoginDone()){
      $this->setUserData();
      $this->addUser();
    }
  }

  private function checkUserLoginDone(){
    $stmt = $this->DatabaseProvider->setSql("select * from user_info where user_id = :user_id");
    $stmt->bindValue(':user_id', $this->UserData["user_id"], \PDO::PARAM_STR);
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
        $this->UserData["UserName"] = $Profile['displayName'];
        $this->UserData["UserImageUrl"] = $Profile['pictureUrl'];
        $this->UserData["UserText"] = $Profile['statusMessage'];
    }
  }

  private function addUser(){
    $stmt = $this->DatabaseProvider->setSql("insert into user_info(user_id,date,user_name,user_image,user_text) values(:id, :date, :username, :userimage, :usertext)");
    $stmt->bindValue(':id', $this->UserData["user_id"], \PDO::PARAM_STR);
    $stmt->bindValue(':date', date("Y-m-d H:i:s"), \PDO::PARAM_STR);
    $stmt->bindValue(':username', $this->UserData["UserName"], \PDO::PARAM_STR);
    $stmt->bindValue(':userimage', $this->UserData["UserImageUrl"], \PDO::PARAM_STR);
    $stmt->bindValue(':usertext', $this->UserData["UserText"], \PDO::PARAM_STR);
    $stmt->execute();
  }
  
  private function outputLog(){
    $DataLogger = new DataLogger();
    $DataLogger->setLogType("html");
    $DataLogger->setFilePath(LOCAL_LOG_PATH."/line.html");
    $DataLogger->setLogData(file_get_contents("php://input"));
    $DataLogger->outputLog();
  }

  public function responseMessage(){
    if($this->EventData instanceof \LINE\LINEBot\Event\MessageEvent\TextMessage){
      $TextMessageControllor = new TextMessageControllor($this->Bot, $this->EventData, $this->UserData);
      $TextMessageControllor->responseMessage();
    }else if($this->EventData instanceof \LINE\LINEBot\Event\MessageEvent\StickerMessage){
      $StickerMessageControllor = new StickerMessageControllor($this->Bot, $this->EventData, $this->UserData);
      $StickerMessageControllor->responseMessage();
    }else if($this->EventData instanceof \LINE\LINEBot\Event\MessageEvent\ImageMessage){
      $ImageMessageControllor = new ImageMessageControllor($this->Bot, $this->EventData, $this->UserData);
      $ImageMessageControllor->responseMessage();
    }else if($this->EventData instanceof \LINE\LINEBot\Event\MessageEvent\LocationMessage){
      $LocationMessageControllor = new LocationMessageControllor($this->Bot, $this->EventData, $this->UserData);
      $LocationMessageControllor->responseMessage();
    }
  }
}