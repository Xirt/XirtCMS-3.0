<?php

// TODO :: Rewrite after moving helper models
require_once(APPPATH . "/helpers/XCMS_Node.php");
require_once(APPPATH . "/helpers/XCMS_Tree.php");

/**
 * XirtCMS Widget for showing article comments section
 *
 * @author      A.G. Gideonse
 * @version     1.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class xwidget_comments extends XCMS_Widget {

    /**
     * CONSTRUCTOR
     * Initializes widget with given configuration
     *
     * @param   Array       $conf           Array containing configuration for this widget
     */
    public function __construct($conf) {

        parent::__construct($conf);

        // Validate given Gravatar size
        if (!is_int($this->config("gravatar_size"))) {
            $this->setConfig("gravatar_size", 150);
        }

        // Validate authorization setting
        $this->setConfig("authorization_required", intval($this->config("authorization_required")));

        // Validate honeypot setting
        $value = $this->config("insert_honeypot");
        $this->setConfig("insert_honeypot", ($value === null) ? 1 : intval($value));

    }

    
    /**
     * Handles any normal requests
     */
    public function show() {

        // Only show for articles
        if (get_instance()->router->class != "article") {
            return;
        }

        if (($comments = $this->_getComments()) === null) {

            $this->view("no_comments", array(
                "config" => $this->getConfig()
            ));

        }

        $this->view("default.tpl", array(
            "authenticated" => XCMS_Authentication::check(),
            "article_id"    => $this->_getArticleId(),
            "config"        => $this->getConfig(),
            "comments"      => $comments,
        ));

    }


    /**
     * Returns the ID of the currently shown article
     *
     * @return  mixed                       The article ID on success, null otherwise
     */
    private function _getArticleId() {
        global $URI;

        if (!($id = intval($URI->rsegment(3)))) {
            return null;
        }

        return $id;

    }


    /**
     * Returns the comments for the currently shown article
     *
     * @return  mixed                       Array with comments on success, null otherwise
     */
    private function _getComments() {

        // Check for article ID
        if (!($id = $this->_getArticleId())) {
            return null;
        }

        // Load comment information
        $this->db->select(XCMS_Tables::TABLE_ARTICLES_COMMENTS . ".*, username, email");
        $this->db->join(XCMS_Tables::TABLE_USERS, XCMS_Tables::TABLE_USERS . ".id = author_id", "left");
        $this->db->order_by("dt_created ASC, parent_id ASC");
        $query = $this->db->get_where(XCMS_Tables::TABLE_ARTICLES_COMMENTS, array(
            "article_id" => $id
        ));

        $comments = new XCMS_Tree();
        foreach ($query->result() as $comment) {

            // Set datetime format
            $comment->dt_created = (new DateTime($comment->dt_created))->format($this->config("dt_format"));

            // Optional: Handle non-registered users
            if (!$comment->author_id || !$comment->username) {

                $comment->author_id = -1;
                $comment->username  = $comment->author_name;
                $comment->email     = $comment->author_email;

            }

            // Optional: Set Gravatar
            if ($this->config("use_gravatar")) {

                $comment->avatar = sprintf("https://www.gravatar.com/avatar/%s?d=%s&s=%d",
                    md5(strtolower(trim($comment->email))),
                    urlencode($this->config("gravatar_default")),
                    $this->config("gravatar_size")
                );

            }

            // Add comment
            $comments->add(new XCMS_Node($comment));

        }

        return $comments->toArray();

    }

}
?>