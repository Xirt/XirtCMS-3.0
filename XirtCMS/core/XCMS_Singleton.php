<?php

/**
 * XirtCMS core class for Singleton classes
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class XCMS_Singleton {

    /**
     * @var Object|null
     * Internal reference to only existing instance of this class
     */
    private static $_instance = NULL;


    /**
     * CONSTRUCTOR
     * Private constructor for singleton creation
     */
    private function __construct() {

        log_message("error", "[XCMS] Invalid initialization attempt of Singleton.");

    }


    /**
     * Returns the only existing instance of this class
     *
     * @return  Object                      The only existing instance of this class
     */
    public static function getInstance() {

        if (self::$_instance == NULL) {

            $className = get_called_class();
            self::$_instance = new $className();

        }

        return self::$_instance;

    }


    /**
     * Empty __clone to prevent duplicate instances
     */
    private function __clone() {
    }

}
?>