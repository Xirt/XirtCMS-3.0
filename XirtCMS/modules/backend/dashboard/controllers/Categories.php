<?php

/**
 * Controller for the "Categories"-GUI and related processes (back end)
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class Categories extends XCMS_Controller {

    /**
     * CONSTRUCTOR
     * Instantiates controller with required helpers, libraries and models
     */
    public function __construct() {

        parent::__construct(75, true);

        // Load helpers
        $this->load->helper("grid");
        $this->load->helper("category");

        // Load modules
        $this->load->model("CategoriesModel", false);

    }


    /**
     * Index page for this controller (shows all items)
     */
    public function index() {

        // Add page scripts
        XCMS_Page::getInstance()->addScript(array(
            "assets/scripts/backend/mng_categories.js"
        ));

        // Add page stylesheets
        XCMS_Page::getInstance()->addStylesheet(array(
            "assets/css/backend/mng_categories.css"
        ));

        // Show template
        $this->load->view("categories.tpl");

    }


    /**
     * Provides JSON overview of requested items
     *
     * @param    int         $id                The ID of the menu for items are requested (optional)
     */
    public function view($id = 0) {

        // Only allow AJAX requests
        if (!$this->input->is_ajax_request()) {
            return show_404();
        }

        // Prepare response ...
        $gridIO = new GridHelper();
        foreach (CategoryHelper::getCategoryTree(false)->toArray() as $node) {

            $node->id = $node->node_id;
            unset($node->node_id);
            $gridIO->addRow($node);

        }

        // ... and output it
        $gridIO->generateResponse($this->output);

    }

}
?>