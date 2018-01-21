<?php

/**
 * Static utility class for XirtCMS permits
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2018
 * @package     XirtCMS
 */
class PermitHelper {

    /**
     * Returns a new default permit for given parameters
     *
     * @param   String      type            The type for which to retrieve the permit
     * @param   int         id              The ID for which to retrieve the permit
     * @return  String                      The requested Permit
     */
    public static function createPermit(String $type, int $id) {
        
        // Prerequisites
        $CI =& get_instance();
        $CI->load->model("PermitModel", false);
        
        // Create permit
        return (new PermitModel())
            ->set("type", $type)
            ->set("id", $id);

    }

    /**
     * Returns requested permit (loaded from DB or defaulted not found)
     * TODO :: Build in caching of permits to improve performance in case of many requests (likely scenario)
     *
     * @param   String      type            The type for which to retrieve the permit
     * @param   int         id              The ID for which to retrieve the permit
     * @return  Object                      The requested permit
     */
    public static function getPermit(String $type, int $id) {
        
        // Prerequisites
        $CI =& get_instance();
        $CI->load->model("PermitModel", false);

        // Retrieve permit
        $permit = new PermitModel();
        if ($permit->load($type, $id)) {
            return $permit;
        }

        return PermitHelper::createPermit($type, $id);

    }
    
    /**
     * Checks whether a valid permit exists for the given parameters
     *
     * @param   String      type            The type for which to check the permit
     * @param   int         id              The ID for which to check the permit
     * @return  bool                        True is valid permit exists, false otherwise
     */
    public static function validPermitExists(String $type, int $id) {
        return PermitHelper::getPermit($type, $id)->isValid();
    }
}
?>