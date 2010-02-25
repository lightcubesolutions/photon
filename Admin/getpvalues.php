<?php
/**
 * getpvalues.php - 
 *
 * @package RBC Project
 * @version 1.0
 * @author LightCube Solutions <info@lightcubesolutions.com>
 * @copyright LightCube Solutions, LLC. 2010
 */

defined("_CFEXEC") || die();

$param = $_REQUEST['param'];
$template = 'System/Templates/getpvalues.tpl';

/// Returns a list of possible values for this object based on what is in the table
function getValues($key_name, $collection){
	$retval = false;
    $db = new DBConn;
    $result = $db->db->command(array('distinct' => $collection, 'key' => $key_name));
    foreach($result['values'] as $val) {
        if ($val !== null)
            $values[] = array($key_name=>$val);
    }
    if (!empty($values)) {
        $retval = $values;
    }
	return $retval;
}

$db = new DBConn;

if ($db->getData('UserFilters', '', array('id'=>$param))) {
	$row = $db->cursor->getNext();
	$key_name = str_replace(" ", "_SPACE_", $row['key_name']);
	$label = $row['param_name'];

	$smarty->assign('label', $label);
	$smarty->assign('key_name', $key_name);	

	switch ($row['type']) {

		case 0: // Text box
			$smarty->assign('textbox', 'true');
			break;

		case 1: // Radio Button
			$smarty->assign('radio', 'true');
			// FIXME: Make Collection name alterable
			$values = getValues($key_name, 'Users');
			if ($values) {
				$smarty->assign('values', $values);
			}
			break;
			
		case 2: // Checkbox
			$smarty->assign('checkbox', 'true');
            // FIXME: Make Collection name alterable
			$values = getValues($key_name, 'Users');
			if ($values) {
				$smarty->assign('values', $values);
			}
			break;

		case 3: // Select (Drop-down Menu)
			$smarty->assign('select', 'true');
			// FIXME: Make Collection name alterable
			$values = getValues($key_name, 'Users');
			if ($values) {
				$smarty->assign('values', $values);
			}
			break;
			
		case 4: // Comparison
			$smarty->assign('comparison', 'true');
			break;
	}
}

?>