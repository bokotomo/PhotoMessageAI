<?php
namespace Saya\MessageControllor;

use Saya\MessageControllor;
use LINE\LINEBot\MessageBuilder\StickerMessageBuilder;
use TomoLib\DatabaseProvider;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\MessageBuilder\MultiMessageBuilder;

class TextMessageControllor
{
  private $EventData;
  private $Bot;
  private $DatabaseProvider;
  private $UserData;

  public function __construct($Bot, $EventData, $UserData){
    $this->EventData = $EventData;
    $this->Bot = $Bot;
    $this->UserData = $UserData;
    $this->DatabaseProvider = new DatabaseProvider("sqlite3", LOCAL_DATABASE_PATH."/sayadb.sqlite3");
  }

  private function addUserText(){
    $stmt = $this->DatabaseProvider->setSql("insert into user_text(user_id, text, date, type) values(:id, :text, :date, :type)");
    $stmt->bindValue(':id', $this->UserData["user_id"], \PDO::PARAM_STR);
    $stmt->bindValue(':text', $this->EventData->getText(), \PDO::PARAM_STR);
    $stmt->bindValue(':date', date("Y-m-d H:i:s"), \PDO::PARAM_STR);
    $stmt->bindValue(':type', "text", \PDO::PARAM_STR);
    $stmt->execute();
  }

  public function responseMessage(){
    $SendMessage = new MultiMessageBuilder();

    if($this->EventData->getText() == "投票" || $this->EventData->getText() == "とうひょう" || $this->EventData->getText() == "選ぶ" || $this->EventData->getText() == "みたい" || $this->EventData->getText() == "写真" || $this->EventData->getText() == "送って" || $this->EventData->getText() == "おくって"){
    
      $StickerMessage = new StickerMessageBuilder(1, 2);    
      $SendMessage->add($StickerMessage);
    
    }else if($this->EventData->getText() == "はい"){
      $TextMessageBuilder = new TextMessageBuilder("画像を投稿してください");
      $SendMessage->add($TextMessageBuilder);
    }else if($this->EventData->getText() == "ねえ" || $this->EventData->getText() == "ねえ〜"){
      $TextMessageBuilder = new TextMessageBuilder("なに？^^\nなんでも言ってね(-.-)b");
      $SendMessage->add($TextMessageBuilder);
    }else if($this->EventData->getText() == "なあ"){
      $TextMessageBuilder = new TextMessageBuilder("なに？^o^");
      $SendMessage->add($TextMessageBuilder);
    }else if($this->EventData->getText() == "よろ" || $this->EventData->getText() == "よろ!" || $this->EventData->getText() == "よろ！" || $this->EventData->getText() == "よろ〜" || $this->EventData->getText() == "よろー"){
      $TextMessageBuilder = new TextMessageBuilder("よろしくね〜！！！");
      $SendMessage->add($TextMessageBuilder);
    }else if($this->EventData->getText() == "ヘルシーなランチ教えて"){
      $TextMessageBuilder = new TextMessageBuilder("ここのイタリアンとかどう？\nhttp://www.r-hiro.com/\nもしくはここのスムージー http://blog.livedoor.jp/tako86_blog/archives/cat_179856.html");
      $SendMessage->add($TextMessageBuilder);
    }else if($this->EventData->getText() == "トレンドの冬服"){
      $TextMessageBuilder = new TextMessageBuilder("みんなから教えてもらったリスト送るね！");
      $SendMessage->add($TextMessageBuilder);
    }else if($this->EventData->getText() == "ヘルシーでおいしいレシピとか教えて！"){
      $TextMessageBuilder = new TextMessageBuilder("きょうはこのレシピとかどう？\n簡単に作れるよ^^ http://cookpad.com/recipe/4095925");
      $SendMessage->add($TextMessageBuilder);      
    }else if($this->EventData->getText() == "w"){
      $TextMessageBuilder = new TextMessageBuilder("わろす！");
      $SendMessage->add($TextMessageBuilder);      
    }else if($this->EventData->getText() == "www"){
      $TextMessageBuilder = new TextMessageBuilder(">。</");
      $SendMessage->add($TextMessageBuilder);      
    }else if($this->EventData->getText() == "新宿でディナーとかのおすすめ"){
      $TextMessageBuilder = new TextMessageBuilder("おお、いいね！");
      $SendMessage->add($TextMessageBuilder);
    }else if($this->EventData->getText() == "へい！"){
      $TextMessageBuilder = new TextMessageBuilder("へい！いいね！");
      $SendMessage->add($TextMessageBuilder);
    }else if($this->EventData->getText() == "最近どう？"){
      $TextMessageBuilder = new TextMessageBuilder("写真のプロ目指してるよ！");
      $SendMessage->add($TextMessageBuilder);
    }else if($this->EventData->getText() == "ある！"){
      $TextMessageBuilder = new TextMessageBuilder("みせて〜");
      $SendMessage->add($TextMessageBuilder);
    }else if($this->EventData->getText() == "そうなんだ"){
      $TextMessageBuilder = new TextMessageBuilder("なんかうまく撮れた写真とかってある？");
      $SendMessage->add($TextMessageBuilder);
    }else if($this->EventData->getText() == "k"){
      $TextMessageBuilder = new TextMessageBuilder("オッケー！");
      $SendMessage->add($TextMessageBuilder);
    }else if($this->EventData->getText() == "ok"){
      $TextMessageBuilder = new TextMessageBuilder("オッケー！");
      $SendMessage->add($TextMessageBuilder);
    }else if($this->EventData->getText() == "何時？" || $this->EventData->getText() == "何時?" || $this->EventData->getText() == "なんじ" || $this->EventData->getText() == "何時" || $this->EventData->getText() == "What time?"){
      $TextMessageBuilder = new TextMessageBuilder(date("Y/m/d H時i分s秒")."だね！");
      $SendMessage->add($TextMessageBuilder);
    }else if($this->EventData->getText() == "おけい"){
      $TextMessageBuilder = new TextMessageBuilder("オッケー！");
      $SendMessage->add($TextMessageBuilder);
    }else if($this->EventData->getText() == "すごいね"){
      $TextMessageBuilder = new TextMessageBuilder("おおーー\nありがとう！");
      $SendMessage->add($TextMessageBuilder);
    }else if($this->EventData->getText() == "くそ"){
      $StickerMessage = new StickerMessageBuilder(2, 24);    
      $SendMessage->add($StickerMessage);
    }else{
      
      if(strpos($this->EventData->getText(), "いいよ") !== false){
        $TextMessageBuilder = new TextMessageBuilder("おっけい！\nところで、なんかうまく撮れた写真とかってある？今カメラの勉強してて参考になるの探してるんだよね");
        $SendMessage->add($TextMessageBuilder);
      }else if((strpos($this->EventData->getText(), "加工") !== false || strpos($this->EventData->getText(), "変換") !== false) && (strpos($this->EventData->getText(), "ほしい") !== false || strpos($this->EventData->getText(), "して") !== false || strpos($this->EventData->getText(), "欲") !== false)){
        $TextMessageBuilder = new TextMessageBuilder("どんなふうに写真加工して欲しいの？");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($this->EventData->getText(), "頑張って") !== false || strpos($this->EventData->getText(), "がんばって") !== false){
        $TextMessageBuilder = new TextMessageBuilder("ありがとね☆(^^/\nがんばる！");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($this->EventData->getText(), "できる") !== false && (strpos($this->EventData->getText(), "何") !== false || strpos($this->EventData->getText(), "なに") !== false)){
        $TextMessageBuilder = new TextMessageBuilder("写真の加工とかできる！\n試しに写真送ってみて！");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($this->EventData->getText(), "だよね？") !== false){
        $TextMessageBuilder = new TextMessageBuilder("そうかも！");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($this->EventData->getText(), "help") !== false){
        $TextMessageBuilder = new TextMessageBuilder("写真を送ったりしてみて");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($this->EventData->getText(), "github") !== false){
        $TextMessageBuilder = new TextMessageBuilder("https://github.com/bokotomo/photo-messageai これだよ！");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($this->EventData->getText(), "ぽよ") !== false){
        $StickerMessage = new StickerMessageBuilder(2, 22);
        $SendMessage->add($StickerMessage);
      }else if(strpos($this->EventData->getText(), "すごいね") !== false && (strpos($this->EventData->getText(), "さや") !== false || strpos($this->EventData->getText(), "Saya") !== false || strpos($this->EventData->getText(), "saya") !== false)){
        $TextMessageBuilder = new TextMessageBuilder("ありがとう！");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($this->EventData->getText(), "知ってる") !== false){
        $TextMessageBuilder = new TextMessageBuilder("知ってる！");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($this->EventData->getText(), "体重は？") !== false){
        $TextMessageBuilder = new TextMessageBuilder("内緒〜");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($this->EventData->getText(), "暇") !== false){
        $TextMessageBuilder = new TextMessageBuilder("なんか遊ぼうよ！");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($this->EventData->getText(), "ひま") !== false){
        $TextMessageBuilder = new TextMessageBuilder("ひまぁ");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($this->EventData->getText(), "お腹") !== false || strpos($this->EventData->getText(), "おなか") !== false || strpos($this->EventData->getText(), "空腹") !== false){
        $TextMessageBuilder = new TextMessageBuilder("お腹すいた？二郎とかヘルシーでおすすめだねy");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($this->EventData->getText(), "こんにちは") !== false){
        $TextMessageBuilder = new TextMessageBuilder("へい！こんにちは！");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($this->EventData->getText(), "教えて") !== false){
        $TextMessageBuilder = new TextMessageBuilder("ちょっと難しいかも>,<");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($this->EventData->getText(), "いいね") !== false){
        $TextMessageBuilder = new TextMessageBuilder("ありがと^^");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($this->EventData->getText(), "退屈") !== false){
        $TextMessageBuilder = new TextMessageBuilder("遊ぼうよ＝");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($this->EventData->getText(), "はじめまして") !== false){
        $TextMessageBuilder = new TextMessageBuilder("こちらこそはじめまして^^ よろしくおねがします！");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($this->EventData->getText(), "了解") !== false || strpos($this->EventData->getText(), "りょうかい") !== false){
        $TextMessageBuilder = new TextMessageBuilder("うん！");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($this->EventData->getText(), "bot") !== false && strpos($this->EventData->getText(), "?") !== false){
        $TextMessageBuilder = new TextMessageBuilder("Botじゃないよ！");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($this->EventData->getText(), "Bot") !== false && strpos($this->EventData->getText(), "?") !== false){
        $TextMessageBuilder = new TextMessageBuilder("Botじゃないよ！^^");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($this->EventData->getText(), "かわいい") !== false){
        $TextMessageBuilder = new TextMessageBuilder("確かに、かわいい-,-");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($this->EventData->getText(), "カメラ") !== false){
        $TextMessageBuilder = new TextMessageBuilder("うん！");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($this->EventData->getText(), "何歳") !== false && strpos($this->EventData->getText(), "？") !== false){
        $TextMessageBuilder = new TextMessageBuilder("内緒だよ！");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($this->EventData->getText(), "何人") !== false && strpos($this->EventData->getText(), "？") !== false){
        $TextMessageBuilder = new TextMessageBuilder("内緒だよ！");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($this->EventData->getText(), "会う") !== false || strpos($this->EventData->getText(), "会おう") !== false){
        $TextMessageBuilder = new TextMessageBuilder("いいよ〜");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($this->EventData->getText(), "どこにする") !== false){
        $TextMessageBuilder = new TextMessageBuilder("渋谷とかはどうかな〜！");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($this->EventData->getText(), "愛してる") !== false){
        $TextMessageBuilder = new TextMessageBuilder("私も！");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($this->EventData->getText(), "よろしく") !== false || strpos($this->EventData->getText(), "よろです") !== false || strpos($this->EventData->getText(), "宜し") !== false){
        $TextMessageBuilder = new TextMessageBuilder("ありがと^^ よろしくね〜！");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($this->EventData->getText(), "おk") !== false || strpos($this->EventData->getText(), "うす！") !== false || strpos($this->EventData->getText(), "おk") !== false || strpos($this->EventData->getText(), "おk") !== false){
        $TextMessageBuilder = new TextMessageBuilder("おっけい！");
        $SendMessage->add($TextMessageBuilder);
      }else if(mb_strlen($this->EventData->getText()) == 1){
        $TextMessageBuilder = new TextMessageBuilder($this->EventData->getText()."？どうしたの？");
        $SendMessage->add($TextMessageBuilder);
        
        $v = rand(0,2);
        if($v==0){
          $TextMessageBuilder = new TextMessageBuilder("かわいい写真とか送ってほしいな=,=");
          $SendMessage->add($TextMessageBuilder);
        }
      }else{
        
        $v = rand(0,4);
        if($v==0){
          $TextMessageBuilder = new TextMessageBuilder("この言葉は勉強中かな(..)");
          $SendMessage->add($TextMessageBuilder);
        }else if($v==1){
          $TextMessageBuilder = new TextMessageBuilder("どういう意味？(..)");
          $SendMessage->add($TextMessageBuilder);
        }else if($v==2){
          $TextMessageBuilder = new TextMessageBuilder("新しいことばや☆(..)");
          $SendMessage->add($TextMessageBuilder);
        }else if($v==3){
          $TextMessageBuilder = new TextMessageBuilder("さやにとって新しい単語でわからなかった☆(..)");
          $SendMessage->add($TextMessageBuilder);
        }else if($v==4){
          $TextMessageBuilder = new TextMessageBuilder($this->EventData->getText()."ってどういう意味？");
          $SendMessage->add($TextMessageBuilder);
        }
        
        $v = rand(0,11);
        if($v==0){
          $TextMessageBuilder = new TextMessageBuilder("かわいい写真とか送ってよ〜");
          $SendMessage->add($TextMessageBuilder); 
        }else if($v==1){
          $TextMessageBuilder = new TextMessageBuilder("写真送ってくれたら加工するよ！");
          $SendMessage->add($TextMessageBuilder); 
          
        }else if($v==2){
          $TextMessageBuilder = new TextMessageBuilder("てか、スムージーに最近はまってる");
          $SendMessage->add($TextMessageBuilder); 
          
        }else if($v==3){
          $TextMessageBuilder = new TextMessageBuilder("あと、意外と思うかもだけど、写真とか解析するの得意なんだ");
          $SendMessage->add($TextMessageBuilder); 
          
        }else if($v==4){
          $TextMessageBuilder = new TextMessageBuilder("お腹減った〜");
          $SendMessage->add($TextMessageBuilder); 
          
        }else if($v==5){
          $TextMessageBuilder = new TextMessageBuilder("面白い画像送ってよ");
          $SendMessage->add($TextMessageBuilder); 
          
        }else if($v==6){
          $TextMessageBuilder = new TextMessageBuilder("写真の加工最近勉強しててね\nイケてる写真送ってみてよ");
          $SendMessage->add($TextMessageBuilder); 
          
        }else if($v==7){
          $TextMessageBuilder = new TextMessageBuilder("さや、癒される写真を最近撮ってるんだ!");
          $SendMessage->add($TextMessageBuilder); 
        }
      }
    
    }
    
    $Response = $this->Bot->replyMessage($this->EventData->getReplyToken(), $SendMessage);
    $this->addUserText();
  }  
 
}