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
     * CONSTRUCTOR
     * Instantiates controller with required helpers, libraries and models
     */
    public function __construct() {

        parent::__construct(75, true);

        // Load helpers
        $this->load->helper("grid");

        // Load models
        $this->load->model("ArticlesModel", false);
        $this->load->model("ExtArticlesModel", false);
        $this->load->model("CategoriesModel", false);

    }


    /**
     * Index page for this controller (main GUI)
     */
    public function index() {

        // Add page scripts
        XCMS_Page::getInstance()->addScript(array(
            "assets/scripts/backend/mng_articles.js",
            "assets/third-party/tinymce/tinymce.js",
            "assets/scripts/tinymce_config.js"
        ));

        // Add page stylesheets
        XCMS_Page::getInstance()->addStylesheet(array(
            "assets/css/backend/mng_articles.css"
        ));

        // Show template
        $this->load->view("articles.tpl", array(
            "categories" => (new CategoriesModel)->load()->toArray()
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

        // Retrieve request
        $gridIO = (new GridHelper())
            ->parseRequest($this->input);

        // Load requested data
        $articles = (new ExtArticlesModel())->init()
            ->set($gridIO->getRequest())
            ->load();

        // Prepare response ...
        $gridIO->setTotal($articles->getTotalCount($gridIO));
        foreach ($articles->toArray() as $article) {

            $gridIO->addRow([
                "id"           => $article->get("id"),
                "title"        => $article->get("title"),
                "dt_created"   => $article->get("dt_created"),
                "dt_publish"   => $article->get("dt_publish"),
                "dt_unpublish" => $article->get("dt_unpublish"),
                "published"    => $article->get("published"),
                "author"       => $article->getAuthor()->get("username")
            ]);

        }

        // ... and output it
        $gridIO->generateResponse($this->output);

    }

}
?>