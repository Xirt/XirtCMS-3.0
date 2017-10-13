<?php

/**
 * Model for list of templates
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class TemplatesModel extends XCMS_Model {

    /**
     * Internal list of items
     * @var array
     */
    protected $_list = array();


    /**
     * CONSTRUCTOR
     * Instantiates controller with required helpers, libraries and models
     */
    public function __construct() {

        parent::__construct();

        // Load models
        $this->load->model("TemplateModel", false);

    }


    /**
     * Loads all requested items
     *
     * @return  Object                      Always this instance
     */
    public function load() {

        // Reset
        $this->_list = array();
    
        // Populate list from database
        $query = $this->_buildQuery()->get(Query::TABLE_TEMPLATES);
        foreach ($query->result() as $row) {
            $this->_list[] = (new TemplateModel())->set((array)$row);
        }

        return $this;

    }


    /**
     * Returns the total numbers of rows in the db for the current list (e.g. without limitations)
     *
     * @return  int                         The total number of items in the DB
     */
    public function getTotalCount() {
        return $this->_buildQuery(true)->count_all_results(Query::TABLE_MENUS);
    }


    /**
     * Retrieves the current list of items
     *
     * @return  Array                       List with the current items
     */
    public function toArray() {
        return $this->_list;
    }


    /**
     * Creates query (using CI QueryBuilder) for retrieving model content (articles)
     *
     * @param   boolean     $filterOnly     Indicates the query type to be returned (retrieve vs. count)
     * @return  Object                      CI Database Instance for chaining purposes
     */
    function _buildQuery($filterOnly = false) {

        // Hook for customized filtering
        $this->db->where_in("ref_id", array_keys($this->_list));
        XCMS_Hooks::execute("templates.build_query", array(
            &$this->db)
        );

        return $this->db;

    }

}
?>