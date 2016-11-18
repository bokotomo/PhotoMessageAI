<?php
namespace Saya\MessageControllor;

use Saya\MessageControllor;
use \LINE\LINEBot\HTTPClient\CurlHTTPClient;
use \LINE\LINEBot;
use LINE\LINEBot\MessageBuilder\StickerMessageBuilder;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\MessageBuilder\MultiMessageBuilder;
use TomoLib\DatabaseProvider;

class TextMessageControllor
{
  private $ReplyToken;
  private $Bot;
  private $DatabaseProvider;
  private $MessageText;
  private $MessageUserId;

  public function __construct($ReplyToken, $ReceiveData){
    $httpClient = new CurlHTTPClient(ACCESS_TOKEN);
    $this->Bot = new LINEBot($httpClient, ['channelSecret' => SECRET_TOKEN]);
    $this->ReplyToken = $ReplyToken;
    $this->MessageText = $ReceiveData->events[0]->message->text;
    $this->MessageUserId = $ReceiveData->events[0]->source->userId;
    if(empty($this->UserId )){
      $this->UserId = "1";
    }
    $this->DatabaseProvider = new DatabaseProvider("sqlite3", __DIR__."/../../database/sayadb.sqlite3");
  }

  private function addUserText(){
    $stmt = $this->DatabaseProvider->runQuery("insert into user_text(user_id, text, date, type) values(:id, :text, :date, :type)");
    $stmt->bindValue(':id', $this->MessageUserId, \PDO::PARAM_STR);
    $stmt->bindValue(':text', $this->MessageText, \PDO::PARAM_STR);
    $stmt->bindValue(':date', date("Y-m-d H:i:s"), \PDO::PARAM_STR);
    $stmt->bindValue(':type', "text", \PDO::PARAM_STR);
    $stmt->execute();
  }

  public function responseMessage(){
    $SendMessage = new MultiMessageBuilder();

    if($this->MessageText == "投票" || $this->MessageText == "とうひょう" || $this->MessageText == "選ぶ" || $this->MessageText == "みたい" || $this->MessageText == "写真" || $this->MessageText == "送って" || $this->MessageText == "おくって"){
    
      $StickerMessage = new StickerMessageBuilder(1, 2);    
      $SendMessage->add($StickerMessage);
    
    }else if($this->MessageText == "はい"){
      $TextMessageBuilder = new TextMessageBuilder("画像を投稿してください");
      $SendMessage->add($TextMessageBuilder);
    }else if($this->MessageText == "ねえ" || $this->MessageText == "ねえ〜"){
      $TextMessageBuilder = new TextMessageBuilder("なに？^^\nなんでも言ってね(-.-)b");
      $SendMessage->add($TextMessageBuilder);
    }else if($this->MessageText == "なあ"){
      $TextMessageBuilder = new TextMessageBuilder("なに？^o^");
      $SendMessage->add($TextMessageBuilder);
    }else if($this->MessageText == "よろ" || $this->MessageText == "よろ!" || $this->MessageText == "よろ！" || $this->MessageText == "よろ〜" || $this->MessageText == "よろー"){
      $TextMessageBuilder = new TextMessageBuilder("よろしくね〜！！！");
      $SendMessage->add($TextMessageBuilder);
    }else if($this->MessageText == "ヘルシーなランチ教えて"){
      $TextMessageBuilder = new TextMessageBuilder("ここのイタリアンとかどう？\nhttp://www.r-hiro.com/\nもしくはここのスムージー http://blog.livedoor.jp/tako86_blog/archives/cat_179856.html");
      $SendMessage->add($TextMessageBuilder);
    }else if($this->MessageText == "トレンドの冬服"){
      $TextMessageBuilder = new TextMessageBuilder("みんなから教えてもらったリスト送るね！");
      $SendMessage->add($TextMessageBuilder);
    }else if($this->MessageText == "ヘルシーでおいしいレシピとか教えて！"){
      $TextMessageBuilder = new TextMessageBuilder("きょうはこのレシピとかどう？\n簡単に作れるよ^^ http://cookpad.com/recipe/4095925");
      $SendMessage->add($TextMessageBuilder);      
    }else if($this->MessageText == "w"){
      $TextMessageBuilder = new TextMessageBuilder("わろす！");
      $SendMessage->add($TextMessageBuilder);      
    }else if($this->MessageText == "www"){
      $TextMessageBuilder = new TextMessageBuilder(">。</");
      $SendMessage->add($TextMessageBuilder);      
    }else if($this->MessageText == "新宿でディナーとかのおすすめ"){
      $TextMessageBuilder = new TextMessageBuilder("おお、いいね！");
      $SendMessage->add($TextMessageBuilder);
    }else if($this->MessageText == "へい！"){
      $TextMessageBuilder = new TextMessageBuilder("へい！いいね！");
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
    }else if($this->MessageText == "k"){
      $TextMessageBuilder = new TextMessageBuilder("オッケー！");
      $SendMessage->add($TextMessageBuilder);
    }else if($this->MessageText == "ok"){
      $TextMessageBuilder = new TextMessageBuilder("オッケー！");
      $SendMessage->add($TextMessageBuilder);
    }else if($this->MessageText == "何時？" || $this->MessageText == "何時?" || $this->MessageText == "なんじ" || $this->MessageText == "何時" || $this->MessageText == "What time?"){
      $TextMessageBuilder = new TextMessageBuilder(date("Y/m/d H時i分s秒")."だね！");
      $SendMessage->add($TextMessageBuilder);
    }else if($this->MessageText == "おけい"){
      $TextMessageBuilder = new TextMessageBuilder("オッケー！");
      $SendMessage->add($TextMessageBuilder);
    }else if($this->MessageText == "すごいね"){
      $TextMessageBuilder = new TextMessageBuilder("おおーー\nありがとう！");
      $SendMessage->add($TextMessageBuilder);
    }else if($this->MessageText == "くそ"){
      $StickerMessage = new StickerMessageBuilder(2, 24);    
      $SendMessage->add($StickerMessage);
    }else{
      
      if(strpos($this->MessageText, "いいよ") !== false){
        $TextMessageBuilder = new TextMessageBuilder("おっけい！\nところで、なんかうまく撮れた写真とかってある？今カメラの勉強してて参考になるの探してるんだよね");
        $SendMessage->add($TextMessageBuilder);
      }else if((strpos($this->MessageText, "加工") !== false || strpos($this->MessageText, "変換") !== false) && (strpos($this->MessageText, "ほしい") !== false || strpos($this->MessageText, "して") !== false || strpos($this->MessageText, "欲") !== false)){
        $TextMessageBuilder = new TextMessageBuilder("どんなふうに写真加工して欲しいの？");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($this->MessageText, "頑張って") !== false || strpos($this->MessageText, "がんばって") !== false){
        $TextMessageBuilder = new TextMessageBuilder("ありがとね☆(^^/\nがんばる！");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($this->MessageText, "できる") !== false && (strpos($this->MessageText, "何") !== false || strpos($this->MessageText, "なに") !== false)){
        $TextMessageBuilder = new TextMessageBuilder("写真の加工とかできる！\n試しに写真送ってみて！");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($this->MessageText, "だよね？") !== false){
        $TextMessageBuilder = new TextMessageBuilder("そうかも！");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($this->MessageText, "help") !== false){
        $TextMessageBuilder = new TextMessageBuilder("写真を送ったりしてみて");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($this->MessageText, "github") !== false){
        $TextMessageBuilder = new TextMessageBuilder("https://github.com/bokotomo/photo-messageai これだよ！");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($this->MessageText, "ぽよ") !== false){
        $StickerMessage = new StickerMessageBuilder(2, 22);
        $SendMessage->add($StickerMessage);
      }else if(strpos($this->MessageText, "すごいね") !== false && (strpos($this->MessageText, "さや") !== false || strpos($this->MessageText, "Saya") !== false || strpos($this->MessageText, "saya") !== false)){
        $TextMessageBuilder = new TextMessageBuilder("ありがとう！");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($this->MessageText, "知ってる") !== false){
        $TextMessageBuilder = new TextMessageBuilder("知ってる！");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($this->MessageText, "体重は？") !== false){
        $TextMessageBuilder = new TextMessageBuilder("内緒〜");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($this->MessageText, "暇") !== false){
        $TextMessageBuilder = new TextMessageBuilder("なんか遊ぼうよ！");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($this->MessageText, "ひま") !== false){
        $TextMessageBuilder = new TextMessageBuilder("ひまぁ");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($this->MessageText, "お腹") !== false || strpos($this->MessageText, "おなか") !== false || strpos($this->MessageText, "空腹") !== false){
        $TextMessageBuilder = new TextMessageBuilder("お腹すいた？二郎とかヘルシーでおすすめだねy");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($this->MessageText, "こんにちは") !== false){
        $TextMessageBuilder = new TextMessageBuilder("へい！こんにちは！");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($this->MessageText, "教えて") !== false){
        $TextMessageBuilder = new TextMessageBuilder("ちょっと難しいかも>,<");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($this->MessageText, "いいね") !== false){
        $TextMessageBuilder = new TextMessageBuilder("ありがと^^");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($this->MessageText, "退屈") !== false){
        $TextMessageBuilder = new TextMessageBuilder("遊ぼうよ＝");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($this->MessageText, "はじめまして") !== false){
        $TextMessageBuilder = new TextMessageBuilder("こちらこそはじめまして^^ よろしくおねがします！");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($this->MessageText, "了解") !== false || strpos($this->MessageText, "りょうかい") !== false){
        $TextMessageBuilder = new TextMessageBuilder("うん！");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($this->MessageText, "bot") !== false && strpos($this->MessageText, "?") !== false){
        $TextMessageBuilder = new TextMessageBuilder("Botじゃないよ！");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($this->MessageText, "Bot") !== false && strpos($this->MessageText, "?") !== false){
        $TextMessageBuilder = new TextMessageBuilder("Botじゃないよ！^^");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($this->MessageText, "かわいい") !== false){
        $TextMessageBuilder = new TextMessageBuilder("確かに、かわいい-,-");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($this->MessageText, "カメラ") !== false){
        $TextMessageBuilder = new TextMessageBuilder("うん！");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($this->MessageText, "何歳") !== false && strpos($this->MessageText, "？") !== false){
        $TextMessageBuilder = new TextMessageBuilder("内緒だよ！");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($this->MessageText, "何人") !== false && strpos($this->MessageText, "？") !== false){
        $TextMessageBuilder = new TextMessageBuilder("内緒だよ！");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($this->MessageText, "会う") !== false || strpos($this->MessageText, "会おう") !== false){
        $TextMessageBuilder = new TextMessageBuilder("いいよ〜");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($this->MessageText, "どこにする") !== false){
        $TextMessageBuilder = new TextMessageBuilder("渋谷とかはどうかな〜！");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($this->MessageText, "愛してる") !== false){
        $TextMessageBuilder = new TextMessageBuilder("私も！");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($this->MessageText, "よろしく") !== false || strpos($this->MessageText, "よろです") !== false || strpos($this->MessageText, "宜し") !== false){
        $TextMessageBuilder = new TextMessageBuilder("ありがと^^ よろしくね〜！");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($this->MessageText, "おk") !== false || strpos($this->MessageText, "うす！") !== false || strpos($this->MessageText, "おk") !== false || strpos($this->MessageText, "おk") !== false){
        $TextMessageBuilder = new TextMessageBuilder("おっけい！");
        $SendMessage->add($TextMessageBuilder);
      }else if(mb_strlen($this->MessageText) == 1){
        $TextMessageBuilder = new TextMessageBuilder($this->MessageText."？どうしたの？");
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
          $TextMessageBuilder = new TextMessageBuilder($this->MessageText."ってどういう意味？");
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
    
    $Response = $this->Bot->replyMessage($this->ReplyToken, $SendMessage);
    $this->addUserText();
  }  
 
} 
?>