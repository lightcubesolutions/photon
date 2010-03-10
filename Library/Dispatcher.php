<?php
/**
 * Dispatcher class
 *
 * @package photon
 * @version 1.0-a
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
    
    private $_action;
    private $_module;
    
    private function _grantaccess()
    {
        $this->status = true;
        $this->controller = "Modules/$this->_module/Controllers/".$this->_action['Controller'];
        $this->action = $this->_action['ActionName'];
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
                // Determine if the user has access.
                // 1. Find the Action. FIXME: Ensure the action is enabled.
                $model = new ActionsModel;
                $this->_action = $model->col->findOne(array('ActionName'=>$user_request,'IsEnabled'=>'1'));
                
                // Before starting down this road, a check should be made if the user is not logged in, just find if the action is public.
                if (!empty($this->_action)) {
                    $this->_module = $this->_action['Module'];
                    $pmodel = new PermissionsModel;
                      
                    // 2. Has Permission been set explicitly for this user?
                    $access = $pmodel->col->findOne(array('Subject'=>$id, 'ActionName'=>$this->_action['ActionName']));
                    
                    if (empty($access)) {
                        
                        // No direct permission
                        // 3. Does this user have permission to the whole Module?
                        $access = $pmodel->col->findOne(array('Subject'=>$id, 'Module'=>$this->_module, 'ActionName'=>array('$exists'=>false)));
                        
                        if (empty($access)) {
                            
                            // No User permission on module
                            // 4. Do any of my groups have permissions?
                            $model = new UsersModel;
                            $mygroups = $model->getGroups($id);
                            if ($mygroups) {
                                $myparents = array();
                                $model = new GroupsModel;
                                foreach ($mygroups as $group) {
                                    $parents = $model->getParents($group);
                                    if ($parents) {
                                        $myparents = array_merge($myparents, $parents);
                                    }
                                }
                                if (!empty($myparents)) {
                                    foreach ($myparents as $group) {
                                        // Check if direct permission is set on action
                                        $access = $pmodel->col->findOne(array('Subject'=>$group, 'ActionName'=>$this->_action['ActionName']));
                                        // Check if permission is set on entire module
                                        if (empty($access)) {
                                            $access = $pmodel->col->findOne(array('Subject'=>$group, 'Module'=>$this->_module, 'ActionName'=>array('$exists'=>false)));
                                        }
                                        if (!empty($access)) {
                                            break;
                                        }
                                        
                                    }
                                }
                            }
                        }
                    }
                }
                if (!empty($access)) {
                    $this->_grantaccess();
                }
                break;
        }
    }
    
    /**
     * dispatch function
     * handles the specified request, by pulling in the appropriate controller
     * @return void
     */
    function dispatch()
    {
        global $auth, $view;
        if (!empty($this->special)) {
        
            // Handle the special login or logout request if it was given
            switch ($this->special) {
        
                case 'login':
                    if ($auth->login($_REQUEST['h'], $_REQUEST['u'])) {
                        $auth->recordLogin();
                        $auth->setSessionInfo($_REQUEST['u']);
        
                        // Set the action - use a previously requested query, if it was requested before login
                        // Otherwise, use the default
                        $redirect = (empty($_SESSION['prev_query'])) ? "a=$default_action" : $_SESSION['prev_query'];
                        // Perform the redirect
                        $view->redirect($redirect);
        
                    } else {
                        $ui = new UITools;
                        $ui->statusMsg('Login Failed', 'error', false);
                    }
                    break;
        
                case 'logout':
                    $auth->logout();
        
                    // Redirect to empty request.
                    header('Location: ?');
                    break;
            }
            
        } else {
            // Set up the Login form if we need to.
            if ($auth->login_form) {
                
                $_SESSION['key'] = $auth->randomString(20);
                
                $_SESSION['prev_query'] = $_SERVER['QUERY_STRING'];
                
                $view->assign('loggedin', false);
                $view->assign('loginkey', $_SESSION['key']);
                $view->register('js', 'sha1.js');
                $view->register('js', 'ajax_functions.js');
                $view->register('js', 'login.js');
            } else {
                // Set up the logout link
                $view->assign('loggedin', true);
                $view->assign('fullname', $_SESSION['FullName']);
            }
            require($this->controller);
            
            $view->display();
        }
    }

}

?>
