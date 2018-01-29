<?php

require("XCMS_Page.php");
require("XCMS_RenderEngine.php");

/**
 * XirtCMS core class extending CI Controller functionality
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class XCMS_Controller extends CI_Controller {

    /**
     * @var array
     * Internal list for holding all configuration values
     */
    private $_config = array();


    /**
     * CONSTRUCTOR
     * Initializes controller instance with default settings for current request
     *
     * @param   int         $auth_level     The authorization level required to access this controller
     * @param   boolean     $isBackend      Toggless between frontend / backend behaviour of controller
     */
    public function __construct($auth_level = 0, $isBackend = false) {

        parent::__construct();
        XCMS_Hooks::init($isBackend);
        $this->load->helper("exception");
        $this->load->library("XCMS_Authentication");
        $this->load->library("cache", array('adapter' => 'file'));

        if ($auth_level && !XCMS_Authentication::check()) {

            // TODO :: Differentiate between frontend / backend
            if (get_class($this) != "Authentication") {
                redirect("backend/authentication");
            }

        }

        // Enable profiler on request
        if (XCMS_Config::get("DEBUG_MODE") && !$this->input->is_ajax_request()) {

            log_message("info", "[XCMS] Enabling CodeIgniter profiler (debug mode).");
            $this->output->enable_profiler(true);

        }

        // Set correct template engine behaviour
        XCMS_Config::set("XCMS_BACKEND", $isBackend ? "TRUE" : "FALSE");
        if ($this->input->is_ajax_request()) {
            XCMS_Config::set("USE_TEMPLATE", "FALSE");
        }

        // Load module configuration (frontend)
        if (!$isBackend && $this->router->class) {
            $this->_loadConfiguration();
        }

    }

    
    /**
     * Additional validition callback (allows alphanumerical, dashes and periods)
     *
     * @param   String      $value          The value to check for this validation
     * @return  boolean                     True if validation was passed, false otherwise
     */
    public function alpha_dashdot($value){
        return preg_match("/^[A-Z0-9._-]+$/i", $value) ? $value : false;
    }


    /**
     * Setter for configuration
     *
     * @param   String      $name           The key of the configuration to be set
     * @param   mixed       $value          The value for the configuration to be set
     * @return  boolean                     Always true
     */
    public function setConfig($name, $value) {
        return ($this->_config[$name] = $value);
    }


    /**
     * Returns the requested configuration value for this module
     *
     * @param   String      $name           The name of the requested configuration value
     * @param   mixed       $default        The default value is the requested configuration value is not set (optional)
     * @return  mixed                       The requested configuration value if found, fallback default or null otherwise
     */
    public function config($name, $default = null) {
        return $this->_config[$name] ?? ($default ?? null);
    }


    /**
     * Returns all configuration values for this module
     *
     * @return  Array                       Array with all configuration settings for this module
     */
    public function getConfigArray() {
        return $this->_config;
    }


    /**
     * Loads all settings related to this module instance
     */
    public function _loadConfiguration() {

        log_message("info", "[XCMS] Loading configuration for Module '{$this->router->class}'.");

        // Retrieve data
        $this->db->join(XCMS_Tables::TABLE_MODULES_SETTINGS, "module_id = id", "left");
        $query = $this->db->get_where(XCMS_Tables::TABLE_MODULES, array("type" => $this->router->class));

        // Parse results
        $conf = $this->router->module_config;
        foreach ($query->result() as $row) {

            // Load requested config
            if ($conf && $conf == $row->id) {
                $this->_config[$row->name] = $row->value;
            }

            // Or default if not preference
            if (!$conf && $row->default) {
                $this->_config[$row->name] = $row->value;
            }

        }

    }
    
}
?>