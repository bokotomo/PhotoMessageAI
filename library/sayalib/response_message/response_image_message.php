<?php
namespace Saya\MessageControllor;

use Saya\MessageControllor;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\MessageBuilder\ImageMessageBuilder;
use LINE\LINEBot\MessageBuilder\MultiMessageBuilder;
use TomoLib\UploadFileProvider;
use TomoLib\DatabaseProvider;

class ImageMessageControllor
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
    $this->ImgName = md5($this->UserData["user_id"]."_".$this->DatabaseProvider->getLastAutoIncrement("saya_upload_imgs")).".jpg";
    $this->insertDBUserUploadImages();
    $this->uploadIMGFile();
  }

  public function insertDBUserUploadImages(){
    $stmt = $this->DatabaseProvider->setSql("insert into saya_upload_imgs(user_id,origin_img_url,conv_img_url) values(:user_id, :origin_url, :conv_url)");
    $stmt->bindValue(':user_id', $this->UserData["user_id"], \PDO::PARAM_STR);
    $stmt->bindValue(':origin_url', URL_ROOT_PATH."/images/userimg/".$this->ImgName, \PDO::PARAM_STR);
    $stmt->bindValue(':conv_url', URL_ROOT_PATH."/images/convimg/".$this->ImgName, \PDO::PARAM_STR);
    $stmt->execute();
  }

  public function uploadIMGFile(){
    $response = $this->Bot->getMessageContent($this->EventData->getMessageId());
    $UploadFileProvider = new UploadFileProvider();
    $FilePath = LOCAL_IMAGES_PATH."/userimg/".$this->ImgName;
    $UploadFileProvider->uploadFileData($FilePath, $response->getRawBody());
  }

  public function responseMessage(){
    $RunScriptPath = LOCAL_SCRIPT_PATH."/image_converter/response_image.sh";
    $LocalUserimgPath = LOCAL_IMAGES_PATH."/userimg/".$this->ImgName;
    $LocalConvimgPath = LOCAL_IMAGES_PATH."/convimg/".$this->ImgName;
    $ShellRunStr = "sh {$RunScriptPath} {$LocalUserimgPath} {$LocalConvimgPath}";
    $Res = system($ShellRunStr);

    $OriginalContentSSLUrl = URL_ROOT_PATH."/images/convimg/".$this->ImgName;
    $PreviewImageSSLUrl = URL_ROOT_PATH."/images/convimg/".$this->ImgName;
    $ImageMessage = new ImageMessageBuilder($OriginalContentSSLUrl, $PreviewImageSSLUrl);
    $TextMessageBuilder = new TextMessageBuilder("こういうのはどう？".$Res);

    $message = new MultiMessageBuilder();
    $message->add($TextMessageBuilder);
    $message->add($ImageMessage);
    $response = $this->Bot->replyMessage($this->EventData->getReplyToken(), $message);
  } 

}