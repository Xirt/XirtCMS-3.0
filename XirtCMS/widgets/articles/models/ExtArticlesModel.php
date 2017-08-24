<?php

/**
 * Controller for showing a multiple XirtCMS articles
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class ExtArticlesModel extends ArticlesModel {
    
    /**
     * @var array
     * Attribute array for this model (valid attributes)
     */
    protected $_attr = array("category", "limit", "sorting");
    
    
    /**
     * Add hooks to influence parent behaviour
     */
    public function init() {
        
        // Hook for article query
        XCMS_Hooks::reset("articles.build_article_query");
        XCMS_Hooks::add("articles.build_article_query", function($stmt) {
            
            // Default query
            $stmt->select(Query::TABLE_ARTICLES . ".*", Query::TABLE_USERS . ".username")
                ->join(Query::TABLE_USERS, Query::TABLE_USERS . ".id = author")
                ->order_by($this->get("sorting"));

            // Optional: Specific category ID
            if (($id = $this->get("category")) && is_numeric($id)) {

                $stmt->join(Query::TABLE_ARTICLES_CATEGORIES, Query::TABLE_ARTICLES . ".id = article_id")
                    ->where("category_id", $id);

            }

            // Optional: Limit amount of rows
            if (($limit = $this->get("limit")) && is_numeric($limit)) {
                $stmt->limit($limit);
            }
            
        });
            
        return $this;
        
    }

}
?>