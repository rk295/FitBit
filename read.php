<?php
// 
// read.php
// --------

// Reads all the files in the backupdir specified in conf.php and stores
// all the summary fields for each day in an array in memcache. Intended 
// to be called daily after fetchAct.php is run, the array it stores can
// be used by get.php
// 

include('conf.php');


$mcHost = 'localhost';
$mcPort = 11211;

if ( ! $memcache_obj = memcache_connect($mcHost,$mcPort) ){
	print "Failed to connect to memcache ($mcHost:$mcPort)";
	exit;
}

if ( isset($_GET['quiet']) ) {
  $log->log("going silent", PEAR_LOG_DEBUG);
  $quiet = true;
}

$allData = array();
$counter = 0;

if ($handle = opendir($backupDir)) {

    while (false !== ($entry = readdir($handle))) {
        if ( $entry == "." || $entry == ".." ) { continue; }

        $date_str = preg_replace('/-summary.json/','',$entry);

        try {
            $dt = new DateTime($date_str); 
        } catch (Exception $e) {
            // Maybe ought to log this somewhere, but for now just continue.
            //echo $e->getMessage();
            continue;
        }
        $timeStamp = $dt->format('U');

        $data = json_decode(file_get_contents($backupDir . "/" . $entry),true);

        $allData[$timeStamp] = $data["summary"];

        $counter++;

    }
    closedir($handle);
    
}else{
	print "Failed to opendir $backupDir\n";
	exit;
}

$memcache_obj->set('fitbit-data', $allData);

if ( ! $quiet ) { print "Saved $counter entries into memcache"; }
exit;

?>