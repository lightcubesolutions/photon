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
	protected $criteria;    //Search criteria for find	
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
	function add($data)
	{
		//Setup the criteria
		$this->criteria = array('Name'=>$data['Name']);
		return parent::add($data);
	}
	
	/**
	 * getParents function
	 * return all parents of a supplied group.
	 * 
	 * @param $id
	 * @return mixed
	 */
	function getParents($id)
	{
	    $retval = false;
	    $ids[] = $id;
        while (!empty($ids)) {
            foreach ($ids as $key=>$id) {
                $parents[] = $id;
                $obj = $this->col->findOne(array('_id' => new MongoID($id)));
                unset($ids[$key]);
                if (array_key_exists('Groups', $obj)) {
                    foreach ($obj['Groups'] as $new) {
                        $ids[] = $new;
                    }
                }                  
            }
        }
        if (!empty($parents)) {
            $retval = $parents;
        }
        return $retval;
	}
	
}

?>