<?php

/**
 * Base model for retrieving XirtCMS usergroups
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class UsergroupsModel extends XCMS_Model {

    /**
     * Internal list of items
     * @var array
     */
    private $_list = array();


    /**
     * CONSTRUCTOR
     * Instantiates controller with required helpers, libraries and models
     */
    public function __construct() {

        parent::__construct();

        // Load models
        $this->load->model("UsergroupModel", false);

    }


    /**
     * Loads the requested items
     *
     * @return  Object                      Always this instance
     */
    public function load() {

        $query = $this->_buildQuery()->get(Query::TABLE_USERGROUPS);
        foreach ($query->result() as $row) {
            $this->_list[] = (new UsergroupModel())->set((array)$row);
        }

        return $this;

    }


    /**
     * Returns the total numbers of rows in the db for the current list (e.g. without limit)
     *
     * @return  Object                      CI Database Instance for chaining purposes
     */
    public function getTotalCount() {
        return $this->_buildQuery(true)->count_all_results(Query::TABLE_USERGROUPS);
    }


    /**
     * Retrieves the current list of items
     *
     * @return  Array                       The list with all items currently populated
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

        $this->db->select(Query::TABLE_USERGROUPS . ".*, count(usergroup_id) as users")
            ->join(Query::TABLE_USERS, Query::TABLE_USERGROUPS . ".id = usergroup_id", "left")
            ->group_by("id, authorization_level, name");

        // Hook for customized filtering
        XCMS_Hooks::execute("usergroups.build_query", array(
            &$this->db, $filterOnly
        ));

        return $this->db;

    }

}
?>