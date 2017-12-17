<?php

/**
 * Controller for the "Articles"-GUI (front-end)
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class Articles extends XCMS_Controller {

    /**
     * Base URL for viewing articles
     * @var string
     */
    const ARTICLE_URL = "article/view/";


    /*
     * CONSTRUCTOR
     * Instantiates controller with required helpers, libraries and models
     */
    public function __construct() {

        parent::__construct();

        // Load helpers
        $this->load->helper("url");
        $this->load->helper("route");
        $this->load->helper("article");

        // Load models
        $this->load->model("CategoryModel", false);
        $this->load->model("ArticlesModel", false);
        $this->load->model("ExtArticlesModel", false);
        $this->load->model("SimplifiedArticleModel", false);

        RouteHelper::init();

    }


    /**
     * Shows all articles in the requested category
     *
     * @param   int         $category       The ID of the category to retrieve
     */
    public function index($category = null) {

        // Check for valid category
        if (!$category = $this->_retrieveCategory($category)) {
            return show_404();
        }

        $this->load->view("default.tpl", array(
            "css_name"   => $this->config("css_name", ""),
            "show_title" => $this->config("show_title", true),
            "channel"    => $this->_retrieveSummary($category, false),
            "articles"   => $this->_retrieveArticles($category, false)
        ));

    }


    /**
     * ALTERNATIVE: Shows all articles
     */
    public function all() {
        $this->index();
    }


    /**
     * ALTERNATIVE: Shows all articles in the requested category
     *
     * @param   int         $category       The ID of the category to retrieve
     */
    public function category($category) {
        $this->index($category);
    }


    /**
     * Retrieves articles using given search attributes
     *
     * @param   int         $category       The ID of the category to retrieve
     * @return  Array                       List containing all loaded articles
     */
    private function _retrieveCategory($category = null) {

        // Attempt to retrieve category
        if (!is_numeric($category) || !$category = (new CategoryModel())->load($category)) {
            return null;
        }

        // Check category status
        if (!$category->get("published")) {
            return null;
        }

        return $category;

    }


    /**
     * Retrieves articles using given search attributes
     *
     * @param   Object      $category       The CategoryModel for which to retrieve the object
     * @return  Array                       List containing all loaded articles
     */
    private function _retrieveArticles($category = null) {

        // Retrieve articles
        $model = (new ExtArticlesModel())
            ->set("sorting",  $this->config("sorting") ?? "dt_publish DESC")
            ->set("category", $category ? $category->get("id") : 0)
            ->set("limit",    $this->config("limit"))
            ->load();

        // Prepare output
        $articles = array();
        foreach ($model->toArray() as $article) {
            $articles[] = (new SimplifiedArticleModel($article))->toObject();
        }

        return $articles;

    }


    /**
     * Creates object with details for given output articles
     *
     * @param   Object      $category       The CategoryModel for which to retrieve the object
     * @return  Object                      The created Object
     */
    private function _retrieveSummary($category = null, $rss = false) {

        return (object) [

            "url"       => current_url(),
            "title"     => $this->_getPageTitle($category),
            "desc"      => $this->_getPageDescription($category)

        ];

    }


    /**
     * Retrieves the title of the current module configuration
     *
     * @param   Object      $category       The CategoryModel for which to retrieve the object
     * @return  String                      The introduction part of the content
     */
    private function _getPageTitle($category = null) {

        $title = XCMS_Config::get("WEBSITE_TITLE");
        if ($moduleTitle = $this->_getModuleTitle()) {
            return $moduleTitle;
        }

        if ($category) {
            return $title . " - " . $category->get("name");
        }

        return $title;

    }


    /**
     * Retrieves the title of the current module configuration
     *
     * @return  String                      The introduction part of the content
     */
    private function _getModuleTitle() {

        $conf = $this->router->module_config;
        $query = $this->db->get_where(XCMS_Tables::TABLE_MODULES, array(
            "type" => $this->router->class
        ));

        // Parse results
        $title = "Articles";
        foreach ($query->result() as $row) {

            // Load requested config
            if ($conf && $conf == $row->id) {
                return $row->name;
            }

            // Or default if not preference
            if (!$conf && $row->default) {
                $title = $row->name;
            }

        }

        return $title;

    }


    /**
     * Retrieves the description for the current request
     *
     * @param   Object      $category       The CategoryModel for which to retrieve the object
     * @return  String                      The description for the current request
     */
    private function _getPageDescription($category = null) {
        return XCMS_Config::get("WEBSITE_DESCRIPTION");
    }

}
?>