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
       $this->PDO = new PDO('mysql:host=ホスト名;dbname=DB名;charset=utf8','ユーザー名','パスワード',
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

} 
?>