<?php

/**
 * User hook to add field capturing small information text
 */
XCMS_Hooks::add("user.get_fields", function(&$fields) {

    $fields[] = (object) [
        "name"  => "short_description",
        "label" => "Short introduction",
        "type"  => "textarea",
        "value" => ""
    ];

});

?>