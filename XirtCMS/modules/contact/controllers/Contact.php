<?php

/**
 * Controller for handling contact requests
 *
 * @author      A.G. Gideonse
 * @version     1.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class Contact extends XCMS_Controller {

    /**
     * CONSTRUCTOR
     * Instantiates controller with required helpers, libraries and models
     */
    public function __construct() {

        parent::__construct();

        // Load libraries
        $this->load->library("email");
        $this->load->library("form_validation");

    }


    /**
     *
     */
    public function index() {
        $this->load->view("form");
    }


    /**
     * Attempts to process posted information
     */
    public function post() {

        try {

            // Attempt to send e-mail
            $data = $this->_retrieveData();
            $this->_validateOrFail($data);
            $this->_sendMail($data);

            // Inform user
            $this->_showSuccess($data);

        } catch (Exception $e) {

            $this->_showFailure($e->getMessage());

        }

    }


    /**
     *
     *
     * @return  Object                      .
     */
    private function _retrieveData() {

        return (Object) [
            "name"    => $this->input->post("name"),
            "company" => $this->input->post("company"),
            "email"   => $this->input->post("email"),
            "phone"   => $this->input->post("phone"),
            "subject" => $this->input->post("subject"),
            "content" => $this->input->post("content")
        ];

    }



    /**
     *
     *
     * @param   Object      $data           .
     * @throws  Exception                   .
     */
    private function _validateOrFail($data) {

        // Set validation rules
        $this->form_validation->set_rules(array(

            array(
                "field" => "name",
                "label" => "Name",
                "rules" => "trim|required"
            ),

            array(
                "field" => "email",
                "label" => "E-mail address",
                "rules" => "trim|required|valid_email"
            ),

            array(
                "field" => "phone",
                "label" => "Phone",
                "rules" => "numeric"
            ),

            array(
                "field" => "subject",
                "label" => "Subject",
                "rules" => "trim|required"
            ),

            array(
                "field" => "content",
                "label" => "Content",
                "rules" => "trim|required"
            ),

        ));

        // Validate given content
        if (!$this->form_validation->set_data((array)$data)->run()) {

            $errors = $this->form_validation->error_array();
            throw new UnexpectedValueException(array_shift($errors));

        }

    }


    /**
     *
     *
     * @param   Object      $data           .
     */
    private function _sendMail($data) {

        // Initialize library
        $this->email->initialize(
            array("mailtype" => "html"
        ));

        // Set e-mail headers
        $this->email->from(XCMS_Config::get("EMAIL_SENDER_EMAIL"), XCMS_Config::get("EMAIL_SENDER_NAME"))
        ->subject($this->config("subject"))
        ->to($this->config("email"));

        // Set e-mail content and send
		// TODO :: Use field 'title' in e-mail to identify man vs woman
        $this->email->message($this->load->view("email.tpl", array(
            "data"   => $this->_escapeData($data),
            "target" => base_url()
        ), true))->send();

    }


    /**
     *
     *
     * @param   String      $msg            .
     */
    private function _showFailure($msg) {

        // Respond to AJAX requests...
        if ($this->input->is_ajax_request()) {

            XCMS_JSON::validationFailureMessage($msg);
            return;

        }

        // ... or show regular output
        $this->load->view("form", array(
            "error" => $msg
        ));

    }


    /**
     *
     *
     * @param   Object      $data           .        .
     */
    private function _showSuccess($data) {

        // Respond to AJAX requests...
        if ($this->input->is_ajax_request()) {

            XCMS_JSON::customContentMessage("Successfully submitted", "The information you have entered was succesfully processed.");
            return;

        }

        // ... or show regular output
        $this->load->view("success", array(
            "data" => $data
        ));

    }


    /**
     * Returns the given list with applicable characters converted to HTML entities
     *
     * @param   array       $data           The list with data to parse
     * @return  array                       The parsed list (with HTML entities)
     */
    private function _escapeData($data) {

        foreach ($data as $field => $value) {
            $data->$field = htmlentities($value);
        }

        return $data;

    }

}
?>