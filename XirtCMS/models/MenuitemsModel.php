<?php

/**
 * Base model for retrieving XirtCMS menu items
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class MenuitemsModel extends XCMS_Model {

    /**
     * @var array
     * Internal list of items
     */
    private $_list = array();


    /**
     * Loads all requested items
     *
     * @param   $id                         The ID of the requested menu
     * @param   $activeOnly                 Toggless loading of unpublished items
     * @return  boolean                     Always true
     */
    public function load($id, $activeOnly = false) {

        $this->_list = array();

        $query = $this->_buildQuery($id)->get(Query::TABLE_MENUITEMS);
        foreach ($query->result() as $row) {

            if (!$activeOnly || $row->published) {
                $this->_list[] = $row;
            }

        }

        return true;

    }


    /**
     * Retrieves the current list of items
     *
     * @return  array                       A list with all items currently populated
     */
    public function toArray() {
        return $this->_list;
    }


    /**
     * Creates query (using CI QueryBuilder) for retrieving model content
     *
     * @param   $id                         The ID of the requested menu
     * @return  Object                      CI Database Instance for chaining purposes
     */
    function _buildQuery($id) {

        $this->db->select("xcms_menu_items.*, source_url, parent_id, ordering");

        $this->db->join(Query::TABLE_MENUITEMS_RELATIONS, "id = node_id");
        $this->db->join(Query::TABLE_MENUITEMS_ROUTES, "id = menuitem_id", "left");
        $this->db->join(Query::TABLE_ROUTES, "xcms_routes.id = route_id", "left");
        $this->db->where(array("menu_id" => $id));
        $this->db->order_by("level", "ASC");

        return $this->db;

    }

}
?>