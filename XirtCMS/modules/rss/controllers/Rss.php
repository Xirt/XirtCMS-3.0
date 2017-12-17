<?php

/**
 * Controller for the "RSS"-GUI (front-end)
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class Rss extends XCMS_Controller {

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
     * Shows all articles in the requested category as RSS feed
     *
     * @param   int         $category       The ID of the category to retrieve
     */
    public function index($category = null) {

        // Check for valid category
        if ($category && !$category = $this->_retrieveCategory($category)) {
            return show_404();
        }

        // Prepare data
        $articles = $this->_retrieveArticles($category);
        $summary  = $this->_retrieveSummary($articles, $category);
        $articles = $this->_convertArticles($articles);

        // Disable default template...
        XCMS_Config::set("USE_TEMPLATE", "FALSE");

        // ... and show content
        $this->output->set_content_type("application/rss+xml");
        $this->load->view("default.tpl", array(
            "css_name"   => $this->config("css_name", ""),
            "show_title" => $this->config("show_title", true),
            "channel"    => $summary,
            "articles"   => $articles
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
            ->set("category", $category ? $category->get("id") : 0)
            ->set("limit",    $this->config("limit"))
            ->load();

        return $model->toArray();

    }


    /**
     * Converts list of given ArticleModel into list of SimplifiedArticleModel
     *
     * @param   Array       $articles       List containing articles as ArticleModel
     * @return  Array                       List containing articles as SimplifiedArticleModel
     */
    private function _convertArticles($articles) {

        // Prepare output
        $converted = array();
        foreach ($articles as $article) {
            $converted[] = (new SimplifiedArticleModel($article))->toObject();
        }

        return $converted;

    }


    /**
     * Creates object with details for given output articles
     *
     * @param   Array       $articles       List containing all loaded articles as ArticleModel
     * @param   Object      $category       The CategoryModel for which to retrieve the object
     * @return  Object                      The created Object
     */
    private function _retrieveSummary($articles, $category = null) {

        return (object) [

            "url"       => current_url(),
            "generator" => $this->_getPageGenerator(),
            "title"     => $this->_getPageTitle($category),
            "desc"      => $this->_getPageDescription($category),
            "pubDate"   => $this->_getPubDate($articles)->format(DateTime::RSS),
            "buildDate" => $this->_getBuildDate($articles)->format(DateTime::RSS)

        ];

    }


    /**
     * Retrieves the title of the current module configuration
     *
     * @param   Object      $category       The CategoryModel for which to retrieve the object
     * @return  String                      The introduction part of the content
     */
    private function _getPageTitle($category = null) {

        if ($category) {
            return XCMS_Config::get("WEBSITE_TITLE") . " - " . $category->get("name");
        }

        return XCMS_Config::get("WEBSITE_TITLE");

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


    /**
     * Retrieves the generator for the current request
     *
     * @return  String                      The generator for the current request
     */
    private function _getPageGenerator() {
        return XCMS_Config::get("WEBSITE_GENERATOR");
    }


    /**
     * Retrieves the last publish date for the feed (e.g. addition of article)
     *
     * @param   Array       $articles       List containing all loaded articles as ArticleModel
     * @return  Object                      The found date as DateTime
     */
    private function _getPubDate($articles) {

        $pubDate = null;
        foreach ($articles as $article) {

            $dt = ArticleHelper::getPublished($article);
            $pubDate = (!$pubDate || $dt > $pubDate) ? $dt : $pubDate;

        }

        return $pubDate ? $pubDate : new DateTime();

    }


    /**
     * Retrieves the last modification date for the feed (e.g. addition / modification of article)
     *
     * @param   Array       $articles       List containing all loaded articles as ArticleModel
     * @return  Object                      The found date as DateTime
     */
    private function _getBuildDate($articles) {

        $buildDate = null;
        foreach ($articles as $article) {

            // TODO :: Retrieve correct value for article (potentially using versioning)
            //$dt = ArticleHelper::getModified($article);
            //$pubDate = (!$buildDate || $dt > $buildDate) ? $dt : $buildDate;

        }

        return $buildDate ? $buildDate : new DateTime();

    }

}
?>