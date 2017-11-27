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
        
        // Load helper
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
    
    
    /**
     * Retrieves the content of one of the log files
     * 
     * @param   int         $id             The sequence no. of the log to be retrieved
     */
    public function get_logfile($id = 0) {
        
        // Only allow AJAX requests
        if (!$this->input->is_ajax_request()) {
            return show_404();
        }

        // Disable default template...
        XCMS_Config::set("USE_TEMPLATE", "FALSE");

        // ... and show content
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($this->_getLogFiles($id)));

    }


    /**
     *
     *
     * @param   int         $id             The sequence no. of the log to be retrieved
     * @return  Array                       An Array with the filenames of the logs
     */
    private function _getLogfiles($id) {

        $logs = get_filenames(XCMS_CONFIG::get("FOLDER_LOGS"), true);
        if (!rsort($logs) || !is_numeric($id) || !isset($logs[$id])) {
            return null;
        }

        return (object) [
            "content" => file($logs[$id]),
            "prev_id" => $id ? $id - 1 : null,
            "next_id" => isset($logs[$id + 1]) ? $id + 1 : null
        ];

    }

}
?>