<?php

$memcache_obj = memcache_connect('localhost', 11211);

$allData = $memcache_obj->get('fitbit-data');

$output = array();

$havePeriod = false;

if ( isset($_GET['period']) && $_GET['period'] != "" && $_GET['period'] != "all" ){

	// FIXME: Need to do some error checking here
	// ideally this will error if used with something
	// that DateInterval doesnt like.

	$period = "P" . strtoupper($_GET['period']);

	$then = new DateTime();	

	// Syntax here:
	// http://php.net/manual/en/dateinterval.construct.php
	$then->sub(new DateInterval($period));

	$havePeriod = true;
}


foreach( $allData as $timestamp => $data){

	$dt = new DateTime("@$timestamp");

	if ( $havePeriod ){
		if ( $dt > $then ) {
			$output[$timestamp] = $data["steps"];
		}
	}else{
		$output[$timestamp] = $data["steps"];
	}
         
}

header('Content-type: application/json');
print json_encode($output,JSON_PRETTY_PRINT);

?>