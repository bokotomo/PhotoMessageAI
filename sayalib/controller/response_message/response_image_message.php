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
  private $UserId;

  public function __construct($Bot, $EventData){
    $this->EventData = $EventData;
    $this->Bot = $Bot;
    if(empty( $this->EventData->getUserId() )){
      $this->UserId = "1";
    }else{
      $this->UserId = $this->EventData->getUserId();
    }
    $this->DatabaseProvider = new DatabaseProvider("sqlite3", ROOT_DIR_PATH."/sayalib/database/sayadb.sqlite3");

    $this->ImgName = md5($this->UserId."_".$this->DatabaseProvider->getLastAutoIncrement("saya_upload_imgs")).".jpg";
    $this->insertDBUserUploadImages();
    $this->uploadIMGFile();
    
  }

  public function insertDBUserUploadImages(){
    $stmt = $this->DatabaseProvider->setSql("insert into saya_upload_imgs(user_id,img_url) values(:user_id, :img_url)");
    $stmt->bindValue(':user_id', $this->UserId, \PDO::PARAM_STR);
    $stmt->bindValue(':img_url', URL_ROOT_PATH."/linebot/saya_photo/convimg/".$this->ImgName, \PDO::PARAM_STR);
    $stmt->execute();
  }

  public function uploadIMGFile(){
    $response = $this->Bot->getMessageContent($this->MessageId);
    $UploadFileProvider = new UploadFileProvider();
    $FilePath = ROOT_DIR_PATH."/userimg/".$this->ImgName;
    $UploadFileProvider->uploadFileData($FilePath, $response->getRawBody());
  }

  public function responseMessage(){
    //$tmp="python ".ROOT_DIR_PATH."/opencvlib/tes.py ".ROOT_DIR_PATH."/userimg/".$this->ImgName." ".ROOT_DIR_PATH."/convimg/".$this->ImgName;
    $tmp="sh ".ROOT_DIR_PATH."/opencvlib/t.sh ".ROOT_DIR_PATH."/userimg/".$this->ImgName." ".ROOT_DIR_PATH."/convimg/".$this->ImgName;
    $te=system($tmp);
    $OriginalContentSSLUrl = URL_ROOT_PATH."/linebot/saya_photo/convimg/".$this->ImgName;
    $PreviewImageSSLUrl = URL_ROOT_PATH."/linebot/saya_photo/convimg/".$this->ImgName;
    $ImageMessage = new ImageMessageBuilder($OriginalContentSSLUrl, $PreviewImageSSLUrl);

    $TextMessageBuilder = new TextMessageBuilder("こういうのはどう？");

    $message = new MultiMessageBuilder();
    $message->add($TextMessageBuilder);
    $message->add($ImageMessage);
    $response = $this->Bot->replyMessage($this->EventData->getReplyToken(), $message);
  } 

}
?>