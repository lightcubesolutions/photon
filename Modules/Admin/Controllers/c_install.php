<?php
/**
 * install.php - Create a config.php file (installation only)
 *
 * @package RBC Project
 * @version 1.0
 * @author LightCube Solutions <info@lightcubesolutions.com>
 * @copyright LightCube Solutions, LLC. 2010
 */

defined('__photon') || die();

if (!empty($_POST)) {
    // Try DB connection
    $dbname = $_POST['dbname'];
    $dbpass = $_POST['dbpass'];
    $dbuser = $_POST['dbuser'];
    $db = new MongoDBHandler;
    if (isset($db->error)) {
        $error = $db->error;
    } else {
        // Check Username/Password
        if (empty($_POST['Username'])) {
            $error = 'No Username supplied.';
        } elseif (empty($_POST['Password']) || empty($_POST['confirm'])) {
            $error = 'You must enter a password and confirm it.';
        } elseif ($_POST['Password'] != $_POST['confirm']) {
            $error = 'User passwords do not match.';
        } else {
            // FIXME: Also perform password length/complexity checks
            // All appears good.
            $tz = $_POST['TZ'];
            // Add the initial user.
            $model = new UsersModel;
            $model->data['Username'] = $_POST['Username'];
            $model->data['Password'] = sha1($_POST['Password']);
            $model->data['FirstName'] = $_POST['FirstName'];
            $model->data['LastName'] = $_POST['LastName'];
            $model->data['IsEnabled'] = '1';
            $model->add();
            // Fetch the new Users' ID
            $user = $model->col->findOne(array('Username'=>$_POST['Username']));
            $id = "$user[_id]";
            
            // Add core permissions to the user
            $model = new PermissionsModel;
            $modules = array(0=>'Admin', 1=>'DevTools');
            foreach($modules as $module) {
                $model->data = array('Subject'=>$id, 'SubjectType'=>'user', 'Module'=>$module);
                $model->add();
            }
            
            // Drop in the core actions
            $model = new ActionsModel;
            // FIXME: find a better way than this
            $actions = array(
                0 => array(
                    'ActionName'=>'actions',
                    'Module'=>'DevTools',
                    'Controller'=>'c_actions.php',
                    'IsEnabled'=>'1'
                ),
                1 => array(
                    'ActionName'=>'home',
                    'Module'=>'Home',
                    'Controller'=>'c_home.php',
                    'IsEnabled'=>'1'
                ),
                2 => array(
                    'ActionName'=>'groups',
                    'Module'=>'Admin',
                    'Controller'=>'c_groups.php',
                    'IsEnabled'=>'1'
                ),
                3 => array(
                    'ActionName'=>'users',
                    'Module'=>'Admin',
                    'Controller'=>'c_users.php',
                    'IsEnabled'=>'1'
                ),
                4 => array(
                    'ActionName'=>'permissions',
                    'Module'=>'Admin',
                    'Controller'=>'c_permissions.php',
                    'IsEnabled'=>'1'
                )
            );
            foreach($actions as $action) {
                $model->data = $action;
                $model->add();
            }
            
            $appkey = $auth->randomString(20);
            // Create the config.php file
            $filecontents = "<?php
// Names
\$appname = 'LightCube photon';
\$shortappname = 'photon';

// App key (a random string of Alphanumeric characters)
\$appkey = '$appkey';

// Timezone
\$tz = '$tz';

// Name of Theme (skin) to use for HTML structure/display
// If unset, the default theme will be used.
//\$theme = '';

// Use the HTML Tidy class to clean up the HTML output?
\$usetidy = false;

// DB connection params
\$dbname  = '$dbname';
\$dbuser  = '$dbuser';
\$dbpass  = '$dbpass';
?>";

            if (!file_put_contents('config.php', $filecontents, LOCK_EX)) {
                $error = 'Unable to write config file.';
            }
        }
    }
    if ($error) {
        $ui = new UITools;
        $ui->statusMsg($error, 'error');
        $view->assign('data', $_POST);
    } else {
        $view->redirect();
    }
}

$view->template = 'Modules/Admin/Views/v_install.html';
$view->pagetitle = "photon :: Installation";
?>