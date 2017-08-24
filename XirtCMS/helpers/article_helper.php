<?php

/**
 * Static utility class for XirtCMS articles
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class ArticleHelper {

    /**
     * Attempts to return the summary section from the given html text
     *
     * @param   String      $html           The html of the article to analyze
     * @return  mixed                       The summary section contents on success, null otherwise
     */
    public static function getSummary($html) {

        $doc = new DOMDocument("1.0", "utf-8");
        if (@$doc->loadHTML($html) && ($summary = $doc->getElementById("introduction"))) {
            return strip_tags($summary->nodeValue);
        }

        return null;

    }

    
    /**
     * Returns the DateTime at which the article is scheduled as published
     *
     * @param   Object      $article        The ArticleModel for which the information should be returned
     * @return  mixed                       The publish DateTime if set, otherwise null
     */
    public static function getPublished($article) {

        if (!($date = $article->get("dt_publish"))) {
            return null;
        }
        
        return new DateTime($date);

    }

    
    /**
     * Returns the DateTime at which the article is scheduled as unpublished
     *
     * @param   Object      $article        The ArticleModel for which the information should be returned
     * @return  mixed                       The unpublish DateTime if set, otherwise null
     */
    public static function getUnpublished($article) {

        if (!($date = $article->get("dt_unpublish"))) {
            return null;
        }

        return new DateTime($date);

    }
    
    
    /**
     * Checks whether the article is published
     *
     * @param   Object      $article        Reference to the ArticleModel to use for this request
     * @return  boolean                     True if the article has been published, false otherwise
     */
    public static function isArticlePublished($article) {
        
        // Check publish date
        if (!($dt = ArticleHelper::getPublished($article)) || $dt > new DateTime()) {
            return false;
        }
        
        // Check unpublish date
        if (!($dt = ArticleHelper::getUnpublished($article)) || $dt < new DateTime()) {
            return false;
        }
        
        return true;
        
    }

}
?>