<?php
namespace TomoLib;
use TomoLib;

class DataLogger
{
  private $FilePath;
  private $LogData;
  private $LogType;

  public function setFilePath($FilePath){
    $this->FilePath = $FilePath;
  }

  public function setLogData($LogData){
    $this->LogData = $LogData;
  }

  public function setLogType($LogType){
    $this->LogType = $LogType;
  }

  public function outputLog(){
    if($this->LogType == "html"){
      $Current = file_get_contents($this->FilePath);
      $Current .= "<div style='font-size:14px;background:#f2f2f2;margin-bottom:10px;padding:10px;'>";
      $Current .= "<div style='margin-bottom:8px;'>".date("Y-m-d H:i:s")."</div>";
      $Current .= $this->LogData;
      $Current .= "</div>";
      file_put_contents($this->FilePath, $Current);
    }
  }
} 
?>