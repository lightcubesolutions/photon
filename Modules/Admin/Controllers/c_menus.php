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

if (isset($_REQUEST['fetch'])) {

		$model = new MenuItemsModel;
		$id = new MongoID($_REQUEST['id']);
		$items = $model->getData("",array('Menu'=>$id, 'IsEnabled'=>'1'));
	    $view->template = 'Modules/Admin/Views/v_menuitems.html';
	    $view->fullhtml = false;	    
	    $view->assign('items', $items);
	    
} else {


if(!empty($_POST)) {

 	$ui = new UITools;
        
	if (isset($_POST['add'])) {
		if (isset($_POST['IsItem'])){
			$model = new MenuItemsModel;

			$data['Name'] = $_POST['Name'];
			$data['IsEnabled'] = $_POST['IsEnabled'];
			$data['ActionID'] = new MongoID($_POST['Action']);
			$data['Menu'] = new MongoID($_POST['MenuID']);
			if (($_POST['ParentItemID']) != 'noparent'){
				$data['ParentItemID'] = new MongoID($_POST['ParentItemID']);
			}
			if ((($_POST['MenuID']) == 'removeme')){
				$ui->statusMsg('Please choose an Menu', 'error');
			}
			elseif ((($_POST['ParentItemID']) == 'removeme')){
				$ui->statusMsg('Please choose an Item', 'error');
			}
			elseif ((($_POST['Action']) == 'removeme')){
				$ui->statusMsg('Please choose an Action', 'error');
			}
			elseif (empty($_POST['Name'])){
				$ui->statusMsg('Please Enter a Name', 'error');
			}
			else{				
				$model->data = $data;
				if($model->add()){
					$ui->statusMsg('Successfully added the new Menu Item: '.$data['Name']);			
				}else {
	               $ui->statusMsg($model->error, 'error');
			 	}
			}  		
		}
		if (isset($_POST['IsMenu'])) {
			$model = new MenusModel;
			$data['Name'] = $_POST['Name'];
			$data['IsEnabled'] = $_POST['IsEnabled'];
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
    
 
    $model = new MenusModel;
    // Find all active Menus
    $menus = $model->getData("",array("IsEnabled"=>"1"));

    $model = new MenuItemsModel;
	//Find all active items based on active Menus
    foreach($menus as $key_value){
    	$items[] = $model->getData('',array("IsEnabled"=>"1", "Menu"=>$key_value['_id']));
    }
   	//Find all children of active items (if there are any)
   	//TODO: Get all children items and make a master list of Menus/Items and their children
    foreach($items as $menu){
    	foreach($menu as $value){
    		$links[] = $model->getData('',array("ParentItemID"=>$value['_id']));
    	}
    }

    

        
    	
    $view->template = 'Modules/Admin/Views/v_menus.html';
    $view->assign('thisaction', "$_SERVER[QUERY_STRING]");
    $view->register('js', 'photon.js');
    $view->pagetitle = "photon :: Navigator Administrator";
    $view->assign('menus', $menus);
    $view->assign('items', $items);
    $view->assign('permobjs', $perms);
    	
} 
?>