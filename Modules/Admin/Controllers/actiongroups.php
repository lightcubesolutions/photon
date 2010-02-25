<?php
/**
 * users.php - Manage users
 *
 * @package RBC Project
 * @version 1.0
 * @author LightCube Solutions <info@lightcubesolutions.com>
 * @copyright LightCube Solutions, LLC. 2010
 */

defined("_CFEXEC") || die();

$db = new DBConn;

if(isset($_POST)) {

    $newgroups = array(
        'GroupName'=>$_POST['GroupName'],
        'ParentGroupID'=>$_POST['ParentGroupID'],
        'IsEnabled'=>$_POST['IsEnabled'],
    	'Actions'=>$_POST['Actions']
    );
    $col = $db->db->ActionGroups;
    
    if (isset($_POST['add'])) {
        // FIXME: Check that user is unique
        try {
            $col->insert($newgroups, true);
        } catch(MongoCursorException $e) {
            $db->error = $e;
        }
    } elseif (isset($_POST['update'])) {
        $col->update(array('GroupName'=>$_POST['GroupName']), array('$set'=>$newgroups));
    } elseif (isset($_POST['del'])) {
        $col->remove(array('GroupName'=>$_POST['GroupName']));
    }
}
$db->getData('ActionGroups');
$cur = $db->cursor;
foreach($cur as $obj){
	$groups[] = $obj;
}
$db->getData('Actions');
$cur = $db->cursor;
foreach($cur as $obj){
	$actions[] = $obj;
}


// Smarty Settings
$template = 'System/Templates/actiongroups.tpl';
$smarty->assign('title', "$shortappname :: Action Groups Admistration");
$smarty->assign('thisaction', "$_SERVER[QUERY_STRING]");
$smarty->assign('groups', $groups);
$smarty->assign('actions', $actions);
?>