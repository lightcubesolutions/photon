<?php
/**
 * c_filters - Controller to manage filter objects
 *
 * @package RBC Project
 * @version 1.0
 * @author LightCube Solutions <info@lightcubesolutions.com>
 * @copyright LightCube Solutions, LLC. 2010
 */

defined("__photon") || die();

$model = new FiltersModel;

if (isset($_REQUEST['fetch'])) {
    $id = new MongoID("$_REQUEST[id]");
    $obj = $model->col->findOne(array('_id'=>$id)); 
    if (!empty($obj['Params'])) {
        $view->assign('params', $obj['Params']);
    }
    $view->assign('filterid', $obj['_id']);
    $view->template = 'Modules/DevTools/Views/v_params.html';
    $view->fullhtml = false;
} else if (isset($_REQUEST['params'])) {
    
    if (!empty($_POST)) {
        $ui = new UITools;
        $id = new MongoID($_POST['_id']);
        if (isset($_POST['add'])) {
            unset($_POST['_id']);
            unset($_POST['add']);
            var_dump($_POST);
            $obj = $model->col->findOne(array('_id'=>$id));
            var_dump($obj);
            $model->col->update(array('_id'=>$id), array('$push'=>array('Params'=>$_POST)));
        } elseif (isset($_POST['update'])) {
            
        } elseif (isset($_POST['del'])) {
            
        }
    }
    $view->template = 'Modules/DevTools/Views/v_filters.html';
    
} else {
    if(!empty($_POST)) {
        $model->data = $_POST;
        
        $ui = new UITools;
        
        if (isset($_POST['add'])) { 
            if ($model->add()) {
               $ui->statusMsg('Successfully added the new filter: '.$data['FilterName']);
            } else {
               $ui->statusMsg($model->error, 'error');
            }    
        } elseif (isset($_POST['update'])) {
            $model->update();
            $ui->statusMsg("Successfully updated the filter: $data[FilterName]");
        } elseif (isset($_POST['del'])) {
            $model->delete();
            $ui->statusMsg("Successfully deleted the filter: $data[FilterName]");
        }
    }
    
    $filters = $model->getData();
    
    $view->template = 'Modules/DevTools/Views/v_filters.html';
    $view->assign('thisaction', "$_SERVER[QUERY_STRING]");
    $view->register('js', 'photon.js');
    $view->assign('filters', $filters);
    $view->pagetitle = "photon :: Filters Administrator";
}

?>