<?php

/**
 * Controller for the "Module Configurations"-GUI and related processes (back end)
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class Moduleconfigurations extends XCMS_Controller {

    /**
     * CONSTRUCTOR
     * Instantiates controller with required helpers, libraries and models
     */
    public function __construct() {

        parent::__construct(75, true);

        // Load helpers
        $this->load->helper("grid");

        // Load models
        $this->load->model("ModuleConfigurationsModel", false);
        $this->load->model("ExtModuleConfigurationsModel", false);
        $this->load->model("ModuleTypesModel", "modules");

    }


    /**
     * Index Page for this controller.
     */
    public function index() {

        // Load module types
        $this->modules->load();

        // Add page scripts
        XCMS_Page::getInstance()->addScript(array(
            "assets/scripts/backend/mng_modules.js"
        ));

        // Add page stylesheets
        XCMS_Page::getInstance()->addStylesheet(array(
            "assets/css/backend/mng_modules.css"
        ));

        // Show template
        $this->load->view("moduleconfigurations", array (
            "moduleTypes" => $this->modules->toArray()
        ));

    }


    /**
     * Listing method for this controller for AJAX requests.
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
        $moduleConfigurations = (new ExtModuleConfigurationsModel())->init()
            ->set($gridIO->getRequest())
            ->load();

        // Prepare response ...
        $gridIO->setTotal($moduleConfigurations->getTotalCount($gridIO));
        foreach ($moduleConfigurations->toArray() as $configuration) {

            $gridIO->addRow([
                "id"      => $configuration->get("id"),
                "name"    => $configuration->get("name"),
                "default" => $configuration->get("default"),
                "type"    => $configuration->get("type")
            ]);

        }

        // ... and output it
        $gridIO->generateResponse($this->output);

    }

}