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
        $this->load->helper("url");
        $this->load->helper("route");
        $this->load->helper("article");

        // Load models
        $this->load->model("ArticlesModel", false);
        $this->load->model("ExtArticlesModel", "articles");

        RouteHelper::init();
        
    }


    /**
     * Shows all articles in the requested category
     *
     * @param   int         $category       The ID of the category to retrieve
     */
    public function index($category = null) {

        // Populate models
        $articles = array();
        foreach ($this->_retrieveArticles($category) as $article) {
            $articles[] = $this->_createArticleObject($article, false);
        }
        
        $this->load->view("default.tpl", array(
            "show_title" => $this->config("show_title", true),
            "css_name"   => $this->config("css_name", ""),
            "title"      => $this->_getPageTitle(),
            "url"        => $this->_getPageURL(),
            "articles"   => $articles
        ));
            
    }
    
    
    /**
     * Shows all articles or all articles in given category as RSS feed
     *
     * @param   int         $category       The ID of the category to retrieve
     */
    public function rss($category = null) {
        
        $this->setConfig("max_length", 500);
        
        // Populate models
        $articles = array();
        foreach ($this->_retrieveArticles($category) as $article) {
            $articles[] = $this->_createArticleObject($article, true);
        }
        
        // Disable default template...
        XCMS_Config::set("USE_TEMPLATE", "FALSE");
        
        // ... and show content
        $this->output->set_content_type("application/rss+xml");        
        $this->load->view("rss.tpl", array(
            "show_title" => $this->config("show_title", true),
            "css_name"   => $this->config("css_name", ""),
            "title"      => $this->_getPageTitle(),
            "url"        => $this->_getPageURL(),
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
     * Retrieves the title of the current module configuration
     *
     * @return  String                      The introduction part of the content
     */
    private function _getPageTitle() {

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
     * Retrieves the URL of the current page
     *
     * @return  String                      The URL for the current page
     */
    private function _getPageURL() {
        return current_url();
    }


    /**
     * Retrieves articles using given search attributes
     *
     * @param   int         $category       The ID of the category to retrieve
     * @return  Array                       List containing all loaded articles
     */
    private function _retrieveArticles($category = null) {

        $articles = (new ExtArticlesModel())
            ->set("sorting",  $this->config("sorting") ?? "dt_publish DESC")
            ->set("limit",    $this->config("limit"))
            ->set("category", $category)
            ->load();

        return $articles->toArray();

    }
    
    
    /**
     * Creates object with article details for given article and output format
     *
     * @param   Object      $category       The ArticleModel for which to create the Object
     * @param   boolean     $rss            Toggless between regular and RSS format output
     * @return  Object                      The created Object
     */
    private function _createArticleObject($article, $rss = false) {

        $obj = $article->getObject();
        
        
        if ($rss) {
            
            $obj->intro   = htmlspecialchars($this->_getIntroduction($article));
            $obj->title   = htmlspecialchars($obj->title);
            $obj->link    = base_url() . $this->_getLink($obj->id);
            $obj->pubDate = $this->_getPublishDate($article);
            $obj->guid    = $this->_getGUID($obj->id);
            
        } else {
            
            $obj->intro   = htmlspecialchars($this->_getIntroduction($article));
            $obj->link    = $this->_getLink($obj->id);
            
        }
        
        return $obj;
        
    }


    /**
     * Retrieves the introduction part of the given content
     *
     * @param   String      $article        The ArticleModel for which to retrieve the summary
     * @return  String                      The introduction part of the content
     */
    private function _getIntroduction($article) {

        // Retrieve introduction
        if (!($introduction = strip_tags(ArticleHelper::getSummary($article)))) {
            $introduction = strip_tags(ArticleHelper::getContent($article));
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

        return RouteHelper::getByTarget(self::ARTICLE_URL . $id, $config)->public_url;

    }
    
    
    /**
     * Retrieves the GUID for the given article
     *
     * @param   int         $id             The article ID for which the link is requested
     * @return  String                      The GUID (hashed MD5 value) for the given article
     */
    private function _getGUID($id) {
        return md5(self::ARTICLE_URL . $id);
    }
    
    
    /**
     * Retrieves the publishing date for the given article
     *
     * @param   Object      $article        The ArticleModel for which to retrieve the publishing date
     * @return  Object                      The DateTime publishing date
     */
    private function _getPublishDate($article) {
        
        if (!$pubDate = ArticleHelper::getPublished($article)) {
            $pubDate = new DateTime();
        }
        
        return $pubDate->format(DateTime::RSS);
        
    }

}
?>