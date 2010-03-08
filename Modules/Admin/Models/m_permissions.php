<?php
/**
 * m_permissions.php - Permissions Model Class
 * 
 * @package photon 
 * @version 1.0-a-a
 * @author LightCube Solutions <info@lightcubesolutions.com>
 * @copyright LightCube Solutions, LLC. 2010
 * @license http://www.lightcubesolutions.com/LICENSE
 */

class PermissionsModel extends Model
{
	/**
     * __construct function initializes collection name
     * @access public
     * @return void
     */
	function __construct()
	{
		parent::__construct();
		$this->col = $this->db->Permissions;		
	}

}

?>