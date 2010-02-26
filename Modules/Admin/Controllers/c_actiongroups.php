<?php
/**
 * c_actiongroups.php - Controller to manage action groups
 *
 * @package photon
 * @version 1.0
 * @author LightCube Solutions <info@lightcubesolutions.com>
 * @copyright LightCube Solutions, LLC. 2010
 */

defined("__photon") || die();

require_once('Modules/Admin/Models/m_actiongroups.php');
require_once('Modules/Admin/Models/m_actions.php');

$model = new ActionGroupsModel;

if(isset($_POST)) {

    $newgroups = array(
        'GroupName'=>$_POST['GroupName'],
        'ParentGroupID'=>$_POST['ParentGroupID'],
        'IsEnabled'=>$_POST['IsEnabled'],
    	'Actions'=>$_POST['Actions']
    );
    
    if (isset($_POST['add'])) {
        // FIXME: Check that user is unique
        $model->data = $newgroups;
        $model->add();
    } elseif (isset($_POST['update'])) {
        $model->update();
    } elseif (isset($_POST['del'])) {
        $model->remove();
    }
}
//Get current groups from Mongo
$groups = $model->getData();
// Get actions for selection
$actionModel = new ActionsModel;
$actions = $actionModel->getData();

// Smarty Settings
$view->template = 'Modules/Admin/Views/v_actiongroups.html';
$view->assign('title', "$shortappname :: Action Groups Admistration");
$view->assign('thisaction', "$_SERVER[QUERY_STRING]");
$view->assign('groups', $groups);
$view->assign('actions', $actions);
?>