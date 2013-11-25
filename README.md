FitBit backup scripts
=====================

These are some simple php scripts I wrote to grab a given days data from 
the fitbit API and save the raw json to a file. I don't yet have a plan 
for what to do with the data, but I figured having them saved as json
in flat text files gives me plenty of oportunity later on.

Currently this requires the the PHP [Oauth](http://php.net/manual/en/book.oauth.php)
extension for communication with the FitBit API.

You can register a new app [here](https://dev.fitbit.com/apps/new) once
registered you'll need to grab the Consumer key and Consumer Secret and 
bung them into the conskey and conssec vars in conf.php

Make sure auth.php can be seen from the internet, then hit that page
which will direct you off to the app auth page on fitbit, login and
grant the app access to your data and it will bounce you back to auth.php
on your server. Hopefully if everything worked out that page should now
display the secret and the tokent you need to use these pages to access
your data.

fetchAct.php
------------

Hit this page and it will default to getting yesterdays data and storing 
it as plain json in a text file in the directory specified in the 
$backupDir variable in conf.php. Must be web server writable.

Optional parameters:

* debug - just supplying this will dump the json to the browser too
* dayToFetch - supply a YYYY-MM-DD date to grab that days data

examples: 

* http://localhost/fetchAct.php?dayToFetch=2013-02-25
* http://localhost/fetchAct.php?dayToFetch=2013-02-25&debug

displayAct.php
--------------

Simple takes a day param and YYYY-MM-DD value to read that file out of 
the dir defined in $backupDir and dumps the contents to the browser.  
Does some basic checking to ensure file exists.

conf.php
--------

Set some vars in here, your keys, the dir to write to and a log file 
if you want to read some of the log data. Its not that interesting 
but might help debugging

read.php
--------

Reads all the files in the backupdir specified in conf.php and stores
all the summary fields for each day in an array in memcache. Intended 
to be called daily after fetchAct.php is run, the array it stores can
be used by:

get.php
-------

Grabs the array from memcache, (currently just _assumes_ it is there)
and returns a json array of timestamp,steps. 

Also takes a period parameter to specify only day in a given period, 
i.e. 8d, 6m etc. 

Currently _only_ supports steps, and has almost not error checking, 
for example will die silently if the data isn't in memcache.

ToDo
====

I'd like to explore the subscription api where you pass a endpoint to
the fitbit api and it sends you data whenever your device syncs

Contact
=======

You can drop me a line at robin {@} kearney.co.uk or [rk295](http://twitter.com/rk295/) 
on Twitter. And I'll occasionally blog about this at http://riviera.org.uk

