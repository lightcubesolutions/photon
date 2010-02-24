<?php
/**
 * RequestHandler class
 * @extends DBConn
 *
 * @version 1.0
 * @copyright LightCube Solutions, LLC. 2009
 * @author LightCube Solutions <info@lightcubesolutions.com>
 */


class RequestHandler extends DBConn
{

    public $handler;            // The main PHP file which performs the logic behind an action
    public $smarty = true;      // Use the Smarty Template Engine?
    public $special;            // Is this a special, internal action?
    public $action;             //
    private $_access;           //
    
    /**
     * 
     * @param int $userid
     * @return void
     */
    private function _setAccess($loginname)
    {
        // FIXME: Actually retrieve the Actions from the DB
        /*
         * 1) Find my Groups
         * 2) Find my Actions (including all ActionGroups)
         */
    	//Set find parameters to enabled actions
    	$where = array("IsEnabled"=>"1");
    	//Get data from Actions collection
        $this->getData('Actions', $sort = array(), $where);
        //Get Cursor
        $cur = $this->cursor;
        //Loop through actions
        foreach ($cur as $obj){
        	$this->_access[] = $obj;
        }
    }
    
    /**
     * _getGroupLink function
     * @param integer $id
     * @return boolean
     */
    private function _getGroupLink($id) {
        $retval = false;
        $this->query = "SELECT g.NavText, a.Handler, a.ActionName
                        FROM Actions a
                        LEFT JOIN ActionGroups g on a.ActionGroupID = g.ActionGroupID
                        WHERE a.ActionGroupID = $id
                        AND a.NavText = g.NavText";
        $this->runQuery();
        if (!empty($this->data)) {
            $retval = $this->data[0];
        }
        return $retval;
    }

    /**
     * _setMainNavList function
     * @return boolean
     */
    private function _setMainNavList()
    {
        $retval = false;
        $list = '';
        $tree = array();
        $curpath = '/';
        
        if (!empty($this->_access)) {
            
            foreach ($this->_access as $action) {
                
                if ($action['IsNav'] == '1') {

                    // Determine the current depth
                    if (isset($depth)) {
                        $olddepth = $depth;
                    } else{
                        $olddepth = $rootdepth = $action['depth'];
                    }
                    $depth = $action['depth'];
                    $ag = $action['ActionGroupName'];

                    // Are we at the Root Depth?
                    if ($depth == $rootdepth) {
                        $class = 'top';
                        $linkclass = 'top_link';
                        $ulclass = 'sub';
                    } else {
                        $class = '';
                        $linkclass = '';
                        $ulclass = '';
                    }
                    
                    // Set up the new path.
                    if ($depth > $olddepth) {
                        // We've descended one level into the tree - append to current path
                        $newpath = $curpath.'/'.$ag;
                        
                    } else if ($depth == $olddepth) {
                        // Same level, but perhaps different sub group?
                        $tmppath = dirname($curpath);
                        $newpath = ($tmppath == '/') ? $tmppath.$ag : $tmppath.'/'.$ag;
                    
                    } else {
                        // Went back up in the tree
                        $tmppath = $curpath;
                        for ($i = $olddepth; $i >= $depth; $i--) {
                            $list .= "</ul>\n</li>\n";
                            $tree[$tmppath] = 0; // Mark that we've closed out any open ul & li tags.
                            $tmppath = dirname($tmppath);
                        }
                        $newpath = ($tmppath == '/') ? $tmppath.$ag : $tmppath.'/'.$ag;
                    }
                                        
                    // Is this the grouplink and have we already set up this group link?
                    if ($newpath == $curpath) {
                        if ($action['NavText'] == $grouplink['NavText']) {
                            // Skip this one. We've already set up the group link.
                            continue;
                        } else {
                            if ($tree[$curpath] == 1) {
                                $list .= "<ul class='$ulclass'>";
                            }
                            $list .= "<li><a href='?a=$action[ActionName]'>$action[NavText]</a></li>\n";
                            ++$tree[$curpath];
                        }
                    } else {
                        // New Link Group
                        // First, make sure it's not a sub-group
                        if ($depth <= $olddepth) {              
                            if ($tree[$curpath] > 1) {
                                $list .= "</ul>\n</li>\n";
                                $tree[$curpath] = 0; // Mark that we've closed out any open ul & li tags.
                            } else {
                                if (!empty($tree)) {
                                    $list .= "</li>\n";
                                }
                            }
                        } else {
                            // This is a sub-group.
                            $list .= ($depth == ($rootdepth+1)) ? '<ul class="sub">' : '<ul>';
                        }
                        $curpath = $newpath;
                        $tree[$curpath] = 1; // Mark that we've opened a new set of ul & li tags.
                        // Does this action have a link associated with the Group Name?
                        $grouplink = $this->_getGroupLink($action['ActionGroupID']);
                        if ($grouplink == false) {
                            // No group link - just set up the list
                            unset($grouplink);
                            $list .= "<li class='$class'><a class='$linkclass' href='#'>$ag</a>\n<ul class='$ulclass'>\n<li><a href='?a=$action[ActionName]'>$action[NavText]</a></li>\n";
                            ++$tree[$curpath];
                        } else {
                            // We have a group link
                            $list .= "<li class='$class'><a class='$linkclass' href='?a=$grouplink[ActionName]'>$grouplink[NavText]</a>\n";
                            if ($grouplink['NavText'] != $action['NavText']) {
                                $list .= "<ul class='$ulclass'>\n<li><a href='?a=$action[ActionName]'>$action[NavText]</a></li>\n";
                                ++$tree[$curpath];
                            }
                        }
                    }
                }
            }
            
            // Close up any remainig open tags.
            for ($i = $depth; $i >= $rootdepth; $i--) {
                $list .= "</ul>\n</li>\n";
            }
            
            // Assign the whole value to the session variable
            $_SESSION['MainNavList'] = $list;
            $retval = true;
        }
        return $retval;
    }

    /**
     * parse function. Determines if a configured action exists from the client's
     * request and loads the handler and smarty data fields with the appropriate
     * values if so
     * 
     * @access public
     * @param mixed $request
     * @return void
     */
    function parse($request)
    {
        $user_request = NULL;
        $retval = false;
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
                $retval = true;
                break;
            default:
                $access = false;
                foreach ($this->_access as $action) {                    
                    if ($action['ActionName'] == $user_request) {
                         $this->action  = $action['ActionName'];
                         $this->smarty  = $action['IsSmarty'];
                         $this->handler = $action['Handler'];
                         $access = true;
                         $retval = true;
                         break;
                    }
                }
                if (!$access) {
                     $this->smarty = true;
                     $this->handler = 'error.php';
                }
                break;
        }

        // Set the Navigation list
        // FIXME: Enable the following...
        // $this->_setMainNavList();

        return $retval;
    }

}

?>
