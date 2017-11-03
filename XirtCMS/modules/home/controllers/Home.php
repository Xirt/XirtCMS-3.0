<?php

/**
 * Controller for redirection to configured home page
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class Home extends XCMS_Controller {

    /**
     * CONSTRUCTOR
     * Instantiates controller with required helpers, libraries and models
     */
    public function __construct() {

        parent::__construct();

        $this->load->helper('url');
        $this->load->model("MenuitemModel", "menuitem");

    }


    /**
     * Attempts to redirect the user to the set homepage
     */
    public function index() {

        // Check for home existence
        if (!$this->menuitem->load()) {

            log_message("error", "No home item configured.");
            return show_404();

        }

        // Candidate home URLs
        $candidates = array(
            $this->menuitem->get("public_url"),
            $this->menuitem->get("target_url"),
            $this->menuitem->get("uri")
        );

        // Remove any invalid candidates
        foreach ($candidates as $key => $candidate) {

            // Prevent loops (same controller)
            if ($key === 1 && strpos(substr($candidate, 0, 5), "home") !== false) {
                unset($candidates[$key]);
            }

            // Prevent loops (same address)
            if (uri_string() == $candidate) {
                unset($candidates[$key]);
            }

        }

        // Check candidates (any left?)
        if (!count($candidates)) {

            log_message("error", "Invalid home item configured (infinite loop).");
            return show_404();

        }

        // Redirect to "Home"-item (first candidate)
        log_message("debug", "Forwarding to set home item.");
        redirect($candidates[0]);

    }

}
?>