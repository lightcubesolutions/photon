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
    // First see if the class is in the Library directory.
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
$dp = new Dispatcher;
$auth = new Authentication;
$view = new View;

// Defined constant to prevent subfiles from being accessed directly.
define('__photon', true);

if (!file_exists('config.php')) {
    // Don't show the login form
    $view->assign('loggedin', true);
    $auth->login_form = false;
    // Display the install form
    $dp->controller = 'Modules/Admin/Controllers/c_install.php';
    $dp->dispatch();
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
        $dp->parse($_REQUEST);
        
        if ($dp->status === false) {
            // No matching action found in the DB. Just use the default 
            $dp->controller = 'Modules/Home/Controllers/c_home.php';
        }
        
        // Handle the request
        $dp->dispatch();
    }
}
?>
