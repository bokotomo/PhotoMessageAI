<?php

require_once(__DIR__."/config.php");
require_once(__DIR__."/line-bot-sdk-php/vendor/autoload.php");
require_once(__DIR__."/sayalib/autoload.php");

$InputData = file_get_contents("php://input");
$ReceiveData = json_decode($InputData);
$ReplyToken = $ReceiveData->events[0]->replyToken;
$MessageType = $ReceiveData->events[0]->message->type;

if($MessageType == "text"){
  $TextMessageControllor = new TextMessageControllor($ReplyToken,$ReceiveData);
  $TextMessageControllor->responseMessage();
}else if($MessageType == "sticker"){
  $StickerMessageControllor = new StickerMessageControllor($ReplyToken,$ReceiveData);
  $StickerMessageControllor->responseMessage();
}else if($MessageType == "image"){
  $ImageMessageControllor = new ImageMessageControllor($ReplyToken,$ReceiveData);
  $ImageMessageControllor->responseMessage();
}else if($MessageType == "location"){
  $LocationMessageControllor = new LocationMessageControllor($ReplyToken,$ReceiveData);
  $LocationMessageControllor->responseMessage();
}

$file = __DIR__.'/line.html';
$current = file_get_contents($file);
$current .= "<div style='font-size:14px;background:#f2f2f2;margin-bottom:10px;padding:10px;'>";
$current .= "<p>".date("Y-m-d H:i:s")."</p>";
$current .= json_encode($ReceiveData);
$current .= "</div>";

file_put_contents($file, $current);
?>