<?php
include('Log.php');

$baseUrl = 'https://api.fitbit.com';

// Get these from dev.fitbit when you register
// your app with fit bit
$conskey = 'PUT YOUR CONSUMER KEY HERE';
$conssec = 'PUT YOUR CONSUMER SECRET HERE';

// These are your oauth details which auth.php
// should display once you've gone through the 
// auth process.
$secret = 'PUT YOUR SECRET HERE';
$token = 'PUT YOUR TOKEN HERE';


// Where to dump the files too...
// Must be writable by the user running the php
// (apache is likely)
$backupDir = "/SOME/BACKUP/DIR";


// log file used by the code, again must be writable
// by whoever is running the code
$log = &Log::singleton("file", "/SOME/PATH/TO/A/LOG.FILE");


// Set up how much to log
$mask = Log::UPTO(PEAR_LOG_DEBUG);
$log->setMask($mask);

?>
