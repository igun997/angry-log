<?php
//Include Your Log Class
include 'inc/log.class.php';
//Include Sample Connection for Dummy Test
include 'inc/db.php';
//Initialize your db sqlite , Columns and table you can see on logs/db/log.db
$log = new Log("logs/db/log.db");
//Excute Logging
$log->logging(); 
$d = $log->list_blocked();
//Vurln Section
if(isset($_GET["id"])){
  $id = $_GET["id"];
  $db = new DB();
  $con = $db->get();
  $c = $con->query("SELECT * FROM login WHERE id=$id");
  while($cs = $c->fetch_array()){
    echo $cs["id"];
  }
}
?>
