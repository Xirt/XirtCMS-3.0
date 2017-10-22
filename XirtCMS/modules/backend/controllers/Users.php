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
        $this->load->helper("grid");

        // Load models
        $this->load->model("UsersModel", false);
        $this->load->model("ExtUsersModel", false);
        $this->load->model("UsergroupsModel", false);

    }


    /**
     * Index page for this controller (main GUI)
     */
    public function index() {

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
            "usergroups" => $this->_getUsergroups()
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

        // Retrieve request
        $gridIO = (new GridHelper())
            ->parseRequest($this->input);

        // Load requested data
        $usergroups = $this->_getUsergroups();
        $users = (new ExtUsersModel())->init()
            ->set($gridIO->getRequest())
            ->load();

        // Prepare response ...
        $gridIO->setTotal($users->getTotalCount($gridIO));
        foreach ($users->toArray() as $user) {

            $gridIO->addRow([
                "id"         => $user->get("id"),
                "email"      => $user->get("email"),
                "username"   => $user->get("username"),
                "real_name"  => $user->get("real_name"),
                "dt_created" => $user->get("dt_created"),
                "usergroup"  => $usergroups[$user->get("usergroup_id")]->get("name")
            ]);

        }

        // ... and output it
        $gridIO->generateResponse($this->output);

    }

    /**
     * Returns list of all known usergroups
     *
     * @return  Array                       The list of known usergroups (with authorization level as key)
     */
    private function _getUsergroups() {

        $usergroups = array();
        foreach ((new UserGroupsModel())->load()->toArray() as $usergroup) {
            $usergroups[$usergroup->get("id")] = $usergroup;
        }

        return $usergroups;

    }

}
?>