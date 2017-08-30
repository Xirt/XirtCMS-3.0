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
     * Add hooks to influence parent behaviour
     */
    public function init() {

        // Hook for article query
        XCMS_Hooks::reset("articles.build_article_query");
        XCMS_Hooks::add("articles.build_article_query", function($stmt) {

            // Default query
            $stmt->select(Query::TABLE_ARTICLES . ".*", Query::TABLE_USERS . ".username")
                ->join(Query::TABLE_USERS, Query::TABLE_USERS . ".id = author");
            
        });
            
        return $this;
        
    }

}
?>