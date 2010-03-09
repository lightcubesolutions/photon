<?php
/**
 * c_menus.php - Manage Nagivation Menus
 *
 * @package photon
 * @version 1.0-a
 * @author LightCube Solutions <info@lightcubesolutions.com>
 * @copyright LightCube Solutions, LLC. 2010
 * @license http://www.lightcubesolutions.com/LICENSE
 */

defined('__photon') || die();

$model = new MenusModel;


if(isset($_POST)) {

 	$ui = new UITools;
        
	if (isset($_POST['add'])) {
		if (isset($_POST['IsItem'])){
			$id = new MongoID($_POST['MenuID']);
			$model->criteria = array('_id'=>$id);
			$data['Items'] = array(
				'_id' => new MongoID(),
				'Name' => $_POST['Name'],
				'IsEnabled' => $_POST['IsEnabled'],
				'Action' => $_POST['Action'],
				'Items' => array()
			);	
			
			$model->data = $data;
			$model->push();
			
		}
		if (isset($_POST['IsMenu'])) {
			$data['Name'] = $_POST['Name'];
			$data['IsEnabled'] = $_POST['IsEnabled'];
			$data['Items'] = array();
			$model->data = $data;
			if($model->add()){
				$ui->statusMsg('Successfully added the new Menu Group: '.$data['Name']);			
			}else {
               $ui->statusMsg($model->error, 'error');
		 	}    
        } elseif (isset($_POST['update'])) {
            $model->update();
            $ui->statusMsg("Successfully updated the Menu group: $data[Name]");
        } elseif (isset($_POST['del'])) {
            $model->delete();
            $ui->statusMsg("Successfully deleted the Menu group: $data[Name]");
        }
	}	
}


    // Find all actions or modules...
    $model = new ActionsModel;
    // All Module names  
    $modules = $model->distinct("Module");
    // All actions sorted by Module
    foreach ($modules['values'] as $module) {
        $actions = $model->getData(array('ActionName'=>1), array('Module'=>$module));
        foreach($actions as $action) {
            $perms[] = array('_id'=>$action['_id'],'Name'=>$action['ActionName'], 'module'=>$module);
        }
    }
    
    // Find all Menus
    $model = new MenusModel;
    // All Menus
    $menus = $model->getData("",array("IsEnabled"=>"1"));
    
    
    
    $view->template = 'Modules/Admin/Views/v_menus.html';
    $view->assign('thisaction', "$_SERVER[QUERY_STRING]");
    $view->register('js', 'jquery-1.3.2.min.js');
    $view->register('js', 'jquery-ui-1.7.2.custom.min.js');
    $view->register('js', 'ajax_functions.js');
    $view->register('css', 'smoothness/jquery-ui-1.7.2.custom.css');
    $view->pagetitle = "$shortappname :: Navigator Administrator";
    $view->assign('permobjs', $perms);
    $view->assign('menus', $menus);

?>