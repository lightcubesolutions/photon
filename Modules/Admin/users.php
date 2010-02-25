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

if(!empty($_POST)) {
    $data = $_POST;
    // Swap out values...
    $data['IsEnabled'] = ($data['IsEnabled'] == 'on') ? 1 : 0;
    $data['Married'] = ($data['Married'] == 'on') ? 1 : 0;
        
    // FIXME: Check all required fields, check passwords match, have the sha1 done on the user's side?
    if (isset($data['Password'])) {
        $data['Password'] = sha1($data['Password']);
        unset($data['confirm']);
    }
    
    $col = $db->db->Users;
    
    if (isset($_POST['add'])) {
        $exists = $col->findOne(array('LoginName'=>$data['LoginName']));
        if (empty($exists)) {
            unset($data['add']);
            $col->insert($data);          
            $smarty->assign('statusclass','statusok');
            $smarty->assign('status','Successfully added '.$data['LoginName']);
        } else {
            $smarty->assign('statusclass','statuserror');
            $smarty->assign('status','Duplicate entry for '.$data['LoginName']);
        }    
    } elseif (isset($_POST['update'])) {
        unset($data['update']);
        $col->update(array('LoginName'=>$data['LoginName']), array('$set'=>$data));
    } elseif (isset($_POST['del'])) {
        $col->remove(array('LoginName'=>$_POST['LoginName']));
    }
}

// Smarty Settings
$template = 'System/Templates/users.tpl';
$smarty->assign('title', "$shortappname :: Users");
$smarty->assign('thisaction', "$_SERVER[QUERY_STRING]");
?>