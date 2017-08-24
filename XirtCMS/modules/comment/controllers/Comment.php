<?php

/**
 * Controller for handling comments for a XirtCMS article
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class Comment extends XCMS_Controller {

    /**
     * CONSTRUCTOR
     * Instantiates controller with required configuration, helpers, libraries and models
     */
    public function __construct() {

        parent::__construct();

        // Load models
        $this->load->model("CommentModel", "comment");

        // Validate authorization setting
        $this->setConfig("authorization_required", intval($this->config("authorization_required")));

    }


    /**
     * Creates a new comment from posted data
     */
    public function create() {

        // Check authorization requirements
        if ($this->config("authorization_required") && !XCMS_Authentication::check()) {

            log_message("info", "[XCMS] Unauthorized attempt to post comment");
            XCMS_JSON::creationFailureMessage("Unauthorized attempt to post comment");
            return;

        }

        // Check user authorization
        if (($authorId = XCMS_Authentication::getUserId()) !== null) {

            // Registered comment author
            $authorName = XCMS_Authentication::getUsername(true);
            $authorMail = null;

        } else {

            // Unregistered comment author
            $authorName = $this->input->post("comment_name");
            $authorMail = $this->input->post("comment_email");
            $authorId    = 0;

            // TODO :: Name validation
            if (!trim($authorName)) {

                XCMS_JSON::validationFailureMessage("Please enter your name.");
                return;

            }

            // TODO :: E-mail validation
            if (!trim($authorMail)) {

                XCMS_JSON::validationFailureMessage("Please enter your e-mail address.");
                return;

            }

        }

        // TODO :: Honeypot validation
        if ($this->input->post("comment_website")) {

            XCMS_JSON::validationFailureMessage("Honeypot filled: potential spambot.");
            return;

        }

        // TODO :: Comment validation
        if (trim($this->input->post("comment_content"))) {

            // Set item values and save
            $this->comment->set("author_id",    $authorId);
            $this->comment->set("author_name",  $authorId ? null : $authorName);
            $this->comment->set("author_email", $authorId ? null : $authorMail);
            $this->comment->set("parent_id",    $this->input->post("parent_id"));
            $this->comment->set("article_id",   $this->input->post("article_id"));
            $this->comment->set("content",      $this->input->post("comment_content"));
            $this->comment->validate();
            $this->comment->save();

            // Repond to AJAX requests...
            if ($this->input->is_ajax_request()) {

                XCMS_JSON::creationSuccessMessage();
                return;

            }

        }

        // Repond to AJAX requests...
        if ($this->input->is_ajax_request()) {

            XCMS_JSON::validationFailureMessage("Please enter your response.");
            return;

        }

        // ... or show regular output
        $this->load->view("comment", array(
            "result"  => $this->form_validation->run(),
            "comment" => $this->comment
        ));

    }

}
?>