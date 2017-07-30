<?php

/**
 * Base model for retrieving XirtCMS Articles in frontend
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
            
            $this->_loadAttributes();
            $this->_parseAndFilterArticles();
            
        }
        
        return $this;

    }
    
    
    /**
     * Returns the total numbers of rows in the db for the current list (e.g. without limitations)
     *
     * @return  int                         The total number of articles in the DB
     */
    public function getTotalCount() {
        return $this->_buildArticleQuery()->count_all_results(Query::TABLE_ARTICLES);
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
     * @return  int                         The number of articles loaded
     */
    protected function _loadArticles() {
        
        $query = $this->_buildArticleQuery(func_get_args())->get(Query::TABLE_ARTICLES);
        foreach ($query->result() as $row) {
            $this->_list[$row->id] = (new ArticleModel())->set((array)$row);
        }
        
        return count($this->_list);
        
    }
    
    
    /**
     * Loads attributes for all items currently in the list
     */
    protected function _loadAttributes() {
        
        $query = $this->_buildAttributeQuery(func_get_args())->get(Query::TABLE_ARTICLES_ATTR);
        foreach ($query->result() as $row) {
            $this->_list[$row->ref_id]->setAttribute($row->name, $row->value);
        }
        
    }
    
    
    /**
     * Checks articles and removes invalid ones
     */
    protected function _parseAndFilterArticles() {
        
        foreach ($this->_list as $id => $article) {            
            XCMS_Hooks::execute("articles.parse_article", array($article));            
        }
        
    }
    
    
    /**
     * Creates query (using CI QueryBuilder) for retrieving relevant articles
     *
     * @return  Object                      CI Database Instance for chaining purposes
     */
    protected function _buildArticleQuery() {
        
        XCMS_Hooks::execute("articles.build_article_query", array(
            &$this->db)
        );
        
        return $this->db;
        
    }
    
    
    /**
     * Creates query (using CI QueryBuilder) for retrieving relevant attributes
     *
     * @return  Object                      CI Database Instance for chaining purposes
     */
    protected function _buildAttributeQuery() {
        
        $this->db->where_in("ref_id", array_keys($this->_list));
        XCMS_Hooks::execute("articles.build_attribute_query", array(
            &$this->db)
        );
        
        return $this->db;

    }
    
}
?>