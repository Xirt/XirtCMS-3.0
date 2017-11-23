<?php

/**
 * Controller for showing a single XirtCMS article
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class Article extends XCMS_Controller {

    /**
     * CONSTRUCTOR
     * Instantiates controller with required helpers, libraries and models
     */
    public function __construct() {

        parent::__construct();

        // Load helpers
        $this->load->helper("article");

        // Load models
        $this->load->model("UserModel", "author");
        $this->load->model("ArticleModel", "article");

    }


    /**
     * Placeholder for invalid requests
     */
    public function index() {
        return show_404();
    }


    /**
     * Attempts to show requested article
     *
     * @param   mixed      $id              The ID of the requested article (non validated)
     */
    public function view($id = 0) {

        // Attempt to load content
        if (!$this->_loadArticle($id)) {
            return show_404();
        }

        // Show content
        $this->_setHeaders($this->article);
        $this->load->view("default.tpl", array(
            "author"       => $this->_getAuthorObject($this->article),
            "article"      => $this->_getArticleObject($this->article),
            "css_name"     => $this->config("css_name", ""),
            "show_title"   => $this->config("show_title", true),
            "show_author"  => $this->config("show_author", false),
            "show_created" => $this->config("show_created", false)
        ));

    }


    /**
     * Attempts to load requested article
     *
     * @param   mixed      $id              The ID of the requested article (non validated)
     * @return  boolean                     True on success, false otherwise
     */
    private function _loadArticle($id) {

        // Attempt to retrieve article
        if (!is_numeric($id) || !$this->article->load($id)) {

            log_message("info", "[XCMS] Failed loading article '{$id}'.");
            return false;

        }

        // Check author details
        if (!$this->article->getAuthor()->get("id")) {

            log_message("info", "[XCMS] Author could not be loaded for requested article '{$id}'.");
            return false;

        }

        // Check article publish status
        if (!ArticleHelper::isArticlePublished($this->article)) {

            log_message("info", "[XCMS] Requested article '{$id}' has been unpublished.");
            return false;

        }

        return true;

    }


    /**
     * Retrieves the article details required for page generation
     *
     * @param   Object      $article        Reference to the ArticleModel to use for this request
     * @return  Object                      Object containing requested details
     */
    private function _getArticleObject($article) {

        // Prepare article
        $article = $this->article->getObject();
        $article->dt_created = ArticleHelper::getPublished($this->article);
        
        // Execute optional hooks
        XCMS_Hooks::execute("article.parse",
            array(&$article->content)
        );

        return $article;

    }


    /**
     * Retrieves retrieve author name for the given author
     *
     * @param   Object      $author         Reference to the UserModel to use for this request
     * @return  String                      The name to display according to current configuration
     */
    private function _getAuthorName($author) {

        if ($this->config("use_username")) {
            return $author->get("username");
        }

        if ($name = $author->getAttribute("name_display", true, $author->get("username"))) {
            return $name;
        }

        return $author->get("username");

    }


    /**
     * Retrieves the author details required for page generation
     *
     * @param   Object      $article        Reference to the ArticleModel to use for this request
     * @return  Object                      Object containing requested details
     */
    private function _getAuthorObject($article) {

        return (object) [
            "id"   => $article->getAuthor()->get("id"),
            "name" => $this->_getAuthorName($article->getAuthor())
        ];

    }


    /**
     * Sets headers for this response to match shown content
     *
     * @param   Object      $article        Reference to the ArticleModel to use for this request
     */
    private function _setHeaders($article) {

        $page = XCMS_Page::getInstance();
        $page->setTitle($article->get("title"), true);
        $page->setMetaTag("language", $article->get("language"));
        $page->setMetaTag("keywords", $article->getAttribute("meta_keywords", true));
        $page->setMetaTag("description", $article->getAttribute("meta_description", true));

    }

}
?>