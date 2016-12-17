<?php
namespace Saya\MessageControllor;

use Saya\MessageControllor;
use LINE\LINEBot\MessageBuilder\StickerMessageBuilder;
use LINE\LINEBot\MessageBuilder\MultiMessageBuilder;
use TomoLib\DatabaseProvider;

class StickerMessageControllor
{
  private $eventData;
  private $bot;
  private $databaseProvider;
  private $userData;

  public function __construct($bot, $eventData, $userData){
    $this->eventData = $eventData;
    $this->bot = $bot;
    $this->userData = $userData;
    $this->databaseProvider = new DatabaseProvider("sqlite3", LOCAL_DATABASE_PATH."/sayadb.sqlite3");
  }

  private function addUserText(){
    $stmt = $this->databaseProvider->setSql("insert into user_text(user_id, text, date, type) values(:id, :text, :date, :type)");
    $stmt->bindValue(':id', $this->userData["user_id"], \PDO::PARAM_STR);
    $stmt->bindValue(':text', $this->eventData->getStickerId().":".$this->eventData->getPackageId(), \PDO::PARAM_STR);
    $stmt->bindValue(':date', date("Y-m-d H:i:s"), \PDO::PARAM_STR);
    $stmt->bindValue(':type', "sticker", \PDO::PARAM_STR);
    $stmt->execute();
  }

  public function responseMessage(){
    $stickerMessage = new StickerMessageBuilder(1, 2);
    $message = new MultiMessageBuilder();
    $message->add($stickerMessage);
    $response = $this->bot->replyMessage($this->eventData->getReplyToken(), $message);
    $this->addUserText();
  }
}