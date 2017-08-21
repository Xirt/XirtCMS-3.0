<?php

/**
 * XirtCMS Widget for showing a simple contact form
 *
 * @author      A.G. Gideonse
 * @version     1.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class xwidget_form_contact extends XCMS_Widget {

    /**
     * CONSTRUCTOR
     * Initializes widget with given configuration
     *
     * @param   Array       $conf           Array containing configuration for this widget
     */
    public function __construct($conf) {

        parent::__construct($conf);

        // Ensure values for template
        $this->setConfig("title",    $this->config("title"));
        $this->setConfig("css_name", $this->config("css_name"));

    }


    /**
     * Handles any normal requests
     */
    public function show() {

        $this->view("default.tpl", array(
            "config" => $this->getConfig()
        ));

    }

}
?>