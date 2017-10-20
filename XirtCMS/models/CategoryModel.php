<?php

/**
 * Base model for retrieving single XirtCMS category
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class CategoryModel extends XCMS_Model {

    /**
     * Attribute array for this model (valid attributes)
     * @var array
     */
    protected $_attr = array(
        "id", "node_id", "parent_id", "name", "level", "ordering", "published", "parent_id"
    );


    /**
     * Loads the requested category
     *
     * @param   int         $id             The id of the category to load
     * @return  mixed                       This instance on success, null otherwise
     */
    public function load($id) {

        // Retrieve data from DB
        $result = $this->_buildQuery($id)->get(XCMS_Tables::TABLE_CATEGORIES);
        if ($result->num_rows()) {

            // Populate model
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

        $this->get("id") ? $this->_update() : $this->_create();
        return $this;

    }


    /**
     * Removes the instance from the DB
     */
    public function remove() {

        // Make sure item has no children
        $result = $this->db->get_where(XCMS_Tables::TABLE_CATEGORIES, array("parent_id", $this->get("id")));
        if ($result->num_rows()) {
            return null;
        }

        // Remove relations
        $this->db->where("node_id", $this->get("id"))
        ->delete(XCMS_Tables::TABLE_CATEGORIES_RELATIONS);

        // Remove item
        $this->db->where("id", $this->get("id"))
        ->delete(XCMS_Tables::TABLE_CATEGORIES);

    }


    /**
     * Creates query (using CI QueryBuilder) for retrieving model content (category)
     *
     * @param   int         $id             The id of the category to load
     * @return  Object                      CI Database Instance for chaining purposes
     */
    private function _buildQuery($id) {

        $this->db->join(XCMS_Tables::TABLE_CATEGORIES_RELATIONS, "id = node_id");
        $this->db->where(array("id" => $id));

        // Hook for customized filtering
        XCMS_Hooks::execute("category.build_query", array(
            &$this->db, $id
        ));

        return $this->db;

    }


    /**
     * Saves the instance in the DB as a new item (create)
     */
    private function _create() {

        $values = $this->getArray();
        $this->db->insert(XCMS_Tables::TABLE_CATEGORIES, $this->_filterRelations($values));

        if ($values["node_id"] = $this->db->insert_id()) {
            $this->db->insert(XCMS_Tables::TABLE_CATEGORIES_RELATIONS, $this->_filterContent($values));
        }

    }


    /**
     * Saves the instance in the DB as a existing item (update)
     */
    private function _update() {

        $this->db->replace(XCMS_Tables::TABLE_CATEGORIES, $this->_filterRelations($this->getArray()));
        $this->db->replace(XCMS_Tables::TABLE_CATEGORIES_RELATIONS, $this->_filterContent($this->getArray()));

    }


    /**
     * Filter all relation related attributes from given data set
     *
     * @param   array       $data           The data set to be filtered
     * @return  array                       The filtered data set
     */
    private function _filterRelations($data) {

        foreach ($data as $key => $value) {

            if (!in_array($key, array("id", "name", "published"))) {
                unset($data[$key]);
            }

        }

        return $data;

    }


    /**
     * Filter all content related attributes from given data set
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