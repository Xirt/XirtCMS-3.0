<?php

/**
 * Base model for retrieving single XirtCMS usergroup
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class UsergroupModel extends XCMS_Model {

    /**
     * Attribute array for this model (valid attributes)
     * @var array
     */
    protected $_attr = array(
        "id", "name", "authorization_level"
    );


    /**
     * Loads the requested usergroup by given ID
     *
     * @param   int         $id             The id of the usergroup to load
     * @return  mixed                       This instance on success, null otherwise
     */
    public function load($id) {

        // Retrieve data
        $result = $this->db->get_where(Query::TABLE_USERGROUPS, array("id" => intval($id)));
        if ($result->num_rows()) {

            // Populate model
            $this->set($result->row());
            return $this;

        }

        return null;

    }


    /**
     * Saves the instance in the DB
     *
     * @return  Object                      Always this instance
     */
    public function save() {

        $this->db->replace(Query::TABLE_USERGROUPS, $this->getArray());
        $this->set("id", $this->db->insert_id());

        return $this;

    }


    /**
     * Removes the instance from the DB
     */
    public function remove() {

        $this->db->delete(Query::TABLE_USERGROUPS,  array(
            "id" => $this->get("id")
        ));

    }

}
?>