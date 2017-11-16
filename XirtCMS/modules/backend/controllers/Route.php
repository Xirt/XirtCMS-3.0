<?php

/**
 * Controller for showing a viewing / modifying a XirtCMS route (back end)
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class Route extends XCMS_Controller {

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
        $this->load->helper("route");

        // Load libraries
        $this->load->library("form_validation");

        // Load models
        $this->load->model("RouteModel", "route");

    }


    /**
     * "View route"-functionality for this controller
     *
     * @param   String      $id           The id of the requested route
     */
    public function view($id = -1) {

        // Validate given route ID
        if (!intval($id) || !$this->route->load($id)) {
            return;
        }

        // Prepare data...
        $data = (Object)array(
            "id"            => $this->route->get("id"),
            "public_url"    => $this->route->get("public_url"),
            "target_url"    => $this->route->get("target_url"),
            "menu_items"    => $this->route->get("menu_items"),
            "module_config" => $this->route->get("module_config"),
            "master"        => $this->route->get("master")
        );

        // ...and output it as JSON
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($data));

    }


    /**
     * "Create route"-functionality for this controller
     */
    public function create() {

        RouteHelper::init();

        // Validate provided input
        if (!$this->form_validation->run()) {

            XCMS_JSON::validationFailureMessage();
            return;

        }

        // Validate provided input
        $publicURL = $this->input->post("route_public_url");
        if (RouteList::getByPublic($publicURL)) {

            XCMS_JSON::validationFailureMessage(
                "This chosen URI is already in use. Please chose a different URI or modify the existing one."
            );
            return;

        }

        // Create item
        $this->route->set("public_url", $publicURL);
        $this->route->set("module_config", null);
        $this->route->set("target_url", "home");
        $this->route->set("master", null);
        $this->route->save();

        // Inform user
        XCMS_JSON::creationSuccessMessage();

    }


    /**
     * "Modify route"-functionality for this controller
     */
    public function modify() {

        // Validate given route ID
        $id = $this->input->post("id");
        if (!intval($id) || !$this->route->load($id)) {

            XCMS_JSON::validationFailureMessage();
            return;

        }

        // Validate provided input
        if (!$this->form_validation->run()) {

            XCMS_JSON::validationFailureMessage();
            return;

        }

        // Save item updates
        $this->route->set("public_url", $this->input->post("route_public_url"));
        $this->route->set("target_url", $this->input->post("route_target_url"));
        $this->route->set("module_config", $this->input->post("route_module_config"));
        $this->route->set("master", $this->input->post("route_master"));
        $this->route->save();

        // Inform user
        XCMS_JSON::modificationSuccessMessage();

    }


    /**
     * Attempts to show the route for the given target URL / module configuration
     */
    public function convert_target_url() {

        RouteHelper::init();

        // Retrieve data...
        $config = $this->input->post("config");
        $targetURL = $this->input->post("uri");
        $publicURL = RouteHelper::proposeRoute($targetURL, $config)->public_url ?? "";

        // ... and provide outcome
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode((object)array(
            "public_url"  => $publicURL,
            "target_url"  => $targetURL,
            "config"      => intval($config),
            "success"     => $publicURL ? true : false
        )));

    }


    /**
     * Attempts to show the route for the given public URL
     */
    public function convert_public_url() {

        RouteHelper::init();

        // Retrieve data...
        $publicURL = $this->input->post("uri");
        $targetURL = RouteList::getByPublic($publicURL)->target_url ?? "";
        $config    = RouteList::getByPublic($publicURL)->module_config ?? -1;

        // ... and provide outcome
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode((object)array(
            "public_url"  => $publicURL,
            "target_url"  => $targetURL,
            "config"      => intval($config),
            "success"     => $targetURL ? true : false
        )));

    }


    /**
     * "Remove route"-functionality for this controller
     *
     * @param   int         $id             The ID of the targetted route
     */
    public function remove($id) {

        // Remove given ID after validation
        if (intval($id) && $this->route->load($id)) {
            $this->route->remove();
        }

    }

}
?>