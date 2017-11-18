<?php

/**
 * Base model for retrieving single XirtCMS widget
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class WidgetModel extends XCMS_Model {

    /**
     * Attribute array for this model (valid attributes)
     * @var array
     */
    protected $_attr = array(
        "id", "name", "type", "ordering", "published", "position", "page_all", "page_default", "page_module", "pages", "settings"
    );


    /**
     * CONSTRUCTOR
     * Instantiates controller with required helpers, libraries and helpers
     */
    public function __construct() {

        parent::__construct();

        // Loader models
        $this->load->model("WidgetSettingsModel", "settings");

    }


    /**
     * Loads the requested model
     *
     * @param   int         $id             The id of the widget to load
     * @return  mixed                       This instance on success, null otherwise
     */
    public function load($id) {

        // Retrieve data (main)
        $result = $this->_buildWidgetQuery($id)->get(XCMS_Tables::TABLE_WIDGETS);
        if ($result->num_rows()) {

            $pages = array();

            // Populate model (main)
            $this->set($result->row());

            // Retrieve configuration (XML)
            $this->settings->load($this->get("id"), $this->get("type"));
            $this->set("settings", $this->settings);

            // Enrich configuration (DB)
            $result = $this->_buildSettingsQuery($id)->get(XCMS_Tables::TABLE_WIDGETS_SETTINGS);
            foreach ($result->result() as $row) {
                $this->settings->set($row->name, $row->value);
            }

            // Retrieve data (page relations)
            $result = $this->_buildSettingsQuery($id)->get(XCMS_Tables::TABLE_WIDGETS_PAGES);
            foreach ($result->result_object() as $row) {
                $pages[] = $row->item_id;
            }

            return $this->set("pages", $pages);

        }

        return null;

    }


    /**
     * Sets a new value for the indicated setting
     *
     * @param   String      $name           The name of the setting
     * @param   String      $value          The new value of the setting
     * @return  boolean                     True on success, false otherwise
     */
    public function setSetting($name, $value) {
        return $this->get("settings")->set($name, $value);
    }


    /**
     * Retries the value for the indicated setting
     *
     * @param   String      $name           The name of the setting
     * @return  mixed                       The value of the indicated setting
     */
    public function getSetting($name) {
        $this->get("settings")->get($name);
    }


    /**
     * Validates the data currently in the instance
     *
     * @return  Object                      Always this instance
     * @throws  Exception                   Thrown in case invalid data is detected within the instance
     */
    public function validate() {

        if ($this->get("settings")) {
            $this->get("settings")->validate();
        }

        return $this;

    }


    /**
     * Saves the instance in the DB
     *
     * @return  Object                      Always this instance
     */
    public function save() {

        $this->get("id") ? $this->_update($this->getArray()) : $this->_create($this->getArray());
        return $this;

    }


    /**
     * Removes the instance from the DB
     */
    public function remove() {

        $this->db->delete(XCMS_Tables::TABLE_WIDGETS_SETTINGS, array(
            "widget_id" => $this->get("id")
        ));

        $this->db->delete(XCMS_Tables::TABLE_WIDGETS_PAGES, array(
            "widget_id" => $this->get("id")
        ));

        $this->db->delete(XCMS_Tables::TABLE_WIDGETS, array(
            "id" => $this->get("id")
        ));

    }


    /**
     * Creates query (using CI QueryBuilder) for retrieving model content (widget)
     *
     * @param   int         $id             The id of the widget to load
     * @return  Object                      CI Database Instance for chaining purposes
     */
    protected function _buildWidgetQuery($id) {

        $this->db->where("id", intval($id));

        // Hook for customized filtering
        XCMS_Hooks::execute("widget.build_query", array(
            &$this, &$this->db, $id
        ));

        return $this->db;

    }


    /**
     * Creates query (using CI QueryBuilder) for retrieving model content (widget's pages)
     *
     * @param   int         $id             The id of the widget to load pages for
     * @return  Object                      CI Database Instance for chaining purposes
     */
    protected function _buildPagesQuery($id) {

        $this->db->where("widget_id", intval($id));

        // Hook for customized filtering
        XCMS_Hooks::execute("widget.build_pages_query", array(
            &$this, &$this->db, $id
        ));

        return $this->db;

    }


    /**
     * Creates query (using CI QueryBuilder) for retrieving model content (widget's settings)
     *
     * @param   int         $id             The id of the widget to load settings for
     * @return  Object                      CI Database Instance for chaining purposes
     */
    protected function _buildSettingsQuery($id) {

        $this->db->where("widget_id", intval($id));

        // Hook for customized filtering
        XCMS_Hooks::execute("widget.build_settings_query", array(
            &$this->db, $id
        ));

        return $this->db;

    }


    /**
     * Saves the given instance values in the DB as a new item (create)
     *
     * @param   array       $values         The values from the instance to save
     */
    private function _create($values) {
        $this->db->insert(XCMS_Tables::TABLE_WIDGETS, $this->_filterConfiguration($values));
    }


    /**
     * Filter all page/settings related attributes from given data set
     *
     * @param   array       $data           The data set to be filtered
     * @return  array                       The filtered data set
     */
    private function _filterConfiguration($data) {

        foreach ($data as $key => $value) {

            if (in_array($key, array("settings", "pages"))) {
                unset($data[$key]);
            }

        }

        return $data;

    }


    /**
     * Saves the given instance values in the DB as a existing item (update)
     *
     * @param   array       $values         The values from the instance to save
     */
    private function _update($values) {

        $this->db->replace(XCMS_Tables::TABLE_WIDGETS, $this->_filterConfiguration($values));
        $this->_updatePages($values["pages"]);
        $values["settings"]->save();

    }


    /**
     * Saves the given pages into the DB for this instance
     *
     * @param   array       $pages          List with page IDs to save
     */
    private function _updatePages($pages) {

        // Validate given pages
        if (!is_array($pages) || !count($pages)) {
            return;
        }

        // Prepare...
        $data = array();
        $id = $this->get("id");

        foreach (array_unique($pages) as $page) {

            array_push($data, array(
                "widget_id" => $id,
                "item_id"   => $page
            ));

        }

        // ... and insert data
        $this->db->delete(XCMS_Tables::TABLE_WIDGETS_PAGES, array("widget_id" => $id));
        $this->db->insert_batch(XCMS_Tables::TABLE_WIDGETS_PAGES, $data);

    }

}
?>