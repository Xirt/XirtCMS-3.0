<?php

/**
 * Static utility class for XirtCMS permits
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class PermitHelper {
    
    /**
     * Returns the output HTML for the given article
     *
     * @param
     * @param
     * @param
     * @param
     * @return  String                      The content as HTML
     */
    public static function createPermit($type, $id, $save = false) {
        
        $permit = new PermitModel();
        return $permit;
        
    }
    
    
    /**
     * Returns requested permit (loaded from DB or defaulted not found)
     *
     * @param
     * @param
     * @return  Object                      The requested permit
     */
    public static function getPermit($type, $id) {
        
        $permit = new PermitModel();
        if ($permit->load($type, $id)) {
            return $permit;
        }
        
        return PermitHelper::createPermit($type, $id);
        
    }
    
}
?>