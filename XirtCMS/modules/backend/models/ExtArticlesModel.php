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
    protected $_attr = array("filter", "page", "limit", "sortColumn", "sortOrder");

    /**
     * Add hooks to influence parent behaviour
     */
    public function init() {

        // Hook for article query
        XCMS_Hooks::reset("articles.build_article_query");
        XCMS_Hooks::add("articles.build_article_query", function($stmt, $filterOnly) {

            // Default query
            $stmt->select(Query::TABLE_ARTICLES . ".*, " . Query::TABLE_USERS . ".username AS author")
                ->join(Query::TABLE_USERS, Query::TABLE_USERS . ".id = author_id");

            // Optional: Filter result
            if ($filter = trim($this->get("filter"))) {

                $stmt->or_like(array(
                    Query::TABLE_ARTICLES . ".id"    => $filter,
                    Query::TABLE_ARTICLES . ".title" => $filter
                ));

            }

            if (!$filterOnly) {

                 // Optional: Limit amount of rows
                if (($limit = $this->get("limit")) && is_numeric($limit) && ($page = $this->get("page")) && is_numeric($page)) {
                    $stmt->limit($limit, ($page - 1) * $limit);
                }

                // Optional: Sort result
                $stmt->order_by($this->get("sortColumn"), $this->get("sortOrder"));

            }


        });

        return $this;

    }

}
?>