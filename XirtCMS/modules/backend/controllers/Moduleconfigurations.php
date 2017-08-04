<?php

/**
 * Controller for the "Module Configurations"-GUI and related processes
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class Moduleconfigurations extends XCMS_Controller {


    /**
     * Constructs the controller with associated model
     */
    public function __construct() {

        parent::__construct(75, true);

        // Load models
        $this->load->model("ModuleConfigurationsModel", "configurations");
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

        // Retrieve BootGrid parameters
        $this->load->helper("db_search");
        $searchObj = new SearchAttributes();
        $searchObj->retrieveFromBootgrid($this->input, (object) array(
            "sortColumn" => "id"
        ));

        // Load requested data
        $this->configurations->load($searchObj);

        // Enrich object...
        $searchObj->rows = array();
        $searchObj->total = $this->configurations->getTotalCount($searchObj);
        foreach ($this->configurations->toArray() as $configuration) {

            $searchObj->rows[] = (Object)array(
                "id"      => $configuration->id,
                "name"    => $configuration->name,
                "default" => $configuration->default,
                "type"    => $configuration->type
            );

        }

        // ...and output it as JSON
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($searchObj));

    }

}
?>