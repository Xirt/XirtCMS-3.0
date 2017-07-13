<?php

/**
 * Article hook to add fields capturing publishing period
 */
XCMS_Hooks::add("article.get_fields", function(&$fields) {

    $fields[] = (object) [
        "name"  => "publish_date",
        "label" => "Publish date",
        "type"  => "date",
        "value" => date("d/m/Y")
    ];

    $fields[] = (object) [
        "name"  => "unpublish_date",
        "label" => "Unpublish date",
        "type"  => "date",
        "value" => "31/12/2099"
    ];

}, 4);

?>