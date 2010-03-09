<?php
/**
 * Model.php - Base Model Class
 * The base file contains the basic strcuture of all Models.
 * All methods contained in this class should be useable by multiple models
 * 
 * @package photon 
 * @version 1.0-a
 * @author LightCube Solutions <info@lightcubesolutions.com>
 * @copyright LightCube Solutions, LLC. 2010
 * @license http://www.lightcubesolutions.com/LICENSE
 */

class Model extends MongoDBHandler
{

    public $data;        //Data to be inserted
    public $criteria;    //Search criteria for find
    
    /**
     * update function
     * Updates document in MongoDB based on _id
     * @return void
     */
    public function update()
    {
        unset($this->data['update']);
        $id = new MongoID($this->data['_id']);
        unset($this->data['_id']);
        $this->col->update(array('_id'=>$id), array('$set'=>$this->data));        
    }
    
    /**
     * delete function
     * Removes document from MongoDB based on _id 
     * @return void
     */
    public function delete()
    {
         $this->col->remove(array('_id'=>new MongoID($this->data['_id'])));
    }
    
    /**
     * add function
     * inserts data into MongoDB
     * 
     * @return unknown_type
     */
    public function add()
    {
        $retval = false;

        // unset the $_POST add field
        unset($this->data['add']);
        
        // Check if this should be a unique entry
        if (!empty($this->criteria)) {
            $exists = $this->col->findOne($this->criteria);
            if (empty($exists)) {
                $retval = $this->_insert();
            } else {
                $this->error = 'Error: Duplicate entry';
            }
        } else {
            // Just insert the new data
            $retval = $this->_insert();
        }

        return $retval;        
    }
    
    /**
     * _insert function
     * performs a safe insert to MongoDB, setting an error value on failure
     * 
     * @access private
	 * @return boolean
     */
    private function _insert()
    {
        $retval = false;
        $status = $this->col->insert($this->data, true);
        if ($status['ok']) {
            $retval = true;
        } else {
            $this->error = $status['err'];
        }
        return $retval;
    }
    
    /**
     * distinct function
     * Returns distinct values for a collection
     * @access public
     * @return array
     */
    public function distinct($key)
    {
    	$values = $this->db->command(array("distinct" => $this->col->getName(), "key" => $key));    
		return $values;
    }
    /*
     * push function
     * Uses the $push mongoDB function
     * @access public
     * @return boolean
     */
    public function push()
    {
    	$retval = false;
    	$status = $this->col->update($this->criteria, array('$push' => $this->data), array("upsert" =>true));
    	echo $status;
    	if($status == true){
    		$retval = true;
    	}
    	else {
            $this->error = $status['err'];
        }
    	
    	return $retval;
    }
}

?>