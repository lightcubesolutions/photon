<?php
/**
 * m_menuitems.php - Menus Model Class
 * 
 * @package photon 
 * @version 1.0-a
 * @author LightCube Solutions <info@lightcubesolutions.com>
 * @copyright LightCube Solutions, LLC. 2010
 * @license http://www.lightcubesolutions.com/LICENSE
 */

class MenuItemsModel extends Model
{
	protected $criteria;    //Search criteria for find
	
	/**
     * __construct function initializes collection name
     * @access public
     * @return void
     */
	function __construct()
	{
		parent::__construct();
		$this->col = $this->db->MenuItems;		
	}
	
	function add($data)
	{
		//Setup the criteria
		$this->criteria = array('_id'=>$data['_id']);
		return parent::add($data);
	}
}

?>