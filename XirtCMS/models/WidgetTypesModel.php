<?php

/**
 * Base model for retrieving XirtCMS widget types
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class WidgetTypesModel extends XCMS_Model {

    /**
     * Internal list of items
     * @var array
     */
    private $_list = array();


    /**
     * Loads all requested items
     *
     * @return  mixed                       This instance on success, null otherwise
     */
    public function load() {

        // Reset
        $this->_list = array();

        // Check contents of master directory
        $location = APPPATH . "widgets/";
        if (!$list = @scandir($location)) {

            log_message("error", "Could not read master widget directory " . $location);
            return null;

        }

        // Loop through contents...
        foreach ($list as $dir) {

            // ... skip files and obsoletes...
            if (in_array($dir, array(".", "..", "backend")) || !is_dir($location . $dir)) {
                continue;
            }

            // ... but check directories for widget configurations
            if ($name = $this->_parseName($location . $dir . "/config.xml", $dir)) {
                $this->_list[$dir] = $name;
            }

        }

        asort($this->_list);
        return $this;

    }


    /**
     * Retrieves the current list of items
     *
     * @return  array                       List with the current items
     */
    public function toArray() {
        return $this->_list;
    }


    /**
     * Attempts to extract the widget name from given file
     *
     * @param   String      $configFile     The path of the input file to load and parse
     * @param   String      $type           The type of the widget to which the file belongs
     * @return  mixed                       The name of the widget or null on failure
     */
    private function _parseName($configFile, $type) {

        if (!is_file($configFile)) {

            log_message("error", $type . ": Unable to find widget configuration");
            return null;

        }

        // Attempt to load widget configuration file
        if (($simpleXML = @simplexml_load_file($configFile)) === false) {

            log_message("error", $type . ": Unable to load widget configuration");
            return null;

        }

        // Check required name for widget
        if (!isset($simpleXML->name) || !strlen($simpleXML->name)) {

            log_message("error", $type . ": Invalid name in widget configuration");
            return null;

        }

        return (string)$simpleXML->name;

    }

}
?>