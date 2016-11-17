<?php
/**
 * [Naming conventions]
 * function : camelCase  (Make the beginning of a word a verb / 単語の先頭を動詞にする)
 * method   : camelCase  (Make the beginning of a word a verb / 単語の先頭を動詞にする)
 * Class    : PascalCase (Make the beginning of a word a noun / 単語の先頭を名詞にする)
 * Variable : PascalCase (Make the beginning of a word a noun / 単語の先頭を名詞にする)
 * DEFINE   : UPPERCASE_SNAKECASE
 * 
 * 2016 Fukumoto
 * E-mail : bokotomo@me.com
 */

require_once(__DIR__."/config.php");
require_once(__DIR__."/vendor/autoload.php");
require_once(__DIR__."/sayalib/autoload.php");

$InputData = file_get_contents("php://input");
$ReceiveData = json_decode($InputData);
$ReplyToken = $ReceiveData->events[0]->replyToken;
$MessageType = $ReceiveData->events[0]->message->type;

if($MessageType == "text"){
  $TextMessageControllor = new TextMessageControllor($ReplyToken, $ReceiveData);
  $TextMessageControllor->responseMessage();
}else if($MessageType == "sticker"){
  $StickerMessageControllor = new StickerMessageControllor($ReplyToken, $ReceiveData);
  $StickerMessageControllor->responseMessage();
}else if($MessageType == "image"){
  $ImageMessageControllor = new ImageMessageControllor($ReplyToken, $ReceiveData);
  $ImageMessageControllor->responseMessage();
}else if($MessageType == "location"){
  $LocationMessageControllor = new LocationMessageControllor($ReplyToken, $ReceiveData);
  $LocationMessageControllor->responseMessage();
}

$ReceiveDataLogger = new ReceiveDataLogger();
$ReceiveDataLogger->setFilePath(__DIR__."/line.html");
$ReceiveDataLogger->setLogType("html");
$ReceiveDataLogger->setLogData($InputData);
$ReceiveDataLogger->outputLog();

?>