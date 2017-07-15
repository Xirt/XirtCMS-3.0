<?php

/**
 * Controller for showing a multiple XirtCMS articles
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class ArticlesModel extends XCMS_Model {

    /**
     * Internal list of items
     */
    private $_list = array();


    /**
     * CONSTRUCTOR
     * Instantiates controller with required helpers, libraries and models
     */
    public function __construct() {

        parent::__construct();

        // Load helpers
        $this->load->helper("article");

        // Load models
        $this->load->model("ArticleModel", false);

    }


    /**
     * Loads all requested items
     *
     * @param   Object      $attr           Object containing search parameters for query customization
     * @return  Object                      Always this instance
     */
    public function load($attr) {

        // Load article
        $query = $this->_buildArticleQuery($attr)->get(Query::TABLE_ARTICLES);
        foreach ($query->result() as $row) {
            $this->_list[$row->id] = (new ArticleModel())->set((array)$row);
        }

        // Load attributes
        $this->_loadAttributes();
        $this->_parseAndFilterArticles();

        return $this;

    }


    /**
     * Loads all requested items from given category
     *
     * @param   int         $id             The ID of the category for which to load the items
     * @param   Object      $attr           Object containing search parameters for query customization
     * @return  boolean                     True on success, false if no items could be loaded
     */
    public function loadFromCategory($id, $attr) {

        // Load article
        $query = $this->_buildArticleQuery($attr, false, $id)->get(Query::TABLE_ARTICLES);
        foreach ($query->result() as $row) {
            $this->_list[$row->id] = (new ArticleModel())->set((array)$row);
        }

        // Load attributes
        $this->_loadAttributes();
        $this->_parseAndFilterArticles();

        return $this;

    }


    /**
     * Loads attributes for all items currently in the list
     */
    private function _loadAttributes() {

        $query = $this->_buildAttributeQuery()->get(Query::TABLE_ARTICLES_ATTR);
        foreach ($query->result() as $row) {
            $this->_list[$row->ref_id]->setAttribute($row->name, $row->value);
        }

    }


    /**
     * Checks articles and removes invalid ones
     */
    private function _parseAndFilterArticles() {

        foreach ($this->_list as $id => $article) {

            // Check publish date
            $article->setAttribute("publish_date", ArticleHelper::getPublished($article));
            if ($article->getAttribute("publish_date", true) > new DateTime()) {

                unset($this->_list[$id]);
                continue;

            }

            // Check unpublish date
            $article->setAttribute("unpublish_date", ArticleHelper::getUnpublished($article));
            if ($article->getAttribute("unpublish_date", true) < new DateTime()) {

                unset($this->_list[$id]);
                continue;

            }

        }

    }


    /**
     * Sorts the loaded list by publish date
     *
     * @return  Object                      Always this instance
     */
    public function sort() {

        // Sorts by publish date
        usort($this->_list, function ($a, $b) {

            // Prepare publish date candidate A
            $publishDate1 = $a->getAttribute("publish_date", true);
            $publishDate2 = $b->getAttribute("publish_date", true);

            // Compare candidates
            if ($publishDate1 == $publishDate2) {
                return 0;
            }

            return ($a > $b) ? -1 : 1;

        });

        return $this;

    }


    /**
     * Returns the total numbers of rows in the db for the current list (e.g. without limit)
     *
     * @param   Object      $attr           Object containing search parameters for query customization
     * @param   int         $id             The ID of the category for which to load the items (optonal)
     * @return  int                         The total number of articles in the DB
     */
    public function getTotalCount($attr, $id = 0) {
        return $this->_buildQuery($attr, true, $category)->count_all_results(Query::TABLE_ARTICLES);
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
     * Creates query (using CI QueryBuilder) for retrieving relevant articles
     *
     * @param   Object      $attr           Object containing search parameters for query customization
     * @param   boolean     $filterOnly     Toggless between only setting the filter for the query (optional)
     * @param   int         $id             The ID of the category for which to load the items (optonal)
     * @return  Object                      CI Database Instance for chaining purposes
     */
    function _buildArticleQuery($attr, $filterOnly = false, $id = 0) {

        if ($attr->searchPhrase) {

            $this->db->or_like(array(
                "id"       => $attr->searchPhrase,
                "title"    => $attr->searchPhrase,
                "username" => $attr->searchPhrase
            ));

        }

        if ($id && is_int($id)) {
            $this->db->join(Query::TABLE_ARTICLES_CATEGORIES, Query::TABLE_ARTICLES_CATEGORIES . ".article_id = id");
            $this->db->where("category_id", $id);
        }

        if ($filterOnly) {
            return $this->db;
        }

        if ($attr->rowCount > 0) {
            $this->db->limit($attr->rowCount, ($attr->current - 1) * $attr->rowCount);
        }

        $this->db->select(Query::TABLE_ARTICLES . ".*, " . Query::TABLE_USERS . ".username");
        $this->db->join(Query::TABLE_USERS, Query::TABLE_USERS . ".id = " . Query::TABLE_ARTICLES . ".author");
        $this->db->where($attr->filters)->order_by($attr->sortColumn, $attr->sortOrder);

        return $this->db;

    }


    /**
     * Creates query (using CI QueryBuilder) for retrieving relevant attributes
     *
     * @return  Object                      CI Database Instance for chaining purposes
     */
    function _buildAttributeQuery() {
        return $this->db->where_in("ref_id", array_keys($this->_list));
    }

}
?>