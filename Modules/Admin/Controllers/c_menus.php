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
	$id =  $_REQUEST['id'];
	switch ($_REQUEST['fetch']) {
	    case 'possibleItems':
	        $items = $model->getData("",array('MenuID'=>$id, 'IsEnabled'=>'1'));
            $view->template = 'Modules/Admin/Views/v_menuitems.html';
	        break;
	    case 'currentItems':
	        $items = $model->getData("",array('MenuID'=>$id));
	        $model = new ActionsModel;
            $actions = $model->getData(array('ActionName'=>'1'));
            $view->assign('actions', $actions);      
	        $model = new MenusModel;
	        $menus = $model->getData(array('Name'=>'1'));
	        $view->assign('menus', $menus);
            $view->template = 'Modules/Admin/Views/v_currentmenuitems.html';
	        break;
	}
    $view->fullhtml = false;	    
    $view->assign('items', $items);
	    
} else {

    if(!empty($_POST)) {
    
     	$ui = new UITools;
		switch ($_POST['type']) {
		    case 'item':
    			$model = new MenuItemsModel;
    			break;
		    case 'menu':
		    default:
		        $model = new MenusModel;
		        break;
		}
		
		$model->data = $_POST;
        $model->data['IsEnabled'] = ($model->data['IsEnabled'] == 'on') ? '1' : '0';
		unset($model->data['type']);
     	
    	if (isset($_POST['add'])) {
    	    
    	    switch($_POST['type']) {
    	        case 'item':
        			if ((($_POST['MenuID']) == 'removeme')) {
        				$ui->statusMsg('Please choose an Menu', 'error');
        			}
        			elseif ((($_POST['ParentItemID']) == 'removeme')) {
        				$ui->statusMsg('Please choose an Item', 'error');
        			}
        			elseif ((($_POST['Action']) == 'removeme')) {
        				$ui->statusMsg('Please choose an Action', 'error');
        			}
        			elseif (empty($_POST['Name'])) {
        				$ui->statusMsg('Please Enter a Name', 'error');
        			}
        			else {
        				if ($model->add()) {
        					$ui->statusMsg('Successfully added the new Menu Item: '.$model->data['Name']);			
        				} else {
        	               $ui->statusMsg($model->error, 'error');
        			 	}
        			} 
        			break; 	
        				
    	        case 'menu':
    	        default:
        			if ($model->add()) {
        				$ui->statusMsg('Successfully added the new Menu: '.$model->data['Name']);			
        			} else {
                       $ui->statusMsg($model->error, 'error');
        		 	}
        		break;
    	    }   

    	} elseif (isset($_POST['update'])) {
            $model->update();
            $ui->statusMsg("Successfully updated $_POST[type]: ".$model->data['Name']);
        } elseif (isset($_POST['del'])) {
            $model->delete();
            $ui->statusMsg("Successfully deleted $_POST[type]: ".$model->data['Name']);
            if ($_POST['type'] == 'menu') {
                $model = new MenuItemsModel;
                $model->col->remove(array('MenuID'=>$this->data['_id']));
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
    $menus = $model->getData();

    $view->template = 'Modules/Admin/Views/v_menus.html';
    $view->assign('thisaction', "$_SERVER[QUERY_STRING]");
    $view->register('js', 'photon.js');
    $view->pagetitle = "photon :: Navigator Administrator";
    $view->assign('menus', $menus);
    $view->assign('permobjs', $perms);  	
} 
?>