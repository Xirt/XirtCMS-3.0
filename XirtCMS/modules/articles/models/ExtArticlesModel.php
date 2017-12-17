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
     * Attribute array for this model (valid attributes)
     * @var array
     */
    protected $_attr = array("category", "limit", "sorting");


    /**
     * Add hooks to influence parent behaviour
     */
    public function __construct() {

        parent::__construct();

        // Remove & add (prevent duplicate hooks in case of no garbage collection)
        XCMS_Hooks::remove("articles.build_article_query", array($this, "_buildQueryCallback"));
        XCMS_Hooks::add("articles.build_article_query", array($this, "_buildQueryCallback"));

    }

    /**
     * Clean-up the created hook to not affect other processes
     */
    function __destruct() {

        XCMS_Hooks::remove("articles.build_article_query", array($this, "_buildQueryCallback"));

    }


    /**
     * The callback method for the hook 'build_article_query'
     *
     * @param   object      $stmt           The DB statement used for article retrieval
     */
    public static function _buildQueryCallback($model, $stmt) {

        // Default query
        $stmt->select(XCMS_Tables::TABLE_ARTICLES . ".*", XCMS_Tables::TABLE_USERS . ".username")
            ->join(XCMS_Tables::TABLE_USERS, XCMS_Tables::TABLE_USERS . ".id = author_id")
            ->order_by($model->get("sorting"));

        // Optional: Specific category ID
        if (($id = $model->get("category")) && is_numeric($id)) {

            $stmt->join(XCMS_Tables::TABLE_ARTICLES_CATEGORIES, XCMS_Tables::TABLE_ARTICLES . ".id = article_id")
                ->where("category_id", $id);

        }

        // Optional: Limit amount of rows
        if (($limit = $model->get("limit")) && is_numeric($limit)) {
            $stmt->limit($limit);
        }

    }

}
?>