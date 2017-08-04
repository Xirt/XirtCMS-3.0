<?php

/**
 * Controller for the "Users"-GUI and related processes (back end)
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class Users extends XCMS_Controller {

    /**
     * CONSTRUCTOR
     * Instantiates controller with required helpers, libraries and models
     */
    public function __construct() {

        parent::__construct(75, true);

        // Load helpers
        $this->load->helper("db_search");

        // Load models
        $this->load->model("UsersModel", "users");
        $this->load->model("UsergroupsModel", "usergroups");

    }


    /**
     * Index page for this controller (main GUI)
     */
    public function index() {

        $this->usergroups->load(new SearchAttributes());
        $usergroups = $this->usergroups->toArray();

        // Add page scripts
        XCMS_Page::getInstance()->addScript(array(
            "assets/scripts/backend/mng_users.js"
        ));

        // Add page stylesheets
        XCMS_Page::getInstance()->addStylesheet(array(
            "assets/css/backend/mng_users.css"
        ));

        // Show template
        $this->load->view("users", array(
            "usergroups" => $usergroups
        ));

    }


    /**
     * Listing method for listing all requested items
     */
    public function view() {

        // Only allow AJAX requests
        if (!$this->input->is_ajax_request()) {
            return show_404();
        }

        // Load requested data
        $searchObj = new SearchAttributes();
        $this->users->load($searchObj->retrieveFromBootgrid($this->input));

        // Enrich object...
        $searchObj->rows = array();
        $searchObj->total = $this->users->getTotalCount($searchObj);
        foreach ($this->users->toArray() as $user) {

            $searchObj->rows[] = (Object) [
                "id"         => $user->id,
                "username"   => $user->username,
                "email"      => $user->email,
                "real_name"  => $user->real_name,
                "usergroup"  => $user->usergroup,
                "dt_created" => $user->dt_created
            ];

        }

        // ...and output it as JSON
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($searchObj));

    }

}
?>