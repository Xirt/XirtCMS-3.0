<?php

/**
 * Back end extension of BaseModel for retrieving multiple XirtCMS templates
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class ExtTemplatesModel extends TemplatesModel {

    /**
     * Attribute array for this model (valid attributes)
     * @var array
     */
    protected $_attr = array("searchPhrase", "current", "rowCount", "sortColumn", "sortOrder");


    /**
     * Add hooks to influence parent behaviour
     */
    public function init() {

        // Hook for retrieval query
        XCMS_Hooks::reset("templates.build_query");
        XCMS_Hooks::add("templates.build_query", function($stmt, $filterOnly) {

            if ($filter = trim($this->get("searchPhrase"))) {

                $stmt->or_like(array(
                    Query::TABLE_TEMPLATES . ".id"     => $filter,
                    Query::TABLE_TEMPLATES . ".name"   => $filter,
                    Query::TABLE_TEMPLATES . ".folder" => $filter
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