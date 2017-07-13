<?php

/**
 * User hook to add fields capturing detailed user name
 */
XCMS_Hooks::add("user.get_fields", function(&$fields) {

    $fields[] = (object) [
        "name"  => "name_display",
        "label" => "Display name",
        "type"  => "text",
        "value" => ""
    ];

    $fields[] = (object) [
        "name"  => "name_first",
        "label" => "First name",
        "type"  => "text",
        "value" => ""
    ];

    $fields[] = (object) [
        "name"  => "name_family",
        "label" => "Family name",
        "type"  => "text",
        "value" => ""
    ];

}, 3);

?>