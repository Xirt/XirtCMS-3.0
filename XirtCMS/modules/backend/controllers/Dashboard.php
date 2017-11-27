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
     * Retrieves the content of one of the log files (incl. predecessor and successor IDs)
     *
     * @param   int         $id             The sequence no. of the log to be retrieved
     */
    public function get_logfile($id = 0) {

        // Only allow AJAX requests
        if (!$this->input->is_ajax_request()) {
            return show_404();
        }

        // ... and show content
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($this->_getLogFile($id)));

    }


    /**
     * Removes all existing logs from the server
     */
    public function clear_logs() {

        // Only allow AJAX requests
        if (!$this->input->is_ajax_request()) {
            return show_404();
        }

        // Remove all logs
        foreach($this->_getLogFiles() as $file) {
            delete_file($file, true);
        }

    }


    /**
     * Removes all existing cached files from the server
     */
    public function clear_cache() {

        // Only allow AJAX requests
        if (!$this->input->is_ajax_request()) {
            return show_404();
        }

        // Remove all logs
        foreach($this->_getCacheFiles() as $file) {
            delete_file($file, true);
        }

    }


    /**
     * Retrieves the content of one of the log files and provides insight on other logs
     *
     * @param   int         $id             The sequence no. of the log to be retrieved
     * @return  Array                       An Array with the filenames of the logs
     */
    private function _getLogFile($id) {

        $logs = $this->_getLogFiles();
        if (!rsort($logs) || !is_numeric($id) || !isset($logs[$id])) {
            return null;
        }

        return (object) [
            "content" => file($logs[$id]),
            "prev_id" => $id ? $id - 1 : null,
            "next_id" => isset($logs[$id + 1]) ? $id + 1 : null
        ];

    }


    /**
     * Retrieves a list of all existing logs
     *
     * @return  Array                       An Array with the filenames of the logs
     */
    private function _getLogFiles() {
        return get_filenames(XCMS_CONFIG::get("FOLDER_LOGS"), true);
    }


    /**
     * Retrieves a list of all cache files
     *
     * @return  Array                       An Array with the files in the cache
     */
    private function _getCacheFiles() {
        return get_filenames(XCMS_CONFIG::get("FOLDER_CACHE"), true);
    }

}
?>