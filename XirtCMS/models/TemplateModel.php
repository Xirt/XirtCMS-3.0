<?php

/**
 * Base model for retrieving single XirtCMS template
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class TemplateModel extends XCMS_Model {

    /**
     * Attribute array for this model (valid attributes)
     * @var array
     */
    protected $_attr = array(
        "id", "name", "folder", "published", "positions"
    );


    /**
     * Loads the requested template by given ID
     *
     * @param   int         $id             The id of the template to load (optional)
     * @param   boolean     $positions      Toggle loading of positions for this template (optional)
     * @return  mixed                       This instance on success, null otherwise
     */
    public function load($id = null, $positions = true) {
        return $this->loadById($id, $positions);
    }


    /**
     * Loads the requested template by given ID
     *
     * @param   int         $id             The id of the template to load
     * @param   boolean     $positions      Toggle loading of positions for this template
     * @return  mixed                       This instance on success, null otherwise
     */
    public function loadById($id = null, $positions = true) {

        // Retrieve data (main)
        $result = $this->_buildQuery($id)->get(XCMS_Tables::TABLE_TEMPLATES);
        if ($result->num_rows()) {

            // Populate model (main)
            $this->set($result->row());

            if ($positions) {

                // Populate model (positions)
                $positions = array();
                $query = $this->db->order_by("position")->get_where(XCMS_Tables::TABLE_TEMPLATES_POSITIONS, array("template_id" => $this->get("id")));
                foreach ($query->result() as $row) {
                    $positions[] = $row->position;
                }

                $this->set("positions", $positions);

            }

            return $this;

        }


        return null;

    }


    /**
     * Loads the requested template by given folder
     *
     * @param   String      $folder         The folder of the template to load
     * @param   boolean     $positions      Toggle loading of positions for this template (optional)
     * @return  mixed                       This instance on success, null otherwise
     */
    public function loadByFolder($folder = null, $positions = true) {

        // Retrieve data (main)
        $result = $this->db->get_where(XCMS_Tables::TABLE_TEMPLATES, array("folder" => $folder));
        if ($result->num_rows()) {

            // Populate model (main)
            $this->set($result->row());

            if ($positions) {

                // Populate model (positions)
                $positions = array();
                $query = $this->db->order_by("position")->get_where(XCMS_Tables::TABLE_TEMPLATES_POSITIONS, array("template_id" => $this->get("id")));
                foreach ($query->result() as $row) {
                    $positions[] = $row->position;
                }

                $this->set("positions", $positions);

            }

            return $this;

        }

        return null;

    }


    /**
     * Validates the data currently in the instance
     *
     * @return  Object                      Always this instance
     * @throws  Exception                   Thrown in case invalid data is detected within the instance
     */
    public function validate() {

        // Validate uniqueness of given folder
        $result = $this->db->get_where(XCMS_Tables::TABLE_TEMPLATES, array("folder" => $this->get("folder")));
        if ($result->num_rows() > 0 && (!$this->get("id") || $result->row()->id != $this->get("id"))) {
            throw new ValidationException("The chosen folder is already in use for a different template.");
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

        $this->db->delete(XCMS_Tables::TABLE_TEMPLATES_POSITIONS,  array(
            "template_id" => $this->get("id")
        ));

        $this->db->delete(XCMS_Tables::TABLE_TEMPLATES,  array(
            "id" => $this->get("id")
        ));

    }


    /**
     * Saves the instance in the DB as a new item (create)
     */
    private function _create() {

        $this->db->insert(XCMS_Tables::TABLE_TEMPLATES, array(
            "name"   => $this->get("name"),
            "folder" => $this->get("folder")
        ));

    }


    /**
     * Saves the instance in the DB as a existing item (update)
     */
    private function _update() {

        $this->db->replace(XCMS_Tables::TABLE_TEMPLATES, array(
            "id"        => $this->get("id"),
            "name"      => $this->get("name"),
            "folder"    => $this->get("folder"),
            "published" => $this->get("published")
        ));

        // Update related positions
        $this->db->delete(XCMS_Tables::TABLE_TEMPLATES_POSITIONS, array("template_id" => $this->get("id")));
        foreach ($this->get("positions") as $position) {

            $this->db->insert(XCMS_Tables::TABLE_TEMPLATES_POSITIONS, array(
                "template_id" => $this->get("id"),
                "position"    => $position
            ));

        }

    }


    /**
     * Creates query (using CI QueryBuilder) for retrieving model content (template)
     *
     * @param   int         $id             The id of the template to load
     * @return  Object                      CI Database Instance for chaining purposes
     */
    protected function _buildQuery($id = null) {

        ($id !== null) ? $this->db->where("id", intval($id)) : $this->db->where("published", 1);

        // Hook for customized filtering
        XCMS_Hooks::execute("template.build_query", array(
            &$this, &$this->db, $id
        ));

        return $this->db;

    }


}
?>