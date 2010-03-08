<?php
/**
 * c_permissions.php - Manage groups
 *
 * @package photon
 * @version 1.0-a
 * @author LightCube Solutions <info@lightcubesolutions.com>
 * @copyright LightCube Solutions, LLC. 2010
 * @license http://www.lightcubesolutions.com/LICENSE
 */

defined('__photon') || die();
$model = new PermissionsModel;  

if (isset($_REQUEST['fetch'])) {

    $id = $_REQUEST['id'];
    $pieces = explode('|', $id);

    if (empty($pieces[1])) {
        $children = $model->getData(array('SubjectType'=>1), array('Module' => $id, 'ActionName'=>array('$exists'=>false)));
    } else {
        $children = $model->getData(array('SubjectType'=>1), array('Module'=>$pieces[0], 'ActionName'=>$pieces[1]));
    }

    switch ($_REQUEST['fetch']) {
        
        case 'mysubjects':

            foreach ($children as $key=>$child) {
                switch ($child['SubjectType']) {
                    case 'user':
                        $model = new UsersModel;
                        $user = $model->col->findOne(array('_id'=>new MongoID($child['Subject'])));
                        $children[$key]['Name'] = "$user[LastName], $user[FirstName] [ $user[Username] ]";
                        break;
                    case 'group':
                        $model = new GroupsModel;
                        $group = $model->col->findOne(array('_id'=>new MongoID($child['Subject'])));
                        $children[$key]['Name'] = $group['Name'];
                        break;
                }
            }
            $view->assign('getsubjects', true);

            break;
            
        case 'possible':
            
            // Find all groups to exclude from the search
            foreach ($children as $key=>$child) {
                switch ($child['SubjectType']) {
                    case 'user':
                        $userexclude[] = new MongoID($child['Subject']);
                        break;
                    case 'group':
                        $grpexclude[] = new MongoID($child['Subject']);
                        break;
                }
            }

            // Grab all the groups except the ones excluded.
            $model = new GroupsModel;
            if (!empty($grpexclude)) {
                $posgroups = $model->getData(array('Name'=>1), array('_id'=>array('$nin'=>$grpexclude)));
            } else {
                $posgroups = $model->getData(array('Name'=>1));
            }
            foreach($posgroups as $key=>$val) {
                $posgroups[$key]['SubjectType'] = 'group';
                $posgroups[$key]['Subject'] = $val['_id'];
            }
            
            // Grab all Users except ones excluded
            $model = new UsersModel;
            $sort = array('LastName'=>1, 'FirstName'=>1, 'Username'=>1);
            if (!empty($userexclude)) {
                $posusers = $model->getData($sort, array('_id'=>array('$nin'=>$userexclude)));
            } else {
                $posusers = $model->getData($sort);
            }
            foreach($posusers as $key=>$val) {
                $posusers[$key]['SubjectType'] = 'user';
                $posusers[$key]['Subject'] = $val['_id'];
                $posusers[$key]['Name'] = "$val[LastName], $val[FirstName] [ $val[Username] ]";
            }
            
            // Combine the results
            $children = array_merge($posgroups, $posusers);
            break;
    }
    $view->template = 'Modules/Admin/Views/v_permschildren.html';
    $view->fullhtml = false;
    $view->assign('children', $children);
    
} else if (isset($_REQUEST['permmod'])) {
    
    $item = explode('_', $_REQUEST['id']);
    $pieces = explode('|', $_REQUEST['action']);
    $type = $item[0];
    $id = $item[1];
    $data = array('Subject'=>$id, 'SubjectType'=>$type);
    if (!empty($pieces[1])) {
        $data['Module'] = $pieces[0];
        $data['ActionName'] = $pieces[1];
    } else {
        $data['Module'] = $_REQUEST['action'];
    }
    
    switch ($_REQUEST['permmod']) {
        case 'add':
            $model->col->save($data);
            break;
        case 'del':
            if (!isset($data['ActionName'])) {
            	$data['ActionName'] = array('$exists'=>false);
            }
            $model->col->remove($data);
            break;
    }
    $view->render = false;
    
} else {

    // Find all actions or modules...
    $model = new ActionsModel;
    // All Module names  
    $modules = $model->distinct("Module");
    // All actions sorted by Module
    foreach ($modules['values'] as $module) {
        $perms[] = array('Name'=>$module, 'type'=>'module');
        $actions = $model->getData(array('ActionName'=>1), array('Module'=>$module));
        foreach($actions as $action) {
            $perms[] = array('Name'=>$action['ActionName'], 'module'=>$module, 'type'=>'action');
        }
    }
    
    $view->template = 'Modules/Admin/Views/v_permissions.html';
    $view->assign('thisaction', "$_SERVER[QUERY_STRING]");
    $view->assign('permobjs', $perms);
    $view->register('js', 'jquery-1.3.2.min.js');
    $view->register('js', 'jquery-ui-1.7.2.custom.min.js');
    $view->register('js', 'jquery.dump.js');
    $view->register('js', 'ajax_functions.js');
    $view->register('css', 'smoothness/jquery-ui-1.7.2.custom.css');
    $view->pagetitle = "$shortappname :: Permissions Administrator";
}
?>