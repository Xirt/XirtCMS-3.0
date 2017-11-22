<?php

/**
 * Controller for showing a viewing / modifying a XirtCMS widget (back end)
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class Widget extends XCMS_Controller {

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
        $this->load->model("WidgetModel", "widget");
        $this->load->model("WidgetTypesModel", "types");

    }


    /**
     * "View widget"-functionality for this controller
     *
     * @param   String      $id           The id of the requested widget
     */
    public function view($id = -1) {

        // Validate widget ID
        if (!is_numeric($id) || !$this->widget->load($id)) {
            return;
        }

        // Prepare data...
        $data = (Object) [
            "id"           => $this->widget->get("id"),
            "name"         => $this->widget->get("name"),
            "page_all"     => $this->widget->get("page_all"),
            "page_default" => $this->widget->get("page_default"),
            "page_module"  => $this->widget->get("page_module"),
            "pages"        => $this->widget->get("pages"),
            "position"     => $this->widget->get("position"),
            "settings"     => $this->widget->get("settings")->toArray()
        ];

        // ...and output it as JSON
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($data));

    }


    /**
     * "Create widget"-functionality for this controller
     */
    public function create() {

        try {

            // Validate provided input
            if (!$this->form_validation->run()) {
                throw new UnexpectedValueException();
            }

            // Validate provided widget type
            $type = $this->input->post("widget_type");
            if (!$this->types->load() || !array_key_exists($type, $this->types->toArray())) {
                throw new UnexpectedValueException();
            }

            // Set & save new updates
            $this->widget->set("name",         $this->input->post("widget_name"));
            $this->widget->set("type",         $this->input->post("widget_type"));
            $this->widget->set("pages",        $this->input->post("widget_pages"));
            $this->widget->set("position",     $this->input->post("widget_position"));
            $this->widget->set("page_all",     (int)!is_null($this->input->post("widget_page_all")));
            $this->widget->set("page_default", (int)!is_null($this->input->post("widget_page_default")));
            $this->widget->set("page_module",  (int)!is_null($this->input->post("widget_page_module")));
            $this->widget->validate();
            $this->widget->save();

            // Inform user
            XCMS_JSON::creationSuccessMessage();

        } catch (Exception $e) {

            XCMS_JSON::validationFailureMessage($e->getMessage());

        }

    }


    /**
     * "Modify Widget"-functionality for this controller
     */
    public function modify() {

        // Validate given widget ID
        $id = $this->input->post("id");
        if (!is_numeric($id) || !$this->widget->load($id)) {

            XCMS_JSON::loadingFailureMessage();
            return;

        }

        try {

            // Validate provided input
            if (!$this->form_validation->run()) {
                throw new UnexpectedValueException();
            }

            // Set & save new updates
            $this->widget->set("name",         $this->input->post("widget_name"));
            $this->widget->set("pages",        $this->input->post("widget_pages"));
            $this->widget->set("position",     $this->input->post("widget_position"));
            $this->widget->set("page_all",     (int)!is_null($this->input->post("widget_page_all")));
            $this->widget->set("page_default", (int)!is_null($this->input->post("widget_page_default")));
            $this->widget->set("page_module",  (int)$this->input->post("widget_page_module"));
            $this->widget->validate();
            $this->widget->save();

            // Inform user
            XCMS_JSON::modificationSuccessMessage();

        } catch (Exception $e) {

            XCMS_JSON::validationFailureMessage($e->getMessage());

        }

    }


    /**
     * "Modify Widget Settings"-functionality for this controller
     */
    public function modify_settings() {

        // Validate given widget ID
        $id = $this->input->post("id");
        if (!is_numeric($id) || !$this->widget->load($id)) {

            XCMS_JSON::loadingFailureMessage();
            return;

        }

        try {

            // Update settings...
            $settings = $this->widget->get("settings");
            foreach ($settings->toArray() as $setting) {

                if (($value = $this->input->post("attr_" . $setting->name)) !== null) {
                    $this->widget->setSetting($setting->name, $value);
                }

            }

            // ... and save them
            $this->widget->validate();
            $this->widget->save();

            // Inform user
            XCMS_JSON::modificationSuccessMessage();


        } catch (Exception $e) {

            XCMS_JSON::validationFailureMessage($e->getMessage());

        }

    }


    /**
     * "Toggle published"-functionality for this controller
     *
     * @param   String      $id           The id of the affected widget
     */
    public function toggle_published($id = 0) {

        if (is_numeric($id) && $this->widget->load($id)) {

            $this->widget->set("published", $this->widget->get("published") ? "0" : "1");
            $this->widget->save();

        }

    }


    /**
     * "Remove widget"-functionality for this controller.
     *
     * @param   String      $id           The id of the affected widget
     */
    public function remove($id = 0) {

        if (is_numeric($id) && $this->widget->load($id)) {
             $this->widget->remove();
        }

    }

}