<?php
/**
 * users.php - Manage users
 *
 * @package photon
 * @version 1.0-a
 * @author LightCube Solutions <info@lightcubesolutions.com>
 * @copyright LightCube Solutions, LLC. 2010
 */

defined('__photon') || die();
$model = new UsersModel;

if(!empty($_POST)) {
    $data = $_POST;
    
    $ui = new UITools;
    
    // Swap out values...
    $data['IsEnabled'] = ($data['IsEnabled'] == 'on') ? '1' : '0';
        
    // FIXME: Check all required fields, check passwords match, have the sha1 done on the user's side?
    if (isset($data['Password'])) {
        $data['Password'] = sha1($data['Password']);
        unset($data['confirm']);
    }   
    if (isset($_POST['add'])) {
        if ($model->add($data)) {
           $ui->statusMsg('Successfully added the new user: '.$data['Username']);
        } else {
           $ui->statusMsg($model->error, 'error');
        }    
    } elseif (isset($_POST['update'])) {
        $model->update($data);
        $ui->statusMsg("Successfully updated the user: $data[Username]");
    } elseif (isset($_POST['del'])) {
        $model->delete($data);
        $ui->statusMsg("Successfully deleted the user: $data[Username]");
    }
}

$users = $model->getData();

$view->template = 'Modules/Admin/Views/v_users.html';
$view->assign('thisaction', "$_SERVER[QUERY_STRING]");
$view->assign('users', $users);
$view->register('js', 'photon.js');
$view->pagetitle = "photon :: Users Administrator";
?>