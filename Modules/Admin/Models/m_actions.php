<?php
/**
 * m_actions.php - Action Model Class
 * 
 * @package photon 
 * @version 1.0-a
 * @author LightCube Solutions <info@lightcubesolutions.com>
 * @copyright LightCube Solutions, LLC. 2010
 */
require('Modules/Admin/Models/m_base.php');

class ActionsModel extends BaseModel
{
	
	/**
     * __construct function initializes collection name
     * @access public
     * @return void
     */
	function __construct()
	{
		parent::__construct();
		$this->col = $this->db->Actions;		
	}

	/**
	 * add function inserts data into MongoDB only if ActionName is unique
	 * @return boolean
	 */
	public function add()
	{
		//Setup the criteria
		$this->criteria = array('ActionName'=>$this->data['ActionName']);
		parent::add();
		
	}


		
}

?>