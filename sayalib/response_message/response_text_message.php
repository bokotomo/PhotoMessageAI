<?php
class TextMessageControllor
{
  private $ReplyToken;
  private $bot;
  private $MessageText;
  private $MessageUserId;

  public function __construct($ReplyToken,$ReceiveData){
    $this->ReplyToken = $ReplyToken;
    $this->MessageText = $ReceiveData->events[0]->message->text;
    $this->MessageUserId = $ReceiveData->events[0]->source->userId;
  }

  public function responseMessage(){
    $LineMessage = new LineMessageHandler($this->ReplyToken);
  
    if($this->MessageText == "投票" || $this->MessageText == "とうひょう" || $this->MessageText == "選ぶ" || $this->MessageText == "みたい" || $this->MessageText == "写真" || $this->MessageText == "送って" || $this->MessageText == "おくって"){
      
      $LineMessage->ConfirmTemplateVoteMessage();
    
    }else if($this->MessageText == "はい"){
      
      $LineMessage->TextMessage("画像を投稿してください");
      
    }else if($this->MessageText == "ねえ"){
      
      $LineMessage->TextMessage("なに？^^");
      
    }else if($this->MessageText == "いいえ"){
      
    }else if($this->MessageText == "最近どう？"){
        $LineMessage->TextMessage("写真のプロ目指してるよ！");
    }else if($this->MessageText == "ある!"){
        $LineMessage->TextMessage("みせて〜");
    }else if($this->MessageText == "そうなんだ"){
        $LineMessage->TextMessage("なんかうまく撮れた写真とかってある？");
    }else{
      
      if(strpos($this->MessageText,"いいよ！") !== false){
        $LineMessage->TextMessage("なんかうまく撮れた写真とかってある？");
      }else if((strpos($this->MessageText,"加工") !== false || strpos($this->MessageText,"変換") !== false) && (strpos($this->MessageText,"ほしい") !== false || strpos($this->MessageText,"して") !== false || strpos($this->MessageText,"欲") !== false)){
        $LineMessage->TextMessage("どんなふうに写真加工して欲しいの？");
      }else{
        $LineMessage->TextMessage($this->MessageText);
      }
    
    }
  }  
 
} 
?>