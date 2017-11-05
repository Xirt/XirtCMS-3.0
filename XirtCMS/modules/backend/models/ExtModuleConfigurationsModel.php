<?php

/**
 * Back end extension of BaseModel for retrieving multiple XirtCMS module configurations
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class ExtModuleConfigurationsModel extends ModuleConfigurationsModel {

    /**
     * Attribute array for this model (valid attributes)
     * @var array
     */
    protected $_attr = array("moduleType", "searchPhrase", "current", "rowCount", "sortColumn", "sortOrder");


    /**
     * Add hooks to influence parent behaviour
     */
    public function init() {

        // Hook for retrieval query
        XCMS_Hooks::reset("moduleconfigurations.build_query");
        XCMS_Hooks::add("moduleconfigurations.build_query", function($stmt, $filterOnly) {
            
            if ($filter = trim($this->get("searchPhrase"))) {
                
                $stmt->or_like(array(
                    XCMS_Tables::TABLE_MODULES . ".id"   => $filter,
                    XCMS_Tables::TABLE_MODULES . ".name" => $filter,
                    XCMS_Tables::TABLE_MODULES . ".type" => $filter
                ));
                
            }
            
            if ($filter = trim($this->get("moduleType"))) {                
                $stmt->or_where(XCMS_Tables::TABLE_MODULES . ".type", $filter);
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