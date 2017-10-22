<?php

/**
 * Controller for showing a viewing / modifying a XirtCMS menu (back end)
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class Menu extends XCMS_Controller {

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
        $this->load->model("MenuModel", "menu");
        $this->load->model("MenusModel", "menus");

    }


    /**
     * "View menu"-functionality for this controller
     *
     * @param   int         $id             The name of the requested menu
     */
    public function view($id = 0) {

        // Validate given ID
        if (!is_numeric($id) || !$this->menu->load($id)) {
            return;
        }

        // Prepare data...
        $data = (Object)array(
            "id"   => $this->menu->get("id"),
            "name" => $this->menu->get("name")
        );

        // ...and output it as JSON
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($data));

    }


    /**
     * "Create Menu"-functionality for this controller
     */
    public function create() {

        try {

            // Validate provided input
            if (!$this->form_validation->run()) {
                throw new UnexpectedValueException(null);
            }

            // Create model...
            $this->menu->set("name", $this->input->post("menu_name"));
            $this->menu->save();

            // Inform user
            XCMS_JSON::creationSuccessMessage();

        } catch (Exception $e) {

            XCMS_JSON::validationFailureMessage($e->getMessage());

        }

    }


    /**
     * "Modify menu"-functionality for this controller
     */
    public function modify() {

        // Validate given user ID
        $id = $this->input->post("menu_id");
        if (!is_numeric($id) || !$this->menu->load($id)) {

            XCMS_JSON::loadingFailureMessage();
            return;

        }

        try {

            // Validate provided input
            if (!$this->form_validation->run()) {
                throw new UnexpectedValueException(null);
            }

            // Set & save new updates
            $this->menu->set("name", $this->input->post("menu_name"));
            $this->menu->validate();
            $this->menu->save();

            // Inform user
            XCMS_JSON::modificationSuccessMessage();

        } catch (Exception $e) {

            XCMS_JSON::validationFailureMessage($e->getMessage());

        }

    }


    /**
     * "Move menu up"-functionality for this controller
     *
     * @param   int         $id             The ID of the affected menu
     */
    public function move_up($id = -1) {

        // Validate given menu ID
        if (!is_numeric($id) || !$this->menu->load($id)) {
            return;
        }

        $target = -1;
        $this->menus->load();
        $menus = $this->menus->toArray();

        // Check for best candidate
        foreach ($menus as $index => $menu) {

            if ($menu->ordering < $this->menu->get("ordering")) {

                // Attempt to find best match
                if ($target == -1 || $menus[$target]->ordering < $menu->ordering) {
                    $target = $index;
                }

                // Stop if best option found
                if ($menu->ordering == $this->menu->get("ordering") - 1) {
                    break;
                }

            }

        }

        if ($target > -1) {

            // Update counterpart
            $this->load->model("MenuModel", "counterpart");
            $this->counterpart->load($menus[$target]->id);
            $this->counterpart->set("ordering", $this->counterpart->get("ordering") + 1);
            $this->counterpart->validate();
            $this->counterpart->save();

            // Update target menu
            $this->menu->set("ordering", $this->menu->get("ordering") - 1);
            $this->menu->validate();
            $this->menu->save();

        }

    }


    /**
     * "Move menu down"-functionality for this controller
     *
     * @param   int         $id             The ID of the affected menu
     */
    public function move_down($id = -1) {

        // Validate given menu ID
        if (!is_numeric($id) || !$this->menu->load($id)) {
            return;
        }

        $target = -1;
        $this->menus->load();
        $menus = $this->menus->toArray();

        // Check for best candidate
        foreach ($menus as $index => $menu) {

            if ($menu->ordering > $this->menu->get("ordering")) {

                // Attempt to find best match
                if ($target == -1 || $menus[$target]->ordering > $menu->ordering) {
                    $target = $index;
                }

                // Stop if best option found
                if ($menu->ordering == $this->menu->get("ordering") + 1) {
                    break;
                }

            }

        }

        if ($target > -1) {

            // Update counterpart
            $this->load->model("MenuModel", "counterpart");
            $this->counterpart->load($menus[$target]->id);
            $this->counterpart->set("ordering", $this->counterpart->get("ordering") - 1);
            $this->counterpart->validate();
            $this->counterpart->save();

            // Update target menu
            $this->menu->set("ordering", $this->menu->get("ordering") + 1);
            $this->menu->validate();
            $this->menu->save();

        }

    }


    /**
     * "Toggle sitemap"-functionality for this controller
     *
     * @param   int         $id             The ID of the affected menu
     */
    public function toggle_sitemap($id = -1) {

        // Validate given item ID
        if (!is_numeric($id) || !$this->menu->load($id)) {
            return;
        }

        $this->menu->set("sitemap", $this->menu->get("sitemap") ? "0" : "1");
        $this->menu->validate();
        $this->menu->save();

    }


    /**
     * "Remove menu"-functionality for this controller
     *
     * @param   int         $id             The ID of the targetted menu
     */
    public function remove($id = -1) {

        if (is_numeric($id) && $this->menu->load($id)) {
            $this->menu->remove();
        }

    }

}
?>