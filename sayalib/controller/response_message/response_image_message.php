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
    $this->DatabaseProvider = new DatabaseProvider("sqlite3", SQLITE_DATABASE_PATH."/sayadb.sqlite3");
    $this->ImgName = md5($this->UserData["user_id"]."_".$this->DatabaseProvider->getLastAutoIncrement("saya_upload_imgs")).".jpg";
    $this->insertDBUserUploadImages();
    $this->uploadIMGFile();
  }

  public function insertDBUserUploadImages(){
    $stmt = $this->DatabaseProvider->setSql("insert into saya_upload_imgs(user_id,img_url) values(:user_id, :img_url)");
    $stmt->bindValue(':user_id', $this->UserData["user_id"], \PDO::PARAM_STR);
    $stmt->bindValue(':img_url', URL_ROOT_PATH."/linebot/saya_photo/convimg/".$this->ImgName, \PDO::PARAM_STR);
    $stmt->execute();
  }

  public function uploadIMGFile(){
    $response = $this->Bot->getMessageContent($this->EventData->getMessageId());
    $UploadFileProvider = new UploadFileProvider();
    $FilePath = ROOT_DIR_PATH."/userimg/".$this->ImgName;
    $UploadFileProvider->uploadFileData($FilePath, $response->getRawBody());
  }

  public function responseMessage(){
    $SellRunStr = "sh ".ROOT_DIR_PATH."/image_converter/response_image.sh ".ROOT_DIR_PATH."/userimg/".$this->ImgName." ".ROOT_DIR_PATH."/convimg/".$this->ImgName;
    $Res = system($SellRunStr);
    $OriginalContentSSLUrl = URL_ROOT_PATH."/linebot/saya_photo/convimg/".$this->ImgName;
    $PreviewImageSSLUrl = URL_ROOT_PATH."/linebot/saya_photo/convimg/".$this->ImgName;
    $ImageMessage = new ImageMessageBuilder($OriginalContentSSLUrl, $PreviewImageSSLUrl);

    $TextMessageBuilder = new TextMessageBuilder("こういうのはどう？".$Res);

    $message = new MultiMessageBuilder();
    $message->add($TextMessageBuilder);
    $message->add($ImageMessage);
    $response = $this->Bot->replyMessage($this->EventData->getReplyToken(), $message);
  } 

}