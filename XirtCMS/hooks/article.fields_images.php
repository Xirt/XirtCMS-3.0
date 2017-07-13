<?php

/**
 * Article hook to add fields capturing image information
 */
XCMS_Hooks::add("article.get_fields", function(&$fields) {

    $fields[] = (object) [
        "name"  => "img_thumb",
        "label" => "Thumbnail Image",
        "type"  => "text",
        "value" => ""
    ];

    $fields[] = (object) [
        "name"  => "img_header",
        "label" => "Header Image",
        "type"  => "text",
        "value" => ""
    ];

});

?>