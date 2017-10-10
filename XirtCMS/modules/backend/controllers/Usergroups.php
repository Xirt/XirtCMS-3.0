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
        $this->load->helper("grid");

        // Load models
        $this->load->model("UsergroupsModel", false);
        $this->load->model("ExtUsergroupsModel", false);

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

        // Retrieve request
        $gridIO = (new GridHelper())
            ->parseRequest($this->input);

        // Load requested data
        $usergroups = (new ExtUsergroupsModel())->init()
            ->set($gridIO->getRequest())
            ->load();

        // Prepare response ...
        $gridIO->setTotal($usergroups->getTotalCount($gridIO));
        foreach ($usergroups->toArray() as $usergroup) {

            $gridIO->addRow([
                "id"                  => $usergroup->get("id"),
                "name"                => $usergroup->get("name"),
                "authorization_level" => $usergroup->get("authorization_level"),
                "users"               => $usergroup->get("users")
            ]);

        }

        // ... and output it
        $gridIO->generateResponse($this->output);

    }

}
?>