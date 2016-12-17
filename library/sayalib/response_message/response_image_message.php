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
  private $eventData;
  private $bot;
  private $databaseProvider;
  private $userData;

  public function __construct($bot, $eventData, $userData){
    $this->eventData = $eventData;
    $this->bot = $bot;
    $this->userData = $userData;
    $this->databaseProvider = new DatabaseProvider(SQL_TYPE, LOCAL_DATABASE_PATH."/sayadb.sqlite3");
    $this->imgName = md5($this->userData["user_id"]."_".$this->databaseProvider->getLastAutoIncrement("saya_upload_imgs")).".jpg";
    $this->uploadIMGFile();
  }

  public function uploadIMGFile(){
    $response = $this->bot->getMessageContent($this->eventData->getMessageId());
    $uploadFileProvider = new UploadFileProvider();
    $filePath = LOCAL_IMAGES_PATH."/userimg/".$this->imgName;
    $uploadFileProvider->uploadFileData($filePath, $response->getRawBody());
  }

  private function chooseCarouselSceneryFilter(){
    $col = new CarouselColumnTemplateBuilder('Good appearance', "景色の見栄えを良くするフィルター", "https://tomo.syo.tokyo/openimg/car.jpg", [
      new PostbackTemplateActionBuilder('決定', "imgtype=appearance&img=".$this->imgName)
    ]);
    $carouselColumnTemplates[] = $col;
    
    $col = new CarouselColumnTemplateBuilder('Fantastic', "景色を幻想的にするフィルター", "https://tomo.syo.tokyo/openimg/car.jpg", [
      new PostbackTemplateActionBuilder('決定', "imgtype=fantastic&img=".$this->imgName)
    ]);
    $carouselColumnTemplates[] = $col;
    
    $col = new CarouselColumnTemplateBuilder('Pro', "一眼レフカメラフィルター", "https://tomo.syo.tokyo/openimg/car.jpg", [
      new PostbackTemplateActionBuilder('決定', "imgtype=pro&img=".$this->imgName)
    ]);
    $carouselColumnTemplates[] = $col;
    
    $carouselTemplateBuilder = new CarouselTemplateBuilder($carouselColumnTemplates);
    $templateMessage = new TemplateMessageBuilder('Good appearance or Fantastic or Pro', $carouselTemplateBuilder);
  
    return $templateMessage;
  }

  private function chooseCarouselHumanFilter(){
    $col = new CarouselColumnTemplateBuilder('Pro', "自撮り向けの一眼レフカメラフィルター", "https://tomo.syo.tokyo/openimg/human.jpg", [
      new PostbackTemplateActionBuilder('決定', "imgtype=pro&img=".$this->imgName)
    ]);
    $carouselColumnTemplates[] = $col;

    $col = new CarouselColumnTemplateBuilder('Good appearance', "人の見栄えを良くするフィルター", "https://tomo.syo.tokyo/openimg/human.jpg", [
      new PostbackTemplateActionBuilder('決定', "imgtype=appearance&img=".$this->imgName)
    ]);
    $carouselColumnTemplates[] = $col;
    
    $col = new CarouselColumnTemplateBuilder('Fantastic', "幻想的にするフィルター", "https://tomo.syo.tokyo/openimg/human.jpg", [
      new PostbackTemplateActionBuilder('決定', "imgtype=fantastic&img=".$this->imgName)
    ]);
    $carouselColumnTemplates[] = $col;
    
    $carouselTemplateBuilder = new CarouselTemplateBuilder($carouselColumnTemplates);
    $templateMessage = new TemplateMessageBuilder('Good appearance or Fantastic or Pro', $carouselTemplateBuilder);
  
    return $templateMessage;
  }

  private function chooseCarouselManyHumanFilter(){

    $col = new CarouselColumnTemplateBuilder('Good appearance', "複数人でも全員の見栄えが良くなるフィルター", "https://tomo.syo.tokyo/openimg/human.jpg", [
      new PostbackTemplateActionBuilder('決定', "imgtype=appearance&img=".$this->imgName)
    ]);
    $carouselColumnTemplates[] = $col;

    $col = new CarouselColumnTemplateBuilder('Pro', "一眼レフカメラフィルター", "https://tomo.syo.tokyo/openimg/human.jpg", [
      new PostbackTemplateActionBuilder('決定', "imgtype=pro&img=".$this->imgName)
    ]);
    $carouselColumnTemplates[] = $col;
    $col = new CarouselColumnTemplateBuilder('Fantastic', "幻想的にするフィルター", "https://tomo.syo.tokyo/openimg/human.jpg", [
      new PostbackTemplateActionBuilder('決定', "imgtype=fantastic&img=".$this->imgName)
    ]);
    $carouselColumnTemplates[] = $col;
    
    $carouselTemplateBuilder = new CarouselTemplateBuilder($carouselColumnTemplates);
    $templateMessage = new TemplateMessageBuilder('Good appearance or Fantastic or Pro', $carouselTemplateBuilder);
  
    return $templateMessage;
  }

  public function responseMessage(){
    $runScriptPath = LOCAL_SCRIPT_PATH."/image_converter/analyze_image.sh";
    $localUserimgPath = LOCAL_IMAGES_PATH."/userimg/".$this->imgName;
    $shellRunStr = "sh {$runScriptPath} {$localUserimgPath}";
    $res = system($shellRunStr);
    $analizeData = json_decode($res);
    if($analizeData->human_num == 1){
      $textMessageBuilder = new TextMessageBuilder("人の画像だね！この辺りとかどう？明るいイメージにして見たよ");
      $templateMessage = $this->chooseCarouselHumanFilter();
    }else if($analizeData->human_num > 1){
      $textMessageBuilder = new TextMessageBuilder("複数人の画像だね！こういうのはどう？");
      $templateMessage = $this->chooseCarouselManyHumanFilter();
    }else if($analizeData->human_num == 0){
      $textMessageBuilder = new TextMessageBuilder("良い景色だね！この辺りが良さそう！");
      $templateMessage = $this->chooseCarouselSceneryFilter();
    }
    $message = new MultiMessageBuilder();
    $message->add($textMessageBuilder);
    $message->add($templateMessage);
    $response = $this->bot->replyMessage($this->eventData->getReplyToken(), $message);
  } 

}