<?php
namespace Saya\MessageControllor;

use Saya\MessageControllor;
use LINE\LINEBot\MessageBuilder\StickerMessageBuilder;
use TomoLib\DatabaseProvider;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\MessageBuilder\MultiMessageBuilder;

class TextMessageControllor
{
  private $eventData;
  private $bot;
  private $databaseProvider;
  private $userData;
  private $text;
  private $userPurpose;
  private $nounPurpose;
  private $verbPurpose;

  public function __construct($bot, $eventData, $userData){
    $this->eventData = $eventData;
    $this->bot = $bot;
    $this->userData = $userData;
    $this->databaseProvider = new DatabaseProvider(SQL_TYPE, LOCAL_DATABASE_PATH."/sayadb.sqlite3");
    $this->text = $this->eventData->getText();
  }

  private function addUserText(){
    $stmt = $this->databaseProvider->setSql("insert into user_text(user_id, text, date, type) values(:id, :text, :date, :type)");
    $stmt->bindValue(':id', $this->userData["user_id"], \PDO::PARAM_STR);
    $stmt->bindValue(':text', $this->text, \PDO::PARAM_STR);
    $stmt->bindValue(':date', date("Y-m-d H:i:s"), \PDO::PARAM_STR);
    $stmt->bindValue(':type', "text", \PDO::PARAM_STR);
    $stmt->execute();
  }

  private function analysisAutonomousVerb($text){
    if(strpos($text, "行") !== false){
      $this->verbPurpose = "WantToDoWith";
    }else if(strpos($text, "食べ") !== false || strpos($text, "食") !== false || strpos($text, "たべ") !== false){
      $this->verbPurpose = "WantToEat";
    }else if(strpos($text, "教") !== false || strpos($text, "知") !== false || strpos($text, "しっ") !== false){
      $this->verbPurpose = "WantToKnow";
    }else if(strpos($text, "送") !== false || strpos($text, "見") !== false || strpos($text, "ある") !== false){
      $this->verbPurpose = "WantToSend";
    }else if(strpos($text, "載") !== false || strpos($text, "のっけ") !== false){
      $this->verbPurpose = "WantToShareOnSNS";
    }else if($text == "し"){
      if($this->nounPurpose == "加工"){
        $this->verbPurpose = "WantToShareOnSNS";
      }else if($this->nounPurpose == "シェア"){
        $this->verbPurpose = "WantToShareOnSNS";
      }else if($this->nounPurpose == "食事"){
        $this->verbPurpose = "WantToGoMeal";
      }
    }else if(strpos($text, "変えて") !== false){
      $this->verbPurpose = "WantToConvertImage";
    }else if(strpos($text, "だよね") !== false){
      $this->verbPurpose = "WantToEmpathy";
    }
  }

  private function responseCertainText($text){
    if($text == "投票" || $text == "とうひょう" || $text == "選ぶ" || $text == "みたい" || $text == "写真" || $text == "送って" || $text == "おくって"){
      $responseMessageBuilder = new StickerMessageBuilder(1, 2);    
    }else if($text == "はい"){
      $responseMessageBuilder = new TextMessageBuilder("画像を投稿してください");
    }else if($text == "ねえ" || $text == "ねえ〜"){
      $responseMessageBuilder = new TextMessageBuilder("なに？^^\nなんでも言ってね(-.-)b");
    }else if($text == "なあ"){
      $responseMessageBuilder = new TextMessageBuilder("なに？^o^");
    }else if($text == "よろ" || $text == "よろ!" || $text == "よろ！" || $text == "よろ〜" || $text == "よろー"){
      $responseMessageBuilder = new TextMessageBuilder("よろしくね〜！！！");
    }else if($text == "ヘルシーなランチ教えて"){
      $responseMessageBuilder = new TextMessageBuilder("ここのイタリアンとかどう？\nhttp://www.r-hiro.com/\nもしくはここのスムージー http://blog.livedoor.jp/tako86_blog/archives/cat_179856.html");
    }else if($text == "トレンドの冬服"){
      $responseMessageBuilder = new TextMessageBuilder("みんなから教えてもらったリスト送るね！");
    }else if($text == "ヘルシーでおいしいレシピとか教えて！"){
      $responseMessageBuilder = new TextMessageBuilder("きょうはこのレシピとかどう？\n簡単に作れるよ^^ http://cookpad.com/recipe/4095925");
    }else if($text == "w"){
      $responseMessageBuilder = new TextMessageBuilder("わろす！");
    }else if($text == "www"){
      $responseMessageBuilder = new TextMessageBuilder(">。</");      
    }else if($text == "新宿でディナーとかのおすすめ"){
      $responseMessageBuilder = new TextMessageBuilder("おお、いいね！");
    }else if($text == "へい！"){
      $responseMessageBuilder = new TextMessageBuilder("へい！いいね！");
    }else if($text == "最近どう？"){
      $responseMessageBuilder = new TextMessageBuilder("写真のプロ目指してるよ！");
    }else if($text == "ある！"){
      $responseMessageBuilder = new TextMessageBuilder("みせて〜");
    }else if($text == "そうなんだ"){
      $responseMessageBuilder = new TextMessageBuilder("なんかうまく撮れた写真とかってある？");
    }else if($text == "k"){
      $responseMessageBuilder = new TextMessageBuilder("オッケー！");
    }else if($text == "ok"){
      $responseMessageBuilder = new TextMessageBuilder("オッケー！");
    }else if($text == "何時？" || $text == "何時?" || $text == "なんじ" || $text == "何時" || $text == "What time?"){
      $$responseMessageBuilder = new TextMessageBuilder(date("Y/m/d H時i分s秒")."だね！");
    }else if($text == "おけい"){
      $responseMessageBuilder = new TextMessageBuilder("オッケー！");
    }else if($text == "すごいね"){
      $responseMessageBuilder = new TextMessageBuilder("おおーー\nありがとう！");
    }else if($text == "くそ"){
      $responseMessageBuilder = new StickerMessageBuilder(2, 24);    
    }else{
      
      if(strpos($text, "いいよ") !== false){
        $responseMessageBuilder = new TextMessageBuilder("おっけい！\nところで、なんかうまく撮れた写真とかってある？今カメラの勉強してて参考になるの探してるんだよね");
      }else if((strpos($text, "加工") !== false || strpos($text, "変換") !== false) && (strpos($text, "ほしい") !== false || strpos($text, "して") !== false || strpos($text, "欲") !== false)){
        $responseMessageBuilder = new TextMessageBuilder("どんなふうに写真加工して欲しいの？");
      }else if(strpos($text, "頑張って") !== false || strpos($text, "がんばって") !== false){
        $responseMessageBuilder = new TextMessageBuilder("ありがとね☆(^^/\nがんばる！");
      }else if(strpos($text, "できる") !== false && (strpos($text, "何") !== false || strpos($text, "なに") !== false)){
        $responseMessageBuilder = new TextMessageBuilder("写真の加工とかできる！\n試しに写真送ってみて！");
      }else if(strpos($text, "だよね？") !== false){
        $responseMessageBuilder = new TextMessageBuilder("そうかも！");
      }else if(strpos($text, "help") !== false){
        $responseMessageBuilder = new TextMessageBuilder("写真を送ったりしてみて");
      }else if(strpos($text, "github") !== false){
        $responseMessageBuilder = new TextMessageBuilder("https://github.com/bokotomo/photo-messageai これだよ！");
      }else if(strpos($text, "ぽよ") !== false){
        $responseMessageBuilder = new StickerMessageBuilder(2, 22);
      }else if(strpos($text, "すごいね") !== false && (strpos($text, "さや") !== false || strpos($text, "Saya") !== false || strpos($text, "saya") !== false)){
        $responseMessageBuilder = new TextMessageBuilder("ありがとう！");
      }else if(strpos($text, "知ってる") !== false){
        $responseMessageBuilder = new TextMessageBuilder("知ってる！");
      }else if(strpos($text, "体重は？") !== false){
        $responseMessageBuilder = new TextMessageBuilder("内緒〜");
      }else if(strpos($text, "暇") !== false){
        $responseMessageBuilder = new TextMessageBuilder("なんか遊ぼうよ！");
      }else if(strpos($text, "ひま") !== false){
        $responseMessageBuilder = new TextMessageBuilder("ひまぁ");
      }else if(strpos($text, "お腹") !== false || strpos($text, "おなか") !== false || strpos($text, "空腹") !== false){
        $responseMessageBuilder = new TextMessageBuilder("お腹すいた？二郎とかヘルシーでおすすめだねy");
      }else if(strpos($text, "こんにちは") !== false){
        $responseMessageBuilder = new TextMessageBuilder("へい！こんにちは！");
      }else if(strpos($text, "教えて") !== false){
        $responseMessageBuilder = new TextMessageBuilder("ちょっと難しいかも>,<");
      }else if(strpos($text, "いいね") !== false){
        $responseMessageBuilder = new TextMessageBuilder("ありがと^^");
      }else if(strpos($text, "良い") !== false || strpos($text, "いい") !== false || strpos($text, "よい") !== false){
        $responseMessageBuilder = new TextMessageBuilder("お！ありがと☆^^");
      }else if(strpos($text, "退屈") !== false){
        $responseMessageBuilder = new TextMessageBuilder("遊ぼうよ＝");
      }else if(strpos($text, "怖い") !== false || strpos($text, "こわい") !== false){
        $responseMessageBuilder = new TextMessageBuilder("え！怖かった？");
      }else if(strpos($text, "はじめまして") !== false){
        $responseMessageBuilder = new TextMessageBuilder("こちらこそはじめまして^^ よろしくおねがします！");
      }else if(strpos($text, "了解") !== false || strpos($text, "りょうかい") !== false){
        $responseMessageBuilder = new TextMessageBuilder("うん！");
      }else if(strpos($text, "bot") !== false && strpos($text, "?") !== false){
        $responseMessageBuilder = new TextMessageBuilder("Botじゃないよ！");
      }else if(strpos($text, "Bot") !== false && strpos($text, "?") !== false){
        $responseMessageBuilder = new TextMessageBuilder("Botじゃないよ！^^");
      }else if(strpos($text, "かわいい") !== false){
        $responseMessageBuilder = new TextMessageBuilder("確かに、かわいい-,-");
      }else if(strpos($text, "カメラ") !== false){
        $responseMessageBuilder = new TextMessageBuilder("うん！");
      }else if(strpos($text, "何歳") !== false && strpos($text, "？") !== false){
        $responseMessageBuilder = new TextMessageBuilder("内緒だよ！");
      }else if(strpos($text, "何人") !== false && strpos($text, "？") !== false){
        $responseMessageBuilder = new TextMessageBuilder("内緒だよ！");
      }else if(strpos($text, "会う") !== false || strpos($text, "会おう") !== false){
        $responseMessageBuilder = new TextMessageBuilder("いいよ〜");
      }else if(strpos($text, "どこにする") !== false){
        $responseMessageBuilder = new TextMessageBuilder("渋谷とかはどうかな〜！");
      }else if(strpos($text, "愛してる") !== false){
        $responseMessageBuilder = new TextMessageBuilder("私も！");
      }else if(strpos($text, "よろしく") !== false || strpos($text, "よろです") !== false || strpos($text, "宜し") !== false){
        $responseMessageBuilder = new TextMessageBuilder("ありがと^^ よろしくね〜！");
      }else if(strpos($text, "おk") !== false || strpos($text, "うす！") !== false || strpos($text, "おk") !== false || strpos($text, "おk") !== false){
        $responseMessageBuilder = new TextMessageBuilder("おっけい！");
      }
    
    }
    return $responseMessageBuilder;
  }

  private function responseUnkwonText($text){
    if(mb_strlen($text) == 1){
      $responseUnkwonText = "{$text}？どうしたの？";
    }else{
      $v = rand(0,4);
      if($v==0){
        $responseUnkwonText = "この言葉は勉強中かな(..)";
      }else if($v==1){
        $responseUnkwonText = "どういう意味？(..)";
      }else if($v==2){
        $responseUnkwonText = "新しいことばや☆(..)";
      }else if($v==3){
        $responseUnkwonText = "さやにとって新しい単語でわからなかった☆(..)";
      }else if($v==4){
        $responseUnkwonText = "{$text}ってどういう意味？";
      }
    }
    $v = rand(0,11);
    if($v==0){
      $responseUnkwonText .= "\nかわいい写真とか送ってよ〜";
    }else if($v==1){
      $responseUnkwonText .= "\n写真送ってくれたら加工するよ！";
    }else if($v==2){
      $responseUnkwonText .= "\nてか、スムージーに最近はまってる";
    }else if($v==3){
      $responseUnkwonText .= "\nあと、意外と思うかもだけど、写真とか解析するの得意なんだ";
    }else if($v==4){
      $responseUnkwonText .= "\nお腹減った〜";
    }else if($v==5){
      $responseUnkwonText .= "\n面白い画像送ってよ";
    }else if($v==6){
      $responseUnkwonText .= "\n写真の加工最近勉強しててね\nイケてる写真送ってみてよ";      
    }else if($v==7){
      $responseUnkwonText .= "\nさや、癒される写真を最近撮ってるんだ!";
    }
    return $responseUnkwonText;
  }

  private function analysisText($text){
    $sendMessage = new MultiMessageBuilder();

    $runScriptPath = LOCAL_SCRIPT_PATH."/mecab_string_analysis/response_analysis.sh";
    $ShellRunStr = "sh {$runScriptPath} {$text}";
    exec($shellRunStr, $res, $returnVal);
    $textArray = $this->convertTextArray($res);

    foreach($textArray as $content){
      if($content[1] == "接頭詞"){
        if($content[2] == "形容詞接続"){
        }else if($content[2] == "数接続"){
        }else if($content[2] == "動詞接続"){
        }else if($content[2] == "名詞接続"){
        }
      }else if($content[1] == "名詞"){
        if($content[2] == "固有名詞"){
          if($content[3] == "地域"){
            if($content[4] == "一般"){
              $this->nounPurpose = $content[0];
            }else if($content[4] == "国"){
              $this->nounPurpose = $content[0];
            }
          }else if($content[3] == "人名"){
            if($content[4] == "一般"){
              $this->nounPurpose = $content[0];
            }else if($content[4] == "姓"){
              $this->nounPurpose = $content[0];
            }else if($content[4] == "名"){
              $this->nounPurpose = $content[0];
            }
          }else if($content[3] == "一般"){
            $this->nounPurpose = $content[0];
          }else if($content[3] == "組織"){
            $this->nounPurpose = $content[0];
          }
        }else if($content[2] == "一般"){
          $this->nounPurpose = $content[0];
        }else if($content[2] == "サ変接続"){
          $this->nounPurpose = $content[0];
        }else if($content[2] == "接尾"){
          $this->nounPurpose = $content[0];
        }
      }else if($content[1] == "動詞"){
        if($content[2] == "自立"){
          $this->analysisAutonomousVerb($content[0]);
        }else if($content[2] == "接尾"){
        }else if($content[2] == "非自立"){
        }
      }else if($content[1] == "副詞"){
        if($content[2] == "一般"){
        }else if($content[2] == "助詞類接続"){
        }
      }else if($content[1] == "助詞"){
        if($content[2] == "格助詞"){
        }else if($content[2] == "係助詞"){
        }else if($content[2] == "終助詞"){
        }else if($content[2] == "接続助詞"){
        }else if($content[2] == "特殊"){
        }else if($content[2] == "副詞化"){
        }else if($content[2] == "副助詞"){
        }else if($content[2] == "並立助詞"){
        }else if($content[2] == "終助詞"){
        }else if($content[2] == "並立助詞"){
        }else if($content[2] == "連体化"){
        }
      }else if($content[1] == "形容詞"){
        if($content[2] == "自立"){
        }else if($content[2] == "接尾"){
        }else if($content[2] == "非自立"){
        }
      }else if($content[1] == "連体詞"){
      }else if($content[1] == "助動詞"){
      }else if($content[1] == "記号"){
        if($content[2] == "句点"){
        }else if($content[2] == "読点"){
        }else if($content[2] == "空白"){
        }else if($content[2] == "アルファベット"){
        }else if($content[2] == "一般"){
        }else if($content[2] == "括弧開"){
        }else if($content[2] == "括弧閉"){
        }
      }else if($content[1] == "フィラー"){
      }
    }

    if($this->verbPurpose == "WantToKnow"){
      if(!empty($this->nounPurpose)){
        $this->userPurpose = $this->nounPurpose."を知りたいの？";
      }else{
        $this->userPurpose = "何を知りたいの？";
      }
      $textMessageBuilder = new TextMessageBuilder($this->userPurpose);
      $sendMessage->add($textMessageBuilder);
    }else if($this->verbPurpose == "WantToSend"){
      $this->userPurpose = $this->nounPurpose."を送ってほしいの？";
      $textMessageBuilder = new TextMessageBuilder($this->userPurpose);
      $sendMessage->add($textMessageBuilder);
    }else if($this->verbPurpose == "WantToDoWith"){
      $this->userPurpose = $this->nounPurpose."したいの？";
      $textMessageBuilder = new TextMessageBuilder($this->userPurpose);
      $sendMessage->add($textMessageBuilder);
    }else if($this->verbPurpose == "WantToConvertImage"){
      $this->userPurpose = "いいよ！写真送って";
      $textMessageBuilder = new TextMessageBuilder($this->userPurpose);
      $sendMessage->add($textMessageBuilder);
    }else if($this->verbPurpose == "WantToEat"){
      $this->userPurpose = $this->nounPurpose."たべたいの？";
      $textMessageBuilder = new TextMessageBuilder($this->userPurpose);
      $sendMessage->add($textMessageBuilder);
    }else if($this->verbPurpose == "WantToShareOnSNS"){
      $this->userPurpose = "なら送ってくれた写真を綺麗にするね！";
      $textMessageBuilder = new TextMessageBuilder($this->userPurpose);
      $sendMessage->add($textMessageBuilder);
    }else{
      $certainMessageBuilder = $this->responseCertainText($text);
      if(!empty($certainMessageBuilder)){
        $sendMessage->add($certainMessageBuilder);
      }else{
        $this->userPurpose = $this->responseUnkwonText($text);
        $textMessageBuilder = new TextMessageBuilder($this->userPurpose);
        $sendMessage->add($textMessageBuilder);
      }
    }

    return $sendMessage;
  }

  private function convertTextArray($res){
    $array = array();
    foreach($res as $r){
      if($r != "EOS"){
        $r = str_replace("\t", ",", $r);
        array_push($array, explode(",", $r));
      }
    }
    return $array;
  }

  public function responseMessage(){
    $sendMessage = $this->analysisText($this->text);
    $response = $this->bot->replyMessage($this->eventData->getReplyToken(), $sendMessage);
    $this->addUserText();
  }
}