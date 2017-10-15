<?php

/**
 * Base model for retrieving single XirtCMS setting
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class SettingModel extends XCMS_Model {

    /**
     * @var array
     * Attribute array for this model (valid attributes)
     */
    protected $_attr = array(
        "name", "value"
    );


    /**
     * Loads the requested model
     *
     * @param   String      $name           The name of the setting to load
     * @return  mixed                       This instance on success, null otherwise
     */
    public function load($name) {

        // Retrieve data (main)
        $result = $this->_buildQuery($name)->get(XCMS_Tables::TABLE_CONFIGURATION);
        if ($result->num_rows()) {

            // Populate model (main)
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

        $this->db->replace(XCMS_Tables::TABLE_CONFIGURATION, $this->getArray());
        return $this;

    }


    /**
     * Creates query (using CI QueryBuilder) for retrieving model content (setting)
     *
     * @param   int         $name           The name of the setting to load
     * @return  Object                      CI Database Instance for chaining purposes
     */
    protected function _buildQuery($name) {

        $this->db->where("name", $name);

        // Hook for customized filtering
        XCMS_Hooks::execute("setting.build_query", array(
            &$this->db, $name
        ));

        return $this->db;

    }

}
?>