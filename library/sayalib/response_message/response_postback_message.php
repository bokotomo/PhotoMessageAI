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
    $PostArrayKey = explode("&", $this->EventData->getPostbackData());
    foreach($PostArrayKey as $v){
      $key = explode("=", $v)[0];
      $val = explode("=", $v)[1];
      $PostArray[$key] = $val;
    }
    $ImgType = $PostArray["imgtype"];
    $ImgName = $PostArray["img"];
    $RunScriptPath = LOCAL_SCRIPT_PATH."/image_converter/response_image.sh";
    $LocalUserimgPath = LOCAL_IMAGES_PATH."/userimg/".$ImgName;
    $LocalConvimgPath = LOCAL_IMAGES_PATH."/convimg/".$ImgName;
    $ShellRunStr = "sh {$RunScriptPath} {$LocalUserimgPath} {$LocalConvimgPath} {$ImgType}";
    $Res = system($ShellRunStr);

    $OriginalContentSSLUrl = URL_ROOT_PATH."/images/convimg/".$ImgName;
    $PreviewImageSSLUrl = URL_ROOT_PATH."/images/convimg/".$ImgName;
    $ImageMessage = new ImageMessageBuilder($OriginalContentSSLUrl, $PreviewImageSSLUrl);
    $Message = new MultiMessageBuilder();
    $Message->add($ImageMessage);
    $response = $this->Bot->replyMessage($this->EventData->getReplyToken(), $Message);
  }
}