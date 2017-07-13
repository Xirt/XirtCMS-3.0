<?php

/**
 * Article hook to add fields capturing SEO information
 */
XCMS_Hooks::add("article.get_fields", function(&$fields) {

    $fields[] = (object) [
        "name"  => "meta_keywords",
        "label" => "META Keywords",
        "type"  => "textarea",
        "value" => ""
    ];

    $fields[] = (object) [
        "name"  => "meta_description",
        "label" => "META Description",
        "type"  => "textarea",
        "value" => ""
    ];

}, 5);

?>