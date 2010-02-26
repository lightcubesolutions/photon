<?php
/**
 * m_users.php - Users Model Class
 * 
 * @package photon 
 * @version 1.0-a
 * @author LightCube Solutions <info@lightcubesolutions.com>
 * @copyright LightCube Solutions, LLC. 2010
 */

class UsersModel extends Model
{
	/**
     * __construct function initializes collection name
     * @access public
     * @return void
     */
	function __construct()
	{
		parent::__construct();
		$this->col = $this->db->Users;		
	}

	/**
	 * add function inserts data into MongoDB only if ActionName is unique
	 * @return boolean
	 */
	function add()
	{
		//Setup the criteria
		$this->criteria = array('LoginName'=>$this->data['LoginName']);
		parent::add();
	}
}

?>