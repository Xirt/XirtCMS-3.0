<?php
if (class_exists("XCMS_Config")) {
    
    $config = array(
        "protocol"  => XCMS_Config::get("EMAIL_PROTOCOL", "mail"),
        "smtp_host" => XCMS_Config::get("EMAIL_HOST"),
        "smtp_user" => XCMS_Config::get("EMAIL_USER"),
        "smtp_pass" => XCMS_Config::get("EMAIL_PASS"),
        "smtp_port" => XCMS_Config::get("EMAIL_PORT", "25")
    );

}
?>