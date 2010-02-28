<?php
/**
 * m_groups.php - Groups Model Class
 * 
 * @package photon 
 * @version 1.0-a
 * @author LightCube Solutions <info@lightcubesolutions.com>
 * @copyright LightCube Solutions, LLC. 2010
 * @license http://www.lightcubesolutions.com/LICENSE
 */

class GroupsModel extends Model
{
	/**
     * __construct function initializes collection name
     * @access public
     * @return void
     */
	function __construct()
	{
		parent::__construct();
		$this->col = $this->db->Groups;		
	}

	/**
	 * add function inserts data into MongoDB only if Name is unique
	 * FIXME: Add check for group loops?
	 * @return boolean
	 */
	function add()
	{
		//Setup the criteria
		$this->criteria = array('Name'=>$this->data['Name']);
		return parent::add();
	}
}

?>