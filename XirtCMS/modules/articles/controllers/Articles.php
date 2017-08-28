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
     * @var string
     * Base URL for viewing articles
     */
    const ARTICLE_URL = "article/view/";

    
    /*
     * CONSTRUCTOR
     * Instantiates controller with required helpers, libraries and models
     */
    public function __construct() {

        parent::__construct();

        // Load helpers
        $this->load->helper("route");
        $this->load->helper("article");
        $this->load->helper("db_search");

        // Load models
        $this->load->model("ArticlesModel", false);
        $this->load->model("ExtArticlesModel", "articles");

    }


    /**
     * Placeholder for invalid requests
     */
    public function index() {

        RouteHelper::init();

        // Populate models
        $articles = array();
        foreach ($this->_retrieveArticles() as $article) {

            // Enrich article
            $article = $article->getObject();
            $article->link = $this->_getLink($article->id);
            $article->introduction = $this->_getIntroduction($article->content);
            $articles[] = $article;

        }

        $this->load->view("default.tpl", array(
            "show_title" => $this->config("show_title", true),
            "css_name"   => $this->config("css_name", ""),
            "articles"   => $articles,
            "title"      => $this->_getTitle()
        ));

    }


    /**
     * Retrieves the title of the current module configuration
     *
     * @return  String                      The introduction part of the content
     */
    private function _getTitle() {

        $conf = $this->router->module_config;
        $query = $this->db->get_where(Query::TABLE_MODULES, array(
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
     * Retrieves articles using given search attributes
     *
     * @return  Array                       List containing all loaded articles
     */
    private function _retrieveArticles() {
        
        $articles = (new ExtArticlesModel())->init()
            ->set("limit",    $this->config("limit"))
            ->set("category", $this->config("category_id"))
            ->set("sorting",  $this->config("sorting") ?? "dt_publish DESC")
            ->load();

        return $articles->toArray();
        
    }


    /**
     * Retrieves the introduction part of the given content
     *
     * @param   String      $content        The context text out of which to retrieve the summary
     * @return  String                      The introduction part of the content
     */
    private function _getIntroduction($content) {

        // Retrieve introduction
        if (!($introduction = strip_tags(ArticleHelper::getSummary($content)))) {
            $introduction = strip_tags($content);
        }

        // Reduce introduction size (optional)
        if (($max = $this->config("max_length", 0)) > 0 && strlen($introduction) > $max) {
            return rtrim(substr($introduction, 0, strpos($introduction, ' ', $max))) . "&#8230;";
        }

        return $introduction;

    }


    /**
     * Retrieves the link to the given article
     *
     * @param   int         $id             The article ID for which the link is requested
     * @return  String                      The link towards the given article
     */
    private function _getLink($id) {

        // Retrieve parameter (module config)
        if (!($config = abs($this->config("module_config"))) || $config < 1) {
            $config = null;
        }

        return RouteHelper::getByTarget(self::ARTICLE_URL . $id, $config)->source_url;

    }

}
?>