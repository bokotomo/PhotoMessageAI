<?php
namespace Saya\MessageControllor;

use Saya\MessageControllor;
use LINE\LINEBot\Event;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\MessageBuilder\ImageMessageBuilder;
use LINE\LINEBot\MessageBuilder\MultiMessageBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselColumnTemplateBuilder;
use LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder;
use LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;
use TomoLib\UploadFileProvider;
use TomoLib\DatabaseProvider;

class PostBackMessageControllor
{
  private $eventData;
  private $bot;
  private $databaseProvider;
  private $userData;

  public function __construct($bot, $eventData, $userData){
    $this->eventData = $eventData;
    $this->bot = $bot;
    $this->userData = $userData;
    $this->databaseProvider = new DatabaseProvider(SQL_TYPE, LOCAL_DATABASE_PATH."/sayadb.sqlite3");
  }

  private function insertDBUserUploadImages($imgName, $convImgName){
    if($this->checkConvImgNameExis($convImgName) == false){
      $stmt = $this->databaseProvider->setSql("insert into saya_upload_imgs(user_id,origin_img_url,conv_img_url,img_name) values(:user_id, :origin_url, :conv_url, :img_name)");
      $stmt->bindValue(':user_id', $this->userData["user_id"], \PDO::PARAM_STR);
      $stmt->bindValue(':origin_url', URL_ROOT_PATH."/images/userimg/".$imgName, \PDO::PARAM_STR);
      $stmt->bindValue(':conv_url', URL_ROOT_PATH."/images/convimg/".$convImgName, \PDO::PARAM_STR);
      $stmt->bindValue(':img_name', $imgName, \PDO::PARAM_STR);
      $stmt->execute();
    }
  }

  private function checkConvImgNameExis($convImgName){
    $stmt = $this->databaseProvider->setSql("select * from saya_upload_imgs where conv_img_url = :conv_url");
    $stmt->bindValue(':conv_url', URL_ROOT_PATH."/images/convimg/".$convImgName, \PDO::PARAM_STR);
    $stmt->execute();
    while($row = $stmt -> fetch(\PDO::FETCH_ASSOC)) {
      return true;
    }
    return false;
  }

  public function responseMessage(){
    $postArrayKey = explode("&", $this->eventData->getPostbackData());
    foreach($postArrayKey as $v){
      $key = explode("=", $v)[0];
      $val = explode("=", $v)[1];
      $postArray[$key] = $val;
    }
    $imgType = $postArray["imgtype"];
    $imgName = $postArray["img"];
    $convImgName = explode(".", $imgName)[0]."_".$imgType.".".explode(".", $imgName)[1];
    $runScriptPath = LOCAL_SCRIPT_PATH."/image_converter/response_image.sh";
    $localUserimgPath = LOCAL_IMAGES_PATH."/userimg/".$imgName;
    $localConvimgPath = LOCAL_IMAGES_PATH."/convimg/".$convImgName;
    $shellRunStr = "sh {$runScriptPath} {$localUserimgPath} {$localConvimgPath} {$imgType}";
    $res = system($shellRunStr);

    $this->insertDBUserUploadImages($imgName, $convImgName);

    $originalContentSSLUrl = URL_ROOT_PATH."/images/convimg/".$convImgName;
    $previewImageSSLUrl = URL_ROOT_PATH."/images/convimg/".$convImgName;
    $imageMessage = new ImageMessageBuilder($originalContentSSLUrl, $previewImageSSLUrl);
    $message = new MultiMessageBuilder();
    $message->add($imageMessage);
    $response = $this->bot->replyMessage($this->eventData->getReplyToken(), $message);
  }
}