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

$model = new ActionsModel;

if(!empty($_POST)) {
    $ui = new UITools;
    
    // Swap out values...
    $_POST['IsEnabled'] = ($_POST['IsEnabled'] == 'on') ? '1' : '0';
    
    $data = $_POST;

    if (isset($_POST['add'])) { 
        if ($model->add($data)) {
           $ui->statusMsg('Successfully added the new action: '.$_POST['ActionName']);
        } else {
           $ui->statusMsg($model->error, 'error');
        }    
    } elseif (isset($_POST['update'])) {
        $model->update($data);
        $ui->statusMsg("Successfully updated the action: $_POST[ActionName]");
    } elseif (isset($_POST['del'])) {
        $model->delete($data);
        $ui->statusMsg("Successfully deleted the action: $_POST[ActionName]");
    }
}

$actions = $model->getData(array('Module'=>1, 'ActionName'=>1));

$view->template = 'Modules/DevTools/Views/v_actions.html';
$view->assign('thisaction', "$_SERVER[QUERY_STRING]");
$view->assign('actions', $actions);
$view->register('js', 'photon.js');
$view->pagetitle = "photon :: Actions Administrator";

?>