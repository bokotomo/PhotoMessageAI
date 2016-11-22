<?php
namespace Saya\MessageControllor;

use Saya\MessageControllor;
use LINE\LINEBot\MessageBuilder\StickerMessageBuilder;
use LINE\LINEBot\MessageBuilder\MultiMessageBuilder;
use TomoLib\DatabaseProvider;

class StickerMessageControllor
{
  private $EventData;
  private $Bot;
  private $DatabaseProvider;
  private $UserData;

  public function __construct($Bot, $EventData, $UserData){
    $this->EventData = $EventData;
    $this->Bot = $Bot;
    $this->UserData = $UserData;
    $this->DatabaseProvider = new DatabaseProvider("sqlite3", __DIR__."/../../database/sayadb.sqlite3");
  }

  private function addUserText(){
    $stmt = $this->DatabaseProvider->setSql("insert into user_text(user_id, text, date, type) values(:id, :text, :date, :type)");
    $stmt->bindValue(':id', $this->UserData["user_id"], \PDO::PARAM_STR);
    $stmt->bindValue(':text', $this->EventData->getStickerId().":".$this->EventData->getPackageId(), \PDO::PARAM_STR);
    $stmt->bindValue(':date', date("Y-m-d H:i:s"), \PDO::PARAM_STR);
    $stmt->bindValue(':type', "sticker", \PDO::PARAM_STR);
    $stmt->execute();
  }

  public function responseMessage(){
    $StickerMessage = new StickerMessageBuilder(1, 2);
    $Message = new MultiMessageBuilder();
    $Message->add($StickerMessage);
    $response = $this->Bot->replyMessage($this->EventData->getReplyToken(), $Message);
    $this->addUserText();
  }
}