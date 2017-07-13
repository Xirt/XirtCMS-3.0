<?php

/**
 * XirtCMS Widget for integrating Google Analytics tracking code
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class xwidget_google_analytics extends XCMS_Widget {

    /**
     * Handles any normal requests
     */
    public function show() {

        $this->view("template.tpl", array(
            "config" => $this->getConfig()
        ));

   }

}
?>