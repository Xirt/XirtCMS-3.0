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
     * @return  mixed                       The unpublish DateTime if set, otherwise the creation DateTime
     */
    public static function getPublished($article) {

        if (!($date = $article->getAttribute("publish_date", true))) {
            return new DateTime($article->get("dt_created"));
        }

        return DateTime::createFromFormat("d/m/Y", $date);

    }

    
    /**
     * Returns the DateTime at which the article is scheduled as unpublished
     *
     * @param   Object      $article        The ArticleModel for which the information should be returned
     * @return  mixed                       The unpublish DateTime if set, otherwise null
     */
    public static function getUnpublished($article) {

        if (!($date = $article->getAttribute("unpublish_date", true))) {
            return null;
        }

        return DateTime::createFromFormat("d/m/Y", $date);

    }

}
?>