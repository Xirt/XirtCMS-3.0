<?php

/**
 * Controller for the "Menus"-GUI and related processes (back end)
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class Menus extends XCMS_Controller {

    /**
     * CONSTRUCTOR
     * Instantiates controller with required helpers, libraries and models
     */
    public function __construct() {

        parent::__construct(75, true);

        // Load helpers
        $this->load->helper("grid");

        // Load models
        $this->load->model("MenusModel", "menus");

    }


    /**
     * Index page for this controller (main GUI)
     */
    public function index() {

        // Add page scripts
        XCMS_Page::getInstance()->addScript(array(
            "assets/scripts/backend/mng_menus.js"
        ));

        // Add page stylesheets
        XCMS_Page::getInstance()->addStylesheet(array(
            "assets/css/backend/mng_menus.css"
        ));

        // Show template
        $this->load->view("menus");

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
        $menus = (new MenusModel())
            ->set($gridIO->getRequest())
            ->load();

        // Prepare response ...
        $gridIO->setTotal($menus->getTotalCount($gridIO));
        foreach ($menus->toArray() as $menu) {

            $gridIO->addRow([
                "id"      => $menu->id,
                "name"    => $menu->name,
                "sitemap" => $menu->sitemap
            ]);

        }

        // ... and output it
        $gridIO->generateResponse($this->output);

    }

}
?>