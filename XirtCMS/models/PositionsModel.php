<?php

/**
 * Base model for retrieving XirtCMS positions
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class PositionsModel extends XCMS_Model {

    /**
     * Internal list of items
     * @var array
     */
    private $_list = array();


    /**
     * Loads data for this instance from DB
     *
     * @return  Object                      Always this instance
     */
    public function load() {

        // Reset
        $this->_list = array();

        // Populate list from database
        $query = $this->db->query(XCMS_Tables::SEL_TEMPLATES_POSITIONS_UNIQUE);
        foreach ($query->result() as $row) {
            $this->_list[] = $row->position;
        }

        return $this;

    }


    /**
     * Retrieves the current list of items
     *
     * @return  array                       List with the current items
     */
    public function toArray() {
        return $this->_list;
    }

}
?>