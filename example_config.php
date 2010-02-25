<?php

// Set global options and rename this file to config.php

// Names
$appname = 'LCS photon';
$shortappname = 'photon';

// App key - should be a random string of Alphanumeric characters.
$appkey = 'x2OpmMkHTay1';

// Timezone
$tz = 'America/New_York';

// DB connection params
// FIXME: find a more elegant way to do the below.
require_once('Library/MongoDBHandler.php');
class DBConn extends MongoDBHandler
{
    protected $dbname  = 'changeme';
    protected $dbuser  = 'changeme';
    protected $dbpass  = 'changeme';
}

?>
