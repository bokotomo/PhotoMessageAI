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

  private function analysisText2($Text){
    
  }

  private function analysisText($Text){
    $SendMessage = new MultiMessageBuilder();

    $RunScriptPath = LOCAL_SCRIPT_PATH."/mecab_string_analysis/response_analysis.sh";
    $ShellRunStr = "sh {$RunScriptPath} ".$this->Text;
    exec($ShellRunStr, $Res, $ReturnVal);
    $TextArray = $this->convertTextArray($Res);
    $TextMessageBuilder = new TextMessageBuilder(json_encode($TextArray));
    //$SendMessage->add($TextMessageBuilder); 
    
    $UserPurpose;
    $WantToKnow = false;
    $WantToSend = false;
    $WantToDoWith = false;
    $WantObject = "";

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
              $WantObject = $content[0];
            }else if($content[4] == "国"){
              $WantObject = $content[0];
            }
          }else if($content[3] == "人名"){
            if($content[4] == "一般"){
              $WantObject = $content[0];
            }else if($content[4] == "姓"){
              $WantObject = $content[0];
            }else if($content[4] == "名"){
              $WantObject = $content[0];
            }
          }else if($content[3] == "一般"){
            $WantObject = $content[0];
          }else if($content[3] == "組織"){
            $WantObject = $content[0];
          }
        }else if($content[2] == "一般"){
          $WantObject = $content[0];
        }else if($content[2] == "サ変接続"){
          $WantObject = $content[0];
        }else if($content[2] == "接尾"){
          $WantObject = $content[0];
        }
      }else if($content[1] == "動詞"){
        if($content[2] == "自立"){
          if(strpos($Text, "行") !== false){
            $WantToDoWith = true;
          }else if(strpos($Text, "教") !== false || strpos($Text, "知") !== false || strpos($Text, "しっ") !== false){
            $WantToKnow = true;
          }else if(strpos($Text, "送") !== false || strpos($Text, "見") !== false || strpos($Text, "ある") !== false){
            $WantToSend = true;
          }
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

    if($WantToKnow){
      $UserPurpose = $WantObject."を知りたいの？";
    }else if($WantToSend){
      $UserPurpose = $WantObject."を送ってほしいの？";
    }else if($WantToDoWith){
      $UserPurpose = $WantObject."したいの？";
    }
    $TextMessageBuilder = new TextMessageBuilder($UserPurpose);
    $SendMessage->add($TextMessageBuilder);

    if($Text == "投票" || $Text == "とうひょう" || $Text == "選ぶ" || $Text == "みたい" || $Text == "写真" || $Text == "送って" || $Text == "おくって"){
      $StickerMessage = new StickerMessageBuilder(1, 2);    
      $SendMessage->add($StickerMessage);
    }else if($Text == "はい"){
      $TextMessageBuilder = new TextMessageBuilder("画像を投稿してください");
      $SendMessage->add($TextMessageBuilder);
    }else if($Text == "ねえ" || $Text == "ねえ〜"){
      $TextMessageBuilder = new TextMessageBuilder("なに？^^\nなんでも言ってね(-.-)b");
      $SendMessage->add($TextMessageBuilder);
    }else if($Text == "なあ"){
      $TextMessageBuilder = new TextMessageBuilder("なに？^o^");
      $SendMessage->add($TextMessageBuilder);
    }else if($Text == "よろ" || $Text == "よろ!" || $Text == "よろ！" || $Text == "よろ〜" || $Text == "よろー"){
      $TextMessageBuilder = new TextMessageBuilder("よろしくね〜！！！");
      $SendMessage->add($TextMessageBuilder);
    }else if($Text == "ヘルシーなランチ教えて"){
      $TextMessageBuilder = new TextMessageBuilder("ここのイタリアンとかどう？\nhttp://www.r-hiro.com/\nもしくはここのスムージー http://blog.livedoor.jp/tako86_blog/archives/cat_179856.html");
      $SendMessage->add($TextMessageBuilder);
    }else if($Text == "トレンドの冬服"){
      $TextMessageBuilder = new TextMessageBuilder("みんなから教えてもらったリスト送るね！");
      $SendMessage->add($TextMessageBuilder);
    }else if($Text == "ヘルシーでおいしいレシピとか教えて！"){
      $TextMessageBuilder = new TextMessageBuilder("きょうはこのレシピとかどう？\n簡単に作れるよ^^ http://cookpad.com/recipe/4095925");
      $SendMessage->add($TextMessageBuilder);      
    }else if($Text == "w"){
      $TextMessageBuilder = new TextMessageBuilder("わろす！");
      $SendMessage->add($TextMessageBuilder);      
    }else if($Text == "www"){
      $TextMessageBuilder = new TextMessageBuilder(">。</");
      $SendMessage->add($TextMessageBuilder);      
    }else if($Text == "新宿でディナーとかのおすすめ"){
      $TextMessageBuilder = new TextMessageBuilder("おお、いいね！");
      $SendMessage->add($TextMessageBuilder);
    }else if($Text == "へい！"){
      $TextMessageBuilder = new TextMessageBuilder("へい！いいね！");
      $SendMessage->add($TextMessageBuilder);
    }else if($Text == "最近どう？"){
      $TextMessageBuilder = new TextMessageBuilder("写真のプロ目指してるよ！");
      $SendMessage->add($TextMessageBuilder);
    }else if($Text == "ある！"){
      $TextMessageBuilder = new TextMessageBuilder("みせて〜");
      $SendMessage->add($TextMessageBuilder);
    }else if($Text == "そうなんだ"){
      $TextMessageBuilder = new TextMessageBuilder("なんかうまく撮れた写真とかってある？");
      $SendMessage->add($TextMessageBuilder);
    }else if($Text == "k"){
      $TextMessageBuilder = new TextMessageBuilder("オッケー！");
      $SendMessage->add($TextMessageBuilder);
    }else if($Text == "ok"){
      $TextMessageBuilder = new TextMessageBuilder("オッケー！");
      $SendMessage->add($TextMessageBuilder);
    }else if($Text == "何時？" || $Text == "何時?" || $Text == "なんじ" || $Text == "何時" || $Text == "What time?"){
      $TextMessageBuilder = new TextMessageBuilder(date("Y/m/d H時i分s秒")."だね！");
      $SendMessage->add($TextMessageBuilder);
    }else if($Text == "おけい"){
      $TextMessageBuilder = new TextMessageBuilder("オッケー！");
      $SendMessage->add($TextMessageBuilder);
    }else if($Text == "すごいね"){
      $TextMessageBuilder = new TextMessageBuilder("おおーー\nありがとう！");
      $SendMessage->add($TextMessageBuilder);
    }else if($Text == "くそ"){
      $StickerMessage = new StickerMessageBuilder(2, 24);    
      $SendMessage->add($StickerMessage);
    }else{
      
      if(strpos($Text, "いいよ") !== false){
        $TextMessageBuilder = new TextMessageBuilder("おっけい！\nところで、なんかうまく撮れた写真とかってある？今カメラの勉強してて参考になるの探してるんだよね");
        $SendMessage->add($TextMessageBuilder);
      }else if((strpos($Text, "加工") !== false || strpos($Text, "変換") !== false) && (strpos($Text, "ほしい") !== false || strpos($Text, "して") !== false || strpos($Text, "欲") !== false)){
        $TextMessageBuilder = new TextMessageBuilder("どんなふうに写真加工して欲しいの？");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($Text, "頑張って") !== false || strpos($Text, "がんばって") !== false){
        $TextMessageBuilder = new TextMessageBuilder("ありがとね☆(^^/\nがんばる！");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($Text, "できる") !== false && (strpos($Text, "何") !== false || strpos($Text, "なに") !== false)){
        $TextMessageBuilder = new TextMessageBuilder("写真の加工とかできる！\n試しに写真送ってみて！");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($Text, "だよね？") !== false){
        $TextMessageBuilder = new TextMessageBuilder("そうかも！");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($Text, "help") !== false){
        $TextMessageBuilder = new TextMessageBuilder("写真を送ったりしてみて");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($Text, "github") !== false){
        $TextMessageBuilder = new TextMessageBuilder("https://github.com/bokotomo/photo-messageai これだよ！");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($Text, "ぽよ") !== false){
        $StickerMessage = new StickerMessageBuilder(2, 22);
        $SendMessage->add($StickerMessage);
      }else if(strpos($Text, "すごいね") !== false && (strpos($Text, "さや") !== false || strpos($Text, "Saya") !== false || strpos($Text, "saya") !== false)){
        $TextMessageBuilder = new TextMessageBuilder("ありがとう！");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($Text, "知ってる") !== false){
        $TextMessageBuilder = new TextMessageBuilder("知ってる！");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($Text, "体重は？") !== false){
        $TextMessageBuilder = new TextMessageBuilder("内緒〜");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($Text, "暇") !== false){
        $TextMessageBuilder = new TextMessageBuilder("なんか遊ぼうよ！");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($Text, "ひま") !== false){
        $TextMessageBuilder = new TextMessageBuilder("ひまぁ");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($Text, "お腹") !== false || strpos($Text, "おなか") !== false || strpos($Text, "空腹") !== false){
        $TextMessageBuilder = new TextMessageBuilder("お腹すいた？二郎とかヘルシーでおすすめだねy");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($Text, "こんにちは") !== false){
        $TextMessageBuilder = new TextMessageBuilder("へい！こんにちは！");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($Text, "教えて") !== false){
        $TextMessageBuilder = new TextMessageBuilder("ちょっと難しいかも>,<");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($Text, "いいね") !== false){
        $TextMessageBuilder = new TextMessageBuilder("ありがと^^");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($Text, "退屈") !== false){
        $TextMessageBuilder = new TextMessageBuilder("遊ぼうよ＝");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($Text, "はじめまして") !== false){
        $TextMessageBuilder = new TextMessageBuilder("こちらこそはじめまして^^ よろしくおねがします！");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($Text, "了解") !== false || strpos($Text, "りょうかい") !== false){
        $TextMessageBuilder = new TextMessageBuilder("うん！");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($Text, "bot") !== false && strpos($Text, "?") !== false){
        $TextMessageBuilder = new TextMessageBuilder("Botじゃないよ！");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($Text, "Bot") !== false && strpos($Text, "?") !== false){
        $TextMessageBuilder = new TextMessageBuilder("Botじゃないよ！^^");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($Text, "かわいい") !== false){
        $TextMessageBuilder = new TextMessageBuilder("確かに、かわいい-,-");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($Text, "カメラ") !== false){
        $TextMessageBuilder = new TextMessageBuilder("うん！");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($Text, "何歳") !== false && strpos($Text, "？") !== false){
        $TextMessageBuilder = new TextMessageBuilder("内緒だよ！");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($Text, "何人") !== false && strpos($Text, "？") !== false){
        $TextMessageBuilder = new TextMessageBuilder("内緒だよ！");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($Text, "会う") !== false || strpos($Text, "会おう") !== false){
        $TextMessageBuilder = new TextMessageBuilder("いいよ〜");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($Text, "どこにする") !== false){
        $TextMessageBuilder = new TextMessageBuilder("渋谷とかはどうかな〜！");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($Text, "愛してる") !== false){
        $TextMessageBuilder = new TextMessageBuilder("私も！");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($Text, "よろしく") !== false || strpos($Text, "よろです") !== false || strpos($Text, "宜し") !== false){
        $TextMessageBuilder = new TextMessageBuilder("ありがと^^ よろしくね〜！");
        $SendMessage->add($TextMessageBuilder);
      }else if(strpos($Text, "おk") !== false || strpos($Text, "うす！") !== false || strpos($Text, "おk") !== false || strpos($Text, "おk") !== false){
        $TextMessageBuilder = new TextMessageBuilder("おっけい！");
        $SendMessage->add($TextMessageBuilder);
      }else if(mb_strlen($Text) == 1){
        $TextMessageBuilder = new TextMessageBuilder($Text."？どうしたの？");
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
          $TextMessageBuilder = new TextMessageBuilder($Text."ってどういう意味？");
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