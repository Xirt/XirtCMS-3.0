<?php

/**
 * Class for simplifying standard XirtCMS ArticleModel
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 * @internal    Internal helper class for the module "Articles"
 */
class SimplifiedArticleModel {

    /**
     * Reference to the article ID for this instance
     * @var string
     */
    private $_id;


    /**
     * Reference to the ArticleModel for this instance
     * @var string
     */
    private $_article;


    /*
     * CONSTRUCTOR
     * Instantiates controller with given article
     *
     * @param   Object                      The ArticleModel for which this SimplifiedArticle is created (mandatory)
     */
    public function __construct(ArticleModel $article = null) {

        // Exception for CI loading
        if (is_null($article)) {
            return;
        }

        $this->_article = $article;
        $this->_id = $article->get("id");

    }


    /**
     * Returns current article details as object for the given output format
     *
     * @return  Object                      The created Object
     */
    public function toObject() {

        return (object) [
            "title" => $this->_getTitle(),
            "intro" => $this->_getSummary(),
            "link"  => $this->_getLink()
        ];

    }


    /**
     * Retrieves the title for the current article
     *
     * @return  String                      The introduction part of the content
     */
    private function _getTitle() {
        return htmlspecialchars($this->_article->get("title"));
    }


    /**
     * Retrieves the introduction for the current article
     *
     * @return  String                      The introduction part of the content
     */
    private function _getSummary() {

        // Retrieve introduction
        $article = $this->_article;
        if (!($summary = strip_tags(ArticleHelper::getSummary($article)))) {
            $summary = strip_tags(ArticleHelper::getContent($article));
        }

        // Reduce introduction size (optional)
        if (($max = get_instance()->config("max_length", 0)) > 0 && strlen($summary) > $max) {
            return rtrim(substr($summary, 0, strpos($summary, ' ', $max))) . "&#8230;";
        }

        return $summary;

    }


    /**
     * Retrieves the link for the given article
     *
     * @return  String                      The link towards the given article
     */
    private function _getLink() {

        // Retrieve parameter (module config)
        if (!($config = abs(get_instance()->config("module_config"))) || $config < 1) {
            $config = null;
        }

        return RouteHelper::getByTarget(Articles::ARTICLE_URL . $this->_id, $config)->public_url;

    }

}
?>