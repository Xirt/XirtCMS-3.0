<?php

/**
 * Controller for the "Menuitems"-GUI and related processes (back end)
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class Menuitems extends XCMS_Controller {

    /**
     * CONSTRUCTOR
     * Instantiates controller with required helpers, libraries and models
     */
    public function __construct() {

        parent::__construct(75, true);

        // Load helpers
        $this->load->helper("grid");
        $this->load->helper("route");
        $this->load->helper("menu");

        // Load modules
        $this->load->model("ModuleTypesModel", "modules");
        $this->load->model("ArticlesModel", "articles");
        $this->load->model("MenuModel", "menu");

    }


    /**
     * Index page for this controller (shows all items)
     *
     * @param   int         $id             The id of the requested menu to display
     */
    public function index($id = 0) {

        // Validate given menu ID
        if (!is_numeric($id) || !$this->menu->load($id)) {
            return show_404();
        }

        // Load all models
        $this->modules->load();

        // Load all articles
        $articles = array();
        $this->articles->load();
        foreach ($this->articles->toArray() as $article) {
            $articles[$article->get("id")] = $article->get("title");
        }

        // Add page scripts
        XCMS_Page::getInstance()->addScript(array(
            "assets/scripts/backend/mng_menuitems.js"
        ));

        // Add page stylesheets
        XCMS_Page::getInstance()->addStylesheet(array(
            "assets/css/backend/mng_menuitems.css"
        ));

        // Show template
        $this->load->view("menuitems.tpl", array(
            "moduleTypes" => $this->modules->toArray(),
            "menu_name"   => htmlspecialchars($this->menu->get("name")),
            "menu_id"     => $this->menu->get("menu_id"),
            "articles"    => $articles
        ));

    }


    /**
     * Provides JSON overview of requested items
     *
     * @param   int         $id             The ID of the menu for items are requested (optional)
     */
    public function view($id = 0) {

        // Only allow AJAX requests
        if (!$this->input->is_ajax_request()) {
            return show_404();
        }

        // Validate given menu ID
        if (!is_numeric($id) || !$this->menu->load($id)) {
            return show_404();
        }

        // Enrich object...
        $gridIO = new GridHelper();
        foreach (MenuHelper::getMenuTree($id, false)->toArray() as $node) {

            $node->item_id = $node->node_id;
            unset($node->node_id);
            $gridIO->addRow($node);

        }

        // ... and output it
        $gridIO->generateResponse($this->output);

    }


    /**
     * Overrides the default mapping of the controller for this instance (required for menu ID)
     *
     * @param   string      $method         The method to be called
     * @param   Array       $params         The parameters to pass to the method
     */
    public function _remap($method, $params = array()) {

        if (method_exists($this, $method)) {
            return call_user_func_array(array($this, $method), $params);
        }

        if (is_numeric($method)) {
            return $this->index($method);
        }

        show_404();

    }

}
?>