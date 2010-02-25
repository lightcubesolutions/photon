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
$path = "Modules/Admin/Models/m_actions.php";
require($path);

$model = new ActionModel;

if(isset($_POST)) {
    $data = $_POST;
    // Swap out values...
    $data['IsEnabled'] = ($data['IsEnabled'] == 'on') ? '1' : '0';
    $model->data = $data;

    if (isset($_POST['add'])) { 
    	$check = $model->add();   
        if (empty($check)) {                    
            $view->assign('statusclass','statusok');
            $view->assign('status','Successfully added '.$data['ActionName']);
        } else {
            $view->assign('statusclass','statuserror');
            $view->assign('status','Duplicate entry for '.$data['ActionName']);
        }
    } elseif (isset($_POST['update'])) {
		$model->update();
    } elseif (isset($_POST['del'])) {
		$model->delete();
    }
}

$model->getDocuments();
$cur = $model->cursor;
foreach($cur as $obj){
    $obj['_id'] = $obj["_id"];
	$actions[] = $obj;
}


$view->template = 'Modules/Admin/Views/v_actions.html';
$view->assign('thisaction', "$_SERVER[QUERY_STRING]");
$view->assign('actions', $actions);
$view->pagetitle = "$shortappname :: Actions Administrator";

?>