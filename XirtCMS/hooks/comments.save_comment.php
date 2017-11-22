<?php

/**
 * Comments hook to send an e-mail after saving a new comment
 */
XCMS_Hooks::add("comments.save_comment", function(&$comment, &$previous) {

    if (!$previous) {
        return;
    }
    
    $CI = get_instance();
    $CI->load->helper("user");
    $CI->load->helper("route");
    $CI->load->library("email");
    
    // Determine recipient of notification
    $recipient = UserHelper::getUser($previous->get("author_id"));
    $recipient = (object)[
        "name"  => $recipient ? $recipient->get("username") : $previous->get("author_name"),
        "email" => $recipient ? $recipient->get("email")    : $previous->get("author_email")
    ];
        
    // Determine author of comment
    $author = UserHelper::getUser($comment->get("author_id"));
    $author = (object)[
        "name"  => $author ? $author->get("username") : $comment->get("author_name"),
        "email" => $author ? $author->get("email")    : $comment->get("author_email")
    ];
    
    // Retrieve comment location
    RouteHelper::init();
    if ($route = RouteHelper::getByTarget("article/view/" . $comment->get("article_id"), null, false)) {
        $commentURL = base_url() . $route->public_url . "#comment-" . $comment->get("parent_id");
    };
    
    if ($recipient->email && $commentURL) {

        $email = $CI->email->initialize(
            array("mailtype" => "html"
        ));
        
        // Set e-mail headers
        $email->from(XCMS_Config::get("EMAIL_SENDER_EMAIL"), XCMS_Config::get("EMAIL_SENDER_NAME"))
            ->subject(htmlspecialchars($author->name) . " responded to your comment")
            ->to($recipient->email);

        // Set e-mail content and send
        $email->message($CI->load->view('emails/comment.notification.tpl', array(
            "recipientName" => htmlspecialchars($recipient->name),
            "authorName"    => htmlspecialchars($author->name),
            "commentURL"    => $commentURL,
            "target"        => base_url()
        ), true))->send();
   
    }

});

?>