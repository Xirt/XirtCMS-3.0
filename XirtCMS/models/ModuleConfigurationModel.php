<?php

/**
 * Base model for retrieving single XirtCMS module configuration
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class ModuleConfigurationModel  extends XCMS_Model {

    /**
     * Attribute array for this model (valid attributes)
     * @var array
     */
    protected $_attr = array(
        "id", "name", "type", "default", "settings"
    );


    /**
     * Loads the requested module configuration
     *
     * @param   int         $id             The ID of the module configuration to load
     * @return  mixed                       This instance on success, null otherwise
     */
    public function load($id) {

        // Retrieve data (main)
        $result = $this->_buildQuery($id)->get(XCMS_Tables::TABLE_MODULES);
        if ($result->num_rows()) {

            $this->set($result->row());

            // Retrieve configuration (XML)
            $this->load->model("ModuleSettingsModel", "settings");
            if ($this->settings->initialize($this->get("type"))) {
                $this->settings->load($this->get("id"));
            }

            return $this->set("settings", $this->settings);

        }

        return null;

    }


    /**
     * Update given setting with given value
     *
     * @param   String      $name           The name of the setting to update
     * @param   String      $value          The new value for the given setting
     * @return  boolean                     True on success, false otherwise
     */
    public function setSetting($name, $value) {
        return $this->get("settings")->set($name, $value);
    }


    /**
     * Returns the value for the requested setting
     *
     * @param   String      $name           The name of the setting to return
     * @return  mixed                       The found value on success or null on failure
     */
    public function getSetting($name) {
        return $this->get("settings")->get($name);
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
     *
     * @return  Object                      Always this instance
     */
    public function remove() {

        // Remove item (incl. settings)
        $this->db->delete(XCMS_Tables::TABLE_MODULES,  array('id' => $this->get("id")));
        if ($this->settings->initialize($this->get("type"))) {

            foreach ($this->settings->toArray() as $setting) {
                $this->settings->remove($setting->name);
            }

            $this->settings->save();

        }

        return $this;

    }


    /**
     * Creates query (using CI QueryBuilder) for retrieving model content (module configuration)
     *
     * @param   int         $id             The id of the setting to load
     * @return  Object                      CI Database Instance for chaining purposes
     */
    protected function _buildQuery($id) {

        $this->db->where("id", intval($id));

        // Hook for customized filtering
        XCMS_Hooks::execute("moduleconfiguration.build_query", array(
            &$this, &$this->db, $id
        ));

        return $this->db;

    }


    /**
     * Saves the model as a new item
     *
     * @param   array       $values         The captured values from the GUI to save
     */
    private function _create($values) {
        $this->db->insert(XCMS_Tables::TABLE_MODULES, $values);
    }


    /**
     * Saves the model as an existing item (update)
     *
     * @param   array       $values         The captured values from the GUI to save
     */
    private function _update($values) {

        $settingsObject = $values["settings"];
        unset($values["settings"]);

        $this->db->replace(XCMS_Tables::TABLE_MODULES, $values);
        $settingsObject->save();

    }

}
?>