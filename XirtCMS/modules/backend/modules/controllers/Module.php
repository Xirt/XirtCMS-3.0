<?php

/**
 * Controller for showing a viewing / modifying a XirtCMS module (back end)
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class Module extends XCMS_Controller {

    /**
     * CONSTRUCTOR
     * Instantiates controller with required helpers, libraries and models
     */
    public function __construct() {

        parent::__construct(75, true);

        // Only allow AJAX requests
        if (!$this->input->is_ajax_request()) {
            return show_404();
        }

        // Load models
        $this->load->model("ModuleMenuModel", false);

    }


    /**
     * "View menu parameters"-functionality for this controller
     *
     * @param   String      $type               The type of module for which to provide the menu options
     */
    public function view_menu_parameters($type) {

        // Validate given name
		$module = new ModuleMenuModel();
        if (!$module->init($type)) {
            return;
        }

        // ...and output it as JSON
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($module->toArray()));

    }

}
?>