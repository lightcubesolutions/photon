<?php
require_once('Classes/MongoHandler.class.inc');

/**
 * DBConn Class
 * Configures a connection to the DB by extending MongoHandler.
 *
 * @version 1.3
 * @author LightCube Solutions
 * @copyright 2009 LightCube Solutions, LLC.
 * @license http://www.lightcubesolutions.com/LICENSE
 */
class DBConn extends MongoHandler
{
    protected $dbname  = 'changeme';
    protected $dbuser  = 'changeme';
    protected $dbpass  = 'changeme';
}


?>
