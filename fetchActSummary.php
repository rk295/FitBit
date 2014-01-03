<?php

include('conf.php');

$debug = false;

if ( isset($_GET['debug']) ) {
  $log->log("enabling debug", PEAR_LOG_DEBUG);
  $debug = true;
}

if ( isset($_GET['quiet']) ) {
  $log->log("going silent", PEAR_LOG_DEBUG);
  $quiet = true;
}

// Which day to fetch the data for?
if ( isset($_GET['dayToFetch']) && $_GET['dayToFetch'] != "" ) {

  $dayToFetch = $_GET['dayToFetch'];

  $log->log("GET paramter detected, fetching data for " . $dayToFetch, PEAR_LOG_DEBUG);

}else{
	$date = new DateTime();
	$date->sub(new DateInterval('P1D'));
	$dayToFetch = $date->format('Y-m-d');

  $log->log("Defaulting to yesterday, " . $dayToFetch, PEAR_LOG_DEBUG);

}

// Fitbit API call (get activities for specified date)
// https://wiki.fitbit.com/display/API/API-Get-Activities
$apiCall = $baseUrl . '/1/user/-/activities/date/' . $dayToFetch . '.json';

$oauth = new OAuth($conskey,$conssec,OAUTH_SIG_METHOD_HMACSHA1,OAUTH_AUTH_TYPE_AUTHORIZATION);

//$oauth->enableDebug();

$oauth->setToken($token,$secret);

try {
  
  $oauth->fetch($apiCall);

} catch(OAuthException $E) {
    echo "Exception caught!\n";
    echo "Response: ". $E->lastResponse . "\n";
    $log->log("OAuthException caught: " . $E->lastResponse, PEAR_LOG_DEBUG);

    exit;
}

$json = $oauth->getLastResponse();


$fileName = $backupDir.'/'.$dayToFetch.'-summary.json';
$log->log("Saving to $fileName", PEAR_LOG_DEBUG);

if ( file_put_contents($fileName, $json) == false ) {
  $log->log("Failed to write to $fileName", PEAR_LOG_CRIT);
}else{
  $log->log("Wrote to $fileName successfully", PEAR_LOG_INFO);
}

$response = json_decode($json);

if ( ! $quiet ) { print "saving to $fileName"; }

if ( $debug ) {
?>
<p><strong>Hint:</strong> The get param you want is <a href="<?php echo $_SERVER['PHP_SELF']; ?>?dayToFetch=2013-01-01">dayToFetch</a> and the date format is YYYY-MM-DD</p>
<?php
  echo "<pre>";
  print_r($response);
  echo "</pre>";
}
?>
