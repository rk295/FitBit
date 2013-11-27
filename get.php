<?php
// 
// get.php
// -------
// 
// Grabs the array from memcache, (currently just _assumes_ it is there)
// and returns a json array of timestamp,steps. 
// 
// Also takes a period parameter to specify only day in a given period, 
// i.e. 8d, 6m etc. 
// 
// Currently _only_ supports steps, and has almost not error checking, 
// for example will die silently if the data isn't in memcache.
// 

include('conf.php');

$memcache_obj = memcache_connect('localhost', 11211);

$allData = $memcache_obj->get('fitbit-data');

$output = array();

$savedThings = array();

if ( isset($_GET['period']) && $_GET['period'] != "" && $_GET['period'] != "all" ){

	// FIXME: Need to do some error checking here
	// ideally this will error if used with something
	// that DateInterval doesnt like.

	$period = "P" . strtoupper($_GET['period']);

	$then = new DateTime();	

	// Syntax here:
	// http://php.net/manual/en/dateinterval.construct.php
	$then->sub(new DateInterval($period));

}

$output['data'][0]['legend']  = "Steps";
$output['data'][1]['legend']  = "Floors";

foreach( $allData as $timestamp => $data){

	$dt = new DateTime("@$timestamp");

	if ( $dt < $then ){ continue; }

	
	$output['data'][0]['data'][$timestamp]  = $data["steps"];
	$output['data'][1]['data'][$timestamp] = $data["floors"];
         
}

header('Content-type: application/json');
print json_encode($output,JSON_PRETTY_PRINT);

?>