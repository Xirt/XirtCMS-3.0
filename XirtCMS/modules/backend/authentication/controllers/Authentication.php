<?php

/**
 * Controller for the "Authentication"-GUI and related processes
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class Authentication extends XCMS_Controller {

    /**
     * CONSTRUCTOR
     * Instantiates controller with required helpers, libraries and models
     */
    public function __construct() {

        parent::__construct(0, true);

        // Load helpers
        $this->load->helper('url');
        $this->load->helper('user');

        // Load models
        $this->load->model("UserModel", "user");

    }


    /**
     * Index Page for this controller.
     */
    public function index() {

        // Add page scripts
        XCMS_Page::getInstance()->addScript(array(
            "assets/scripts/backend/mng_authentication.js"
        ));

        // Add page stylesheets
        XCMS_Page::getInstance()->addStylesheet(array(
            "assets/css/backend/mng_authentication.css"
        ));

        // Show template
        $this->load->view("authentication.tpl");

    }


    /**
     * "View Article"-Page for this controller
     */
    public function authenticate() {

        $username = $this->input->post("user_name");
        $password = $this->input->post("user_password");
        $remember = ($this->input->post("user_cookies") == "on");

        // Attempt authentication
        $this->user->loadByUsername($username);
        switch (XCMS_Authentication::create($this->user, $password, $remember)) {

            case true:

                $this->output->set_content_type("application/json");
                $this->output->set_output(json_encode((object)array(
                    "type"    => "success",
                    "title"   => "Authentication succesful",
                    "message" => "You will be redirected to the dashboard in a moment."
                )));
                break;

            case 0:

                $this->output->set_content_type("application/json");
                $this->output->set_output(json_encode((object)array(
                    "type"    => "error",
                    "title"   => "Authentication failure",
                    "message" => "The given username/password combination was not recognized."
                )));
                break;

            default:

                $this->output->set_content_type("application/json");
                $this->output->set_output(json_encode((object)array(
                    "type"    => "error",
                    "title"   => "Authentication failure",
                    "message" => "An unknown error occurred during authentication. Please try again later."
                )));
                break;

        }

    }


    /**
     * "View Article"-Page for this controller
     */
    public function reset_password() {

        // Validate given username
        if (!$this->user->loadByUsername($this->input->post("request_name"))) {

            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode((object)array(
                "type"    => "error",
                "title"   => "Request failure",
                "message" => "The given username was not recognized."
            )));

            return;

        }

        // Validate given e-mail address
        if ($this->user->get("email") != $this->input->post("request_email")) {

            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode((object)array(
                "type"    => "error",
                "title"   => "Request failure",
                "message" => "The given username/e-mail address combination was not recognized."
            )));

            return;

        }

        // Reset the password
        $password = XCMS_Authentication::generatePassword();
        $this->user->set("password", $password);
        $this->user->validate();
        $this->user->save();

        // Inform the user (e-mail)
        UserHelper::commmunicatePassword($this->user, $password);

        // Inform the user (GUI)
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode((object)array(
            "type"    => "info",
            "title"   => "Request succesful",
            "message" => "An e-mail has been sent with your new authentication details."
        )));

    }


    /**
     * Destroys session and reroutes to the "Authentication"-GUI
     */
    public function logout() {

        XCMS_Authentication::destroy();
        redirect('backend/authentication');

    }


    /**
     * Reroutes requests when already authorized
     *
     * @param   String      $method         The requested method for this instance
     * @param   array       $params         The parameters to be passed to the method for this instance
     */
    public function _remap($method, $params = array()) {

        if (XCMS_Authentication::check() && $method != "logout") {

            $this->load->helper('url');
            redirect('backend/dashboard');
            return;

        }

        // Otherwise normal behaviour
        if (method_exists($this, $method)) {
            return call_user_func_array(array($this, $method), $params);
        }

        show_404();

    }

}
?>