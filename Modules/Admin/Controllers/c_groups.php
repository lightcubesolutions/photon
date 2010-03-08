<?php
/**
 * c_groups.php - Manage groups
 *
 * @package photon
 * @version 1.0-a
 * @author LightCube Solutions <info@lightcubesolutions.com>
 * @copyright LightCube Solutions, LLC. 2010
 * @license http://www.lightcubesolutions.com/LICENSE
 */

defined('__photon') || die();

$model = new GroupsModel;

if (isset($_REQUEST['fetch'])) {
    $id = $_REQUEST['id'];
    switch ($_REQUEST['fetch']) {
        
        case 'mysubjects':
            $childgroups = $model->getData('', array('Groups' => $id));
            foreach ($childgroups as $key=>$val) {
                $childgroups[$key]['type'] = 'group';
            }
            $model = new UsersModel;
            $childusers = $model->getData('', array('Groups' => $id));
            foreach ($childusers as $key=>$val) {
                $childusers[$key]['type'] = 'user';
            }
            $children = array_merge($childgroups, $childusers);
            $view->assign('getsubjects', true);

            break;
            
        case 'possible':
            
            // Find all groups to exclude from the search
            // First, exclude itself
            $obj = $model->col->findOne(array('_id' => new MongoID($id)));
            if (array_key_exists('Groups', $obj)) {
                $ids = $obj['Groups'];
            }
            $grpexclude[] = new MongoID($id);
            
            // Next, exclude any parents
            while (!empty($ids)) {
                foreach ($ids as $key=>$item) {
                    $grpexclude[] = new MongoID($item);
                    $obj = $model->col->findOne(array('_id' => new MongoID($item)));
                    unset($ids[$key]);
                    if (array_key_exists('Groups', $obj)) {
                        foreach ($obj['Groups'] as $new) {
                            $ids[] = $new;
                        }
                    }                  
                }
            }
            
            // Finally, exclude any direct children
            $obj = $model->getData('', array('Groups'=>$id));
            foreach ($obj as $item) {
                $grpexclude[] = new MongoID($item['_id']);
            }

            // Grab all the groups except the ones excluded.
            $posgroups = $model->getData(array('Name'=>1), array('_id'=>array('$nin'=>$grpexclude)));
            foreach($posgroups as $key=>$val) {
                $posgroups[$key]['type'] = 'group';
            }
            
            // Grab all Users except ones that are direct children of this group
            $model = new UsersModel;
            $sort = array('LastName'=>1, 'FirstName'=>1, 'Username'=>1);
            $posusers = $model->getData($sort, array('Groups' => array('$ne'=>$id)));
            foreach($posusers as $key=>$val) {
                $posusers[$key]['type'] = 'user';
            }
            
            // Combine the results
            $children = array_merge($posgroups, $posusers);
            break;
    }
    $view->template = 'Modules/Admin/Views/v_groupchildren.html';
    $view->fullhtml = false;
    $view->assign('children', $children);
    
} else if (isset($_REQUEST['grpmod'])) {
    $item = explode('_', $_REQUEST['id']);
    $grp = $_REQUEST['grp'];
    $id = new MongoID($item[1]);
    if ($item[0] == 'user') {
        $model = new UsersModel;
    }
    switch ($_REQUEST['grpmod']) {
        case 'add':
            $where = array(
                '_id' => $id,
                'Groups' => array(
                	'$ne'=>$grp
                )
            );
            $data = array('$push' => array('Groups'=>$grp));
            break;
        case 'del':
            $where = array('_id' => $id);
            $data = array('$pull' => array('Groups'=>$grp));
            break;
    }
    $model->col->update($where, $data);
    $view->render = false;
    
} else {

    if(!empty($_POST)) {
        $data = $_POST;
        // Swap out values...
        $data['IsEnabled'] = ($data['IsEnabled'] == 'on') ? '1' : '0';
        
        $model->data = $data;
     
        $ui = new UITools;
        
        if (isset($_POST['add'])) {
            if ($model->add()) {
               $ui->statusMsg('Successfully added the new group: '.$data['Name']);
            } else {
               $ui->statusMsg($model->error, 'error');
            }    
        } elseif (isset($_POST['update'])) {
            $model->update();
            $ui->statusMsg("Successfully updated the group: $data[Name]");
        } elseif (isset($_POST['del'])) {
            $model->delete();
            $ui->statusMsg("Successfully deleted the group: $data[Name]");
        }
    }
    
    $groups = $model->getData(array('Name'=>1));
    
    $view->template = 'Modules/Admin/Views/v_groups.html';
    $view->assign('thisaction', "$_SERVER[QUERY_STRING]");
    $view->assign('groups', $groups);
    $view->register('js', 'jquery-1.3.2.min.js');
    $view->register('js', 'jquery-ui-1.7.2.custom.min.js');
    $view->register('js', 'ajax_functions.js');
    $view->register('css', 'smoothness/jquery-ui-1.7.2.custom.css');
    $view->pagetitle = "$shortappname :: Groups Administrator";
}
?>