<?php

require APPPATH . "third_party/MX/Router.php";

/**
 * XirtCMS core class extending CI Router functionality with DB access
 * TODO :: Make instance rely on Query class for DB table information
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class XCMS_Router extends MX_Router {

    /**
     * @var String|null
     * Module configuration for the requested page
     */
    public $module_config = null;


    /**
     * @var int|null
     * Master node for the requested page
     */
    private $master_node = null;


    /**
     * @var array
     * Active nodes for the requested page
     */
    private $active_nodes = array();


    /**
     * CONSTRUCTOR
     * Initializes instance with looked-up route information
     *
     * @param   mixed       $routing        The CI routing parameters (normally an array)
     */
    public function __construct($routing = NULL) {

        $this->uri =& load_class("URI", "core");
        $this->config =& load_class("Config", "core");
        $this->enable_query_strings = (! is_cli() && $this->config->item("enable_query_strings") === TRUE);

        // If a directory override is configured, it has to be set before any dynamic routing logic
        is_array($routing) && isset($routing["directory"]) && $this->set_directory($routing["directory"]);

        if (!$this->_set_db_routing()) {

            log_message("info", "[XCMS] Routing not set based on DB information.");
            $this->_set_routing();

        }

        // Set any routing overrides that may exist in the main index file
        if (is_array($routing)) {

            empty($routing["controller"]) OR $this->set_class($routing["controller"]);
            empty($routing["function"])   OR $this->set_method($routing["function"]);

        }

        log_message("info", "[XCMS] Router initialized.");

    }


    /**
     * Attempts to set internal routing based on request URI information and DB data
     *
     * @return  boolean                     True on remapping, false otherwise
     */
    protected function _set_db_routing() {

        // Prevent redundant DB access
        if (!$this->uri->segments || $this->enable_query_strings) {
            return false;
        }

        // Include required DB classes
        require_once(APPPATH . "config/database.php");
        require_once(BASEPATH . "database/DB.php");

        // Retrieve URI from DB
        $dbc =& DB($db["default"], true);
        $dbc->join("xcms_menu_routes", "route_id = id", "left");
        $query = $dbc->get_where("xcms_routes", array(
            "source_url" => implode("/", $this->uri->segments)
        ));

        if ($route = $query->first_row()) {

            log_message("info", "[XCMS] Setting routing based on DB information.");

            // Set routing details
            $this->_set_request(explode("/", $route->target_url));
            $this->module_config = $route->module_config;

            // Set active menu items
            foreach ($query->result() as $row) {
                if ($row->menuitem_id) {
                    $this->active_nodes[] = $row->menuitem_id;
                }
            }

            if ($route->master) {

                // Retrieve master ID from DB
                $dbc =& DB($db["default"], true);
                $dbc->join("xcms_menu_routes", "route_id = id", "left");
                $query = $dbc->get_where("xcms_routes", array(
                    "id" => $route->master
                ));

                // Retrieve master
                if ($query->first_row()->menuitem_id) {
                    $this->master_node = $query->first_row()->menuitem_id;
                }

            }

            $dbc->close();
            return true;

        }

        $dbc->close();
        return false;

    }


    /**
     * Retrieves the current master node
     *
     * @param   boolean     $master         Toggle inclusion of the master node (if available)
     * return   array                       The currently active nodes
     */
    public function getActiveNodes($master = true) {

        if ($master && $this->master_node) {
            return array_merge($this->active_nodes, array($this->master_node));
        }

        return $this->active_nodes;

    }


    /**
     * Retrieves the current master node
     *
     * @return  mixed                       The current masternode configured (might be null if not set)
     */
    public function getMasterNode() {
        return $this->master_node;
    }

}
?>