<?php
/**
 * m_base.php - Base Model Class
 * The base file contains the basic strcuture of all Models.
 * All methods contained in this class should be useable by multiple models
 * 
 * @package photon 
 * @version 1.0-a
 * @author LightCube Solutions <info@lightcubesolutions.com>
 * @copyright LightCube Solutions, LLC. 2010
 */
defined("__photon") || die();



class BaseModel extends DBConn
{

	public $data;		//Data to be inserted


	
	public function add(){
		
	}
	
	public function delete(){
		
	}
	
	public function update(){
		
	}

	/*
	 * checkExists function
	 * Checks Mongo Collection for a document.
	 * @access public
	 * @return boolean
	 */
	public function checkExists($condition){
		$retval = true;
		$exists = $this->col->findOne($condition);
		if (empty($exists)) {
			$retval = false;
		}
		return $retval;
	}
		
}

?>