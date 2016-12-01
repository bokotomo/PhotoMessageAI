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
    $this->uploadIMGFile();
  }

  public function uploadIMGFile(){
    $response = $this->Bot->getMessageContent($this->EventData->getMessageId());
    $UploadFileProvider = new UploadFileProvider();
    $FilePath = LOCAL_IMAGES_PATH."/userimg/".$this->ImgName;
    $UploadFileProvider->uploadFileData($FilePath, $response->getRawBody());
  }

  private function chooseCarouselSceneryFilter(){
    $col = new CarouselColumnTemplateBuilder('Good appearance', "景色の見栄えを良くするフィルター", "https://tomo.syo.tokyo/openimg/car.jpg", [
        new PostbackTemplateActionBuilder('決定', "imgtype=appearance&img=".$this->ImgName)
    ]);
    $CarouselColumnTemplates[] = $col;
    
    $col = new CarouselColumnTemplateBuilder('Fantastic', "景色を幻想的にするフィルター", "https://tomo.syo.tokyo/openimg/car.jpg", [
        new PostbackTemplateActionBuilder('決定', "imgtype=fantastic&img=".$this->ImgName)
    ]);
    $CarouselColumnTemplates[] = $col;
    
    $col = new CarouselColumnTemplateBuilder('Pro', "一眼レフカメラフィルター", "https://tomo.syo.tokyo/openimg/car.jpg", [
        new PostbackTemplateActionBuilder('決定', "imgtype=pro&img=".$this->ImgName)
    ]);
    $CarouselColumnTemplates[] = $col;
    
    $carouselTemplateBuilder = new CarouselTemplateBuilder($CarouselColumnTemplates);
    $templateMessage = new TemplateMessageBuilder('Good appearance or Fantastic or Pro', $carouselTemplateBuilder);
  
    return $templateMessage;
  }

  private function chooseCarouselHumanFilter(){
    $col = new CarouselColumnTemplateBuilder('Pro', "自撮り向けの一眼レフカメラフィルター", "https://tomo.syo.tokyo/openimg/human.jpg", [
        new PostbackTemplateActionBuilder('決定', "imgtype=pro&img=".$this->ImgName)
    ]);
    $CarouselColumnTemplates[] = $col;

    $col = new CarouselColumnTemplateBuilder('Good appearance', "人の見栄えを良くするフィルター", "https://tomo.syo.tokyo/openimg/human.jpg", [
        new PostbackTemplateActionBuilder('決定', "imgtype=appearance&img=".$this->ImgName)
    ]);
    $CarouselColumnTemplates[] = $col;
    
    $col = new CarouselColumnTemplateBuilder('Fantastic', "幻想的にするフィルター", "https://tomo.syo.tokyo/openimg/human.jpg", [
        new PostbackTemplateActionBuilder('決定', "imgtype=fantastic&img=".$this->ImgName)
    ]);
    $CarouselColumnTemplates[] = $col;
    
    $carouselTemplateBuilder = new CarouselTemplateBuilder($CarouselColumnTemplates);
    $templateMessage = new TemplateMessageBuilder('Good appearance or Fantastic or Pro', $carouselTemplateBuilder);
  
    return $templateMessage;
  }

  private function chooseCarouselManyHumanFilter(){

    $col = new CarouselColumnTemplateBuilder('Good appearance', "複数人でも全員の見栄えが良くなるフィルター", "https://tomo.syo.tokyo/openimg/human.jpg", [
        new PostbackTemplateActionBuilder('決定', "imgtype=appearance&img=".$this->ImgName)
    ]);
    $CarouselColumnTemplates[] = $col;

    $col = new CarouselColumnTemplateBuilder('Pro', "一眼レフカメラフィルター", "https://tomo.syo.tokyo/openimg/human.jpg", [
        new PostbackTemplateActionBuilder('決定', "imgtype=pro&img=".$this->ImgName)
    ]);
    $CarouselColumnTemplates[] = $col;
    $col = new CarouselColumnTemplateBuilder('Fantastic', "幻想的にするフィルター", "https://tomo.syo.tokyo/openimg/human.jpg", [
        new PostbackTemplateActionBuilder('決定', "imgtype=fantastic&img=".$this->ImgName)
    ]);
    $CarouselColumnTemplates[] = $col;
    
    $carouselTemplateBuilder = new CarouselTemplateBuilder($CarouselColumnTemplates);
    $templateMessage = new TemplateMessageBuilder('Good appearance or Fantastic or Pro', $carouselTemplateBuilder);
  
    return $templateMessage;
  }

  public function responseMessage(){
    $RunScriptPath = LOCAL_SCRIPT_PATH."/image_converter/analyze_image.sh";
    $LocalUserimgPath = LOCAL_IMAGES_PATH."/userimg/".$this->ImgName;
    $ShellRunStr = "sh {$RunScriptPath} {$LocalUserimgPath}";
    $Res = system($ShellRunStr);
    $AnalizeData = json_decode($Res);
    if($AnalizeData->human_num == 1){
      $TextMessageBuilder = new TextMessageBuilder("落ち着いた画像だね！この辺りとかどう？明るいイメージにして見たよ");
      $TemplateMessage = $this->chooseCarouselHumanFilter();
    }else if($AnalizeData->human_num > 1){
      $TextMessageBuilder = new TextMessageBuilder("複数人の画像だね！こういうのはどう？");
      $TemplateMessage = $this->chooseCarouselManyHumanFilter();
    }else if($AnalizeData->human_num == 0){
      $TextMessageBuilder = new TextMessageBuilder("良い景色だね！この辺りが良さそう！");
      $TemplateMessage = $this->chooseCarouselSceneryFilter();
    }
    $message = new MultiMessageBuilder();
    $message->add($TextMessageBuilder);
    $message->add($TemplateMessage);
    $response = $this->Bot->replyMessage($this->EventData->getReplyToken(), $message);
  } 

}