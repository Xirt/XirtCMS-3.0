<?php

/**
 * Base model for retrieving XirtCMS module types
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class ModuleTypesModel extends XCMS_Model {

    /**
     * Internal list of items
     * @var array
     */
    private $_list = array();


    /**
     * Loads all module types
     *
     * @return  Object                      Always this instance
     */
    public function load() {

        // Loop through all module master directories
        foreach (Modules::$locations as $location => $offset) {

            // Check contents of master directory
            if (!$list = @scandir($location)) {

                log_message("error", "Could not read master module directory " . $location);
                continue;

            }

            // Loop through contents...
            foreach ($list as $dir) {

                // ... skip files and obsoletes...
                if (in_array($dir, array(".", "..", "backend")) || !is_dir($location . $dir)) {
                    continue;
                }

                // ... but check directories for module configurations
                if ($name = $this->_parseName($location . $dir . "/config.xml", $dir)) {
                    $this->_list[$dir] = $name;
                }

            }

        }

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
     * Attempts to extract the module name from given file
     *
     * @param   String      $configFile     The path of the input file to load and parse
     * @param   String      $type           The type of the module to which the file belongs
     * @return  mixed                       The name of the module or null on failure
     */
    private function _parseName($configFile, $type) {

        if (!is_file($configFile)) {

            log_message("error", $type . ": Unable to find module configuration");
            return null;

        }

        // Attempt to load module configuration file
        if (($simpleXML = @simplexml_load_file($configFile)) === false) {

            log_message("error", $type . ": Unable to load module configuration");
            return null;

        }

        // Check required name for module
        if (!isset($simpleXML->name) || !strlen($simpleXML->name)) {

            log_message("error", $type . ": Invalid name in module configuration");
            return null;

        }

        return (string)$simpleXML->name;

    }

}
?>