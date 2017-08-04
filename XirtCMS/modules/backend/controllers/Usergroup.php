<?php

/**
 * Controller for showing a viewing / modifying a XirtCMS usergroup (back end)
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class Usergroup extends XCMS_Controller {

    /**
     * CONSTRUCTOR
     * Instantiates controller with required helpers, libraries and models
     */
    public function __construct() {

        parent::__construct(75, false);

        // Only allow AJAX requests
        if (!$this->input->is_ajax_request()) {
            return show_404();
        }

        // Load libraries
        $this->load->library("form_validation");

        // Load models
        $this->load->model("UsergroupModel", "usergroup");

    }


    /**
     * "View usergroup"-functionality for this controller
     *
     * @param   int         $id             The ID of the affected usergroup
     */
    public function view($id = -1) {

        // Validate given name
        if (!is_numeric($id) || !$this->usergroup->load($id)) {
            return;
        }

        // Prepare data...
        $data = (object) [
            "id"                  => $this->usergroup->get("id"),
            "name"                => $this->usergroup->get("name"),
            "authorization_level" => $this->usergroup->get("authorization_level")
        ];

        // ...and output it as JSON
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($data));

    }


    /**
     * "Create usergroup"-functionality for this controller
     */
    public function create() {

        try {

            // Validate provided input
            if ($this->form_validation->run()) {
                throw new UnexpectedValueException(null);
            }

            // Set item values, validate and save
            $this->usergroup->set("authorization_level", $this->input->post("usergroup_authorization_level"));
            $this->usergroup->set("name", $this->input->post("usergroup_name"));
            $this->usergroup->validate();
            $this->usergroup->save();

            // Inform user
            XCMS_JSON::creationSuccessMessage();

        } catch (Exception $e) {

            XCMS_JSON::validationFailureMessage($e->getMessage());

        }

    }


    /**
     * "Modify usergroup"-functionality for this controller
     */
    public function modify() {

        // Validate given route ID
        $id = $this->input->post("id");
        if (!intval($id) || !$this->usergroup->load($id)) {

            XCMS_JSON::loadingFailureMessage();
            return;

        }

        try {

            // Validate provided input
            if ($this->form_validation->run()) {
                throw new UnexpectedValueException(null);
            }

            // Set item values, validate and save
            $this->usergroup->set("authorization_level", $this->input->post("usergroup_authorization_level"));
            $this->usergroup->set("name", $this->input->post("usergroup_name"));
            $this->usergroup->validate();
            $this->usergroup->save();

            // Inform user
            XCMS_JSON::modificationSuccessMessage();

        } catch (Exception $e) {

            XCMS_JSON::validationFailureMessage($e->getMessage());

        }

    }


    /**
     * "Remove usergroup"-functionality for this controller
     *
     * @param   int         $id             The ID of the affected usergroup
     */
    public function remove($id) {

        if (intval($id) && $this->usergroup->load($id)) {
            $this->usergroup->remove();
        }

    }

}
?>