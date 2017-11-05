<?php

/**
 * Controller for showing a viewing / modifying a XirtCMS module configuration (back end)
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class Moduleconfiguration extends XCMS_Controller {

    /**
     * Constructs the controller with associated model
     */
    public function __construct() {

        parent::__construct(75, true);

        // Only allow AJAX requests
        if (!$this->input->is_ajax_request()) {
            return show_404();
        }

        // Load helpers
        $this->load->helper("attribute");
        $this->load->helper("moduleconfiguration");

        // Load libraries
        $this->load->library('form_validation');

        // Load model
        $this->load->model('ModuleConfigurationModel', 'configuration');
        $this->load->model("ModuleTypesModel", "types");

    }


    /**
     * "View ModuleConfiguration"-Page for this controller
     *
     * @param   int         $id             The ID of the requested configuration
     */
    public function view($id = 0) {

        // Validate given ID
        if (!is_numeric($id) || !$this->configuration->load($id)) {
            return;
        }

        // Prepare data...
        $data = (Object)array(
            "id"       => $this->configuration->get("id"),
            "name"     => $this->configuration->get("name"),
            "settings" => $this->configuration->get("settings")->toArray()
        );

        // ...and output it as JSON
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($data));

    }


    /**
     * "Create ModuleConfiguration"-functionality for this controller
     */
    public function create() {

        try {

            // Validate provided input
            if (!$this->form_validation->run()) {
                throw new UnexpectedValueException(null);
            }

            // Validate provided module type
            $type = $this->input->post("configuration_type");
            if (!$this->types->load() || !array_key_exists($type, $this->types->toArray())) {
                throw new UnexpectedValueException(null);
            }

            // Set & save new updates
            $this->configuration->set("name", $this->input->post("configuration_name"));
            $this->configuration->set("type", $type);
            $this->configuration->validate();
            $this->configuration->save();

            // Inform user
            XCMS_JSON::creationSuccessMessage();

        } catch (Exception $e) {

            XCMS_JSON::validationFailureMessage($e->getMessage());

        }

    }


    /**
     * "Modify ModuleConfiguration"-functionality for this controller
     */
    public function modify() {

        // Validate given item ID
        $id = $this->input->post("id");
        if (!is_numeric($id) || !$this->configuration->load($id)) {
            XCMS_JSON::loadingFailureMessage();
        }

        try {

            // Validate provided input
            if ($this->form_validation->run()) {
                throw new UnexpectedValueException(null);
            }

            // Save details
            $this->configuration->set("name", $this->input->post("configuration_name"));
            $this->configuration->save();

            // Inform user
            XCMS_JSON::modificationSuccessMessage();

        } catch (Exception $e) {

            XCMS_JSON::validationFailureMessage($e->getMessage());
            return;

        }

    }


    /**
     * "Modify ModuleConfiguration Settings"-functionality for this controller
     */
    public function modify_settings() {

        // Validate given item ID
        $id = $this->input->post("id");
        if (!is_numeric($id) || !$this->configuration->load($id)) {
            XCMS_JSON::loadingFailureMessage();
        }

        // Update settings...
        $settings = $this->configuration->get("settings");
        foreach ($settings->toArray() as $setting) {

            // Retrieve value
            $value = $setting->default;
            if ($this->input->post("setting_" . $setting->name) !== null) {
                $value = $this->input->post("setting_" . $setting->name);
            }

            // Check value validity
            if (!AttributeHelper::isValidInput($setting, $value)) {
                $value = $settings->default;
            }

            // Update value
            $this->configuration->setSetting($setting->name, $value);

        }

        // ...and save them
        $this->configuration->save();

        // Inform user
        XCMS_JSON::modificationSuccessMessage();

    }


    /**
     * "Toggle default"-functionality for this controller
     *
     * @param   int         $id             The ID of the affected configuration
     */
    public function toggle_default($id = -1) {

        // Validate given item ID
        if (!is_numeric($id) || !$this->configuration->load($id)) {
            return;
        }

        resetDefaultModuleConfiguration($this->configuration->get("type"));
        $this->configuration->set("default", $this->configuration->get("default") ? "0" : "1");
        $this->configuration->save();

    }


    /**
     * "Remove configuration"-Page for this controller.
     *
     * @param   int         $id             The ID of the affected configuration
     */
    public function remove($id = 0) {

        // Remove given ID after validation
        if (is_numeric($id) && $this->configuration->load($id)) {
            $this->configuration->remove();
        }

    }

}
?>