<?php
/**
 * [Naming conventions]
 * function : camelCase  (Make the beginning of a word a verb / 単語の先頭を動詞にする)
 * method   : camelCase  (Make the beginning of a word a verb / 単語の先頭を動詞にする)
 * Class    : PascalCase (Make the beginning of a word a noun / 単語の先頭を名詞にする)
 * Variable : PascalCase (Make the beginning of a word a noun / 単語の先頭を名詞にする)
 * DEFINE   : UPPERCASE_SNAKECASE
 * 
 * [Comment]
 * don't write comments (コメントは書かない)
 * 
 * [Document]
 * Write all the usage of classes and functions in the document (クラスや関数の使い方は全てドキュメントに記入)
 * 
 * [Indent]
 * Indent is for two characters (インデントは半角スペース2文字分)
 * 
 * [Argument]
 * If there are multiple arguments, put a single space after the delimited comma (引数が複数ある場合は区切りのカンマの後に半角スペースを1つ挟む)
 * 
 * [information]
 * 2016 Fukumoto
 * E-mail : bokotomo@me.com
 */

use Saya\MessageControllor\TextMessageControllor;
use Saya\MessageControllor\StickerMessageControllor;
use Saya\MessageControllor\ImageMessageControllor;
use Saya\MessageControllor\LocationMessageControllor;
use TomoLib\DataLogger;

require_once(__DIR__."/config.php");
require_once(__DIR__."/vendor/autoload.php");
require_once(__DIR__."/sayalib/autoload.php");
require_once(__DIR__."/tomolib/autoload.php");

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

$DataLogger = new DataLogger();
$DataLogger->setLogType("html");
$DataLogger->setFilePath(__DIR__."/line.html");
$DataLogger->setLogData($InputData);
$DataLogger->outputLog();

?>