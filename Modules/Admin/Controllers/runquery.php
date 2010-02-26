<?php
/**
 * runquery.php - 
 *
 * @package RBC Project
 * @version 1.0
 * @author LightCube Solutions <info@lightcubesolutions.com>
 * @copyright LightCube Solutions, LLC. 2010
 */

defined("_CFEXEC") || die();

$id = NULL;
$template = 'System/Templates/runquery.tpl';
$db = new MongoDBHandler;
$dir = dirname($_SERVER["SCRIPT_FILENAME"]);

$req = str_replace("'", '', $_REQUEST);
foreach ($req as $key=>$val) {
    if (strpos($key, 'text_') !== false) {
        $key = str_replace('text_', '', $key);
        $text[$key]['text'] = $val;
    }
    if (strpos($key, 'TXTOP_') !== false) {
        $key = str_replace('TXTOP_', '', $key);
        $text[$key]['op'] = $val;
    }
    if (strpos($key, 'radio_') !== false) {
        $key = str_replace('radio_', '', $key);
        $filters[$key] = $val;
    }
    if (strpos($key, 'checkbox_') !== false) {
        $key = str_replace('checkbox_', '', $key);
        $filters[$key] = array('$in'=>explode('|', $val));
    }
    if (strpos($key, 'select_') !== false) {
        $key = str_replace('select_', '', $key);
        $filters[$key] = $val;
    }
    if (strpos($key, 'CMP_') !== false) {
        $key = str_replace('CMP_', '', $key);
        $compare[$key][0] = $val;
    }
    if (strpos($key, 'CMPOP_') !== false) {
        $key = str_replace('CMPOP_', '', $key);
        $compare[$key]['op'] = $val;
    }
    if (strpos($key, 'BTW_') !== false) {
        $key = str_replace('BTW_', '', $key);
        $compare[$key][1] = $val;
    }
}
if (!empty($text)) {
    foreach ($text as $key=>$val) {
        switch ($val['op']) {
            case 'begins':
                $filters[$key] = new MongoRegex("/^$val[text].*/i");
                break;
            case 'contains';
                $filters[$key] = new MongoRegex("/$val[text].*/i");
                break;
        }
    }
}
if (!empty($compare)) {
    foreach ($compare as $key=>$val) {
        switch ($val['op']) {
            case '>':
                $filters[$key] = array('$gt'=>$val[0]);
                break;
            case '<':
                $filters[$key] = array('$lt'=>$val[0]);
                break;
            case 'IS':
                $filters[$key] = $val[0];
                break;
            case 'BTW':
                $filters[$key] = array('$gte'=>$val[0], '$lte'=>$val[1]);
                break;
        }   
    }
}

if (!empty($filters)) {

    $db = new MongoDBHandler;
    
	if ($db->getData('Users', '', $filters)) {
		$num = $db->cursor->count();
		if ($num == 1) {
    		$users = "user";
    		$plural = "There is";
    	} else {
    		$users = "users";
    		$plural = "There are";
    	}
   		$smarty->assign('users', $users);
   		$smarty->assign('plural', $plural);
   		$smarty->assign('num', number_format($num));
   		foreach($db->cursor as $obj) {
   			$results[] = $obj;
   		}
		$smarty->assign('results', $results);

    } else {
		$smarty->assign('error', 'No matching records found.');
    }

}


?>