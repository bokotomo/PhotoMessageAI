<?php
namespace TomoLib;
use TomoLib;

class UploadFileProvider
{
  private $SqlType;
  
  public function __construct(){
  }

  public function uploadFileData($FilePath, $FileData){
    touch($FilePath);
    file_put_contents($FilePath, $FileData);
    //system("chmod 755 ".$FileData);
    //chmod($FilePath, 755);
  }

} 
?>