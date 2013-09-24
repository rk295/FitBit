<html><body><pre>
<?php

include('conf.php');

$debug = false;

if ( isset($_GET['debug']) ) {
  $log->log("enabling debug", PEAR_LOG_DEBUG);
  $debug = true;
}

// Which day to fetch the data for?
if ( isset($_GET['day']) && $_GET['day'] != "" ) {

  $day = $_GET['day'];

  $log->log("GET paramter detected, fetching data for " . $day, PEAR_LOG_DEBUG);

}else{
  print "Pass a the day parameter in the format of YYYY-MM-DD";
  exit;
}

$fileName = $backupDir.'/'.$day.'.json';

$log->log("Reading $fileName", PEAR_LOG_DEBUG);

if ( ! file_exists($fileName) ){
  $log->log("File ($fileName) doesnt exist", PEAR_LOG_CRIT);
  exit;
}

$data = file_get_contents($fileName);

if ( $data == "" ){
  $log->log("Failed to open $fileName", PEAR_LOG_CRIT);
}else{
  $log->log("Read $fileName ok", PEAR_LOG_DEBUG);
}

print_r(json_decode($data));
?>
</pre></body></html>
