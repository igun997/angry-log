<?php
/**
 * Automated Block IP if Someone Tried to Attack Software with SQL Injection
 * @author Indra Gunanda
 * @email  indra.gunanda@gmail.com
 */
class Log extends SQLite3
{
  public $con;
  function __construct($db)
  {
    $dir = 'sqlite:'.$db;// Db Log on Logs/db/log.db
    $dbh  = new PDO($dir) or die("cannot open the database");
    $this->con = $dbh;
  }
  //Auto Generate a Tables , This just a lazy mode to listing array to table.
  public function table($data = array(),$title = array())
  {
    $rows = array();
    foreach ($data as $row) {
        $cells = array();
        foreach ($row as $cell) {
            $cells[] = "<td>{$cell}</td>";
        }
        $rows[] = "<tr>" . implode('', $cells) . "</tr>";
    }
    $head = [];
    $head[] = "<tr>";
    foreach ($title as $key => $value) {
      $head[] = "<th>".$value."</th>";
    }
    $head[] = "</tr>";
    return "<table border='1px' class='hci-table'>".implode('', $head). implode('', $rows) . "</table>";
  }
  //Get All Log
  //Choose Return As Array or as a Table
  function get_log($array = false)
  {
    $con = $this->con;
    $c = $con->query("SELECT * FROM access_log");
    $cs = $c->fetchAll();
    if($array){
      return $cs;
    }else{
      echo $this->table($cs);
    }
  }
  // SQL Injection Section Block Your Own searchstring or Use Standar searchstring
  function sqlinjection($param = [])
  {
    if(count($param) < 1){
      $query = "SELECT * FROM access_log WHERE (searchstring LIKE '%20UNION%' OR searchstring LIKE '%CONCAT%')";
    }else{
      $query = "select * from access_log where (".implode("searchstring LIKE ",$param).")";
    }
    $con = $this->con;
    $c = $con->query($query);
    $cs = $c->fetchAll();
    $total = count($cs);
    if($total > 0){
      $iplist = [];
      foreach ($cs as $key => $value) {
        $iplist[] = $value["ip"];
      }
      $iplist = array_unique($iplist);
      $this->blockip($iplist);
    }
  }
  //Block Some IPs
  public function blockip($ip=[])
  {
    foreach ($ip as $key => $value) {
      $lines = file(".htaccess", FILE_IGNORE_NEW_LINES);
      $ips = "Deny from ".$value;
      if(!in_array($ips,$lines)){
        $f = fopen(".htaccess", "a+");
        fwrite($f, "\nDeny from ".$value."\n");
        fclose($f);
      }
    }
  }
  //White List Your IP
  public function whiteip($ip="")
  {
    $ip = "Deny from ".$ip;
    $lines = file(".htaccess", FILE_IGNORE_NEW_LINES);
    $remove = $ip;
    foreach($lines as $key => $line){
      if(stristr($line, $remove)) unset($lines[$key]);
    }
    $data = [];
    foreach ($lines as $key => $value) {
      $data[] = $value."\n";
    }
    $file = fopen(".htaccess", "w");
    fwrite($file, implode("",$data));
    fclose($file);
  }
  //List Blocked IP
  public function list_blocked()
  {
    $ip = "Deny from ";
    $lines = file(".htaccess", FILE_IGNORE_NEW_LINES);
    $remove = $ip;
    $blocked = [];
    foreach($lines as $key => $line){
      if(stristr($line, $remove)){
          $blocked[] = $line;
      }
    }
    return $blocked;
  }
  //Clean Log on DB
  public function clean_log()
  {
    $con = $this->con;
    $c = $con->query("DELETE FROM access_log");
    if(!$c){
        $this->close_response("<br>See Log");
    }
  }
  //Close Response ip if Fail
  function close_response($message)
  {
    ob_end_clean();
    header("Connection: close\r\n");
    header("Content-Encoding: none\r\n");
    ignore_user_abort(true); // optional
    ob_start();
    echo ('Connection Closed');
    echo $message;
    $size = ob_get_length();
    header("Content-Length: $size");
    ob_end_flush();     // Strange behaviour, will not work
    flush();            // Unless both are called !
    ob_end_clean();
    sleep(5);
  }
  //Logging Section and insert into SQLITE DB
  function logging()
  {
    //ASSIGN VARIABLES TO USER INFO

    $con = $this->con;
    $q = "insert into access_log(ip,ref,searchstring,useragent,time) values(:ip,:referrer,:query,:userAgent,:time)";
    $c = $con->prepare($q,array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $time = date("Y-m-d H:i:s");
    $ip = getenv('REMOTE_ADDR');
    $userAgent = getenv('HTTP_USER_AGENT');
    $referrer = getenv('HTTP_REFERER');
    $query = getenv('QUERY_STRING');
    $c->execute(array(':ip' => $ip, ':referrer' => $referrer, ':query' => $query, ':userAgent' => $userAgent, ':time' => $time));
    if(!$c){
      $this->close_response("<br>See Log");
    }

  }
}

 ?>
