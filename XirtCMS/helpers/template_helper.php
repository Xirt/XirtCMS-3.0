<?php

/**
 * Static utility class for XirtCMS templates
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class TemplateHelper {

    /**
     * Resets the published status of all templates
     */
    public static function resetActiveTemplate() {
    
       $CI =& get_instance();
       $CI->db->update(XCMS_Tables::TABLE_TEMPLATES, array("published" => 0));
    
    }
    
}
?>