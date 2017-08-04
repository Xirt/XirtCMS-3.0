<?php

/**
 * Controller for the "Articles"-GUI and related processes (back end)
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class Articles extends XCMS_Controller {

    /**
     * Constructs the controller with associated model
     */
    public function __construct() {

        parent::__construct(75, true);

        // Load helpers
        $this->load->helper("db_search");

        // Load models
        $this->load->model("ArticlesModel", "articles");
        $this->load->model("CategoriesModel", "categories");

    }


    /**
     * Index page for this controller (main GUI)
     */
    public function index() {

        // Retrieve categories
        $this->categories->load(new SearchAttributes());

        // Add page scripts
        XCMS_Page::getInstance()->addScript(array(
            "assets/scripts/backend/mng_articles.js",
            "assets/third-party/tinymce/tinymce.js",
            "assets/third-party/datepicker/bootstrap-datepicker.min.js",
            "assets/third-party/datepicker/locales/bootstrap-datepicker.en-GB.min.js"
        ));

        // Add page stylesheets
        XCMS_Page::getInstance()->addStylesheet(array(
            "assets/css/backend/mng_articles.css",
            "assets/third-party/datepicker/bootstrap-datepicker.min.css"
        ));

        // Show template
        $this->load->view("articles.tpl", array(
            "categories" => $this->categories->toArray()
        ));

    }


    /**
     * Listing method for listing all requested items
     */
    public function view() {

        // Only allow AJAX requests
        if (!$this->input->is_ajax_request()) {
            return show_404();
        }

        // Load requested data
        $searchObj = new SearchAttributes();
        $this->articles->load($searchObj->retrieveFromBootgrid($this->input));

        // Enrich object...
        $searchObj->rows = array();
        $searchObj->total = $this->articles->getTotalCount($searchObj);
        foreach ($this->articles->toArray() as $article) {

            $searchObj->rows[] = (Object) array(
                "id"         => $article->get("id"),
                "title"      => $article->get("title"),
                "author"     => $article->get("username"),
                "dt_created" => $article->get("dt_created")
            );

        }

        // ...and output it as JSON
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($searchObj));

    }

}
?>