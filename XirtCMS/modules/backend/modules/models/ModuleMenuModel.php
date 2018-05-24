<?php

/**
 * Base model for retrieving XirtCMS module menu parameters
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class ModuleMenuModel extends CI_Model {

    /**
     * The type of module for which the parameters apply
     * @var string|null
     */
    private $_moduleType = null;


    /**
     * The parameters for the given module
     * @var array
     */
    private $_params = array();


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
     * Loads the current possible parameters from the module configuration file
     *
     * @param   String      $type           The type of module to load the parameters for
     * @return  boolean                     True on success, false on failure
     */
    public function init($type) {

        $this->_moduleType = $type;

        $backup = Modules::$locations;
        Modules::$locations = array(
            APPPATH.'modules/frontend/' => '../modules/frontend/',
            APPPATH.'modules/shared/' => '../modules/shared/'
        ) + Modules::$locations;
        
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

        Modules::$locations = $backup;
        return false;

    }



    /**
     * Retrieves the current list of parameters
     *
     * @return   array                      List with the current parameters
     */
    public function toArray() {
        return $this->_params;
    }


    /**
     * Attempts to extract parameter information from given file
     *
     * @param   String      $configFile     The path of the input file to load and parse
     * @return  boolean                     True on success, false on failure
     */
    private function _parseFile($configFile) {

        if (($xml = simplexml_load_file($configFile)) === false) {

            log_message("error", $this->_moduleType . ": Unable to open module configuration");
            return false;

        }

		// Return parameters if configured
		if (isset($xml->menu) && isset($xml->menu->param)) {
			return $this->_parseSettings($xml->menu);
		}

        return true;

    }


    /**
     * Attempts to extract parameter information from given XML
     *
     * @param   String      $simpleXML      The input XML to parse
     * @return  boolean                     True on success, false on failure
     */
    private function _parseSettings($simpleXML) {

        $this->_params = array();
        foreach ($simpleXML->param as $param) {

            try {

                $this->_params[] = $this->_parseSetting($param);

            } catch (Exception $e) {

                log_message("error", $this->_moduleType . ": " . $e->getMessage());
                return false;

            }

        }

        return true;

    }


    /**
     * Attempts to parse the given input XML for parameter information
     *
     * @param   Object      $simpleXML      The input XML to parse as SimpleXML object
     * @return  array                       Containing parsed parameter information
     */
    private function _parseSetting($simpleXML) {

        $attribs = new stdClass();
        foreach ($simpleXML->attributes() as $name => $value) {
            $attribs->$name = (string)$value;
        }

        // Check required name for menu parameter
        if (!isset($attribs->name) || !strlen($attribs->name)) {
            throw new ConfigurationException("Invalid name for module menu parameter");
        }

        // Check required type for menu parameter
        if (!isset($attribs->type) || !in_array($attribs->type, array("text", "textarea", "select", "menu", "category"))) {
            throw new ConfigurationException("Invalid type for module menu parameter");
        }

        // Check required label for menu parameter
        if (!isset($attribs->label) || !strlen($attribs->label)) {
            throw new ConfigurationException("Invalid label for module menu parameter");
        }

        // Check required description for menu parameter
        if (isset($attribs->description) && !strlen($attribs->description)) {
            throw new ConfigurationException("Invalid description for module menu parameter");
        }

        // Check optional default for menu parameter
        if (!isset($attribs->default)) {
            log_message("debug", $this->_moduleType . ": No default given for module menu parameter (assuming null)");
        }

        // Create basic parameter...
        $param = new stdClass();
		$param->name        = $attribs->name;
        $param->type        = in_array($attribs->type, array("text", "textarea", "select")) ? $attribs->type : "select";
        $param->label       = $attribs->label;
        $param->description = $attribs->description ?? null;
        $param->default     = $attribs->default ?? null;
        $param->value       = $param->default;

        // ... and extend if needed
        if (in_array($attribs->type, array("select"))) {
            $param->options = $this->_parseOptions($simpleXML);
        }

        // Special case: all menus (selection)
        if (in_array($attribs->type, array("menu"))) {
            $param->options = $this->_getMenuOptions();
        }

        // Special case: all categories (selection)
        if (in_array($attribs->type, array("category"))) {
            $param->options = $this->_getCategoryOptions();
        }

        return $param;

    }


    /**
     * Attempts to parse the given input XML for option information
     *
     * @param   Object      $simpleXML      The input XML to parse as SimpleXML object
     * @return  array                       Containing parsed option information
     */
    private function _parseOptions($simpleXML) {

        // Check required options for parameter
        if (!isset($simpleXML->option) || is_array($simpleXML->option)) {
            throw new ConfigurationException("Invalid options for parameter");
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
     * @return   array                      Containing menu option information
     */
    private function _getMenuOptions() {

        $options = array();
        foreach ((new MenusModel())->load()->toArray() as $menu) {

            // Create option...
            $option = new stdClass();
            $option->name  = $menu->name;
            $option->value = $menu->id;

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