<?php

/**
 * Controller for the "Usergroups"-GUI and related processes (back end)
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class Usergroups extends XCMS_Controller {

    /**
     * CONSTRUCTOR
     * Instantiates controller with required helpers, libraries and models
     */
    public function __construct() {

        parent::__construct(75, true);

        // Load helpers
        $this->load->helper("db_search");

        // Load models
        $this->load->model("UsergroupsModel", "usergroups");

    }


    /**
     * Index page for this controller (shows all items)
     */
    public function index() {

        // Add page scripts
        XCMS_Page::getInstance()->addScript(array(
            "assets/scripts/backend/mng_usergroups.js"
        ));

        // Add page stylesheets
        XCMS_Page::getInstance()->addStylesheet(array(
            "assets/css/backend/mng_usergroups.css"
        ));

        // Show template
        $this->load->view("usergroups");

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
        $this->usergroups->load($searchObj->retrieveFromBootgrid($this->input));

        // Enrich object...
        $searchObj->rows = array();
        $searchObj->total = $this->usergroups->getTotalCount($searchObj);
        foreach ($this->usergroups->toArray() as $usergroup) {

            $searchObj->rows[] = (object)[
                "id"                  => $usergroup->id,
                "name"                => $usergroup->name,
                "authorization_level" => $usergroup->authorization_level,
                "users"               => $usergroup->users
            ];

        }

        // ...and output it as JSON
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($searchObj));

    }

}
?>