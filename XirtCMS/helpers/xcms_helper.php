<?php

/**
 * Static utility class for XirtCMS JSON communication
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class XCMS_JSON {

    /**
     * Replaces the output buffer with a given title / message in JSON format
     *
     * @param   String      $title          The title to be used for the JSON message
     * @param   String      $message        The meesage to be used for the JSON message
     * @param   String      $type           The type of the message
     */
    public static function customContentMessage($title, $message, $type = "info") {

        $CI =& get_instance();

        $CI->output->set_content_type("application/json");
        $CI->output->set_output(json_encode((object)array(
            "type"    => $type,
            "title"   => $title,
            "message" => $message
        )));

    }
    
    
    /**
     * Replaces the output buffer with a default XirtCMS "Loading failure"-message in JSON format
     *
     * @param   String      $message        The meesage to be used for the JSON message
     */
    public static function loadingFailureMessage($message = null) {
        
        self::customContentMessage(
            "Loading failure",
            ($message ? $message : null) ?? "The targetted item could not be found.",
            "error"
        );
        
    }


    /**
     * Replaces the output buffer with a default XirtCMS "Creation Success"-message in JSON format
     *
     * @param   String      $message        A customized message replacing the default XirtCMS message
     */
    public static function creationSuccessMessage($message = null) {

        self::customContentMessage(
            "Creation succesful",
            ($message ? $message : null) ?? "The new item has been created successfully.",
            "info"
        );

    }


    /**
     * Replaces the output buffer with a default XirtCMS "Creation Failure"-message in JSON format
     *
     * @param   String      $message        A customized message replacing the default XirtCMS message
     */
    public static function creationFailureMessage($message = null) {

        self::customContentMessage(
            "Creation failure",
            ($message ? $message : null) ?? "The new item could not be created.",
            "error"
        );

    }


    /**
     * Replaces the output buffer with a default XirtCMS "Modification Success"-message in JSON format
     *
     * @param   String      $message        A customized message replacing the default XirtCMS message
     */
    public static function modificationSuccessMessage($message = null) {

        self::customContentMessage(
            "Modification succesful",
            ($message ? $message : null) ?? "The modifications have been saved succesfully.",
            "info"
        );

    }


    /**
     * Replaces the output buffer with a default XirtCMS "Modification Failure"-message in JSON format
     *
     * @param   String      $message        A customized message replacing the default XirtCMS message
     */
    public static function modificationFailureMessage($message = null) {

        self::customContentMessage(
            "Modification failure",
            ($message ? $message : null) ?? "The modifications could not be succesfully saved. Please try again later.",
            "error"
        );

    }


    /**
     * Replaces the output buffer with a default XirtCMS "Validation Failure"-message in JSON format
     *
     * @param   String      $message        A customized message replacing the default XirtCMS message
     */
    public static function validationFailureMessage($message = null) {
	
        self::customContentMessage(
            "Validation failure",
            ($message ? $message : null) ?? "The supplied data could not be processed (validation failed).",
            "error"
        );

    }

}
?>