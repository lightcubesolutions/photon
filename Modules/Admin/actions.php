<?php
/**
 * actionsadmin.php - Manage actions
 *
 * @package RBC Project
 * @version 1.0
 * @author LightCube Solutions <info@lightcubesolutions.com>
 * @copyright LightCube Solutions, LLC. 2010
 */

defined("_CFEXEC") || die();

$db = new DBConn;

if(isset($_POST)) {
    $data = $_POST;
    // Swap out values...
    $data['IsEnabled'] = ($data['IsEnabled'] == 'on') ? '1' : '0';
    $data['IsSmarty'] = ($data['IsSmarty'] == 'on') ? '1' : '0';

    $col = $db->db->Actions;
    
    if (isset($_POST['add'])) {
        $exists = $col->findOne(array('ActionName'=>$data['ActionName']));
        if (empty($exists)) {
            unset($data['add']);
            $col->insert($data);          
            $smarty->assign('statusclass','statusok');
            $smarty->assign('status','Successfully added '.$data['ActionName']);
        } else {
            $smarty->assign('statusclass','statuserror');
            $smarty->assign('status','Duplicate entry for '.$data['ActionName']);
        }
    } elseif (isset($_POST['update'])) {
        unset($data['update']);
        $id = new MongoID($data['_id']);
        unset($data['_id']);
        $col->update(array('_id'=>$id), array('$set'=>$data));
    } elseif (isset($_POST['del'])) {
        $col->remove(array('_id'=>new MongoID($data['_id'])));
    }
}

$db->getData('Actions');
$cur = $db->cursor;
foreach($cur as $obj){
    $obj['_id'] = $obj["_id"];
	$actions[] = $obj;
}

// Smarty Settings
$template = 'System/Templates/actions.tpl';
$smarty->assign('title', "$shortappname :: Action Administrator");
$smarty->assign('thisaction', "$_SERVER[QUERY_STRING]");
$smarty->assign('actions', $actions)
?>