<?php
/**
 * setparams.php - 
 *
 * @package RBC Project
 * @version 1.0
 * @author LightCube Solutions <info@lightcubesolutions.com>
 * @copyright LightCube Solutions, LLC. 2010
 */

defined("_CFEXEC") || die();

// Initial empty parameter.
$options[] = array('id'=>'', 'param_name'=>'...');

// Pull the actual parameters
$db = new DBConn;
$db->getData('UserFilters', array('param_name'=>1));
foreach ($db->cursor as $obj) {
    $options[] = $obj;
}
 
$template = 'System/Templates/setparams.tpl';
$smarty->assign('options', $options);

?>
