<?php
namespace TomoLib;
use TomoLib;
use \PDO;

class DatabaseProvider
{
  private $SqlType;
  private $SqlPath;
  private $PDO;
  private $NameSqlite;
  private $NameMysql;
  private $NamePostgre;

  public function __construct($SqlType, $SqlPath){
    $this->SqlType = $SqlType;
    $this->SqlPath = $SqlPath;
    $this->NameSqlite = "sqlite3";
    $this->NameMysql = "mysql";
    $this->NamePostgre = "postgre";

    if($this->SqlType == $this->NameSqlite){
      $this->PDO = new PDO("sqlite:".$this->SqlPath);
    }else if($SqlType == $this->NameMysql){
      $host = 'host';
      $dbname = 'dbname';
      $user = 'pguser';
      $password = 'pguser';
      $this->PDO = new PDO('mysql:host='.$host.';dbname='.$dbname.';charset=utf8', $user, $password, array(PDO::ATTR_EMULATE_PREPARES => false));
    }else if($SqlType == $this->NamePostgre){
      $dsn = 'pgsql:dbname=uriage host=localhost port=0000';
      $user = 'pguser';
      $password = 'pguser';
      $this->PDO = new PDO($dsn, $user, $password);
    }
  }

  public function setSql($SqlStr){
    $Stmt = $this->PDO->prepare($SqlStr);
    return $Stmt;
  }

  public function getLastAutoIncrement($TableName){
    if($this->SqlType == $this->NameSqlite){
      $stmt = $this->setSql("SELECT seq FROM sqlite_sequence where name = '{$TableName}'");
      $stmt->execute();
      while($row = $stmt -> fetch(PDO::FETCH_ASSOC)) {
        return ($row["seq"] + 1);
      }
    }else if($SqlType == $this->NameMysql){

    }else if($SqlType == $this->NamePostgre){

    }
  }

} 
?>