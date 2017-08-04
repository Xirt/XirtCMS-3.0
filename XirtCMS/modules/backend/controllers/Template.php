<?php

/**
 * Controller for showing a viewing / modifying a XirtCMS template (back end)
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class Template extends XCMS_Controller {

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

        // Load helpers
        $this->load->helper("template");

        // Load libraries
        $this->load->library("form_validation");

        // Load models
        $this->load->model("TemplateModel", "model");
        $this->load->model("TemplateModel", "candidate");

    }


    /**
     * "View template"-functionality for this controller
     *
     * @param   int         $id             The ID of the requested template
     */
    public function view($id = 0) {

        // Validate given ID
        if (!is_numeric($id) || !$this->model->load($id)) {
            return;
        }

        // Prepare data...
        $data = (Object)array(
            "id"        => $this->model->get("id"),
            "name"      => $this->model->get("name"),
            "folder"    => $this->model->get("folder"),
            "positions" => $this->model->get("positions")
        );

        // ...and output it as JSON
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($data));

    }


    /**
     * "Create template"-functionality for this controller
     */
    public function create() {

        try {

            // Validate provided input
            if ($this->form_validation->run()) {
                throw new UnexpectedValueException(null);
            }

            // Set & save new updates
            $this->model->set("name", $this->input->post("template_name"));
            $this->model->set("folder", $this->input->post("template_folder"));
            $this->model->validate();
            $this->model->save();

            // Inform user
            XCMS_JSON::creationSuccessMessage();

        } catch (Exception $e) {

            XCMS_JSON::validationFailureMessage($e->getMessage());

        }

    }


    /**
     * "Modify template"-functionality for this controller
     */
    public function modify() {

        // Validate given item ID
        $id = $this->input->post("id");
        if (!is_numeric($id) || !$this->model->load($id)) {

            XCMS_JSON::loadingFailureMessage();
            return;

        }

        try {

            // Validate provided input
            if ($this->form_validation->run()) {
                throw new UnexpectedValueException(null);
            }

            // Set & save new updates
            $this->model->set("name",      $this->input->post("template_name"));
            $this->model->set("folder",    $this->input->post("template_folder"));
            $this->model->set("positions", explode(",", $this->input->post("positions_list")));
            $this->model->validate();
            $this->model->save();

            // Inform user
            XCMS_JSON::modificationSuccessMessage();

        } catch (Exception $e) {

            XCMS_JSON::validationFailureMessage($e->getMessage());
            return;

        }

    }


    /**
     * "Toggle published"-functionality for this controller
     *
     * @param   int         $id             The ID of the targetted template
     */
    public function toggle_published($id = -1) {

        // Validate given item ID
        if (!is_numeric($id) || !$this->model->load($id)) {
            XCMS_JSON::loadingFailureMessage();
        }

        TemplateHelper::resetActiveTemplate();
        $this->model->set("published", $this->model->get("published") ? "0" : "1");
        $this->model->save();

    }


    /**
     * "Remove template"-functionality for this controller
     *
     * @param   int         $id             The ID of the targetted template
     */
    public function remove($id = -1) {

        if (is_numeric($id) && $this->model->load($id)) {
            $this->model->remove();
        }

    }

}
?>