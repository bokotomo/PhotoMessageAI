<?php
namespace Saya\MessageControllor;

use Saya\MessageControllor;
use \LINE\LINEBot\HTTPClient\CurlHTTPClient;
use \LINE\LINEBot;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\MessageBuilder\ImageMessageBuilder;
use LINE\LINEBot\MessageBuilder\MultiMessageBuilder;
use TomoLib\UploadFileProvider;
use TomoLib\DatabaseProvider;

class ImageMessageControllor
{
  private $ReplyToken;
  private $Bot;
  private $MessageId;
  private $UserId;
  private $DatabaseProvider;
  private $ImgName;

  public function __construct($ReplyToken, $ReceiveData){
    $httpClient = new CurlHTTPClient(ACCESS_TOKEN);
    $this->Bot = new LINEBot($httpClient, ['channelSecret' => SECRET_TOKEN]);
    $this->ReplyToken = $ReplyToken;
    $this->MessageId = $ReceiveData->events[0]->message->id;
    $this->UserId = $ReceiveData->events[0]->source->userId;

    $this->DatabaseProvider = new DatabaseProvider("sqlite3", ROOT_DIR_PATH."/sayalib/database/sayadb.sqlite3");

    $this->ImgName = $this->UserId."_".$this->DatabaseProvider->getLastInsertId("saya_upload_imgs").".jpg";
    //$this->ImgName = md5()."_"date("YmdHis").".jpg"
    $this->insertUserUploadImage();
    $this->uploadImgFile();
  }

  public function insertUserUploadImage(){
    $stmt = $this->DatabaseProvider->runQuery("insert into saya_upload_imgs(user_id,img_url) values(:user_id, :img_url)");
    $stmt->bindValue(':user_id', $this->UserId, \PDO::PARAM_STR);
    $stmt->bindValue(':img_url', URL_ROOT_PATH."/linebot/saya_photo/userimg/".$this->ImgName, \PDO::PARAM_STR);
    $stmt->execute();
  }

  public function uploadImgFile(){
    $response = $this->Bot->getMessageContent($this->MessageId);
    $UploadFileProvider = new UploadFileProvider();
    $FilePath = ROOT_DIR_PATH."/userimg/".$this->ImgName;
    $UploadFileProvider->uploadFileData($FilePath, $response->getRawBody());
  }

  public function responseMessage(){
    $OriginalContentSSLUrl = URL_ROOT_PATH."/linebot/saya_photo/userimg/".$this->ImgName;
    $PreviewImageSSLUrl = URL_ROOT_PATH."/linebot/saya_photo/userimg/".$this->ImgName;
    $ImageMessage = new ImageMessageBuilder($OriginalContentSSLUrl, $PreviewImageSSLUrl);

    $TextMessageBuilder = new TextMessageBuilder("こういうのはどう？");

    $message = new MultiMessageBuilder();
    $message->add($TextMessageBuilder);
    $message->add($ImageMessage);
    $response = $this->Bot->replyMessage($this->ReplyToken, $message);
  } 
 
}
?>