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

        // Load Libraries
        $this->load->library("form_validation");

        // Load models
        $this->load->model("CommentModel", false);

        // Validate authorization setting
        $this->setConfig("authorization_required", intval($this->config("authorization_required")));

    }


    /**
     * Validates and saves new comment based on posted data
     */
    public function create() {

        try {

            $this->_validate();

            // Actual creation
            $current = $this->_createComment();
            $current->validate();
            $current->save();

            // Response creation (hooks)
            $previous = $this->_getComment($current->get("parent_id"));
            XCMS_Hooks::execute("comments.save_comment", array(
                &$current, &$previous
            ));

            // Response creation (GUI)
            $this->_createReponse(
                "Creation succesful",
                "The new item has been created successfully."
            );

        } catch (Exception $e) {

            $this->_createReponse(
                "Validation failure",
                $e->getMessage(),
                false
            );

        }

    }


    /**
     * Creates a new comment from posted data
     */
    private function _createComment() {

        if (XCMS_Authentication::check()) {

            $comment = (new CommentModel())
                ->set("author_id",    XCMS_Authentication::getUserId())
                ->set("author_name",  null)
                ->set("author_email", null);

        } else {

            $comment = (new CommentModel())
                ->set("author_id",    0)
                ->set("author_name",  $this->input->post("comment_name"))
                ->set("author_email", $this->input->post("comment_email"));

        }

        return $comment
            ->set("article_id", $this->input->post("article_id"))
            ->set("parent_id",  $this->input->post("parent_id"))
            ->set("content",    $this->input->post("comment_content"));

    }


    /**
     * Attempts to retrieve an existing comment
     *
     * @param   int         $id             The ID of the comment to retrieve
     * @return  mixed                       The CommentModel on success, null on failure
     */
    private function _getComment($id) {

        $comment = new CommentModel();
        return $comment->load($id) ? $comment : null;

    }


    /**
     * Creates output for a given (creation) request
     *
     * @param   String      $title          The title (header) of the resulting output
     * @param   String      $title          The message (body) of the resulting output
     * @param   boolean     $type           Toggles the type (success vs. failure) of the output
     */
    private function _createReponse($title, $message, $success = true) {

        // Show AJAX output...
        if ($this->input->is_ajax_request()) {

            $success ?  XCMS_JSON::creationSuccessMessage($message) : XCMS_JSON::validationFailureMessage($message);
            return;

        }

        // ... or show regular output
        $this->load->view("default.tpl", array(
            "css_name" => $this->config("css_name", ""),
            "title"    => $title,
            "message"  => $message
        ));

    }


    /**
     * Validates given input (posted data)
     *
     * @throws  Object                      ValidationException thrown in case of a validation failure
     * @return  boolean                     Always true
     */
    private function _validate() {

        // Check authorization requirements
        if ($this->config("authorization_required") && !XCMS_Authentication::check()) {

            log_message("info", "[XCMS] Unauthorized attempt to post comment");
            throw new ValidationException("Unauthorized attempt to post comment");

        }

        // Regular validation
        $this->_setValidationRules();
        if (!$this->form_validation->run()) {

            $errors = $this->form_validation->error_array();
            throw new ValidationException(array_shift($errors));

        }

        return true;

    }


    /**
     * Sets rules for _validate()-logic
     */
    private function _setValidationRules() {

        $config = array(

            array(

                "field" => "comment_content",
                "label" => "",
                "rules" => "trim|required",
                "errors" => array(
                    "required" => "Please enter your response."
                )

            ),

            array(

                "field" => "comment_website",
                "label" => "",
                "rules" => "max_length[0]",
                "errors" => array(
                    "required" => "Honeypot filled: potential spambot."
                )

            )

        );

        if (!XCMS_Authentication::check()) {

            $config[] = array(

                "field" => "comment_name",
                "label" => "",
                "rules" => "trim|required|max_length[32]",
                "errors" => array(
                    "required"   => "Please enter your name.",
                    "max_length" => "Name is exceeding maximum length (32 chars)."
                )

            );

            $config[] = array(

                "field" => "comment_email",
                "label" => "",
                "rules" => "trim|required|valid_email",
                "errors" => array(
                    "required"    => "Please enter your e-mail address.",
                    "valid_email" => "Please enter a valid e-mail address."
                )

            );

        }

        $this->form_validation->set_rules($config);

    }

}
?>