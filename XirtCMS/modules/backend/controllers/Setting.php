<?php

/**
 * Controller for showing a viewing / modifying a XirtCMS settings (back end)
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class Setting extends XCMS_Controller {

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

        // Load libraries
        $this->load->library("form_validation");

        // Load models
        $this->load->model("SettingModel", "setting");

    }


    /**
     * "View setting"-functionality for this controller
     *
     * @param   String      $name           The name of the requested setting
     */
    public function view($name = null) {

        // Validate given name
        if (!strlen($name) || !$this->setting->load($name)) {
            return;
        }

        // Prepare data...
        $data = (Object)array(
            "name"  => $this->setting->get("name"),
            "value" => $this->setting->get("value")
        );

        // ...and output it as JSON
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($data));

    }


    /**
     * "Modify setting"-functionality for this controller
     */
    public function modify() {

        // Validate given user ID
        $name = $this->input->post("setting_name");
        if (!strlen($name) || !$this->setting->load($name)) {

            XCMS_JSON::loadingFailureMessage();
            return;

        }

        try {

            // Validate provided input
            if (!$this->form_validation->run()) {
                throw new UnexpectedValueException();
            }

            // Set & save new updates
            $this->setting->set("value", $this->input->post("setting_value"));
            $this->setting->save();

            // Inform user
            XCMS_JSON::modificationSuccessMessage();

        } catch (Exception $e) {

            XCMS_JSON::validationFailureMessage($e->getMessage());

        }

    }

}
?>