<?php
/**
 * m_actions.php - Action Model Class
 * 
 * @package photon 
 * @version 1.0-a
 * @author LightCube Solutions <info@lightcubesolutions.com>
 * @copyright LightCube Solutions, LLC. 2010
 * @license http://www.lightcubesolutions.com/LICENSE
 */

class ActionsModel extends Model
{
	/**
     * __construct function initializes collection name
     * @access public
     * @return void
     */
	public function __construct()
	{
		parent::__construct();
		$this->col = $this->db->Actions;		
	}

    /**
     * @see Library/Model#add()
     */
	public function add()
	{
		//Setup the criteria
		$this->criteria = array('ActionName'=>$this->data['ActionName']);
		parent::add();
		
	}
}
?>
