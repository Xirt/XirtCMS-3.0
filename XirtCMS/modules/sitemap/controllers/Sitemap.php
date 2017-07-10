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
		$this->load->helper("db_search");
		$this->load->helper("menu");
		$this->load->helper("url");

		// Load models
		$this->load->model("MenusModel", "menus");

	}


	/**
     * Show sitemap as (X)HTML
	 */
	public function index() {

		// Show content
		XCMS_Page::getInstance()->setTitle("Sitemap", true);
		$this->load->view("view_html", array(
			"css_name"   => $this->config("css_name", ""),
            "show_title" => $this->config("show_title", true),
			"menus"      => $this->_getContent()
		));

	}


	/**
     * Show sitemap as XML
	 */
	public function xml() {

		// Prepare items
		$items = array();
		foreach ($this->_getContent() as $menu) {

			foreach ($menu->items as $item) {

				if ($item->type == "module") {
					$items[] = $item;
				}

			}

		}

		// Disable default template...
		XCMS_Config::set("USE_TEMPLATE", "FALSE");

		// ... and show content
		$this->output->set_content_type("text/xml");
		$this->load->view("view_xml", array(
			"baseURL" => base_url(),
			"items"   => $items
		));

	}


    /**
     * Returns the list of menus (with entries) to be displayed in the sitemap
     *
     * @return  array                       Array containing all menus to be displayed
     */
	private function _getContent() {

		$this->menus->load(new SearchAttributes());
		$menus = $this->menus->toArray();

		foreach ($menus as $index => $menu) {

			// Filter hidden menus
			if (!$menu->sitemap) {

				unset($menus[$index]);
				continue;

			}

			$hidden = array();
			$menu->items = array();

			foreach (MenuHelper::getMenu($menu->id, true) as $item) {

				// Filter hidden nodes
				if (!$item->sitemap || in_array($item->parent_id, $hidden)) {

					$hidden[] = $item->node_id;
					continue;

				}

				$menu->items[] = $item;

			}

		}

		return $menus;

	}

}
?>