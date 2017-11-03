<?php

/**
 * Base model for retrieving single XirtCMS menu item
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class MenuitemModel extends XCMS_Model {

    /**
     * Attribute array for this model (valid attributes)
     * @var array
     */
    protected $_attr = array(
        "id", "menu_id", "name", "type", "level", "ordering", "parent_id", "home", "published", "sitemap", "relations",
        "public_url", "target_url", "uri", "anchor", "module_config", "relations"
    );


    /**
     * Loads the requested menu item or home item (if no ID given)
     *
     * @param   int         $id             The ID of the menu to load (optional)
     * @param   boolean     $ext_rel        Toggless extending the item with relation information
     * @return  mixed                       This instance on success, null otherwise
     */
    public function load($id = null, $ext_rel = false) {

        // Retrieve data
        $result = $this->_buildQuery($id)->get(XCMS_Tables::TABLE_MENUITEMS);
        if ($result->num_rows() == 0) {
            return null;
        }

        // Populate model (main)
        $this->set($result->row());
        $this->set("relations", 0);

        // Populate model (routing)
        switch ($this->get("type")) {

            case "module":

                $this->set(array(
                    "target_url"    => $this->get("target_url"),
                    "public_url"    => $this->get("public_url"),
                    "anchor"        => (string)substr($this->get("uri"), 1),
                    "module_config" => (int)$this->get("module_config")
                ));

            break;

            case "anchor":

                $this->set(array(
                    "target_url"    => null,
                    "public_url"    => $this->get("uri"),
                    "anchor"        => (string)substr($this->get("uri"), 1),
                    "module_config" => -1
                ));

            break;

            default:

                $this->set(array(
                    "target_url"    => null,
                    "public_url"    => $this->get("uri"),
                    "anchor"        => "",
                    "module_config" => -1
                ));

            break;

        }

        // Optional additional info
        if ($ext_rel && $this->get("route_id")) {

            $this->db->where("route_id", $this->get("route_id"));
            $count = $this->db->count_all_results(XCMS_Tables::TABLE_MENUITEMS_ROUTES);
            $this->set("relations", $count ? $count - 1 : 0);

        }

        return $this;

    }


    /**
     * Saves the instance in the DB
     *
     * @return  Object                      Always this instance
     */
    public function save() {

        $this->get("id") ? $this->_update() : $this->_create();
        return $this;

    }


    /**
     * Removes the instance from the DB
     */
    public function remove() {

        $this->db->delete(XCMS_Tables::TABLE_MENUITEMS_RELATIONS, array("node_id" => $this->get("id")));
        $this->db->delete(XCMS_Tables::TABLE_MENUITEMS, array("id" => $this->get("id")));

    }


    /**
     * Creates query (using CI QueryBuilder) for retrieving model content (module configuration)
     *
     * @param   int         $id             The id of the setting to load
     * @return  Object                      CI Database Instance for chaining purposes
     */
    protected function _buildQuery($id) {

        $this->db->select(XCMS_Tables::TABLE_MENUITEMS . ".*, parent_id, level, ordering, public_url, target_url, route_id, module_config")
        ->join(XCMS_Tables::TABLE_MENUITEMS_RELATIONS, XCMS_Tables::TABLE_MENUITEMS . ".id = node_id", "inner")
        ->join(XCMS_Tables::TABLE_MENUITEMS_ROUTES, XCMS_Tables::TABLE_MENUITEMS . ".id = menuitem_id", "left")
        ->join(XCMS_Tables::TABLE_ROUTES, XCMS_Tables::TABLE_ROUTES . ".id = route_id", "left");

        ($id === null) ? $this->db->where("home", 1) : $this->db->where(XCMS_Tables::TABLE_MENUITEMS . ".id", $id);

        // Hook for customized filtering
        XCMS_Hooks::execute("menuitem.build_query", array(
            &$this->db, $id
        ));

        return $this->db;

    }


    /**
     * Saves the given instance values in the DB as a new item (create)
     */
    private function _create() {

        $values = $this->getArray();
        $this->db->insert(XCMS_Tables::TABLE_MENUITEMS, $this->_filterRelations($values));

        if ($values["node_id"] = $this->db->insert_id()) {
            $this->db->insert(XCMS_Tables::TABLE_MENUITEMS_RELATIONS, $this->_filterContent($values));
        }

    }


    /**
     * Filter all relation/route related attributes from given data set
     *
     * @param   array       $data           The data set to be filtered
     * @return  array                       The filtered data set
     */
    private function _filterRelations($data) {

        foreach ($data as $key => $value) {

            if (!in_array($key, array("id", "menu_id", "name", "type", "home", "published", "sitemap", "language", "uri"))) {
                unset($data[$key]);
            }

        }

        return $data;

    }


    /**
     * Filter all content/route related attributes from given data set
     *
     * @param   array       $data           The data set to be filtered
     * @return  array                       The filtered data set
     */
    private function _filterContent($data) {

        foreach ($data as $key => $value) {

            if (!in_array($key, array("node_id", "parent_id", "ordering", "level"))) {
                unset($data[$key]);
            }

            if ($key == "id") {
                $data["node_id"] = $value;
            }

        }

        return $data;

    }


    /**
     * Saves the given instance values in the DB as a existing item (update)
     */
    private function _update() {

        $this->db->replace(XCMS_Tables::TABLE_MENUITEMS, $this->_filterRelations($this->getArray()));
        $this->db->replace(XCMS_Tables::TABLE_MENUITEMS_RELATIONS, $this->_filterContent($this->getArray()));

    }

}
?>