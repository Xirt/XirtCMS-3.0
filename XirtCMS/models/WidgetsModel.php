<?php

/**
 * Base model for retrieving XirtCMS widgets
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class WidgetsModel extends XCMS_Model {

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

        // Load helpers
        $this->load->helper("article");

        // Load models
        $this->load->model("WidgetModel", false);

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
        $query = $this->_buildQuery()->get(XCMS_Tables::TABLE_WIDGETS);
        foreach ($query->result() as $row) {
            $this->_list[] = (new WidgetModel())->set((array)$row);
        }

        return $this;
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
     * Returns the total numbers of rows in the db for the current list (e.g. without limitations)
     *
     * @return  int                         The total number of items in the DB
     */
    public function getTotalCount() {
        return $this->_buildQuery(true)->count_all_results(XCMS_Tables::TABLE_WIDGETS);
    }


    /**
     * Creates query (using CI QueryBuilder) for retrieving model content (widgets)
     *
     * @param   boolean     $filterOnly     Indicates the query type to be returned (retrieve vs. count)
     * @return  Object                      CI Database Instance for chaining purposes
     */
    function _buildQuery($filterOnly = false) {

        // Hook for customized filtering
        XCMS_Hooks::execute("widgets.build_query", array(
            &$this->db, $filterOnly
        ));

        return $this->db;

    }

}
?>