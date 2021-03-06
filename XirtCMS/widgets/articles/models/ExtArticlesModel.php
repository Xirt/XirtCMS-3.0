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
    protected $_attr = array("categories", "limit", "sorting");


    /**
     * Add hooks to influence parent behaviour
     */
    public function init() {

        // Hook for article query
        XCMS_Hooks::reset("articles.build_article_query");
        XCMS_Hooks::add("articles.build_article_query", function($model, $stmt) {

            // Default query
            $stmt->select(XCMS_Tables::TABLE_ARTICLES . ".*")
                ->order_by($model->get("sorting"));

            // Optional: Specific category ID
            if ($categories = $model->get("categories")) {

                $stmt->join(XCMS_Tables::TABLE_ARTICLES_CATEGORIES, XCMS_Tables::TABLE_ARTICLES . ".id = article_id")
                    ->where_in("category_id", $categories);

            }

            // Optional: Limit amount of rows
            if (($limit = $model->get("limit")) && is_numeric($limit)) {
                $stmt->limit($limit);
            }

        });

        return $this;

    }

}
?>