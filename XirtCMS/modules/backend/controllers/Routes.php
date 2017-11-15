<?php

/**
 * Controller for the "Routes"-GUI and related processes (back end)
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class Routes extends XCMS_Controller {

    /**
     * CONSTRUCTOR
     * Instantiates controller with required helpers, libraries and models
     */
    public function __construct() {

        parent::__construct(75, true);

        // Load helpers
        $this->load->helper("grid");

        // Load models
        $this->load->model("RoutesModel", false);
        $this->load->model("ExtRoutesModel", false);
        $this->load->model("ModuleTypesModel", "modules");

    }


    /**
     * Index page for this controller (shows all items)
     */
    public function index() {

        // Load all models
        $this->modules->load();

        // Add page scripts
        XCMS_Page::getInstance()->addScript(array(
            "assets/scripts/backend/mng_routes.js"
        ));

        // Add page stylesheets
        XCMS_Page::getInstance()->addStylesheet(array(
            "assets/css/backend/mng_routes.css"
        ));

        // Show template
        $this->load->view("routes.tpl", array(
            "moduleTypes" => $this->modules->toArray()
        ));

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
        $routes = (new ExtRoutesModel())->init()
            ->set($gridIO->getRequest())
            ->load();

        // Prepare response ...
        $gridIO->setTotal($routes->getTotalCount($gridIO));
        foreach ($routes->toArray() as $route) {

            $gridIO->addRow([
                "id"         => $route->get("id"),
                "public_url" => $route->get("public_url"),
                "target_url" => $route->get("target_url"),
                "menu_items" => $route->get("menu_items") ? true : false
            ]);

        }

        // ... and output it
        $gridIO->generateResponse($this->output);

    }


    /**
     * Returns list of available module configurations
     *
     * @return  Array                       List with all known module configurations
     */
    private function _getModuleConfigurations() {

        return (new ExtModuleConfigurationsModel())->init()
            ->set("sortColumn", "name")
            ->set("sortOrder", "ASC")
            ->load()->toArray();

    }

}
?>