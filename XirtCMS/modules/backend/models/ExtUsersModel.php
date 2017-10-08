<?php

/**
 * Controller for showing a multiple XirtCMS users
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class ExtUsersModel extends UsersModel {

    /**
     * @var array
     * Attribute array for this model (valid attributes)
     */
    protected $_attr = array("searchPhrase", "current", "rowCount", "sortColumn", "sortOrder");


    /**
     * Add hooks to influence parent behaviour
     */
    public function init() {

        // Hook for article query
        XCMS_Hooks::reset("users.build_article_query");
        XCMS_Hooks::add("users.build_article_query", function($stmt, $filterOnly) {

            if ($filter = trim($this->get("searchPhrase"))) {

                $stmt->or_like(array(
                    Query::TABLE_USERS . ".id"        => $filter,
                    Query::TABLE_USERS . ".username"  => $filter,
                    Query::TABLE_USERS . ".email"     => $filter,
                    Query::TABLE_USERS . ".real_name" => $filter,
                    Query::TABLE_USERGROUPS . ".name" => $filter
                ));

            }

            if (!$filterOnly) {

                if (($rowCount = $this->get("rowCount")) > 0) {
                    $stmt->limit($rowCount, ($this->get("current") - 1) * $rowCount);
                }

                $stmt->order_by($this->get("sortColumn"), $this->get("sortOrder"));

            }


        });

        return $this;

    }

}
?>