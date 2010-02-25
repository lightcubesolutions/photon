<?php
/**
 * DBHandler Class
 * Manages all SQL database transactions. Collects returned data into easily
 * accessible arrays and reports all SQL errors.
 *
 * @version 1.3
 * @author LightCube Solutions
 * @copyright 2009 LightCube Solutions, LLC.
 * @license http://www.lightcubesolutions.com/LICENSE
 */

class DBHandler
{
    protected $dbtype;   // The database server type: mysql or mssql
    protected $dbname;   // The database name, or schema
    protected $dbuser;   // The database user name
    protected $dbpass;   // The database user's password
    protected $contact;  // The email address to send messages to when using mailDie

    public $data;        // An array containing any data results returned from the query
    public $error;       // The last error message issued by the SQL Server
    public $query;       // The SQL query which will be run.

    private $_link;      // The link to the SQL server.
    private $_connected; // Status of the server connection.
    private $_bindData;  // Holder for data ready to be inserted or updated.

    /**
     * __construct function.
     * 
     * Initializes a database connection which will be present while the object is in use.
     * @access public
     * @return void
     */
    function __construct()
    {
        switch ($this->dbtype) {
        case "mssql":
            $this->_link = mssql_connect($this->dbhost, $this->dbuser, $this->dbpass);
            if (!$this->_link) {
                $this->error = "Error connecting to MS SQL server $this->dbhost using $this->dbuser";
                $this->_connected = false;
            } else {
                mssql_min_error_severity(5);
                mssql_min_message_severity(17);
                mssql_select_db($this->dbname, $this->_link);
                $this->_connected = true;
            }
            break;
        case "mysql":
            $this->_link = @mysqli_connect($this->dbhost, $this->dbuser, $this->dbpass, $this->dbname);
            if (!$this->_link) {
                $this->error = mysqli_connect_error();
                $this->_connected = false;
            } else {
                $this->_connected = true;
            }
            break;
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
        switch ($this->dbtype) {
        case "mssql":
            if ($this->_connected) {
                mssql_close($this->_link);
            }
            break;
        case "mysql":
            if ($this->_connected) {
                $this->_link->close();
            }
            break;
        }
    }


    /**
     * getFields function.
     * 
     * Returns a table's field names (column names).     
     * @access public
     * @param mixed $table
     * @return array|boolean
     */
    function getFields($table)
    {
        $retval = false;
        switch ($this->dbtype) {
        case 'mssql':
            $this->query = "SELECT COLUMN_NAME as [Field]
                FROM INFORMATION_SCHEMA.COLUMNS
                WHERE TABLE_NAME = '$table'
                ORDER BY ORDINAL_POSITION";
            break;
        case 'mysql':
            $this->query = "describe `$table`";
            break;
        }
        if ($this->runQuery(true)) {
            $temp = array();
            $i = 0;
            foreach ($this->data as $key=>$val) {
                $temp[$i] = $val['Field'];
                $i++;
            }
            $retval = $temp;
        }
        return $retval;
    }


    /**
     * bind function.
     * 
     * @access public
     * @param mixed $table
     * @param array $data. (default: array())
     * @return boolean
     */
    function bind($table, $data=array())
    {
        $retval = false;
        if (!empty($data)) {
            $temp = array();
            $foundkey = false;
            $fieldlist = $this->getFields($table);
            foreach ($fieldlist as $field) {
                if (array_key_exists($field, $data)) {
                    $temp[$field] = $data[$field];
                    $foundkey = true;
                }
            }
            if ($foundkey) {
                $retval = true;
                $this->_bindData = $temp;
            }
        }
        return $retval;
    }


    /**
     * insert function.
     * 
     * @access public
     * @param mixed $table
     * @return boolean
     */
    function insert($table)
    {
        $retval = false;
        if (!empty($this->_bindData)) {
            $fields = '';
            $values = '';
            $this->query = "INSERT INTO `$table` (";
            foreach ($this->_bindData as $key=>$val) {
                $fields .= (strlen($fields)) ? ", `$key`" : "`$key`";
                $values .= (strlen($values)) ? ", '$val'" : "'$val'";
            }
            $this->query .= "$fields) VALUES ($values)";
            if ($this->runQuery(false)) {
                $retval = true;
            }
        }
        return $retval;
    }


    /**
     * update function.
     * 
     * @access public
     * @param mixed $table
     * @param mixed $where
     * @return boolean
     */
    function update($table, $where)
    {
        $retval = false;
        if (!empty($this->_bindData)) {
            $this->query = "UPDATE `$table` SET ";
            $first = true;
            foreach ($this->_bindData as $key=>$val) {
                $this->query .= ($first) ? '' : ', ';
                $this->query .= "`$key` = '$val'";
            }
            $this->query .= "WHERE $where";
            if ($this->runQuery(false)) {
                $retval = true;
            }
        }
        return $retval;
    }


    /**
     * runQuery function.
     * 
     * The main engine behind the DBHandler class - executes a SQL query.
     * @access public
     * @final
     * @param bool $autocommit. (default: true) Whether or not MySQL's autocommit feature should be on.
     * @return boolean
     */
    final public function runQuery($autocommit = true)
    {
        $retval = false;
        $this->data = array();
        if ($this->_connected) {
            switch ($this->dbtype) {
            case "mssql":
                if ($result = msquery($this->query, $this->_link)) {
                    for ($i = 0; $row = mssql_fetch_assoc($result); ++$i) {
                        $this->data[$i] = $row;
                    }
                    mssql_free_result($result);
                    $retval = true;
                } else {
                    $this->error = mssql_get_last_message();
                }
                break;

            case "mysql":
                // If autocommit was set to false, turn it off
                ($autocommit) ? true : $this->_link->autocommit(FALSE);

                if ($result = $this->_link->query($this->query)) {
                    $type = gettype($result); // An insert or Update will return true (boolean).
                    if ($type == 'object') {
                        for ($i = 0; $row = $result->fetch_array(MYSQLI_ASSOC); ++$i) {
                            $this->data[$i] = $row;
                        }
                        $result->close();
                    }

                    // Commit the query if autocommit has been turned off
                    ($autocommit) ? true : $this->_link->commit();
                    $retval = true;
                } else {
                    $this->error = $this->_link->error;
                }
                break;
            }
        }
        return $retval;
    }


    /**
     * getData function.
     * 
     * @access public
     * @param mixed $table
     * @param bool $ordBy. (default: false)
     * @param string $ordDir. (default: 'ASC')
     * @param bool $where. (default: false)
     * @return boolean
     */
    function getData($table, $ordBy = false, $ordDir = 'ASC', $where = false)
    {
        if ($where) {
            $where = "WHERE $where";
        }
        if ($ordBy) {
            $ordBy = "ORDER BY $ordBy $ordDir";
        }
        $this->query = "SELECT * FROM $table $where $ordBy";
        $retval = ($this->runQuery(false)) ? true : false;
        return $retval;
    }


    /**
     * die_error function.
     * 
     * Prints out the last error and any stored SQL query and dies.
     * @access public
     * @return void
     */
    function dieError()
    {
        die ("Error: $this->error\nSQL Query: $this->query\n");
    }


    /**
     * mailDie function.
     * 
     * Mails the last SQL Server error and dies
     * @access public
     * @return void
     */
    function mailDie()
    {
        $str = "$this->error\n$this->query";
        mail($this->contact, 'SQL Error Encountered', "$str");
        die("$str\n");
    }
    
    
}


?>
