<?php

/**
 * XirtCMS Widget for showing detailed author information (for XirtCMS articles)
 *
 * @author      A.G. Gideonse
 * @version     1.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class xwidget_author extends XCMS_Widget {

    /**
     * Shows the content
     */
    public function show() {

        // Widget only valid for module "article"
        if (get_instance()->router->class != "article") {
            return;
        }

        // Set default (if not in DB)
        if ($this->config("show_name") === null) {
            $this->setConfig("show_name", true);
        }

        // Set default (if not in DB)
        if ($this->config("use_username") === null) {
            $this->setConfig("use_username", true);
        }

        // Set default (if not in DB)
        if ($this->config("use_gravatar") === null) {
            $this->setConfig("use_gravatar", true);
        }

        // Display template
        $this->view("default.tpl", array(
            "author" => $this->_getAuthor(get_instance()->article->getAuthor()),
            "config" => $this->getConfig()
        ));

    }


    /**
     * Retrieves author information for display purposes for the given author
     *
     * @param   Object      $author         Reference to the UserModel to use for this request
     * @return  Object                      Object containing the author information
     */
    private function _getAuthor($author) {

        return (object) [
            "name"         => $this->_getAuthorName($author),
            "introduction" => $this->_getIntroduction($author),
            "avatar"       => $this->_getAvatarLocation($author)
        ];

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

        return $author->getAttribute("name_display", true, $author->get("username"));

    }


    /**
     * Attempts to retrieve introduction text for the given author
     *
     * @param   Object      $author         Reference to the UserModel to use for this request
     * @return  String                      The introduction text or an empty String on failure
     */
    private function _getIntroduction($author) {
        return $author->getAttribute("short_description", true, "");
    }


    /**
     * Attempts to retrieve the avatar location for the given author
     *
     * @param   Object      $author         Reference to the UserModel to use for this request
     * @return  String                      The avatar location or an empty String on failure
     */
    private function _getAvatarLocation($author) {

        if ($this->config("use_gravatar")) {
            return "https://www.gravatar.com/avatar/" . md5(strtolower(trim($author->get("email")))) . "?s=150";
        }

        return "";

    }

}
?>