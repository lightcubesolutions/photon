<?php
/**
 * users.php - Manage users
 *
 * @package RBC Project
 * @version 1.0
 * @author LightCube Solutions <info@lightcubesolutions.com>
 * @copyright LightCube Solutions, LLC. 2010
 */

defined('__photon') || die();
require_once('Modules/Admin/Models/m_users.php');
$model = new UsersModel;

if(!empty($_POST)) {
    $data = $_POST;
    // Swap out values...
    $data['IsEnabled'] = ($data['IsEnabled'] == 'on') ? '1' : '0';
        
    // FIXME: Check all required fields, check passwords match, have the sha1 done on the user's side?
    if (isset($data['Password'])) {
        $data['Password'] = sha1($data['Password']);
        unset($data['confirm']);
    }
    
    $model->data = $data;
        
    if (isset($_POST['add'])) {
        $check = $model->add();   
        if (empty($check)) {
       
        } else {

        }    
    } elseif (isset($_POST['update'])) {
        $model->update();
    } elseif (isset($_POST['del'])) {
        $model->delete();
    }
}

$view->template = 'Modules/Admin/Views/v_users.html';
$view->assign('thisaction', "$_SERVER[QUERY_STRING]");
$view->assign('actions', $actions);
$view->pagetitle = "$shortappname :: Users Administrator";
?>