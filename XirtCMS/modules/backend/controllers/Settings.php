<?php

/**
 * Controller for the "Settings"-GUI and related processes (back end)
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class Settings extends XCMS_Controller {

    /**
     * CONSTRUCTOR
     * Instantiates controller with required helpers, libraries and models
     */
    public function __construct() {

        parent::__construct(75, true);

        // Load helpers
        $this->load->helper("grid");

        // Load models
        $this->load->model("SettingsModel", false);
        $this->load->model("ExtSettingsModel", false);

    }


    /**
     * Index page for this controller (main GUI)
     */
    public function index() {

        // Add page scripts
        XCMS_Page::getInstance()->addScript(array(
            "assets/scripts/backend/mng_settings.js"
        ));

        // Add page stylesheets
        XCMS_Page::getInstance()->addStylesheet(array(
            "assets/css/backend/mng_settings.css"
        ));

        // Show template
        $this->load->view("settings.tpl");

    }


    /**
     * Listing method for listing all requested items
     */
    public function view() {

        // Only allow AJAX requests
        if (!$this->input->is_ajax_request()) {
            return show_404();
        }

        // Retrieve request
        $gridIO = (new GridHelper())
            ->parseRequest($this->input);

        // Load requested data
        $settings = (new ExtSettingsModel())->init()
            ->set($gridIO->getRequest())
            ->load();

        // Prepare response ...
        $gridIO->setTotal($settings->getTotalCount($gridIO));
        foreach ($settings->toArray() as $setting) {

            $gridIO->addRow([
                "name"  => $setting->get("name"),
                "value" => $setting->get("value")
            ]);

        }

        // ... and output it
        $gridIO->generateResponse($this->output);

    }

}
?>