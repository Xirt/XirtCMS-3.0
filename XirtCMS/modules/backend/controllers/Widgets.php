<?php

/**
 * Controller for the "Widgets"-GUI and related processes (back end)
 *
 * @author     A.G. Gideonse
 * @version    3.0
 * @copyright  XirtCMS 2016 - 2017
 * @package    XirtCMS
 */
class Widgets extends XCMS_Controller {

    /**
     * CONSTRUCTOR
     * Instantiates controller with required helpers, libraries and models
     */
    public function __construct() {

        parent::__construct(75, true);

        // Load helpers
        $this->load->helper("grid");
        $this->load->helper("menu");
        $this->load->helper("widget");

        // Load models
        $this->load->model("MenusModel", false);
        $this->load->model("WidgetsModel", false);
        $this->load->model("ExtWidgetsModel", false);
        $this->load->model("WidgetTypesModel", false);
        $this->load->model("ModuleConfigurationsModel", false);
        $this->load->model("ExtModuleConfigurationsModel", false);

    }


    /**
     * Index page for this controller (shows all items)
     */
    public function index() {

        // Retrieve required lists
        $menus          = $this->_getMenus();
        $positions      = $this->_getPositions();
        $types          = $this->_getWidgetTypes();
        $menuEntries    = $this->_getMenuEntries($menus);
        $configurations = $this->_getModuleConfigurations();

        // Add page scripts
        XCMS_Page::getInstance()->addScript(array(
            "assets/scripts/backend/mng_widgets.js"
        ));

        // Add page stylesheets
        XCMS_Page::getInstance()->addStylesheet(array(
            "assets/css/backend/mng_widgets.css"
        ));

        // Show template
        $this->load->view("widgets.tpl", array(
            "configurations" => $configurations,
            "menuEntries"    => $menuEntries,
            "positions"      => $positions,
            "menus"          => $menus,
            "types"          => $types
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
        $widgets = (new ExtWidgetsModel())->init()
            ->set($gridIO->getRequest())
            ->load();

        // Prepare response ...
        $gridIO->setTotal($widgets->getTotalCount($gridIO));
        foreach ($widgets->toArray() as $widget) {

            $gridIO->addRow([
                "id"        => $widget->get("id"),
                "name"      => $widget->get("name"),
                "position"  => $widget->get("position"),
                "ordering"  => $widget->get("ordering"),
                "published" => $widget->get("published"),
                "type"      => $widget->get("type")
            ]);

        }

        // ... and output it
        $gridIO->generateResponse($this->output);

    }


    /**
     * Returns list of available menus
     *
     * @return  Array                       List with all known menus
     */
    private function _getMenus() {
        return (new MenusModel())->load()->toArray();
    }


    /**
     * Returns list of available widget positions
     *
     * @return  Array                       List with all known widget positions
     */
    private function _getPositions() {

        WidgetHelper::init(false, false);
        $positions = WidgetHelper::getValidPositions();
        sort($positions);

        return $positions;

    }


    /**
     * Returns list of available widget types
     *
     * @return  Array                       List with all known widget types
     */
    private function _getWidgetTypes() {
        return (new WidgetTypesModel())->load()->toArray();
    }


    /**
     * Returns list of available menu entries
     *
     * @param   Array       $menus          List with menus to check
     * @return  Array                       List with all known menu entries
     */
    private function _getMenuEntries($menus) {

        // Retrieve menu items
        $list = array();
        foreach ($menus as $menu) {

            $entries = array();

            $menuObject = MenuHelper::getMenuTree($menu->get("id"));
            foreach ($menuObject->toArray() as $node) {

                $entries[] = (object) [
                    "value" => $node->node_id,
                    "label" => htmlspecialchars($node->name)
                ];

            }

            $list[$menu->get("id")] = $entries;

        }

        return $list;

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