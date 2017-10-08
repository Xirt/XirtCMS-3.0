<?php

/**
 * Controller for showing a multiple XirtCMS usergroups
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class ExtUsergroupsModel extends UsergroupsModel {

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
        XCMS_Hooks::reset("usergroups.build_article_query");
        XCMS_Hooks::add("usergroups.build_article_query", function($stmt, $filterOnly) {

            if ($filter = trim($this->get("searchPhrase"))) {

                $stmt->or_like(array(
                    Query::TABLE_USERGROUPS . ".id"                  => $filter,
                    Query::TABLE_USERGROUPS . ".name"                => $filter,
                    Query::TABLE_USERGROUPS . ".authorization_level" => $filter
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