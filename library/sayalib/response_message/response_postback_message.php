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
  private $EventData;
  private $Bot;
  private $DatabaseProvider;
  private $UserData;

  public function __construct($Bot, $EventData, $UserData){
    $this->EventData = $EventData;
    $this->Bot = $Bot;
    $this->UserData = $UserData;
    $this->DatabaseProvider = new DatabaseProvider(SQL_TYPE, LOCAL_DATABASE_PATH."/sayadb.sqlite3");
  }

  private function insertDBUserUploadImages($ImgName, $ConvImgName){
    if($this->checkConvImgNameExis($ConvImgName) == false){
      $stmt = $this->DatabaseProvider->setSql("insert into saya_upload_imgs(user_id,origin_img_url,conv_img_url,img_name) values(:user_id, :origin_url, :conv_url, :img_name)");
      $stmt->bindValue(':user_id', $this->UserData["user_id"], \PDO::PARAM_STR);
      $stmt->bindValue(':origin_url', URL_ROOT_PATH."/images/userimg/".$ImgName, \PDO::PARAM_STR);
      $stmt->bindValue(':conv_url', URL_ROOT_PATH."/images/convimg/".$ConvImgName, \PDO::PARAM_STR);
      $stmt->bindValue(':img_name', $ImgName, \PDO::PARAM_STR);
      $stmt->execute();
    }
  }

  private function checkConvImgNameExis($ConvImgName){
    $stmt = $this->DatabaseProvider->setSql("select * from saya_upload_imgs where conv_img_url = :conv_url");
    $stmt->bindValue(':conv_url', URL_ROOT_PATH."/images/convimg/".$ConvImgName, \PDO::PARAM_STR);
    $stmt->execute();
    while($row = $stmt -> fetch(\PDO::FETCH_ASSOC)) {
      return true;
    }
    return false;
  }

  public function responseMessage(){
    $PostArrayKey = explode("&", $this->EventData->getPostbackData());
    foreach($PostArrayKey as $v){
      $key = explode("=", $v)[0];
      $val = explode("=", $v)[1];
      $PostArray[$key] = $val;
    }
    $ImgType = $PostArray["imgtype"];
    $ImgName = $PostArray["img"];
    $ConvImgName = explode(".", $ImgName)[0]."_".$ImgType.".".explode(".", $ImgName)[1];
    $RunScriptPath = LOCAL_SCRIPT_PATH."/image_converter/response_image.sh";
    $LocalUserimgPath = LOCAL_IMAGES_PATH."/userimg/".$ImgName;
    $LocalConvimgPath = LOCAL_IMAGES_PATH."/convimg/".$ConvImgName;
    $ShellRunStr = "sh {$RunScriptPath} {$LocalUserimgPath} {$LocalConvimgPath} {$ImgType}";
    $Res = system($ShellRunStr);

    $this->insertDBUserUploadImages($ImgName, $ConvImgName);

    $OriginalContentSSLUrl = URL_ROOT_PATH."/images/convimg/".$ConvImgName;
    $PreviewImageSSLUrl = URL_ROOT_PATH."/images/convimg/".$ConvImgName;
    $ImageMessage = new ImageMessageBuilder($OriginalContentSSLUrl, $PreviewImageSSLUrl);
    $Message = new MultiMessageBuilder();
    $Message->add($ImageMessage);
    $response = $this->Bot->replyMessage($this->EventData->getReplyToken(), $Message);
  }
}