<?php

/**
 * Controller for showing a viewing / modifying a XirtCMS permit (back end)
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2018
 * @package     XirtCMS
 */
class Permit extends XCMS_Controller {

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

        $this->load->model("PermitModel", "permit");

    }


    /**
     * "View permit"-Page for this controller.
     *
     * @param   String      $type           The type of the requested permit
     * @param   int         $id             The id of the requested menuitem
     */
    public function view($type = "", $id = 0) {

        // Validate given ID
        if (is_numeric($id) && !$this->permit->load($type, $id)) {

            $this->permit->set("type", $type);
            $this->permit->set("id", $id);

        }

        // Prepare data...
        $data = (Object)array(
            "id"         => $this->permit->get("id"),
            "type"       => $this->permit->get("type"),
            "dt_start"   => $this->permit->get("dt_start"),
            "dt_expiry"  => $this->permit->get("dt_expiry"),
            "access_min" => $this->permit->get("access_min"),
            "access_max" => $this->permit->get("access_max")
        );

        // ...and output it as JSON
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($data));

    }

}
?>