<?php

/**
 *
 *
 * @author      A.G. Gideonse
 * @version     1.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class xwidget_articles extends XCMS_Widget {

    /**
     * @var string
     * Base URL for viewing articles
     */
    const ARTICLE_URL = "article/view/";


    /**
     * Shows the content
     */
    public function show() {

        // Load helpers
        $this->load->helper("route");
        $this->load->helper("article");
        $this->load->helper("category");

        // Load models
        $this->load->model("ArticlesModel", false);
        require_once "models/ExtArticlesModel.php";

        RouteHelper::init();

        // Populate models
        $articles = array();
        foreach ($this->_retrieveArticles() as $article) {

            // Enrich article
            $data = $article->getObject();
            $data->link = $this->_getLink($data->id);
            $data->date = $this->_getDate($article);
            $articles[] = $data;

        }

        $this->view("table.tpl", array(
            "show_title" => $this->config("show_title", true),
            "css_name"   => $this->config("css_name", ""),
            "show_more"  => $this->config("show_more"),
            "title"      => $this->config("title"),
            "articles"   => $articles
        ));

    }


    /**
     * Retrieves articles using given search attributes
     *
     * @return  Array                       List containing all loaded articles
     */
    private function _retrieveArticles() {

        // Retrieve articles
        $articles = (new ExtArticlesModel())->init()
            ->set("limit",      $this->config("limit"))
            ->set("categories", $this->_retrieveCategories())
            ->set("sorting",    $this->config("sorting") ?? "dt_publish DESC")
            ->load();

        return $articles->toArray();

    }


    /**
     * Retrieves all requested categories (if configured)
     *
     * @return  Array                       List containing all required categories
     */
    private function _retrieveCategories() {

        // Check for valid input
        if (!($id = $this->config("category_id"))) {
            return null;
        }

        // Check category existence
        $tree = CategoryHelper::getCategoryTree();
        if (!($category = $tree->getItemById($id))) {
            return null;
        }

        // Prepare result
        $categories = array($id);
        foreach ($category->toArray() as $category) {
            $categories[] = $category->node_id;
        }

        return $categories;

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



    /**
     * Returns the publish date for the given article
     *
     * @param   Object      $article        The article for which the date is requested
     * @return  String                      The publish date or "--" if unknown (format error)
     */
    private function _getDate($article) {

        $dt = ArticleHelper::getPublished($article);
        if ($format = $this->config("dt_format", XCMS_Config::get("DT_FORMAT"))) {
            return $dt->format($format);
        }

        return "--";

    }

}
?>