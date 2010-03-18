<?php
/**
 * MongoDBHandler Class
 * Manages all MongoDB transactions. Collects returned data into easily
 * accessible arrays and reports all errors.
 *
 * @version 1.3
 * @author LightCube Solutions
 * @copyright 2010 LightCube Solutions, LLC.
 * @license http://www.lightcubesolutions.com/LICENSE
 */

class MongoDBHandler
{
    protected $dbname;   // The database name
    protected $dbuser;   // The database user name
    protected $dbpass;   // The database user's password

    public $cursor;      // The Mongo cursor for returned data
    public $error;       // The last error message issued by the Server
    public $db;          // The actual DB object.
    public $grid; 		 // Grid object
    public $fileid; 	 // The mongoid of a stored file
    public $col;		 // The MongoDB Collection Object
    
    private $_link;      // The link to the server.
    private $_connected; // Status of the server connection.
  
    /**
     * __construct function.
     * 
     * Initializes a database connection which will be present while the object is in use.
     * @access public
     * @return void
     */
    function __construct()
    {
        global $dbname, $dbuser, $dbpass;
        $this->dbname = $dbname;
        $this->dbuser = $dbuser;
        $this->dbpass = $dbpass;
        
        // Make the initial connection
        // FIXME: could probably allow for all the PHP options, here, like server URL, etc
        // TODO: Improve exception handling per issue #305
        try {
            $this->_link = new Mongo();
            // Select the DB
            $this->db = $this->_link->selectDB($this->dbname);
            // Authenticate
            $result = $this->db->authenticate($this->dbuser, $this->dbpass);
            if ($result['ok'] == 0) {
                // Authentication failed.
                $this->error = ($result['errmsg'] == 'auth fails') ? 'Database Authentication Failure' : $result['errmsg'];
                $this->_connected = false;
            } else {
                $this->_connected = true;
            }
        } catch (Exception $e) {
            $this->error = (empty($this->error)) ? 'Database Connection Error' : $this->error;
        }
    }

    /**
     * __destruct function.
     * 
     * Closes the database connection when the object is no longer used.
     * @access public
     * @return void
     */
    function __destruct()
    {
        $this->_link->close();
    }
    
    /**
     * getData function - Return array of collection documents with optional sorting and filtering
     * @param array $sort
     * @param array $where
     * @return mixed
     */
    function getData($sort = array(), $where = array())
    {
        $retarray = array();
        // Only try if the connection has been established.
        if ($this->_connected) {
                                    
            // Grab the data
            $cursor = $this->getCursor($where);
            //$this->cursor = $this->col->find($where);
            if (is_object($cursor)) {
                $cursor->sort($sort);
            	foreach($cursor as $obj){
					$retarray[] = $obj;
				}			
            }else{
            	return false;
            }           
        }
        return $retarray;
    }
    
    /**
     * saveFile function
     * Saves one file to the database with a specificed path
     * 
     * @param string $path
     * @param array $info
     * @return mixed
     */
    function saveFile($path, $info = array())
    {
    	$retval = false;
    	// Only try if the connection and path has been established.
 		$grid = $this->getGrid();
    	if (is_object($grid) && isset($path)){
    		$retval = $grid->storeFile($path, $info);
    	}
    	return $retval;
    }
    
    /**
     * removeFile function
     * Removes one or more files from Mongo depending on the specification
     * @param array $where
     * @param boolean $isRemoved
     * @return boolean
     */
    function removeFile($where = array())
    {
    	$retval = false;
    	//Only try if the connection and where is set
    	$grid = $this->getGrid();
    	if (is_object($grid) && isset($where))
    	{
    		//Remove the file Mongo based on the $where param
			$retval = ($grid->remove($where) == TRUE)? TRUE : FALSE;
    	}	
    	return $retval;
    }
    
    /**
     * getGrid funtion
     * Gets the GridFS Object once the connection is made to Mongo
     * @return boolean
     */
    function getGrid()
    {
    	$retval = false;
    	if ($this->_connected){
    		//Get GridFS Object
    		$retval = $this->db->getGridFS();
    	}
    	return $retval;
    }
    
    /*
     * getCursor function
     * Gets the MongoDB cursor of a collection based on search criteria
     * @return mixed
     */
 	function getCursor($where = array())
    {
    	$retval = false;
        // Only try if the connection has been established.
        if ($this->_connected) {                       
            // Grab the data            
            $cursor = $this->col->find($where);
            if ($cursor->count() > 0){
            	$retval = $cursor;
            }	
        }
		return $retval;
    }    
}

?>