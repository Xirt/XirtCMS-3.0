<?php

/**
 * Back end extension of BaseModel for retrieving multiple XirtCMS settings
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class ExtSettingsModel extends SettingsModel {

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
        XCMS_Hooks::reset("settings.build_query");
        XCMS_Hooks::add("settings.build_query", function($model, $stmt, $filterOnly) {

            if ($filter = trim($model->get("searchPhrase"))) {

                $stmt->or_like(array(
                    XCMS_Tables::TABLE_CONFIGURATION . ".name"  => $filter,
                    XCMS_Tables::TABLE_CONFIGURATION . ".value" => $filter
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