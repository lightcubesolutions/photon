<?php
/**
 * Dispatcher class
 *
 * @package photon
 * @version 1.0
 * @author LightCube Solutions <info@lightcubesolutions.com>
 * @copyright LightCube Solutions, LLC. 2010
 * @license http://www.lightcubesolutions.com/LICENSE
 */


class Dispatcher
{

    public $controller;         // The main PHP file which performs the logic behind an action
    public $special;            // Is this a special, internal action?
    public $action;             //
    public $status;
 
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
        
        $db = new MongoDBHandler;

        $id = (isset($_SESSION['Userid'])) ? $_SESSION['Userid'] : '0';
        
        // Check to see if the user requested the special 'login' or 'logout'.
        switch ($user_request) {
            case 'login':
            case 'logout':
                $this->special = $user_request;
                $this->status = true;
                break;
            default:
                $access = false;
                // Determine if the user has access.
                // 1. Find the Action.
                $db->col = $db->db->Actions;
                $action = $db->col->findOne(array('ActionName'=>$user_request));
                
                if (!empty($action)) {
                    $module = $action['Module'];
                    $db->col = $db->db->Permissions;   
                    // 2. Has Permission been set explicitly for this user?
                    $access = $db->col->findOne(array('Subject'=>$id, 'ActionName'=>$action['ActionName']));
                    
                    if (!empty($access)) {
                        $access = true;
                        $this->status = true;
                        $this->controller = "Modules/$module/Controllers/$action[Controller]";
                        $this->action = $action['ActionName'];
                    }
                }
                break;
        }
    }

}

?>
