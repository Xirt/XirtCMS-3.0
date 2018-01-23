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
     * Retrieves all valid permits for the request
     * TODO :: Refacture code
     * 
     * @return  Array                       All valid permits
     */
    public static function getValidPermits() {

        if ($permits = XCMS_Cache::get("permits")) {            
            return $permits;
        }
        
        // Prerequisites
        $CI =& get_instance();

		$currentLevel = UserHelper::getAuthorizationLevel();	
        $currentDateTime = (new DateTime())->format("Y-m-d G:i:s");
        
        $CI->db->where("active", 1)       
            ->where("dt_start <=", $currentDateTime)
            ->where("dt_expiry >=", $currentDateTime)
            ->group_start()
                ->where("access_min IS NULL")
                ->or_where("access_min >=", $currentLevel)
            ->group_end()
            ->group_start()
                ->where("access_max IS NULL")
                ->or_where("access_max <=", $currentLevel)
            ->group_end();
        
        // Hook for customized filtering
        XCMS_Hooks::execute("permits.build_query", array(
            &$CI, &$CI->db
        ));
        
        $permits = array();
        foreach ($CI->db->get(XCMS_Tables::TABLE_PERMITS)->result() as $permit) {
            $permits[$permit->type][$permit->id] = (new PermitModel())->set($permit);
        }

        XCMS_Cache::set("permits", $permits);
        return $permits;

    }

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
        $permits = PermitHelper::getValidPermits();
        if (isset($permits[$type][$id])) {
            return $permits[$type][$id];
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