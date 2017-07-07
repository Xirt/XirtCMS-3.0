<?php

/**
 * XirtCMS template loader
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class XCMS_Template {
    
    
    /**
     * @var Object|null
     * Internal reference to CI
     */
    private $CI;

    
    /**
     * CONSTRUCTOR
     * Initializes template loader with CI reference
     */
    function __construct() {

        $this->CI =& get_instance();
        $this->CI->load->helper("url");

    }

    
    /**
     * Loads the given view and populates it with given data
     * 
     * @param   String      $view           The view to be loaded
     * @param   array       $data           Array containing data to be shown in the given view
     */
    public function load($view = null, $data = null) {

        $view = $view ? $view : "default";

        if (file_exists(APPPATH . "views/" . $view)) {

            $body_view_path = $view;

        } else if (file_exists(APPPATH . "views/" . $view . ".php")) {

            $body_view_path = $view . ".php";

        } else {

            show_error("Unable to load the requested view: " . $view);
            return;

        }

        if (substr($view, 0, 7) == "backend") {
            $data["main"] = $this->CI->load->view($body_view_path, $data, TRUE);
        }

        $this->CI->load->view("backend/template.php", $this->_enrich($data));

    }

}
?>