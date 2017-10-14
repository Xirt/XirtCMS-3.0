<?php

/**
 * Base model for retrieving XirtCMS users
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class UsersModel extends XCMS_Model {

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
        $this->load->model("UserModel", false);

    }


    /**
     * Loads the requested users
     *
     * @return  Object                      Always this instance
     */
    public function load() {

        // Reset
        $this->_list = array();

        // Populate list from database
        $query = $this->_buildQuery()->get(Query::TABLE_USERS);
        foreach ($query->result() as $row) {
            $this->_list[] = (new UserModel())->set((array)$row);
        }

        return $this;

    }


    /**
     * Returns the total numbers of rows in the db for the current list (e.g. without limit)
     *
     * @return  int                         The total number of articles in the DB
     */
    public function getTotalCount() {
        return $this->_buildQuery(true)->count_all_results(Query::TABLE_USERS);
    }


    /**
     * Retrieves the current list of items
     *
     * @return  array                       The list with all items currently populated
     */
    public function toArray() {
        return $this->_list;
    }


    /**
     * Creates query (using CI QueryBuilder) for retrieving model content (users)
     *
     * @param   boolean     $filterOnly     Indicates the query type to be returned (retrieve vs. count)
     * @return  Object                      CI Database Instance for chaining purposes
     */
    function _buildQuery($filterOnly = false) {

        $this->db->select(Query::TABLE_USERS . ".*, " . Query::TABLE_USERGROUPS . ".name AS usergroup")
            ->join(Query::TABLE_USERGROUPS, Query::TABLE_USERGROUPS . ".id = usergroup_id");

        // Hook for customized filtering
        XCMS_Hooks::execute("users.build_query", array(
            &$this->db, $filterOnly
        ));

        return $this->db;

    }

}
?>