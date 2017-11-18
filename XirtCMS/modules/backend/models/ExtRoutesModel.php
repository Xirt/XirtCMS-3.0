<?php

/**
 * Back end extension of BaseModel for retrieving multiple XirtCMS routes
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class ExtRoutesModel extends RoutesModel {

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
        XCMS_Hooks::reset("routes.build_query");
        XCMS_Hooks::add("routes.build_query", function($model, $stmt, $filterOnly) {

            if ($filter = trim($model->get("searchPhrase"))) {

                $stmt->or_like(array(
                    XCMS_Tables::TABLE_ROUTES . ".public_url"    => $filter,
                    XCMS_Tables::TABLE_ROUTES . ".target_url" => $filter
                ));

            }

            if (!$filterOnly) {

                if (($rowCount = $model->get("rowCount")) > 0) {
                    $stmt->limit($rowCount, ($model->get("current") - 1) * $rowCount);
                }

                $stmt->order_by($model->get("sortColumn"), $model->get("sortOrder"));

            }

        });

        return $this;

    }

}
?>