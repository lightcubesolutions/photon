<?php
/**
 * Dispatcher class
 *
 * @package photon
 * @version 1.0
 * @author LightCube Solutions <info@lightcubesolutions.com>
 * @copyright LightCube Solutions, LLC. 2010
 * @license 
 */


class Dispatcher
{

    public $controller;         // The main PHP file which performs the logic behind an action
    public $special;            // Is this a special, internal action?
    public $action;             //
    public $status;
    private $_access;           //
    
    /**
     * 
     * @param int $userid
     * @return void
     */
    private function _setAccess($loginname)
    {
    	//Set find parameters to enabled actions
    	$where = array("IsEnabled"=>"1");
    	
        $db = new DBConn;
    	//Get data from Actions collection
        $db->getData('Actions', $sort = array(), $where);
        //Get Cursor
        $cur = $db->cursor;
        //Loop through actions
        foreach ($cur as $obj){
        	$this->_access[] = $obj;
        }
    }
 
    /**
     * parse function. Determines if a configured action exists from the client's
     * request and assigns the controller property with the appropriate value if so.
     * 
     * @access public
     * @param mixed $request
     * @return void
     */
    function parse($request)
    {
        $this->status = false;
        $user_request = NULL;
        if (array_key_exists('a', $request)) {
            $user_request = $request['a'];
        }

        $loginname = (isset($_SESSION['LoginName'])) ? $_SESSION['LoginName'] : 0;

        // Set the Navigation elements and Actions to which the user has access
        $this->_setAccess($loginname);
        
        // Check to see if the user requested the special 'login' or 'logout'.
        switch ($user_request) {
            case 'login':
            case 'logout':
                $this->special = $user_request;
                $this->status = true;
                break;
            default:
                $access = false;
                foreach ($this->_access as $action) {                    
                    if ($action['ActionName'] == $user_request) {
                         $this->action  = $action['ActionName'];
                         
                         // FIXME: should be controller.
                         $this->controller = "Modules/$action[Module]/Controllers/$action[Controller]";
                         $access = true;
                         $this->status = true;
                         break;
                    }
                }
                if (!$access) {
                     $this->controller = 'error.php';
                }
                break;
        }
    }

}

?>
