<?php

/**
 * Controller for the "Templates"-GUI and related processes (back end)
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class Templates extends XCMS_Controller {

    /**
     * CONSTRUCTOR
     * Instantiates controller with required helpers, libraries and models
     */
    public function __construct() {

        parent::__construct(75, true);

        // Load helpers
        $this->load->helper("db_search");

        // Load models
        $this->load->model("TemplatesModel", "templates");

    }


    /**
     * Index page for this controller (shows all items)
     */
    public function index() {

        // Add page scripts
        XCMS_Page::getInstance()->addScript(array(
            "assets/scripts/backend/mng_templates.js"
        ));

        // Add page stylesheets
        XCMS_Page::getInstance()->addStylesheet(array(
            "assets/css/backend/mng_templates.css"
        ));

        // Show template
        $this->load->view("templates");

    }


    /**
     * Provides JSON overview of requested items
     */
    public function view() {

        // Only allow AJAX requests
        if (!$this->input->is_ajax_request()) {
            return show_404();
        }

        // Load requested data
        $searchObj = new SearchAttributes();
        $this->templates->load($searchObj->retrieveFromBootgrid($this->input, (object) array(
            "sortColumn" => "id"
        )));

        // Enrich object...
        $searchObj->rows = array();
        $searchObj->total = $this->templates->getTotalCount($searchObj);
        foreach ($this->templates->toArray() as $module) {

            $searchObj->rows[] = (Object)array(
                "id"        => $module->id,
                "name"      => $module->name,
                "folder"    => $module->folder,
                "published" => $module->published

            );

        }

        // ...and output it as JSON
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($searchObj));

    }

}
?>