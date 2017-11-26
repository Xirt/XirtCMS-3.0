<?php
/**
 * Controller for the "Dashboard"-GUI and related processes (back end)
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class Dashboard extends XCMS_Controller {

    /**
     * CONSTRUCTOR
     * Instantiates controller with required helpers, libraries and models
     */
    public function __construct() {
    
        parent::__construct(75, true);

        $this->load->helper("filesystem");

    }


    /**
     * Index page for this controller (main GUI)
     */
    public function index() {

        // Add page stylesheets
        XCMS_Page::getInstance()->addStylesheet(array(
            "assets/css/backend/mng_dashboard.css"
        ));

        // Add page stylesheets
        XCMS_Page::getInstance()->addScript(array(
            "assets/scripts/backend/mng_dashboard.js"
        ));

        $this->load->view("dashboard.tpl", array(
            "cacheSize" => formatBytes(getDirectorySize(XCMS_CONFIG::get("FOLDER_CACHE")), 0),
            "logSize" => formatBytes(getDirectorySize(XCMS_CONFIG::get("FOLDER_LOGS")), 0)
        ));

    }
    
    
    public function get_logfile($id) {
        
        $logs = $this->_getLogfiles();
        if (!is_numeric($id) || !isset($logs[$id])) {
            return;
        }

        // Disable default template...
        XCMS_Config::set("USE_TEMPLATE", "FALSE");

        // ... and show content
        $this->output->set_content_type("text/plain");
        print(nl2br(file_get_contents($logs[$id])));

    }
    
    
    /**
     *
     *
     * @return  Array                       An Array with the filenames of the logs
     */
    public function _getLogfiles() {        
        return get_filenames(XCMS_CONFIG::get("FOLDER_LOGS"), true);
    }

}
?>