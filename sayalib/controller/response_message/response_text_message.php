<?php
namespace Saya\MessageControllor;

use Saya\MessageControllor;
use \LINE\LINEBot\HTTPClient\CurlHTTPClient;
use \LINE\LINEBot;
use LINE\LINEBot\MessageBuilder\StickerMessageBuilder;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\MessageBuilder\MultiMessageBuilder;

class TextMessageControllor
{
  private $ReplyToken;
  private $Bot;
  private $MessageText;
  private $MessageUserId;

  public function __construct($ReplyToken, $ReceiveData){
    $httpClient = new CurlHTTPClient(ACCESS_TOKEN);
    $this->Bot = new LINEBot($httpClient, ['channelSecret' => SECRET_TOKEN]);
    $this->ReplyToken = $ReplyToken;
    $this->MessageText = $ReceiveData->events[0]->message->text;
    $this->MessageUserId = $ReceiveData->events[0]->source->userId;
  }

  public function responseMessage(){
    $SendMessage = new MultiMessageBuilder();

    if($this->MessageText == "投票" || $this->MessageText == "とうひょう" || $this->MessageText == "選ぶ" || $this->MessageText == "みたい" || $this->MessageText == "写真" || $this->MessageText == "送って" || $this->MessageText == "おくって"){
    
    }else if($this->MessageText == "はい"){
      $TextMessageBuilder = new TextMessageBuilder("画像を投稿してください");
      $SendMessage->add($TextMessageBuilder);
      
    }else if($this->MessageText == "ねえ"){
      $TextMessageBuilder = new TextMessageBuilder("なに？^^");
      $SendMessage->add($TextMessageBuilder);
      
    }else if($this->MessageText == "ヘルシーなランチ教えて"){
      $TextMessageBuilder = new TextMessageBuilder("ここのイタリアンとかどう？http://www.r-hiro.com/　もしくはここのスムージー http://blog.livedoor.jp/tako86_blog/archives/cat_179856.html");
      $SendMessage->add($TextMessageBuilder);
      
    }else if($this->MessageText == "トレンドの冬服"){
      $TextMessageBuilder = new TextMessageBuilder("みんなから教えてもらったリスト送るね！");
      $SendMessage->add($TextMessageBuilder);
    }else if($this->MessageText == "ヘルシーでおいしいレシピとか教えて！"){
      $TextMessageBuilder = new TextMessageBuilder("きょうはこのレシピとかどう？簡単に作れるよ^^ http://cookpad.com/recipe/4095925");
      $SendMessage->add($TextMessageBuilder);      
    }else if($this->MessageText == "新宿でディナーとかのおすすめ"){
      $TextMessageBuilder = new TextMessageBuilder("おお、いいね！");
      $SendMessage->add($TextMessageBuilder);
    }else if($this->MessageText == "最近どう？"){
      $TextMessageBuilder = new TextMessageBuilder("写真のプロ目指してるよ！");
      $SendMessage->add($TextMessageBuilder);
    }else if($this->MessageText == "ある！"){
      $TextMessageBuilder = new TextMessageBuilder("みせて〜");
      $SendMessage->add($TextMessageBuilder);
    }else if($this->MessageText == "そうなんだ"){
      $TextMessageBuilder = new TextMessageBuilder("なんかうまく撮れた写真とかってある？");
      $SendMessage->add($TextMessageBuilder);
    }else{
      
      if(strpos($this->MessageText,"いいよ！") !== false){
        $TextMessageBuilder = new TextMessageBuilder("なんかうまく撮れた写真とかってある？");
        $SendMessage->add($TextMessageBuilder);
      }else if((strpos($this->MessageText,"加工") !== false || strpos($this->MessageText,"変換") !== false) && (strpos($this->MessageText,"ほしい") !== false || strpos($this->MessageText,"して") !== false || strpos($this->MessageText,"欲") !== false)){
        $TextMessageBuilder = new TextMessageBuilder("どんなふうに写真加工して欲しいの？");
        $SendMessage->add($TextMessageBuilder);
      }else if(mb_strlen($this->MessageText) == 1){
        $TextMessageBuilder = new TextMessageBuilder($this->MessageText."？どうしたの？");
        $SendMessage->add($TextMessageBuilder);
      }else{
        $TextMessageBuilder = new TextMessageBuilder("この言葉は勉強中かな(..)");
        $SendMessage->add($TextMessageBuilder);
      }
    
    }
    
    $Response = $this->Bot->replyMessage($this->ReplyToken, $SendMessage);
  }  
 
} 
?>