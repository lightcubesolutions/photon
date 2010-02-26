<?php
/**
 * c_actions.php - Controller to manage actions
 *
 * @package photon
 * @version 1.0-a
 * @author LightCube Solutions <info@lightcubesolutions.com>
 * @copyright LightCube Solutions, LLC. 2010
 */

defined("__photon") || die();

require_once('Modules/Admin/Models/m_actions.php');

$model = new ActionsModel;

if(isset($_POST)) {
    $data = $_POST;
    // Swap out values...
    $data['IsEnabled'] = ($data['IsEnabled'] == 'on') ? '1' : '0';
    $model->data = $data;

    if (isset($_POST['add'])) { 
    	$check = $model->add();   
        if (empty($check)) {
            // No error
        } else {
            // Error
        }
    } elseif (isset($_POST['update'])) {
		$model->update();
    } elseif (isset($_POST['del'])) {
		$model->delete();
    }
}

$actions = $model->getData();

$view->template = 'Modules/Admin/Views/v_actions.html';
$view->assign('thisaction', "$_SERVER[QUERY_STRING]");
$view->assign('actions', $actions);
$view->pagetitle = "$shortappname :: Actions Administrator";

?>