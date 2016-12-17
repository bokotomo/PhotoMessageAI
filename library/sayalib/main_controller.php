<?php
namespace Saya;

require_once(__DIR__."/response_message/response_text_message.php");
require_once(__DIR__."/response_message/response_image_message.php");
require_once(__DIR__."/response_message/response_sticker_message.php");
require_once(__DIR__."/response_message/response_location_message.php");
require_once(__DIR__."/response_message/response_postback_message.php");

use Saya;
use Saya\MessageControllor\TextMessageControllor;
use Saya\MessageControllor\StickerMessageControllor;
use Saya\MessageControllor\ImageMessageControllor;
use Saya\MessageControllor\LocationMessageControllor;
use Saya\MessageControllor\PostBackMessageControllor;
use \LINE\LINEbot\HTTPClient\CurlHTTPClient;
use \LINE\LINEbot;
use TomoLib\DatabaseProvider;
use TomoLib\DataLogger;

class MainControllor
{
  private $eventData;
  private $bot;
  private $databaseProvider;
  private $userData;

  public function __construct($bot, $eventData){
    $this->outputLog();
    $this->eventData = $eventData;
    $this->bot = $bot;
    if(empty( $this->eventData->getUserId() )){
      $this->userData["user_id"] = "1";
    }else{
      $this->userData["user_id"] = $this->eventData->getUserId();
    }
    $this->databaseProvider = new DatabaseProvider(SQL_TYPE, LOCAL_DATABASE_PATH."/sayadb.sqlite3");
    if(!$this->checkUserLoginDone()){
      $this->setuserData();
      $this->addUser();
    }
  }

  private function checkUserLoginDone(){
    $stmt = $this->databaseProvider->setSql("select * from user_info where user_id = :user_id");
    $stmt->bindValue(':user_id', $this->userData["user_id"], \PDO::PARAM_STR);
    $stmt->execute();
    while($row = $stmt -> fetch(\PDO::FETCH_ASSOC)) {
      return true;
    }
    return false;
  }

  private function setuserData(){
    $response = $this->bot->getProfile($this->eventData->getUserId());
    if ($response->isSucceeded()) {
        $profile = $response->getJSONDecodedBody();
        $this->userData["UserName"] = $profile['displayName'];
        $this->userData["UserImageUrl"] = $profile['pictureUrl'];
        $this->userData["UserText"] = $profile['statusMessage'];
    }
  }

  private function addUser(){
    $stmt = $this->databaseProvider->setSql("insert into user_info(user_id,date,user_name,user_image,user_text) values(:id, :date, :username, :userimage, :usertext)");
    $stmt->bindValue(':id', $this->userData["user_id"], \PDO::PARAM_STR);
    $stmt->bindValue(':date', date("Y-m-d H:i:s"), \PDO::PARAM_STR);
    $stmt->bindValue(':username', $this->userData["UserName"], \PDO::PARAM_STR);
    $stmt->bindValue(':userimage', $this->userData["UserImageUrl"], \PDO::PARAM_STR);
    $stmt->bindValue(':usertext', $this->userData["UserText"], \PDO::PARAM_STR);
    $stmt->execute();
  }
  
  private function outputLog(){
    $dataLogger = new DataLogger();
    $dataLogger->setLogType("html");
    $dataLogger->setFilePath(LOCAL_LOG_PATH."/line.html");
    $dataLogger->setLogData(file_get_contents("php://input"));
    $dataLogger->outputLog();
  }

  public function responseMessage(){
    if($this->eventData->getType() === "message"){
      if($this->eventData instanceof \LINE\LINEbot\Event\MessageEvent\TextMessage){
        $textMessageControllor = new TextMessageControllor($this->bot, $this->eventData, $this->userData);
        $textMessageControllor->responseMessage();
      }else if($this->eventData instanceof \LINE\LINEbot\Event\MessageEvent\StickerMessage){
        $stickerMessageControllor = new StickerMessageControllor($this->bot, $this->eventData, $this->userData);
        $stickerMessageControllor->responseMessage();
      }else if($this->eventData instanceof \LINE\LINEbot\Event\MessageEvent\ImageMessage){
        $imageMessageControllor = new ImageMessageControllor($this->bot, $this->eventData, $this->userData);
        $imageMessageControllor->responseMessage();
      }else if($this->eventData instanceof \LINE\LINEbot\Event\MessageEvent\LocationMessage){
        $locationMessageControllor = new LocationMessageControllor($this->bot, $this->eventData, $this->userData);
        $locationMessageControllor->responseMessage();
      }
    }else if($this->eventData->getType() === "postback"){
      $postBackMessageControllor = new PostBackMessageControllor($this->bot, $this->eventData, $this->userData);
      $postBackMessageControllor->responseMessage();
    }
  }
}