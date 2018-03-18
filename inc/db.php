<?php
class DB
{
  public $con;
  function __construct()
  {
    $con = new mysqli("localhost","root","","dbs");
    if($con){
      $this->con = $con;
    }else{
      exit("Connection Failed");
    }
  }
  public function get()
  {
    return $this->con;
  }
}

 ?>
