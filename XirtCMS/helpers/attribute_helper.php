<?php

/**
 * Static utility class for XirtCMS attributes
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class AttributeHelper {

    /**
     * Checks whether the given value is valid for the given setting
     *
     * @param   Object      $setting        The settings for which the value needs to be checked
     * @param   mixed       $value          The value to be checked
     * @return  boolean                     True if the valid is allowed for the setting, false otherwise
     */
    public static function isValidInput($setting, $value) {

        if (!in_array($setting->type, array("select"))) {
            return true;
        }

        foreach ($setting->options as $option) {

            if ($option->value == $value) {
                return true;
            }

        }

        return false;

    }

}
?>