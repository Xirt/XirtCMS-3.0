<?php

/**
 * Base model for retrieving single XirtCMS route
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class RouteModel extends XCMS_Model {

    /**
     * Attribute array for this model (valid attributes)
     * @var array
     */
    protected $_attr = array(
        "id", "public_url", "target_url", "menu_items", "module_config", "master"
    );


    /**
     * CONSTRUCTOR
     * Instantiates controller with required helpers, libraries and helpers
     */
    public function __construct() {

        parent::__construct();

        // Load helpers
        $this->load->helper("route");

    }


    /**
     * Loads the requested route
     *
     * @param   int         $id             The id of the route to load
     * @return  mixed                       This instance on success, null otherwise
     */
    public function load($id) {

        // Retrieve data
        $result = $this->_buildQuery($id)->get(XCMS_Tables::TABLE_ROUTES);
        if ($result->num_rows()) {

            $this->set($result->row());
            return $this;

        }

        return null;

    }


    /**
     * Saves the instance in the DB
     *
     * @return  Object                      Always this instance
     */
    public function save() {

        // Upsert into database...
        $this->db->replace(XCMS_Tables::TABLE_ROUTES, array(
            "id"            => $this->get("id"),
            "public_url"    => $this->get("public_url"),
            "target_url"    => $this->get("target_url"),
            "module_config" => $this->get("module_config"),
            "master"        => $this->get("master") ? $this->get("master") : NULL
        ));

        // ... and update item ID (for creations)
        $this->set("id", $this->db->insert_id());
        return $this;

    }


    /**
     * Removes the instance from the DB
     */
    public function remove() {

        // Remove route
        RouteHelper::removeRelation($this->get("id"));
        $this->db->delete(XCMS_Tables::TABLE_ROUTES,  array(
            "id" => $this->get("id")
        ));

    }


    /**
     * Creates query (using CI QueryBuilder) for retrieving model content (route)
     *
     * @param   int         $id             The id of the route to load
     * @return  Object                      CI Database Instance for chaining purposes
     */
    protected function _buildQuery($id) {

        ($id !== null) ? $this->db->where("id", $id) : $this->db->where("active", 1);

        $this->db->select("id, public_url, target_url, module_config, master, count(menuitem_id) as menu_items")
            ->join(XCMS_Tables::TABLE_MENUITEMS_ROUTES, "route_id = id", "left")
            ->group_by("id, public_url, target_url, module_config, master")
            ->where("id", intval($id));

        // Hook for customized filtering
        XCMS_Hooks::execute("route.build_query", array(
            &$this, &$this->db, $id
        ));

        return $this->db;

    }

}
?>