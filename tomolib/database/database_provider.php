<?php
namespace TomoLib;
use TomoLib;
use \PDO;

class DatabaseProvider
{
  private $SqlType;
  private $SqlPath;
  private $PDO;

  public function __construct($SqlType, $SqlPath){
    $this->SqlType = $SqlType;
    $this->SqlPath = $SqlPath;
    if($SqlType == "sqlite3"){
      $this->PDO = new PDO("sqlite:".$this->SqlPath);
    }
  }
  
  public function runQuery($SqlStr){
    $Stmt = $this->PDO->prepare($SqlStr);
    return $Stmt;
  }

} 
?>