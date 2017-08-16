<?php

/**
 * MenuitemModel for XirtCMS (single menu item)
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class MenuitemModel extends XCMS_Model {

    /**
     * @var array
     * Attribute array for this model (valid attributes)
     */
    protected $_attr = array(
        "id", "menu_id", "name", "type", "level", "ordering", "parent_id", "home", "published", "sitemap", "relations",
        "source_url", "target_url", "uri", "anchor", "module_config", "relations"
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
        $query = ($id === null) ? Query::SEL_MENUITEMS_HOME : Query::SEL_MENUITEMS_ID;
        $result = $this->db->query($query, intval($id));
        if ($result->num_rows() == 0) {
            return false;
        }

        // Populate model (main)
        $this->set($result->row());
        $this->set("relations", 0);

        // Populate model (routing)
        switch ($this->get("type")) {

            case "module":

                $this->set(array(
                    "source_url"    => $this->get("source_url"),
                    "target_url"    => $this->get("target_url"),
                    "uri"            => null,
                    "anchor"        => (string)substr($this->get("uri"), 1),
                    "module_config"    => (int)$this->get("module_config")
                ));

            break;

            case "anchor":

                $this->set(array(
                    "source_url"    => null,
                    "target_url"    => null,
                    "uri"            => $this->get("uri"),
                    "anchor"        => (string)substr($this->get("uri"), 1),
                    "module_config"    => -1
                ));

            break;

            default:

                $this->set(array(
                    "source_url"    => null,
                    "uri"            => $this->get("uri"),
                    "anchor"        => "",
                    "module_config"    => -1
                ));

            break;

        }

        // Optional additional info
        if ($ext_rel && $this->get("route_id")) {

            $this->db->where("route_id", $this->get("route_id"));
            $count = $this->db->count_all_results(Query::TABLE_MENUITEMS_ROUTES);
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

        $this->db->delete(Query::TABLE_MENUITEMS_RELATIONS, array("node_id" => $this->get("id")));
        $this->db->delete(Query::TABLE_MENUITEMS, array("id" => $this->get("id")));

    }


    /**
     * Saves the given instance values in the DB as a new item (create)
     */
    private function _create() {

        $values = $this->getArray();
        $this->db->insert(Query::TABLE_MENUITEMS, $this->_filterRelations($values));

        if ($values["node_id"] = $this->db->insert_id()) {
            $this->db->insert(Query::TABLE_MENUITEMS_RELATIONS, $this->_filterContent($values));
        }

    }


    /**
     * Saves the given instance values in the DB as a existing item (update)
     */
    private function _update() {

        $this->db->replace(Query::TABLE_MENUITEMS, $this->_filterRelations($this->getArray()));
        $this->db->replace(Query::TABLE_MENUITEMS_RELATIONS, $this->_filterContent($this->getArray()));

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

}
?>