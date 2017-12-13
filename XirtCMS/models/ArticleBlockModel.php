<?php

/**
 * Base model for retrieving single XirtCMS article block
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class ArticleBlockModel extends XCMS_Model {

    /**
     * Attribute array for this model (valid attributes)
     * @var array
     */
    protected $_attr = array(
        "ref_id", "ordering", "type", "content", "settings"
    );

    /**
     * Loads the requested article block by given article ID and ordering
     *
     * @param   mixed         $id           The article ID of the article block to load
     * @param   mixed         $ordering     The ordering of the article block to load
     * @return  mixed                       This instance on success, null otherwise
     */
    public function load($id, $ordering) {

        // Retrieve data
        $result = $this->_buildQuery($id)->get(XCMS_Tables::TABLE_ARTICLES_BLOCKS);
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

        $this->get("id") ? $this->_update() : $this->_create();
        return $this;

    }


    /**
     * Removes the instance from the DB
     */
    public function remove() {

        $this->db->delete(XCMS_Tables::TABLE_ARTICLES_BLOCKS,  array(
            "ordering" => $this->get("ordering"),
            "ref_id" => $this->get("ref_id")
        ));

    }


    /**
     * Creates query (using CI QueryBuilder) for retrieving model content (article block)
     *
     * @param   mixed         $id           The article ID of the article block to load
     * @param   mixed         $ordering     The ordering of the article block to load
     * @return  Object                      CI Database Instance for chaining purposes
     */
    protected function _buildQuery($id, $ordering) {

        $this->db
            ->where("ordering", intval($ordering))
            ->where("id", intval($id));

        // Hook for customized filtering
        XCMS_Hooks::execute("user.build_query", array(
            &$this, &$this->db, $id, $ordering
        ));

        return $this->db;

    }


    /**
     * Saves the given instance values in the DB as a new item (create)
     */
    private function _create() {
        $this->db->insert(XCMS_Tables::TABLE_ARTICLES_BLOCKS, $this->getArray());
    }


    /**
     * Saves the given instance values in the DB as a existing item (update)
     */
    private function _update() {
        $this->db->replace(XCMS_Tables::TABLE_ARTICLES_BLOCKS, $this->getArray());
    }

}
?>