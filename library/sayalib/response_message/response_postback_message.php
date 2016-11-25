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

  public function responseMessage(){
/*
    $RunScriptPath = LOCAL_SCRIPT_PATH."/image_converter/response_image.sh";
    $LocalUserimgPath = LOCAL_IMAGES_PATH."/userimg/".$this->ImgName;
    $LocalConvimgPath = LOCAL_IMAGES_PATH."/convimg/".$this->ImgName;
    $ShellRunStr = "sh {$RunScriptPath} {$LocalUserimgPath} {$LocalConvimgPath}";
    $Res = system($ShellRunStr);

    $OriginalContentSSLUrl = URL_ROOT_PATH."/images/convimg/".$this->ImgName;
    $PreviewImageSSLUrl = URL_ROOT_PATH."/images/convimg/".$this->ImgName;
    $ImageMessage = new ImageMessageBuilder($OriginalContentSSLUrl, $PreviewImageSSLUrl);
*/
    $PostArray = explode("=", $this->EventData->getPostbackData());
    $ImgType = $PostArray[1];
    if($ImgType === "appearance"){
      $TextMessageBuilder = new TextMessageBuilder("景色の画像だね！この辺りが良さそう！".$ImgType."!");
    }else if($ImgType === "fantastic"){
      $TextMessageBuilder = new TextMessageBuilder("景色の画像だね！この辺りが良さそう！".$ImgType."!");
    }else if($ImgType === "pro"){
      $TextMessageBuilder = new TextMessageBuilder("景色の画像だね！この辺りが良さそう！".$ImgType."!");
    }
    
    $message = new MultiMessageBuilder();
    $message->add($TextMessageBuilder);
//    $message->add($ImageMessage);
    $response = $this->Bot->replyMessage($this->EventData->getReplyToken(), $message);
  } 

}