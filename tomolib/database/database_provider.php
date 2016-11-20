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
    }else if($SqlType == "mysql"){
      $host = 'host';
      $dbname = 'dbname';
      $user = 'pguser';
      $password = 'pguser';
      $this->PDO = new PDO('mysql:host='.$host.';dbname='.$dbname.';charset=utf8', $user, $password, array(PDO::ATTR_EMULATE_PREPARES => false));
    }else if($SqlType == "postgre"){
      $dsn = 'pgsql:dbname=uriage host=localhost port=0000';
      $user = 'pguser';
      $password = 'pguser';
      $this->PDO = new PDO($dsn, $user, $password);
    }
  }
  
  public function runQuery($SqlStr){
    $Stmt = $this->PDO->prepare($SqlStr);
    return $Stmt;
  }

  public function getLastInsertId($TableName){

    $stmt = $this->runQuery("select * from {$TableName} where ROWID = last_insert_rowid()");
    $stmt->execute();
    while($row = $stmt -> fetch(PDO::FETCH_ASSOC)) {
      return $row["auto_increment"];
    }
  }

} 
?>