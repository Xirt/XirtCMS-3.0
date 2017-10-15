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
        $result = $this->_buildQuery($id)->get(XCMS_Tables::TABLE_USERGROUPS);
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

        $this->db->replace(XCMS_Tables::TABLE_USERGROUPS, $this->getArray());
        $this->set("id", $this->db->insert_id());

        return $this;

    }


    /**
     * Removes the instance from the DB
     */
    public function remove() {

        $this->db->delete(XCMS_Tables::TABLE_USERGROUPS,  array(
            "id" => $this->get("id")
        ));

    }


    /**
     * Creates query (using CI QueryBuilder) for retrieving model content (usergroup)
     *
     * @param   int         $id             The id of the usergroup to load
     * @return  Object                      CI Database Instance for chaining purposes
     */
    protected function _buildQuery($id) {

        $this->db->where("id", intval($id));

        // Hook for customized filtering
        XCMS_Hooks::execute("usergroup.build_query", array(
            &$this->db, $id
        ));

        return $this->db;

    }

}
?>