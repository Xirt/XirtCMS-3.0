<?php

/**
 * Base model for retrieving XirtCMS articles
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2018
 * @package     XirtCMS
 */
class ArticlesModel extends XCMS_Model {

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
        $this->load->model("ArticleModel", false);

    }


    /**
     * Loads all requested items
     *
     * @return  Object                      Always this instance
     */
    public function load() {

        if ($this->_loadArticles()) {

            $this->_loadUsers();
            $this->_loadContent();
            $this->_loadAttributes();
            $this->_parseAndFilterArticles();

        }

        return $this;

    }


    /**
     * Returns the total numbers of rows in the db for the current list (e.g. without limitations)
     *
     * @return  int                         The total number of items in the DB
     */
    public function getTotalCount() {
        return $this->_buildArticleQuery(true)->count_all_results(XCMS_Tables::TABLE_ARTICLES);
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
     * Loads all requested items
     *
     * @return  int                         The number of items loaded
     */
    protected function _loadArticles() {

        $query = $this->_buildArticleQuery()->get(XCMS_Tables::TABLE_ARTICLES);
        foreach ($query->result() as $row) {
            $this->_list[$row->id] = (new ArticleModel())->set((array)$row);
        }

        return count($this->_list);

    }


    /**
     * Loads content for all items currently in the list
     */
    protected function _loadContent() {

        $query = $this->_buildContentQuery()->get(XCMS_Tables::TABLE_ARTICLES_BLOCKS);
        foreach ($query->result() as $row) {

            $this->_list[$row->ref_id]->setArticleBlocks(
                (new ArticleBlockModel())->set((array)$row)
            );

        }

    }


    /**
     * Loads users for all items currently in the list
     */
    protected function _loadUsers() {

        $authors = array();

        // Retrieve authors
        $query = $this->_buildUsersQuery()->get(XCMS_Tables::TABLE_USERS);
        foreach ($query->result() as $row) {
            $authors[$row->id] = (new UserModel())->set("username", $row->username);
        }

        // Merge authors into articles
        foreach ($this->_list as $id => $article) {
            $this->_list[$id]->setAuthor($authors[$article->get("author_id")]);
        }

    }


    /**
     * Loads attributes for all items currently in the list
     */
    protected function _loadAttributes() {

        $query = $this->_buildAttributeQuery()->get(XCMS_Tables::TABLE_ARTICLES_ATTR);
        foreach ($query->result() as $row) {
            $this->_list[$row->ref_id]->setAttribute($row->name, $row->value);
        }

    }


    /**
     * Checks articles and removes invalid ones
     */
    protected function _parseAndFilterArticles() {

        foreach ($this->_list as $article) {

            XCMS_Hooks::execute("articles.parse_article", array(
                &$article
            ));

        }

    }


    /**
     * Creates query (using CI QueryBuilder) for retrieving model content (articles)
     *
     * @param   boolean     $filterOnly     Indicates the query type to be returned (retrieve vs. count)
     * @return  Object                      CI Database Instance for chaining purposes
     */
    protected function _buildArticleQuery($filterOnly = false) {

        // Hook for customized filtering
        XCMS_Hooks::execute("articles.build_article_query", array(
            &$this, &$this->db, $filterOnly
        ));

        return $this->db;

    }


    /**
     * Creates query (using CI QueryBuilder) for retrieving article blocks (content)
     *
     * @return  Object                      CI Database Instance for chaining purposes
     */
    protected function _buildContentQuery() {

        $articles = array();
        foreach ($this->_list as $article) {
            $articles[] = $article->get("id");
        }

        // Create requested query
        $this->db->where_in("ref_id", $articles);
        XCMS_Hooks::execute("articles.build_content_query", array(
            &$this, &$this->db, $articles)
        );

        return $this->db;

    }


    /**
     * Creates query (using CI QueryBuilder) for retrieving relevant users (authors)
     *
     * @return  Object                      CI Database Instance for chaining purposes
     */
    protected function _buildUsersQuery() {

        $authors = array();
        foreach ($this->_list as $article) {
            $authors[] = $article->get("author_id");
        }

        // Create requested query
        $this->db->where_in("id", array_unique($authors));
        XCMS_Hooks::execute("articles.build_users_query", array(
            &$this, &$this->db)
        );

        return $this->db;

    }


    /**
     * Creates query (using CI QueryBuilder) for retrieving relevant attributes
     *
     * @return  Object                      CI Database Instance for chaining purposes
     */
    protected function _buildAttributeQuery() {

        // Hook for customized filtering
        $this->db->where_in("ref_id", array_keys($this->_list));
        XCMS_Hooks::execute("articles.build_attribute_query", array(
            &$this, &$this->db)
        );

        return $this->db;

    }

}
?>