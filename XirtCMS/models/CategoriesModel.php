<?php

/**
 * Base model for retrieving XirtCMS categories
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class CategoriesModel extends XCMS_Model {

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
        $this->load->model("CategoryModel", false);

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
        $query = $this->_buildQuery()->get(XCMS_Tables::TABLE_CATEGORIES);
        foreach ($query->result() as $row) {
            $this->_list[] = (new CategoryModel())->set((array)$row);
        }

        return $this;

    }


    /**
     * Returns the total numbers of rows in the db for the current list (e.g. without limitations)
     *
     * @return  int                         The total number of items in the DB
     */
    public function getTotalCount() {
        return $this->_buildQuery(true)->count_all_results(XCMS_Tables::TABLE_CATEGORIES);
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

        $this->db->select(XCMS_Tables::TABLE_CATEGORIES . ".*");
        $this->db->select("level, ordering, parent_id, count(category_id) as articles");
        $this->db->join(XCMS_Tables::TABLE_CATEGORIES_RELATIONS, "node_id = id");
        $this->db->join(XCMS_Tables::TABLE_ARTICLES_CATEGORIES, "category_id = id", "left");
        $this->db->group_by("id, name, level, ordering, parent_id");
        $this->db->order_by("level", "ASC");

        // Front-end filter for unpublished items
        if (!XCMS_Config::get("XCMS_BACKEND")) {
            $this->db->where("published", 1);
        }

        // Hook for customized filtering
        XCMS_Hooks::execute("categories.build_categories_query", array(
            &$this, &$this->db, $filterOnly
        ));

        return $this->db;

    }

}
?>