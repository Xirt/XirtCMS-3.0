<?php

/**
 * Class for managing XirtCMS Cache
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2018
 * @package     XirtCMS
 */
class XCMS_Cache {

    /**
     * @var Object
     * Internal cache reference
     */
    protected static $_data = array();


    /**
     * Attempts to retrieve an item from the cache
     *
     * @param   string      $id             The ID of the cached item
     * @return  mixed                       The cached item or null if not available
     */
    public static function get(String $id) {

        if (array_key_exists($id, self::$_data)) {
            return self::$_data[$id];
        }

        return null;

    }


    /**
     * Attempts to save an item in the cache
     *
     * @param   string      $id             The ID of the cached item
     * @param   mixed       $data           The data to be cached
     * @param   bool        $override       Toggles overriding of existing cached data
     * @return  bool                        True on success, false otherwise
     */
    public static function set(String $id, $data, $override = true) {

        if (!array_key_exists($id, self::$_data) || $override) {
            return (self::$_data[$id] = $data);
        }

        return false;

    }

}
?>