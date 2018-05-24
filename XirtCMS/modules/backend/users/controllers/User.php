<?php

/**
 * Controller for showing a viewing / modifying a XirtCMS user (back end)
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class User extends XCMS_Controller {

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

        // Load helpers
        $this->load->helper('user');

        // Load libraries
        $this->load->library("email");
        $this->load->library("form_validation");

        // Load models
        $this->load->model("UserModel", "user");
        $this->load->model("UserModel", "candidate");

    }


    /**
     * "View User"-functionality for this controller
     *
     * @param   int         $id             The ID of the requested user
     */
    public function view($id = -1) {

        // Validate given ID
        if (!is_numeric($id) || !$this->user->load($id)) {
            return;
        }

        // Prepare data...
        $data = (object) [
            "id"           => $this->user->get("id"),
            "email"        => $this->user->get("email"),
            "username"     => $this->user->get("username"),
            "real_name"    => $this->user->get("real_name"),
            "usergroup_id" => $this->user->get("usergroup_id"),
            "attributes"   => $this->user->getAttributes()
        ];

        // ...and output it as JSON
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($data));

    }


    /**
     * "Create Uuser"-functionality for this controller
     */
    public function create() {

        try {

            // Validate provided input
            if (!$this->form_validation->run()) {
                throw new UnexpectedValueException(null);
            }

            // Set & save new updates
            $password = XCMS_Authentication::generatePassword();
            $this->user->set("real_name",    $this->input->post("user_real_name"));
            $this->user->set("username",     $this->input->post("user_username"));
            $this->user->set("usergroup_id", $this->input->post("user_usergroup_id"));
            $this->user->set("email",        $this->input->post("user_email"));
            $this->user->set("password",     $password);
            $this->user->validate();
            $this->user->save();

            // Inform the user (e-mail)
            UserHelper::commmunicatePassword($this->user, $password);
            XCMS_JSON::creationSuccessMessage();

        } catch (Exception $e) {

            XCMS_JSON::validationFailureMessage($e->getMessage());

        }

    }


    /**
     * "Modify User"-functionality for this controller
     */
    public function modify() {

        // Validate given user ID
        $id = $this->input->post("user_id");
        if (!is_numeric($id) || !$this->user->load($id)) {

            XCMS_JSON::loadingFailureMessage();
            return;

        }

        try {

            // Validate provided input
            if (!$this->form_validation->run()) {
                throw new UnexpectedValueException();
            }

            // Validate authorization to modify profile (protect administrator)
            if ($this->user->get("id") == 1 && $this->user->get("id") != XCMS_Authentication::getUserId()) {

                XCMS_JSON::modificationFailureMessage("Only the main administrator can modify this profile.");
                return;

            }

            // Set & save new updates
            $this->user->set("usergroup_id", $this->input->post("user_usergroup_id"));
            $this->user->set("real_name",    $this->input->post("user_real_name"));
            $this->user->set("username",     $this->input->post("user_username"));
            $this->user->set("email",        $this->input->post("user_email"));
            $this->user->validate();
            $this->user->save();

            // Inform user
            XCMS_JSON::modificationSuccessMessage();

        } catch (Exception $e) {

            XCMS_JSON::validationFailureMessage($e->getMessage());

        }

    }


    /**
     * "Modify user attributes"-functionality for this controller
     */
    public function modify_attributes() {

        // Validate given user ID
        $id = $this->input->post("user_id");
        if (!is_numeric($id) || !$this->user->load($id)) {

            XCMS_JSON::loadingFailureMessage();
            return;

        }

        // Set & save new updates
        foreach ($this->user->getAttributes() as $attribute) {

            $this->user->setAttribute($attribute->name, "");
            if ($value = $this->input->post("attr_" . $attribute->name)) {
                $this->user->setAttribute($attribute->name, $value);
            }

        }

        $this->user->validate();
        $this->user->save();

        // Inform user
        XCMS_JSON::modificationSuccessMessage();

    }


    /**
     * "Reset Password"-functionality for this controller
     */
    public function reset_password() {

        // Validate given user ID
        $id = $this->input->post("user_id");
        if (!is_numeric($id) || !$this->user->load($id)) {

            XCMS_JSON::loadingFailureMessage();
            return;

        }

        try {

            // Validate provided input
            if (!$this->form_validation->run()) {
                throw new UnexpectedValueException(null);
            }

            // Validate authorization to modify profile (protect administrator)
            if ($this->user->get("id") == 1 && $this->user->get("id") != XCMS_Authentication::getUserId()) {
                throw new UnexpectedValueException("Only the main administrator can modify this profile.");
            }

            // Reset the password
            $password = $this->input->post("user_password");
            $this->user->set("password", $password);
            $this->user->validate();
            $this->user->save();

            // Inform user
            UserHelper::commmunicatePassword($this->user, $password);
            XCMS_JSON::modificationSuccessMessage("The password has been reset and provided via e-mail.");

        } catch (Exception $e) {

            XCMS_JSON::validationFailureMessage($e->getMessage());

        }

    }


    /**
     * "Remove user"-functionality for this controller
     *
     * @param   int         $id             The ID of the targetted user
     */
    public function remove($id = -1) {

        if (is_numeric($id) && $id > 1 && $this->user->load($id)) {
            $this->user->remove();
        }

    }

}
?>