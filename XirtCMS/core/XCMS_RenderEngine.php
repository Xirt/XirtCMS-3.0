<?php

/**
 * XirtCMS core class for final page rendering
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class XCMS_RenderEngine {

    /**
     * Generated page content and flushes it to the output buffer
     */
    public static function render() {

        $CI =& get_instance();
        $CI->load->model("TemplateModel", "model");

        // Check request type
        log_message("info", "[XCMS] Starting final rendering of page content.");
        if ($CI->input->is_ajax_request() || !XCMS_Config::get("USE_TEMPLATE")) {

            $CI->output->enable_profiler(false);
            return;

        }

        // Capture main content
        XCMS_Page::getInstance()->setContent($CI->output->get_output());
        $CI->output->set_output(null);

        // Handle frontend vs. backend request
        if (!XCMS_Config::get("XCMS_BACKEND") && $CI->model->load(null, false)) {

            $CI->load->template($CI->model->get("folder"));
            return;

        } else if (XCMS_Config::get("XCMS_BACKEND")) {

            $CI->load->view("backend");
            return;

        }

        log_message("error", "[XCMS] No default template configured.");
        show_404();


    }


    /**
     * Shows page header for the current request
     */
    public static function header() {

        $CI =& get_instance();
        $CI->load->view("xcms_header", array(
            "base_url"    => base_url(),
            "title"       => XCMS_Page::getInstance()->getTitle(),
            "metaTags"    => XCMS_Page::getInstance()->getMetaTags(),
            "scripts"     => XCMS_Page::getInstance()->getScripts(),
            "styleSheets" => XCMS_Page::getInstance()->getStylesheets()
        ));

    }


    /**
     * Shows module (content) for the current request
     */
    public static function module() {
        print(XCMS_Page::getInstance()->getContent());
    }


    /**
     * Shows widget(s) for given position for the current request
     *
     * @param   String      $position       The position for which to display the widget(s)
     */
    public static function widget($position) {

        // Load helpers
        $CI =& get_instance();
        $CI->load->helper("widget");

        WidgetHelper::init();
        foreach(WidgetHelper::retrieve($position) as $widget) {

            log_message("info", "[XCMS] Loading widget '{$widget->type}' on '{$position}'.");
            include_once(APPPATH . "widgets/" . $widget->type . "/index.php");

            $className = "xwidget_" . $widget->type;
            (new $className($widget->settings))->show();

        }

    }


    /**
     * Shows page footer for the current request
     */
    public static function footer() {

        $CI =& get_instance();
        $CI->load->view("xcms_footer");

    }

}
?>