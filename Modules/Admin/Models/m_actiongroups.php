<?php
/**
 * m_actiongroups.php - ActionGroups Model Class
 * 
 * @package photon 
 * @version 1.0-a
 * @author LightCube Solutions <info@lightcubesolutions.com>
 * @copyright LightCube Solutions, LLC. 2010
 */

class ActionGroupsModel extends Model
{
	/**
     * __construct function initializes collection name
     * @access public
     * @return void
     */
	public function __construct()
	{
		parent::__construct();
		$this->col = $this->db->ActionGroups;		
	}

    /**
     * @see Library/Model#add()
     */
	public function add()
	{
		//Setup the criteria
		$this->criteria = array('GroupName'=>$this->data['GroupName']);
		parent::add();
		
	}
}
?>