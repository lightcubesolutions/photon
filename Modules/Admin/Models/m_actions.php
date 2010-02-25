<?php
/**
 * m_actions.php - Action Model Class
 * The base file contains the basic strcuture of all Models.
 * All methods contained in this class should be useable by multiple models
 * 
 * @package photon 
 * @version 1.0-a
 * @author LightCube Solutions <info@lightcubesolutions.com>
 * @copyright LightCube Solutions, LLC. 2010
 */
require('Modules/Admin/Models/m_base.php');

class ActionModel extends BaseModel
{
	public $key;
	
	/**
     * __construct function initializes collection name
     * @access public
     * @return void
     */
	function __construct(){
		parent::__construct();

		$collection = "Actions";
		$this->col = $this->db->$collection;		
	}

	/**
	 * add function inserts data into MongoDB only if ActionName is unique
	 * @return boolean
	 */
	public function add(){
		$retval = false;
		$this->keycondition = array('ActionName'=>$this->data['ActionName']);
		if (empty($this->data)){
			//Can't insert empty data!!!			
			$retval = false;
		}
		else{			
			//Set search condition		
			$condition = array('ActionName'=>$this->data['ActionName']);
			//Add unique document if it is unique
			$retval = $this->addUnique($condition);				
		}		
		return $retval;  		
	}
	
	/**
	 * delete function
	 * Removes document from MongoDB based on _id 
	 * @return void
	 */
	public function delete(){
		 $this->col->remove(array('_id'=>new MongoID($this->data['_id'])));
	}
	
	/**
	 * update function
	 * Updates document in MongoDB based on _id
	 * @return void
	 */
	public function update(){
		unset($this->data['update']);
		$id = new MongoID($this->data['_id']);
		unset($this->data['_id']);
		$this->col->update(array('_id'=>$id), array('$set'=>$this->data));		
	}
		
}

?>