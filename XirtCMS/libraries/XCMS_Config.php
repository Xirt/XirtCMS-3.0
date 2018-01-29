<?php

require APPPATH . "config/PermitTypes.php";
require APPPATH . "config/XCMS_Tables.php";

/**
 * XirtCMS main configuration manager (e.g. application parameters)
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class XCMS_Config {

    /**
     * @var Object|null
     * Internal reference to CI
     */
    private $CI;


    /**
     * @var array
     * Array containing all parameters
     */
    private static $_list = array();


    /**
     * CONSTRUCTOR
     * Initializes instance with current application parameters (from DB)
     */
    function __construct() {

        $this->CI =& get_instance();
        $this->_load();

        date_default_timezone_set(self::get("SESSION_TIMEZONE"));

    }


    /**
     * Loads parameters from DB
     */
    private function _load() {

        // Retrieve data from DB
        $query = $this->CI->db->get(XCMS_Tables::TABLE_CONFIGURATION);
        foreach ($query->result() as $metaInfo) {
            self::set($metaInfo->name, $metaInfo->value);
        }

        // Update CodeIgniter configuration
        if ($dir = self::get("FOLDER_CACHE")) {
            $this->CI->config->set_item("cache_path", FCPATH . $dir);
        }

    }


    /**
     * Save a parameter/value configuration item
     *
     * @param   String      $name           The name of the configuration item to save
     * @param   String      $value          The value of the configuration item
     */
    public static function set($name, $value) {
        self::$_list[$name] = $value;
    }


    /**
     * Retrieves a configuration value
     *
     * @param   String      $name           The name of the configuration item to retrieve
     * @return  mixed                       The requested value or null on failure
     */
    public static function get($name) {

        if (isset(self::$_list[$name])) {

            // Convert boolean configuration values
            if (in_array(self::$_list[$name], array("TRUE", "FALSE"))) {
                return (self::$_list[$name] == "TRUE");
            }

            return self::$_list[$name];

        }

        return null;

    }

}
?>