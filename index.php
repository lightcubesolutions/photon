<?php
/**
 * index.php - The entry point for the application
 *
 * @package photon
 * @version 1.0
 * @author LightCube Solutions <info@lightcubesolutions.com>
 * @copyright LightCube Solutions, LLC. 2009
 */

// Include necessary files - the order here is important.
require('config.php');      // Contains global configuration
require('dbconfig.php');    // Contains DB configuration
require('Classes/SecureLogin.class.inc');
require('Classes/RequestHandler.class.inc');
require('Classes/UserInterface.class.inc');

// Stores the client's IP address globally
$ip = $_SERVER['REMOTE_ADDR'];

// Start the PHP session
session_name($appkey);
session_start();

// Defined constant to prevent subfiles from being accessed directly.
define("_CFEXEC", true);

// Set default TimeZone
date_default_timezone_set('America/New_York');

// Instantiate new global instances of classes we will reuse.
$db = new DBConn;
$sl = new SecureLogin;
$rq = new RequestHandler;
$ui = new UserInterface;

// Make sure the Session isn't expired or the recorded IP is invalid
$sl->checkSession($ip);

// Parse the Query Parameters given, which is in the $_REQUEST array
$rq_result = $rq->parse($_REQUEST);

// No action found in the DB. Just use the default as set in config.php
if ($rq_result === false) {
    $rq_result = $rq->parse(array('a'=>$default_action));
}

if (!empty($rq->special)) {

    // Handle the special login or logout request if it was given
    switch ($rq->special) {

        case 'login':
            if ($sl->login($_REQUEST['h'], $_REQUEST['u'])) {
                $sl->recordLogin($ip);
                $sl->setSessionInfo($_REQUEST['u']);

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
            $sl->logout();

            // Redirect to empty request.
            header('Location: ?');
            break;
    }
} else {
    // This is a normal request.

    if ($rq->smarty) {
        // Set up and use the Smarty engine to display HTML.
        require('Classes/Smarty_config.class.inc');
        $smarty = new MySmarty;

        // Set up the Login form if we need to.
        if ($sl->login_form) {

            $smarty->assign('login_form', true);
            
            $_SESSION['key'] = $sl->randomString(20);
            $_SESSION['prev_query'] = $_SERVER['QUERY_STRING'];

            $smarty->assign('key', $_SESSION['key']);
            $smarty->assign('title', "$shortappname :: Login");
            $smarty->assign('login_script', $sl->login_script);

        } else {
            // Set up the logout link
            $smarty->assign('loggedin', true);
            $smarty->assign('fullname', $_SESSION['FullName']);
        }
        
        // Include the handler - all normal processing happens here.
        include($rq->handler);

        // Set up the navigation menu.
        if (empty($navbar)) {
            $navbar = str_replace("class=\"$rq->action\"", 'class="navsel"', $_SESSION['MainNavList']);
        }
        $smarty->assign('navbar', $navbar);

        // If the handler file does not set the $template variable,
        // set to the default template as set in config.php.
        $template = (empty($template)) ? $default_template : $template;

        // Render the HTML via Smarty
        ob_start();
        $smarty->display($template);
        $html = ob_get_contents();
        ob_end_clean();
        
        // Attempt to Tidy the output.
        if ($usetidy && class_exists('tidy')) {
            $tidy = new tidy();
            $tidy->parseString($html, array(
                'hide-comments' => TRUE,
                'output-xhtml' => TRUE,
                'indent' => TRUE,
                'wrap' => 0
            ));
            $tidy->cleanRepair();
            echo tidy_get_output($tidy);
        } else {
            // Just dump it as is
            echo $html;
        }

    } else {
        // Don't use the Smarty engine. e.g., when using Ajax to return a boolean
        if ($rq_result) {
            include($rq->handler);
        }
    }
}
?>
