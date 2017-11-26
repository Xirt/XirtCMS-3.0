<?php
/**
 * Controller for the "Dashboard"-GUI and related processes (back end)
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class Dashboard extends XCMS_Controller {

    /**
     * CONSTRUCTOR
     * Instantiates controller with required helpers, libraries and models
     */
    public function __construct() {

        parent::__construct(75, true);

    }


    /**
     * Index page for this controller (main GUI)
     */
    public function index() {

        // Add page stylesheets
        XCMS_Page::getInstance()->addStylesheet(array(
            "assets/css/backend/mng_dashboard.css"
        ));

        $this->load->view("dashboard.tpl");

    }

}
?>