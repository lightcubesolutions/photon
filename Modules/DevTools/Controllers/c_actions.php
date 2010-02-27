<?php
/**
 * c_actions.php - Controller to manage actions
 *
 * @package RBC Project
 * @version 1.0
 * @author LightCube Solutions <info@lightcubesolutions.com>
 * @copyright LightCube Solutions, LLC. 2010
 */

defined("__photon") || die();

require_once('Modules/DevTools/Models/m_actions.php');

$model = new ActionsModel;

if(isset($_POST)) {
    $data = $_POST;

    require('Library/UITools.php');
    $ui = new UITools;
    
    // Swap out values...
    $data['IsEnabled'] = ($data['IsEnabled'] == 'on') ? '1' : '0';
    
    $model->data = $data;

    if (isset($_POST['add'])) { 
        if ($model->add()) {
           $ui->statusMsg('Successfully added the new action: '.$data['ActionName']);
        } else {
           $ui->statusMsg($model->error, 'error');
        }    
    } elseif (isset($_POST['update'])) {
        $model->update();
        $ui->statusMsg("Successfully updated the action: $data[ActionName]");
    } elseif (isset($_POST['del'])) {
        $model->delete();
        $ui->statusMsg("Successfully deleted the action: $data[ActionName]");
    }
}

$actions = $model->getData();

$view->template = 'Modules/DevTools/Views/v_actions.html';
$view->assign('thisaction', "$_SERVER[QUERY_STRING]");
$view->assign('actions', $actions);
$view->pagetitle = "$shortappname :: Actions Administrator";

?>