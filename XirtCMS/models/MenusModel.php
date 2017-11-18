<?php

/**
 * Base model for retrieving XirtCMS menus
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class MenusModel extends XCMS_Model {

    /**
     * Internal list of items
     * @var array
     */
    private $_list = array();


    /**
     * Loads all requested items
     *
     * @return  Object                      Always this instance
     */
    public function load() {

        $query = $this->_buildQuery()->get(XCMS_Tables::TABLE_MENUS);
        foreach ($query->result() as $row) {
            $this->_list[] = $row;
        }

        return $this;

    }


    /**
     * Returns the total numbers of rows in the db for the current list (e.g. without limit)
     *
     * @return  int                         The total number of articles in the DB
     */
    public function getTotalCount() {
        return $this->_buildQuery(true)->count_all_results(XCMS_Tables::TABLE_MENUS);
    }


    /**
     * Retrieves the current list of items
     *
     * @return  array        List with the current items
     */
    public function toArray() {
        return $this->_list;
    }


    /**
     * Creates query (using CI QueryBuilder) for retrieving model content
     *
     * @param   boolean     $filterOnly     Indicates the query type to be returned (retrieve vs. count)
     * @return  Object                      CI Database Instance for chaining purposes
     */
    function _buildQuery($filterOnly = false) {

        $this->db->order_by("ordering", "ASC");

        // Hook for customized filtering
        XCMS_Hooks::execute("menus.build_article_query", array(
            &$this, &$this->db, $filterOnly
        ));

        return $this->db;

    }

}
?>