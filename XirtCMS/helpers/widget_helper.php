<?php

/**
 * Instantiated utility class for XirtCMS widgets
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class WidgetHelper {

    /**
     * @var null|Object
     * Internal reference to Code Ignitor instance
     */
    private static $_CI = null;


    /**
     * @var array
     * Internal list of known routes
     */
    private static $_list = array();


    /**
     * CONSTRUCTOR
     * Initializes the instance with all known widgets
     *
     * @param   boolean     $published      Toggle between all / published widgets
     * @param   boolean     $current        Toggle between all / request specific widgets
     */
    public static function init($published = true, $current = true) {

        if (!self::$_CI) {

            self::$_CI =& get_instance();
            self::_load($published, $current);

        }

    }

    
    /**
     * Returns a list with all widgets for a given position on the current page
     *
     * @param   String      $position       The position for which to retrieve the widgets
     * @return  array                       A list with all widgets for the given position
     */
    public static function retrieve($position) {

        $defaults = array();
        $candidates = array();
        $module_config = get_instance()->router->module_config;

        foreach (self::$_list as $widget) {

            // Exclude irrelevant items (on position)
            if ($widget->position != $position) {
                continue;
            }

            // Exclude irrelevant items (on module)
            if ($widget->page_module && $widget->page_module != $module_config) {
                continue;
            }

            // Keep track of defaults
            if ($widget->page_default) {

                $defaults[] = $widget;
                continue;

            }

            $candidates[] = $widget;
            continue;

        }

        return count($candidates) ? $candidates : $defaults;

    }

    
    /**
     * Returns a list with all available positions in templates
     *
     * @return  array                       A list with all valid positions
     */
    public static function getValidPositions() {

        $positions = array();
        foreach (self::$_CI->db->get(XCMS_Tables::TABLE_TEMPLATES_POSITIONS)->result() as $row) {
            $positions[] = $row->position;
        }

        return $positions;

    }


    /**
     * Loads all known routes into the internal list
     * 
     * @param   boolean     $published      Toggle between all / published widgets
     * @param   boolean     $current        Toggle between all / request specific widgets
     */
    private static function _load($published, $current) {

        self::$_CI->db->select(XCMS_Tables::TABLE_WIDGETS . ".*");
        self::$_CI->db->join(XCMS_Tables::TABLE_WIDGETS_PAGES, "id = widget_id", "left");

        if ($published) {
            self::$_CI->db->where("published", 1);
        }

        if ($current) {

            // Make filter page specific
            self::$_CI->db->group_start();
            self::$_CI->db->or_where("page_all", 1);

            // Make filter page specific
            if (count($nodes = self::$_CI->router->getActiveNodes())) {
                self::$_CI->db->or_where_in("item_id", $nodes);
            }

            self::$_CI->db->group_end();

        }

        // Loads the complete list of routes
        $query = self::$_CI->db->get(XCMS_Tables::TABLE_WIDGETS);
        foreach ($query->result() as $row) {

            self::$_list[$row->id] = $row;
            self::$_list[$row->id]->settings = array();

        }

        // Enrich with settings
        self::$_CI->db->where_in("widget_id", array_keys(self::$_list));
        $query = self::$_CI->db->get(XCMS_Tables::TABLE_WIDGETS_SETTINGS);
        foreach ($query->result() as $row) {
            self::$_list[$row->widget_id]->settings[$row->name] = $row->value;
        }

    }

}
?>