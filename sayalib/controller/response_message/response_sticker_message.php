<?php
namespace Saya\MessageControllor;

use Saya\MessageControllor;
use \LINE\LINEBot\HTTPClient\CurlHTTPClient;
use \LINE\LINEBot;
use LINE\LINEBot\MessageBuilder\StickerMessageBuilder;
use LINE\LINEBot\MessageBuilder\MultiMessageBuilder;
use TomoLib\DatabaseProvider;

class StickerMessageControllor
{
  private $ReplyToken;
  private $Bot;
  private $MessageStickerId;
  private $MessagePackageId;
  private $MessageUserId;

  public function __construct($ReplyToken, $ReceiveData){
    $httpClient = new CurlHTTPClient(ACCESS_TOKEN);
    $this->Bot = new LINEBot($httpClient, ['channelSecret' => SECRET_TOKEN]);
    $this->ReplyToken = $ReplyToken;
    $this->MessageUserId = $ReceiveData->events[0]->source->userId;
    $this->MessageStickerId = $ReceiveData->events[0]->message->stickerId;
    $this->MessagePackageId = $ReceiveData->events[0]->message->packageId;
    if(empty($this->UserId )){
      $this->UserId = "1";
    }
    $this->DatabaseProvider = new DatabaseProvider("sqlite3", __DIR__."/../../database/sayadb.sqlite3");
  }

  private function addUserText(){
    $stmt = $this->DatabaseProvider->setSql("insert into user_text(user_id, text, date, type) values(:id, :text, :date, :type)");
    $stmt->bindValue(':id', $this->MessageUserId, \PDO::PARAM_STR);
    $stmt->bindValue(':text', $this->MessageStickerId.":".$this->MessagePackageId, \PDO::PARAM_STR);
    $stmt->bindValue(':date', date("Y-m-d H:i:s"), \PDO::PARAM_STR);
    $stmt->bindValue(':type', "sticker", \PDO::PARAM_STR);
    $stmt->execute();
  }

  public function responseMessage(){
    $StickerMessage = new StickerMessageBuilder(1, 2);
    $message = new MultiMessageBuilder();
    $message->add($StickerMessage);
    $response = $this->Bot->replyMessage($this->ReplyToken, $message);
    $this->addUserText();
  }
}
?>