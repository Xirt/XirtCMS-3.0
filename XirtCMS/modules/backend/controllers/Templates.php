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
        $this->load->helper("grid");

        // Load models
        $this->load->model("TemplatesModel", false);
        $this->load->model("ExtTemplatesModel", false);

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

        // Retrieve request
        $gridIO = (new GridHelper())
        ->parseRequest($this->input);

        // Load requested data
        $templates = (new ExtTemplatesModel())->init()
        ->set($gridIO->getRequest())
        ->load();

        // Prepare response ...
        $gridIO->setTotal($templates->getTotalCount($gridIO));
        foreach ($templates->toArray() as $template) {

            $gridIO->addRow([
                "id"        => $template->get("id"),
                "name"      => $template->get("name"),
                "folder"    => $template->get("folder"),
                "published" => $template->get("published")
            ]);

        }

        // ... and output it
        $gridIO->generateResponse($this->output);

    }

}
?>