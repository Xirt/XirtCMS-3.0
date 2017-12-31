<?php

/**
 * Base model for holding XirtCMS widget attributes
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class WidgetSettingsModel extends CI_Model {

    /**
     * Internal reference (ID) to the related widget
     * @var int
     */
    private $_widgetRef = -1;


    /**
     * The type of widget for which the settings apply
     * @var int|null
     */
    private $_widgetType = null;


    /**
     * The settings for the given widget
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
        $this->load->model("MenusModel", "menus");

    }


    /**
     * Loads the requested model
     *
     * @param   int         $id             The ID of the widget to load settings for
     * @param   String      $type           The type of the widget to load settings for
     * @return  mixed                       This object on success, null otherwise
     */
    public function load($id, $type) {

        $this->_widgetRef = $id;
        $this->_widgetType = $type;

        // Attempt to load widget settings
        foreach (array(APPPATH) as $mod_path) {

            // Check for configuration file
            if (!file_exists($mod_path . "widgets/" . $this->_widgetType . "/config.xml")) {
                continue;
            }

            // Attempt to load found configuration file
            if (($xml = @simplexml_load_file($mod_path . "widgets/" . $this->_widgetType . "/config.xml")) === false) {
                log_message("error", $this->_widgetType . ": Unable to load widget configuration");
            }

            $this->_parseSettings($xml->params);
            return $this;

        }

        log_message("error",  $this->_widgetType . ": Unable to find widget configuration");
        return null;

    }


    /**
     * Setter for a single setting
     *
     * @param   String      $name           The key of the setting to be set
     * @param   String      $value          The value for the setting
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
     * Validates the data currently in the instance
     *
     * @throws  Exception                   Thrown in case invalid data is detected within the instance
     */
    public function validate() {

        foreach ($this->_settings as $setting) {

            if (in_array($setting->type, array("select"))) {

                foreach ($setting->options as $option) {

                    if ($option->value == $setting->value) {
                        continue 2;
                    }

                }

                throw new ValidationException("[WidgetSettingsModel] Invalid widget setting for {$setting->name}.");

            }

        }

    }


    /**
     * Save the current settings in the DB
     *
     * @return  Object                      Always this instance
     */
    public function save() {

        // Updates all attributes in DB
        foreach ($this->_settings as $setting) {

            $this->db->replace(XCMS_Tables::TABLE_WIDGETS_SETTINGS, array(
                "widget_id" => $this->_widgetRef,
                "name"      => $setting->name,
                "value"     => $setting->value
            ));

        }

        return $this;

    }


    /**
     * Retrieves the current list of settings
     *
     * @return  array                       The list with the current settings
     */
    public function toArray() {
        return $this->_settings;
    }


    /**
     * Attempts to extract setting information from givne XML
     *
     * @param  Object       $simpleXML      The input XML to parse
     */
    private function _parseSettings($simpleXML) {

        $this->_settings = array();
        foreach($simpleXML->param as $setting) {

            try {
                $this->_settings[] = $this->_parseSetting($setting);
            } catch (Exception $e) {
                log_message("error", $this->_widgetType . ": " . $e->getMessage());
            }

        }

    }


    /**
     * Attempts to parse the given input XML for setting information
     *
     * @param   Object      $simpleXML      The input XML to parse
     * @return  array                       Containing parsed setting information
     */
    private function _parseSetting($simpleXML) {

        $attribs = new stdClass();
        foreach ($simpleXML->attributes() as $name => $value) {
            $attribs->$name = (string)$value;
        }

        // Check required name for setting
        if (!isset($attribs->name) || !strlen($attribs->name)) {
            throw new ConfigurationException("Invalid name for setting");
        }

        // Check required type for setting
        if (!isset($attribs->type) || !in_array($attribs->type, array("text", "textarea", "select", "menu", "category"))) {
            throw new ConfigurationException("Invalid type for setting");
        }

        // Check required label for setting
        if (!isset($attribs->label) || !strlen($attribs->label)) {
            throw new ConfigurationException("Invalid label for setting");
        }

        // Check optional default for setting
        if (!isset($attribs->default)) {
            log_message("debug", $this->_widgetType . ": No default given for setting (assuming null)");
        }

        // Create basic setting...
        $setting = new stdClass();
        $setting->name        = $attribs->name;
        $setting->type        = in_array($attribs->type, array("text", "textarea", "select")) ? $attribs->type : "select";
        $setting->label       = $attribs->label;
        $setting->description = isset($attribs->description) ? $attribs->description : null;
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
     * @param   Object      $simpleXML      The input XML to parse
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
            $option->name  = $attribs->name;
            $option->value = $attribs->value;

            // ... and add it
            $options[] = $option;

        }

        return $options;

    }


    /**
     * Returns all menus as option information
     *
     * @return  array                       Containing menu option information
     */
    private function _getMenuOptions() {

        $options = array();
        foreach ($this->menus->load()->toArray() as $menu) {

            // Create option...
            $option = new stdClass();
            $option->name  = $menu->get("name");
            $option->value = $menu->get("id");

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