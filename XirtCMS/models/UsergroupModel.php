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
        "id", "name", "authorization_level", "users"
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

        $this->db->replace(XCMS_Tables::TABLE_USERGROUPS, $this->_filterMetadata($this->getArray()));
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
     * Filter all metadata related attributes from given data set
     *
     * @param   array       $data           The data set to be filtered
     * @return  array                       The filtered data set
     */
    private function _filterMetadata($data) {

        foreach ($data as $key => $value) {

            if (!in_array($key, array("id", "name", "authorization_level"))) {
                unset($data[$key]);
            }

        }

        return $data;

    }


    /**
     * Creates query (using CI QueryBuilder) for retrieving model content (usergroup)
     *
     * @param   int         $id             The id of the usergroup to load
     * @return  Object                      CI Database Instance for chaining purposes
     */
    protected function _buildQuery($id) {

        $this->db->select(XCMS_Tables::TABLE_USERGROUPS . ".*, count(usergroup_id) as users")
            ->join(XCMS_Tables::TABLE_USERS, XCMS_Tables::TABLE_USERGROUPS . ".id = usergroup_id", "left")
            ->where(XCMS_Tables::TABLE_USERGROUPS . ".id", intval($id))
            ->group_by("id, authorization_level, name");

        // Hook for customized filtering
        XCMS_Hooks::execute("usergroup.build_query", array(
            &$this, &$this->db, $id
        ));

        return $this->db;

    }

}
?>