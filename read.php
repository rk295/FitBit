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

$allData = array();
$counter = 0;

if ($handle = opendir($backupDir)) {

    while (false !== ($entry = readdir($handle))) {
        if ( $entry == "." || $entry == ".." ) { continue; }

        $date_str = preg_replace('/-summary.json/','',$entry);

        $dt = new DateTime($date_str); 
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

print "Saved $counter entries into memcache";
exit;

?>