<?php
/**
 * index.php - The entry point for the application
 *
 * @package photon
 * @version 1.0-a
 * @author LightCube Solutions <info@lightcubesolutions.com>
 * @copyright LightCube Solutions, LLC. 2010
 * @license http://www.lightcubesolutions.com/LICENSE
 */

// Auto include necessary classes - PHP runs this whenever it is asked
// to use a Class it doesn't yet know about.
function __autoload($class)
{
    $try = "Library/$class.php";
    if (file_exists($try)) {
        require($try);
    } else {
        // Maybe this is a Model
        $name = str_replace('model', '', strtolower($class));
        $modules = scandir('Modules');
        foreach ($modules as $module) {
            if (is_dir("Modules/$module")) {
                $try = "Modules/$module/Models/m_$name.php";
                if (file_exists($try)) {
                    require($try);
                    break;
                }
            }
        }
    }
}

// Instantiate new global instances of classes we will reuse.
$dispatch = new Dispatcher;
$auth = new Authentication;
$view = new View;

// Defined constant to prevent subfiles from being accessed directly.
define('__photon', true);

if (!file_exists('config.php')) {
    // Don't show the login form
    $view->assign('loggedin', true);
    include('Modules/Admin/Controllers/c_install.php');
    $view->display();
} else {
    // Include site configuration.   
    require('config.php');    

    // Start the PHP session
    session_name($appkey);
    session_start();
    
    // Set the use tidy option.
    $view->usetidy = $usetidy;
    
    // Set the theme.
    $view->theme = $theme;
    
    // Set default TimeZone
    date_default_timezone_set($tz);
    
    // Make sure the Session isn't expired or the recorded IP is invalid
    if ($auth->checkSession()) {
    
        if (isset($_SESSION['expired'])) {
            $ui = new UITools;
            $ui->statusMsg('Your session has expired. Please login again.');
            unset($_SESSION['expired']);   
        }
        
        // Parse the $_REQUEST array, look for the action
        $dispatch->parse($_REQUEST);
        
        if ($dispatch->status === false) {
            // No matching action found in the DB. Just use the default 
            $dispatch->controller = 'Modules/Home/Controllers/c_home.php';
        }
        
        if (!empty($dispatch->special)) {
        
            // Handle the special login or logout request if it was given
            // FIXME: Put this logic elsewhere?
            switch ($dispatch->special) {
        
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
                        echo "<span class='error'>Login Failed</span>";
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
                
                $view->assign('loginkey', $_SESSION['key']);
                $view->register('js', 'sha1.js');
                $view->register('js', 'ajax_functions.js');
                $view->register('js', 'login.js');
            } else {
                // Set up the logout link
                $view->assign('loggedin', true);
                $view->assign('fullname', $_SESSION['FullName']);
            }
            require($dispatch->controller);
            
            $view->display();

        }
    }
}
?>
