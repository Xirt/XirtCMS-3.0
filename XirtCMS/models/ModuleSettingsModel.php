<?php

/**
 * Base model for retrieving XirtCMS module settings
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class ModuleSettingsModel extends CI_Model {

    /**
     * The internal reference to indicate whether the instance has been initialized
     * @var boolean
     */
    private $_init = false;


    /**
     * The internal reference of module for which the settings apply
     * @var int|null
     */
    private $_moduleRef = null;


    /**
     * The type of module for which the settings apply
     * @var string|null
     */
    private $_moduleType = null;


    /**
     * The settings for the given module
     * @var array
     */
    private $_settings = array();


    /**
     * CONSTRUCTOR
     * Instantiates controller with required helpers, libraries and helpers
     */
    function __construct() {

        parent::__construct();

        // Load helpers
        $this->load->helper("category");

        // Load models
        $this->load->model("MenusModel", false);

    }


    /**
     * Loads the current possible settings from the module configuration file
     *
     * @param   String      $type           The type of module to load the settings for
     * @return  boolean                     True on success, false on failure
     */
    public function initialize($type) {

        $this->_moduleType = $type;

        // Loop through all module master directories
        foreach (Modules::$locations as $location => $offset) {

            // Check for module directory...
            if (!is_dir($location . $this->_moduleType)) {
                continue;
            }

            // ... and attempt to load module configuration if found
            if ($this->_parseFile($location . $this->_moduleType . "/config.xml")) {
                return ($this->_init = true);
            }

        }

        return false;

    }


    /**
     * Loads the current settings from the database
     *
     * @param   int         $id             The ID of the module to load settings for
     * @return  Object                      Always this instance
     */
    public function load($id) {

        if ($this->_init && $id) {

            // Reset
            $this->_list = array();

            // Populate list from database
            $result = $this->_buildQuery($id)->get(XCMS_Tables::TABLE_MODULES_SETTINGS);
            foreach ($result->result() as $row) {
                $this->set($row->name, $row->value);
            }

        }

        $this->_moduleRef = $id;
        return $this;

    }


    /**
     * Update given setting with given value
     *
     * @param   String      $name           The name of the setting to update
     * @param   String      $setting        The new value for the given setting
     * @return  boolean                     True on success, false otherwise
     */
    public function set($name, $value) {

        foreach ($this->_settings as $key => $setting) {

            if ($setting->name == $name) {
                return ($this->_settings[$key]->value = $value);
            }

        }

        return false;

    }


    /**
     * Returns the value for the requested setting
     *
     * @param   String      $name           The name of the setting to return
     * @return  mixed                       The found value on success or null on failure
     */
    public function get($name) {

        foreach ($this->_settings as $setting) {

            if ($setting->name == $name) {
                return $setting->value;
            }

        }

        return null;

    }


    /**
     * Removes (unsets) the given setting
     *
     * @param   String      $name           The name of the setting to remove
     * @return  Object                      Always this instance
     */
    public function remove($name) {

        foreach ($this->_settings as $key => $setting) {

            if ($setting->name == $name) {
                unset($this->_settings[$key]);
            }

        }

        return $this;

    }


    /**
     * Save the current settings
     *
     * @return  Object                      Always this instance
     */
    public function save() {

        if ($this->_init) {

            // Remove old DB values
            $this->db->delete(XCMS_Tables::TABLE_MODULES_SETTINGS, array(
                "module_id" => $this->_moduleRef
            ));

            // Save all new DB values
            foreach ($this->_settings as $setting) {

                $this->db->replace(XCMS_Tables::TABLE_MODULES_SETTINGS, array(
                    "module_id" => $this->_moduleRef,
                    "name"      => $setting->name,
                    "value"     => $setting->value
                ));

            }

        }

        return $this;

    }


    /**
     * Retrieves the current list of settings
     *
     * @return   array                      List with the current settings
     */
    public function toArray() {
        return $this->_settings;
    }
    
    
    /**
     * Creates query (using CI QueryBuilder) for retrieving model content (module settings)
     *
     * @param   int         $id             The id of the module settings to load
     * @return  Object                      CI Database Instance for chaining purposes
     */
    protected function _buildQuery($id) {
        
        $this->db->where("module_id", intval($id));
        
        // Hook for customized filtering
        XCMS_Hooks::execute("modulesettings.build_query", array(
            &$this, &$this->db, $id
        ));
        
        return $this->db;
        
    }


    /**
     * Attempts to extract setting information from given file
     *
     * @param   String      $configFile     The path of the input file to load and parse
     * @return  boolean                     True on success, false on failure
     */
    private function _parseFile($configFile) {

        if (($xml = simplexml_load_file($configFile)) === false) {

            log_message("error", $this->_moduleType . ": Unable to open module configuration");
            return false;

        }

        return $this->_parseSettings($xml->params);

    }


    /**
     * Attempts to extract setting information from givne XML
     *
     * @param   String      $simpleXML      The input XML to parse
     * @return  boolean                     True on success, false on failure
     */
    private function _parseSettings($simpleXML) {

        $this->_settings = array();
        foreach($simpleXML->param as $setting) {

            try {

                $this->_settings[] = $this->_parseSetting($setting);

            } catch (Exception $e) {

                log_message("error", $this->_moduleType . ": " . $e->getMessage());
                return false;

            }

        }

        return true;

    }


    /**
     * Attempts to parse the given input XML for setting information
     *
     * @param   Object      $simpleXML      The input XML to parse as SimpleXML object
     * @return  array                       Containing parsed setting information
     */
    private function _parseSetting($simpleXML) {

        $attribs = new stdClass();
        foreach ($simpleXML->attributes() as $name => $value) {
            $attribs->$name = (string)$value;
        }

        // Check required name for setting
        if (!isset($attribs->name) || !strlen($attribs->name)) {
            throw new ConfigurationException("Invalid name for module setting");
        }

        // Check required type for setting
        if (!isset($attribs->type) || !in_array($attribs->type, array("text", "textarea", "select", "menu", "category"))) {
            throw new ConfigurationException("Invalid type for module setting");
        }

        // Check required label for setting
        if (!isset($attribs->label) || !strlen($attribs->label)) {
            throw new ConfigurationException("Invalid label for module setting");
        }

        // Check optional default for setting
        if (!isset($attribs->default)) {
            log_message("debug", $this->_moduleType . ": No default given for module setting (assuming null)");
        }

        // Create basic setting...
        $setting = new stdClass();
        $setting->name        = $attribs->name;
        $setting->type        = in_array($attribs->type, array("text", "textarea", "select")) ? $attribs->type : "select";
        $setting->label       = $attribs->label;
        $setting->description = $attribs->description ?? null;
        $setting->default     = isset($attribs->default) ? $attribs->default : null;
        $setting->value       = $setting->default;

        // ... and extend if needed
        if (in_array($attribs->type, array("select"))) {
            $setting->options = $this->_parseOptions($simpleXML);
        }

        // Special case: all menus (selection)
        if (in_array($attribs->type, array("menu"))) {
            $setting->options = $this->_getMenuOptions();
        }

        // Special case: all categories (selection)
        if (in_array($attribs->type, array("category"))) {
            $setting->options = $this->_getCategoryOptions();
        }

        return $setting;

    }


    /**
     * Attempts to parse the given input XML for option information
     *
     * @param   Object      $simpleXML      The input XML to parse as SimpleXML object
     * @return  array                       Containing parsed option information
     */
    private function _parseOptions($simpleXML) {

        // Check required options for setting
        if (!isset($simpleXML->option) || is_array($simpleXML->option)) {
            throw new ConfigurationException("Invalid options for setting");
        }

        $options = array();
        foreach ($simpleXML->option as $option) {

            $attribs = new stdClass();
            foreach ($option->attributes() as $name => $value) {
                $attribs->$name = (string)$value;
            }

            // Check required name for option
            if (!isset($attribs->name) || !strlen($attribs->name)) {
                throw new ConfigurationException("Invalid name for option");
            }

            // Check required value for option
            if (!isset($attribs->value) || !strlen($attribs->value)) {
                throw new ConfigurationException("Invalid value for option");
            }

            // Create option...
            $option = new stdClass();
            $option->name    = $attribs->name;
            $option->value    = $attribs->value;

            // ... and add it
            $options[] = $option;

        }

        return $options;

    }


    /**
     * Returns all menus as option information
     *
     * @return   array                      Containing menu option information
     */
    private function _getMenuOptions() {

        $options = array();
        foreach ((new MenusModel())->load()->toArray() as $menu) {

            // Create option...
            $option = new stdClass();
            $option->name    = $menu->name;
            $option->value    = $menu->id;

            // ... and add it
            $options[] = $option;

        }

        return $options;

    }


    /**
     * Returns all categories as option information
     *
     * @return  array                       Containing category option information
     */
    private function _getCategoryOptions() {

        $options = array();
        foreach (CategoryHelper::getCategoryTree()->toArray() as $category) {

            if ($category->level) {

                // Indentation for tree structure
                $padding = str_pad("", $category->level * 3, " ") . "- ";
                $category->name = str_replace(" ", "&nbsp;", $padding) . $category->name;

            }

            // Create option...
            $option = new stdClass();
            $option->name  = $category->name;
            $option->value = $category->node_id;

            // ... and add it
            $options[] = $option;

        }

        return $options;

    }

}
?>