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
  private $Text;
  private $UserPurpose;
  private $NounPurpose;
  private $VerbPurpose;

  public function __construct($Bot, $EventData, $UserData){
    $this->EventData = $EventData;
    $this->Bot = $Bot;
    $this->UserData = $UserData;
    $this->DatabaseProvider = new DatabaseProvider(SQL_TYPE, LOCAL_DATABASE_PATH."/sayadb.sqlite3");
    $this->Text = $this->EventData->getText();
  }

  private function addUserText(){
    $stmt = $this->DatabaseProvider->setSql("insert into user_text(user_id, text, date, type) values(:id, :text, :date, :type)");
    $stmt->bindValue(':id', $this->UserData["user_id"], \PDO::PARAM_STR);
    $stmt->bindValue(':text', $this->Text, \PDO::PARAM_STR);
    $stmt->bindValue(':date', date("Y-m-d H:i:s"), \PDO::PARAM_STR);
    $stmt->bindValue(':type', "text", \PDO::PARAM_STR);
    $stmt->execute();
  }

  private function analysisAutonomousVerb($Text){
    if(strpos($Text, "行") !== false){
      $this->VerbPurpose = "WantToDoWith";
    }else if(strpos($Text, "教") !== false || strpos($Text, "知") !== false || strpos($Text, "しっ") !== false){
      $this->VerbPurpose = "WantToKnow";
    }else if(strpos($Text, "送") !== false || strpos($Text, "見") !== false || strpos($Text, "ある") !== false){
      $this->VerbPurpose = "WantToSend";
    }else if(strpos($Text, "載") !== false || strpos($Text, "のっけ") !== false){
      $this->VerbPurpose = "WantToShareOnSNS";
    }else if($Text == "し"){
      if($this->NounPurpose == "加工"){
        $this->VerbPurpose = "WantToShareOnSNS";
      }else if($this->NounPurpose == "シェア"){
        $this->VerbPurpose = "WantToShareOnSNS";
      }else if($this->NounPurpose == "食事"){
        $this->VerbPurpose = "WantToGoMeal";
      }
    }else if(strpos($Text, "変えて") !== false){
      $this->VerbPurpose = "WantToConvertImage";
    }else if(strpos($Text, "だよね") !== false){
      $this->VerbPurpose = "WantToEmpathy";
    }
  }

  private function responseCertainText($Text){
    if($Text == "投票" || $Text == "とうひょう" || $Text == "選ぶ" || $Text == "みたい" || $Text == "写真" || $Text == "送って" || $Text == "おくって"){
      $ResponseMessageBuilder = new StickerMessageBuilder(1, 2);    
    }else if($Text == "はい"){
      $ResponseMessageBuilder = new TextMessageBuilder("画像を投稿してください");
    }else if($Text == "ねえ" || $Text == "ねえ〜"){
      $ResponseMessageBuilder = new TextMessageBuilder("なに？^^\nなんでも言ってね(-.-)b");
    }else if($Text == "なあ"){
      $ResponseMessageBuilder = new TextMessageBuilder("なに？^o^");
    }else if($Text == "よろ" || $Text == "よろ!" || $Text == "よろ！" || $Text == "よろ〜" || $Text == "よろー"){
      $ResponseMessageBuilder = new TextMessageBuilder("よろしくね〜！！！");
    }else if($Text == "ヘルシーなランチ教えて"){
      $ResponseMessageBuilder = new TextMessageBuilder("ここのイタリアンとかどう？\nhttp://www.r-hiro.com/\nもしくはここのスムージー http://blog.livedoor.jp/tako86_blog/archives/cat_179856.html");
    }else if($Text == "トレンドの冬服"){
      $ResponseMessageBuilder = new TextMessageBuilder("みんなから教えてもらったリスト送るね！");
    }else if($Text == "ヘルシーでおいしいレシピとか教えて！"){
      $ResponseMessageBuilder = new TextMessageBuilder("きょうはこのレシピとかどう？\n簡単に作れるよ^^ http://cookpad.com/recipe/4095925");
    }else if($Text == "w"){
      $ResponseMessageBuilder = new TextMessageBuilder("わろす！");
    }else if($Text == "www"){
      $ResponseMessageBuilder = new TextMessageBuilder(">。</");      
    }else if($Text == "新宿でディナーとかのおすすめ"){
      $ResponseMessageBuilder = new TextMessageBuilder("おお、いいね！");
    }else if($Text == "へい！"){
      $ResponseMessageBuilder = new TextMessageBuilder("へい！いいね！");
    }else if($Text == "最近どう？"){
      $ResponseMessageBuilder = new TextMessageBuilder("写真のプロ目指してるよ！");
    }else if($Text == "ある！"){
      $ResponseMessageBuilder = new TextMessageBuilder("みせて〜");
    }else if($Text == "そうなんだ"){
      $ResponseMessageBuilder = new TextMessageBuilder("なんかうまく撮れた写真とかってある？");
    }else if($Text == "k"){
      $ResponseMessageBuilder = new TextMessageBuilder("オッケー！");
    }else if($Text == "ok"){
      $ResponseMessageBuilder = new TextMessageBuilder("オッケー！");
    }else if($Text == "何時？" || $Text == "何時?" || $Text == "なんじ" || $Text == "何時" || $Text == "What time?"){
      $$ResponseMessageBuilder = new TextMessageBuilder(date("Y/m/d H時i分s秒")."だね！");
    }else if($Text == "おけい"){
      $ResponseMessageBuilder = new TextMessageBuilder("オッケー！");
    }else if($Text == "すごいね"){
      $ResponseMessageBuilder = new TextMessageBuilder("おおーー\nありがとう！");
    }else if($Text == "くそ"){
      $ResponseMessageBuilder = new StickerMessageBuilder(2, 24);    
    }else{
      
      if(strpos($Text, "いいよ") !== false){
        $ResponseMessageBuilder = new TextMessageBuilder("おっけい！\nところで、なんかうまく撮れた写真とかってある？今カメラの勉強してて参考になるの探してるんだよね");
      }else if((strpos($Text, "加工") !== false || strpos($Text, "変換") !== false) && (strpos($Text, "ほしい") !== false || strpos($Text, "して") !== false || strpos($Text, "欲") !== false)){
        $ResponseMessageBuilder = new TextMessageBuilder("どんなふうに写真加工して欲しいの？");
      }else if(strpos($Text, "頑張って") !== false || strpos($Text, "がんばって") !== false){
        $ResponseMessageBuilder = new TextMessageBuilder("ありがとね☆(^^/\nがんばる！");
      }else if(strpos($Text, "できる") !== false && (strpos($Text, "何") !== false || strpos($Text, "なに") !== false)){
        $ResponseMessageBuilder = new TextMessageBuilder("写真の加工とかできる！\n試しに写真送ってみて！");
      }else if(strpos($Text, "だよね？") !== false){
        $ResponseMessageBuilder = new TextMessageBuilder("そうかも！");
      }else if(strpos($Text, "help") !== false){
        $ResponseMessageBuilder = new TextMessageBuilder("写真を送ったりしてみて");
      }else if(strpos($Text, "github") !== false){
        $ResponseMessageBuilder = new TextMessageBuilder("https://github.com/bokotomo/photo-messageai これだよ！");
      }else if(strpos($Text, "ぽよ") !== false){
        $ResponseMessageBuilder = new StickerMessageBuilder(2, 22);
      }else if(strpos($Text, "すごいね") !== false && (strpos($Text, "さや") !== false || strpos($Text, "Saya") !== false || strpos($Text, "saya") !== false)){
        $ResponseMessageBuilder = new TextMessageBuilder("ありがとう！");
      }else if(strpos($Text, "知ってる") !== false){
        $ResponseMessageBuilder = new TextMessageBuilder("知ってる！");
      }else if(strpos($Text, "体重は？") !== false){
        $ResponseMessageBuilder = new TextMessageBuilder("内緒〜");
      }else if(strpos($Text, "暇") !== false){
        $ResponseMessageBuilder = new TextMessageBuilder("なんか遊ぼうよ！");
      }else if(strpos($Text, "ひま") !== false){
        $ResponseMessageBuilder = new TextMessageBuilder("ひまぁ");
      }else if(strpos($Text, "お腹") !== false || strpos($Text, "おなか") !== false || strpos($Text, "空腹") !== false){
        $ResponseMessageBuilder = new TextMessageBuilder("お腹すいた？二郎とかヘルシーでおすすめだねy");
      }else if(strpos($Text, "こんにちは") !== false){
        $ResponseMessageBuilder = new TextMessageBuilder("へい！こんにちは！");
      }else if(strpos($Text, "教えて") !== false){
        $ResponseMessageBuilder = new TextMessageBuilder("ちょっと難しいかも>,<");
      }else if(strpos($Text, "いいね") !== false){
        $ResponseMessageBuilder = new TextMessageBuilder("ありがと^^");
      }else if(strpos($Text, "退屈") !== false){
        $ResponseMessageBuilder = new TextMessageBuilder("遊ぼうよ＝");
      }else if(strpos($Text, "はじめまして") !== false){
        $ResponseMessageBuilder = new TextMessageBuilder("こちらこそはじめまして^^ よろしくおねがします！");
      }else if(strpos($Text, "了解") !== false || strpos($Text, "りょうかい") !== false){
        $ResponseMessageBuilder = new TextMessageBuilder("うん！");
      }else if(strpos($Text, "bot") !== false && strpos($Text, "?") !== false){
        $ResponseMessageBuilder = new TextMessageBuilder("Botじゃないよ！");
      }else if(strpos($Text, "Bot") !== false && strpos($Text, "?") !== false){
        $ResponseMessageBuilder = new TextMessageBuilder("Botじゃないよ！^^");
      }else if(strpos($Text, "かわいい") !== false){
        $ResponseMessageBuilder = new TextMessageBuilder("確かに、かわいい-,-");
      }else if(strpos($Text, "カメラ") !== false){
        $ResponseMessageBuilder = new TextMessageBuilder("うん！");
      }else if(strpos($Text, "何歳") !== false && strpos($Text, "？") !== false){
        $ResponseMessageBuilder = new TextMessageBuilder("内緒だよ！");
      }else if(strpos($Text, "何人") !== false && strpos($Text, "？") !== false){
        $ResponseMessageBuilder = new TextMessageBuilder("内緒だよ！");
      }else if(strpos($Text, "会う") !== false || strpos($Text, "会おう") !== false){
        $ResponseMessageBuilder = new TextMessageBuilder("いいよ〜");
      }else if(strpos($Text, "どこにする") !== false){
        $ResponseMessageBuilder = new TextMessageBuilder("渋谷とかはどうかな〜！");
      }else if(strpos($Text, "愛してる") !== false){
        $ResponseMessageBuilder = new TextMessageBuilder("私も！");
      }else if(strpos($Text, "よろしく") !== false || strpos($Text, "よろです") !== false || strpos($Text, "宜し") !== false){
        $ResponseMessageBuilder = new TextMessageBuilder("ありがと^^ よろしくね〜！");
      }else if(strpos($Text, "おk") !== false || strpos($Text, "うす！") !== false || strpos($Text, "おk") !== false || strpos($Text, "おk") !== false){
        $ResponseMessageBuilder = new TextMessageBuilder("おっけい！");
      }
    
    }
    return $ResponseMessageBuilder;
  }

  private function responseUnkwonText($Text){
    if(mb_strlen($Text) == 1){
      $responseUnkwonText = "{$Text}？どうしたの？";
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
        $responseUnkwonText = "{$Text}ってどういう意味？";
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

  private function analysisText($Text){
    $SendMessage = new MultiMessageBuilder();

    $RunScriptPath = LOCAL_SCRIPT_PATH."/mecab_string_analysis/response_analysis.sh";
    $ShellRunStr = "sh {$RunScriptPath} {$Text}";
    exec($ShellRunStr, $Res, $ReturnVal);
    $TextArray = $this->convertTextArray($Res);

    foreach($TextArray as $content){
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
              $this->NounPurpose = $content[0];
            }else if($content[4] == "国"){
              $this->NounPurpose = $content[0];
            }
          }else if($content[3] == "人名"){
            if($content[4] == "一般"){
              $this->NounPurpose = $content[0];
            }else if($content[4] == "姓"){
              $this->NounPurpose = $content[0];
            }else if($content[4] == "名"){
              $this->NounPurpose = $content[0];
            }
          }else if($content[3] == "一般"){
            $this->NounPurpose = $content[0];
          }else if($content[3] == "組織"){
            $this->NounPurpose = $content[0];
          }
        }else if($content[2] == "一般"){
          $this->NounPurpose = $content[0];
        }else if($content[2] == "サ変接続"){
          $this->NounPurpose = $content[0];
        }else if($content[2] == "接尾"){
          $this->NounPurpose = $content[0];
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

    if($this->VerbPurpose == "WantToKnow"){
      if(!empty($this->NounPurpose)){
        $this->UserPurpose = $this->NounPurpose."を知りたいの？";
      }else{
        $this->UserPurpose = "何を知りたいの？";
      }
      $TextMessageBuilder = new TextMessageBuilder($this->UserPurpose);
      $SendMessage->add($TextMessageBuilder);
    }else if($this->VerbPurpose == "WantToSend"){
      $this->UserPurpose = $this->NounPurpose."を送ってほしいの？";
      $TextMessageBuilder = new TextMessageBuilder($this->UserPurpose);
      $SendMessage->add($TextMessageBuilder);
    }else if($this->VerbPurpose == "WantToDoWith"){
      $this->UserPurpose = $this->NounPurpose."したいの？";
      $TextMessageBuilder = new TextMessageBuilder($this->UserPurpose);
      $SendMessage->add($TextMessageBuilder);
    }else if($this->VerbPurpose == "WantToConvertImage"){
      $this->UserPurpose = "いいよ！写真送って";
      $TextMessageBuilder = new TextMessageBuilder($this->UserPurpose);
      $SendMessage->add($TextMessageBuilder);
    }else if($this->VerbPurpose == "WantToShareOnSNS"){
      $this->UserPurpose = "なら送ってくれた写真を綺麗にするね！";
      $TextMessageBuilder = new TextMessageBuilder($this->UserPurpose);
      $SendMessage->add($TextMessageBuilder);
    }else{
      $CertainMessageBuilder = $this->responseCertainText($Text);
      if(!empty($CertainMessageBuilder)){
        $SendMessage->add($CertainMessageBuilder);
      }else{
        $this->UserPurpose = $this->responseUnkwonText($Text);
        $TextMessageBuilder = new TextMessageBuilder($this->UserPurpose);
        $SendMessage->add($TextMessageBuilder);
      }
    }

    return $SendMessage;
  }

  private function convertTextArray($Res){
    $Array = array();
    foreach($Res as $res){
      if($res != "EOS"){
        $res = str_replace("\t", ",", $res);
        array_push($Array, explode(",", $res));
      }
    }
    return $Array;
  }

  public function responseMessage(){
    $SendMessage = $this->analysisText($this->Text);
    $Response = $this->Bot->replyMessage($this->EventData->getReplyToken(), $SendMessage);
    $this->addUserText();
  }  

}