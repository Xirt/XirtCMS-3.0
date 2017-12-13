<?php

/**
 * Base model for retrieving XirtCMS article blocks
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class ArticleBlocksModel extends XCMS_Model {

    /**
     * Internal reference (ID) to the related model
     * @var int
     */
    private $_ref = -1;


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
        $this->load->model("ArticleBlockModel", false);

    }


    /**
     * Loads the article blocks for the given article ID
     *
     * @param   int           $id           The article ID for which to load the article blocks
     * @return  Object                      Always this instance
     */
    public function load($id) {

        // Reset
        $this->_list = array();

        // Populate list from database
        $query = $this->_buildQuery($id)->get(XCMS_Tables::TABLE_ARTICLES_BLOCKS);
        foreach ($query->result() as $row) {
            $this->_list[] = (new ArticleBlockModel())->set((array)$row);
        }

        $this->_ref = $id;
        return $this;

    }


    /**
     * Saves the current list of blocks
     *
     * @return  Object                      Always this instance
     */
    public function save() {

        $this->remove($this->_ref);
        foreach ($this->_list as $key => $block) {

            $block->set("ordering", $key);
            $block->save();

        }

        return $this;

    }



    /**
     * Adds new article block to list
     *
     * @param   Object      $block          The article block to add
     * @return  Object                      Always this instance
     */
    public function add(ArticleBlockModel $block) {

        $this->_list[] = $block;
        return $this;

    }


    /**
     * Returns the number of items in the list
     *
     * @return  int                         The number of items in the list
     */
    public function getTotalCount() {
        return count($this->_list);
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
     * Creates query (using CI QueryBuilder) for retrieving model content (article blocks)
     *
     * @param   int           $id           The article ID for which to load the article blocks
     * @return  Object                      CI Database Instance for chaining purposes
     */
    function _buildQuery($id) {

        $this->db->where("ref_id", intval($id))
            ->order_by("ordering", "ASC");

        // Hook for customized filtering
        XCMS_Hooks::execute("article_blocks.build_query", array(
            &$this, &$this->db, $id
        ));

        return $this->db;

    }


    /**
     * Removes article blocks in list or for the for given article ID
     *
     * @param   int         $id             The article ID to remove blocks for
     */
    public function remove($ref = null) {

        if (!is_int($id)) {

            // Update DB (referenced records)
            return $this->db->delete(XCMS_Tables::TABLE_ARTICLES_BLOCKS, array(
                "ref_id" => $id
            ));

        }

        // Update DB (listed records)
        foreach ($this->_list as $block) {
            $block->remove();
        }

    }

}
?>