<?php
namespace TomoLib;
use TomoLib;
use \PDO;

class DatabaseProvider
{
  private $sqlType;
  private $sqlPath;
  private $PDO;
  private $nameSqlite;
  private $nameMysql;
  private $namePostgre;

  public function __construct($sqlType, $sqlPath){
    $this->sqlType = $sqlType;
    $this->sqlPath = $sqlPath;
    $this->nameSqlite = "sqlite3";
    $this->nameMysql = "mysql";
    $this->namePostgre = "postgre";

    if($this->sqlType == $this->nameSqlite){
      $this->PDO = new PDO("sqlite:".$this->sqlPath);
    }else if($sqlType == $this->nameMysql){
      $host = 'host';
      $dbname = 'dbname';
      $user = 'pguser';
      $password = 'pguser';
      $this->PDO = new PDO('mysql:host='.$host.';dbname='.$dbname.';charset=utf8', $user, $password, array(PDO::ATTR_EMULATE_PREPARES => false));
    }else if($sqlType == $this->namePostgre){
      $dsn = 'pgsql:dbname=uriage host=localhost port=0000';
      $user = 'pguser';
      $password = 'pguser';
      $this->PDO = new PDO($dsn, $user, $password);
    }
  }

  public function setSql($SqlStr){
    $stmt = $this->PDO->prepare($SqlStr);
    return $stmt;
  }

  public function getLastAutoIncrement($tableName){
    if($this->sqlType == $this->nameSqlite){
      $stmt = $this->setSql("SELECT seq FROM sqlite_sequence where name = '{$tableName}'");
      $stmt->execute();
      while($row = $stmt -> fetch(PDO::FETCH_ASSOC)) {
        return ($row["seq"] + 1);
      }
    }else if($sqlType == $this->nameMysql){

    }else if($sqlType == $this->namePostgre){

    }
  }
}