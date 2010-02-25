<?php
/**
 * userinfo.php - 
 *
 * @package RBC Project
 * @version 1.0
 * @author LightCube Solutions <info@lightcubesolutions.com>
 * @copyright LightCube Solutions, LLC. 2010
 */

defined("_CFEXEC") || die();

$template = 'System/Templates/userinfo.tpl';

$req = str_replace("'", '', $_REQUEST);
$user = str_replace('userinfo_', '', $_REQUEST['user']);

$db = new DBConn;
$userdata = $db->db->Users->findOne(array('LoginName'=>$user));
ob_start();
var_dump($userdata);
$dump = ob_get_contents();
ob_end_clean();

$smarty->assign('userdata', $userdata);
$smarty->assign('dump', $dump);
?>
