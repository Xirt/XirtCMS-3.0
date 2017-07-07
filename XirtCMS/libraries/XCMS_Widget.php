<?php

/**
 * Default XirtCMS Widget class
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class XCMS_Widget {

    /**
     * @var Object|null
     * Internal reference to CI DB connection
     */
    protected $db = null;


    /**
     * @var array
     * Internal list for holding all configuration values
     */
    private $_config = array();


    /**
     * CONSTRUCTOR
     * Initializes widget with given configuration and default DB connection
     *
     * @param   array       $conf           Array containing configuration for this widget
     */
    public function __construct($conf = array()) {

        $this->db =& get_instance()->db;
        $this->load =& get_instance()->load;

        $this->_config = $conf;

    }


    /**
     * Shows the content (normal)
     */
    public function show() {
    }


    /**
     * Shows the content (AJAX)
     */
    public function ajax() {
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
     * Returns the requested configuration value for this widget
     *
     * @param   String      $name           The name of the requested configuration value
     * @param   mixed       $default        The default value is the requested configuration value is not set (optional)
     * @return  mixed                       The requested configuration value if found, fallback default or null otherwise
     */
    public function config($name, $default = null) {
        return $this->_config[$name] ?? ($default ?? null);
    }


    /**
     * Returns all configuration values for this widget
     *
     * @return  Array                       Array with all configuration settings for this widget
     */
    public function getConfigArray() {
        return $this->_config;
    }


    /**
     * Returns all configuration values for this widget
     *
     * @return  Object                      Object with all configuration settings for this widget
     */
    public function getConfig() {
        return (object)$this->_config;
    }
    
    
    /**
     * Loads the given view and displays it using given variables
     *
     * @param    String     $view           The view file to load
     * @param    array      $attribs        The attributes to pass to the view (optional)
     * @param    boolean    $return         Toggles between returning the view output or not (optional)
     */
    public function view($view, $attribs = array(), $return = false) {

        $path = "widgets/" . substr(get_class($this), 8) . "/";
        
        $this->load->add_package_path(APPPATH . $path, true);
        $this->load->view($view, $attribs, $return);
        $this->load->remove_package_path(APPPATH . $path);
        
    }

}
?>