<?php
namespace Saya\MessageControllor;

use Saya\MessageControllor;
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
    $stmt = $this->DatabaseProvider->setSql("insert into saya_upload_imgs(user_id,origin_img_url,conv_img_url,img_name) values(:user_id, :origin_url, :conv_url, :img_name)");
    $stmt->bindValue(':user_id', $this->UserData["user_id"], \PDO::PARAM_STR);
    $stmt->bindValue(':origin_url', URL_ROOT_PATH."/images/userimg/".$this->ImgName, \PDO::PARAM_STR);
    $stmt->bindValue(':conv_url', URL_ROOT_PATH."/images/convimg/".$this->ImgName, \PDO::PARAM_STR);
    $stmt->bindValue(':img_name', $this->ImgName, \PDO::PARAM_STR);
    $stmt->execute();
  }

  public function uploadIMGFile(){
    $response = $this->Bot->getMessageContent($this->EventData->getMessageId());
    $UploadFileProvider = new UploadFileProvider();
    $FilePath = LOCAL_IMAGES_PATH."/userimg/".$this->ImgName;
    $UploadFileProvider->uploadFileData($FilePath, $response->getRawBody());
  }

  private function chooseCarouselFilter(){
    $col = new CarouselColumnTemplateBuilder('Good appearance', "景色の見栄えを良くするフィルター", "https://tomo.syo.tokyo/openimg/car.jpg", [
        new PostbackTemplateActionBuilder('決定', "imgtype=appearance")
    ]);
    $CarouselColumnTemplates[] = $col;
    
    $col = new CarouselColumnTemplateBuilder('Fantastic', "景色を幻想的にするフィルター", "https://tomo.syo.tokyo/openimg/car.jpg", [
        new PostbackTemplateActionBuilder('決定', "imgtype=fantastic")
    ]);
    $CarouselColumnTemplates[] = $col;
    
    $col = new CarouselColumnTemplateBuilder('Pro', "一眼レフカメラフィルター", "https://tomo.syo.tokyo/openimg/car.jpg", [
        new PostbackTemplateActionBuilder('決定', "imgtype=pro")
    ]);
    $CarouselColumnTemplates[] = $col;
    
    $carouselTemplateBuilder = new CarouselTemplateBuilder($CarouselColumnTemplates);
    $templateMessage = new TemplateMessageBuilder('Good appearance or Fantastic or Pro', $carouselTemplateBuilder);
  
    return $templateMessage;
  }

  public function responseMessage(){
    $TextMessageBuilder = new TextMessageBuilder("景色の画像だね！この辺りが良さそう！");
    $TemplateMessage = $this->chooseCarouselFilter();
    $message = new MultiMessageBuilder();
    $message->add($TextMessageBuilder);
    $message->add($TemplateMessage);
    $response = $this->Bot->replyMessage($this->EventData->getReplyToken(), $message);
  } 

}