<?php

$memcache_obj = memcache_connect('localhost', 11211);

$dir="/home/robin/Dropbox/Backups/fitbit/dailyactivities";

$allData = array();

if ($handle = opendir($dir)) {

    while (false !== ($entry = readdir($handle))) {
        if ( $entry == "." || $entry == ".." ) { continue; }


        $date_str = preg_replace('/-summary.json/','',$entry);

        $dt = new DateTime($date_str); 
        $timeStamp = $dt->format('U');

        $data = json_decode(file_get_contents($dir . "/" . $entry),true);

        $allData[$timeStamp] = $data["summary"];

    }
    closedir($handle);
}

$memcache_obj->set('fitbit-data', $allData);

?>
