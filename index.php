<?php
/**
 * index.php - The entry point for the application
 *
 * @package photon
 * @version 1.0
 * @author LightCube Solutions <info@lightcubesolutions.com>
 * @copyright LightCube Solutions, LLC. 2010
 * @license http://www.lightcubesolutions.com/LICENSE
 */

// Include site configuration.
require('config.php');

// Include necessary Libraries
require('Library/Dispatcher.php');
require('Library/Authentication.php');
require('Library/View.php');

// Instantiate new global instances of classes we will reuse.
$dispatch = new Dispatcher;
$auth = new Authentication;
$view = new View;

// Set the use tidy option.
$view->usetidy = $usetidy;

// Set the theme.
$view->theme = $theme;

// Start the PHP session
session_name($appkey);
session_start();

// Defined constant to prevent subfiles from being accessed directly.
define("__photon", true);

// Set default TimeZone
date_default_timezone_set($tz);

// Make sure the Session isn't expired or the recorded IP is invalid
$auth->checkSession();

// Parse the Query Parameters given, which is in the $_REQUEST array
$dispatch->parse($_REQUEST);

// No action found in the DB. Just use the default as set in config.php
if ($dispatch->status === false) {
    $dispatch->handler = 'Modules/Home/Controllers/c_home.php';
}

if (!empty($dispatch->special)) {

    // Handle the special login or logout request if it was given
    // FIXME: Have view output the below.
    switch ($dispatch->special) {

        case 'login':
            if ($auth->login($_REQUEST['h'], $_REQUEST['u'])) {
                $auth->recordLogin();
                $auth->setSessionInfo($_REQUEST['u']);

                // Set the action - use a previously requested query, if it was requested before login
                // Otherwise, use the default
                $redirect = (empty($_SESSION['prev_query'])) ? "a=$default_action" : $_SESSION['prev_query'];

                // Perform the redirect
                // It's odd, but IE needs something here before the script, so add a space char '&nbsp;'
                echo "&nbsp;
                  <script>
                    window.location = '?$redirect';
                  </script>";
            } else {
                echo "Login Failed
                  <script>
                    document.getElementById('login_status').className = 'error';
                  </script>";
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

        $view->assign('login_form', true);
        
        $_SESSION['key'] = $auth->randomString(20);
        $_SESSION['prev_query'] = $_SERVER['QUERY_STRING'];

        $view->assign('key', $_SESSION['key']);
        $view->assign('title', "$shortappname :: Login");
        $view->assign('login_script', $auth->login_script);

    } else {
        // Set up the logout link
        $view->assign('loggedin', true);
        $view->assign('fullname', $_SESSION['FullName']);
    }
    include($dispatch->handler);
    $view->display();
}
?>
