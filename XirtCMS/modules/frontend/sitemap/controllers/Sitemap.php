<?php

/**
 * Controller for showing a sitemap
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class Sitemap extends XCMS_Controller {

    /**
     * CONSTRUCTOR
     * Instantiates controller with required configuration, helpers, libraries and models
     */
    public function __construct() {

        parent::__construct();

        // Load helpers
        $this->load->helper("menu");
        $this->load->helper("url");
        $this->load->helper("route");

        // Load models
        $this->load->model("MenusModel", "menus");
        $this->load->model("ArticlesModel", false);
        $this->load->model("ModuleConfigurationsModel", false);

    }


    /**
     * Show sitemap as (X)HTML
     */
    public function index() {

        // Show content
        XCMS_Page::getInstance()->setTitle("Sitemap", true);
        $this->load->view("view_html.tpl", array(
            "css_name"   => $this->config("css_name", ""),
            "show_title" => $this->config("show_title", true),
            "menus"      => $this->_getContent($this->config("trigger_html_hooks", false))
        ));

    }


    /**
     * Show sitemap as XML
     */
    public function xml() {

        // Prepare items
        $items = array();
        foreach ($this->_getContent($this->config("trigger_xml_hooks", true)) as $menu) {

            foreach ($menu->items as $item) {

                if ($item->type == "internal") {
                    $items[] = $item;
                }

            }

        }

        // Disable default template...
        XCMS_Config::set("USE_TEMPLATE", "FALSE");

        // ... and show content
        $this->output->set_content_type("text/xml");
        $this->load->view("view_xml.tpl", array(
            "baseURL" => base_url(),
            "items"   => $items
        ));

    }


    /**
     * Returns the list of menus (with entries) to be displayed in the sitemap
     *
     * @param   boolean     $considerHooks  Toggle triggering of related hooks
     * @return  array                       Array containing all menus to be displayed
     */
    private function _getContent($considerHooks) {

        $this->menus->load();
        $menus = $this->menus->toArray();

        foreach ($menus as $index => $menu) {

            // Filter hidden menus
            if (!$menu->get("sitemap")) {

                unset($menus[$index]);
                continue;

            }

            $hidden = array();
            $menu->items = array();

            foreach (MenuHelper::getMenu($menu->get("id"), true) as $item) {

                if ($item->type != "internal") {
                    continue;
                }

                // Filter hidden nodes
                if (!$item->sitemap || in_array($item->parent_id, $hidden)) {

                    $hidden[] = $item->node_id;
                    continue;

                }

                $menu->items[] = $item;

                // Optional hooks
                if ($considerHooks) {

                    RouteList::init();
                    XCMS_Hooks::execute("sitemap.add_item",
                        array(&$menu->items, &$item)
                    );

                }


            }

        }

        return $menus;

    }

}
?>