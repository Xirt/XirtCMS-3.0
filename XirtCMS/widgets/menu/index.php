<?php

/**
 * XirtCMS Widget for showing a menu
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class xwidget_menu extends XCMS_Widget {

    /**
     * Shows the content
     */
    public function show() {

        // Load helpers
        $this->load->helper("url");
        $this->load->helper("menu");

        // Retrieve relevant menu branch
        $menu = MenuHelper::getMenuTree($this->config("menu_id"));
        if ($this->config("parent_id") && !($menu = $menu->getItemById($this->config("parent_id")))) {
            $menu = array();
        }

        // Show template
        switch ($this->config("show_type")) {

            case 1:
                $this->displayDivList($menu->toArray());
                break;

            case 2:
                $this->displayTreeList($menu);
                break;

            default:
                $this->displayPlainLinks($menu->toArray());
                break;

        }

    }


    /**
     * Outputs given menu with links only
     *
     * @param   array       $menu           The menu as a list (Array)
     */
    private function displayPlainLinks($menu) {

        $menuOutput = array();
        $this->_populateListItems($menuOutput, $menu);
        $this->_populateListSeperators($menuOutput);

        $this->view("tpl_plain.tpl", array(
            "config" => $this->getConfig(),
            "menu"   => $menuOutput
        ));

    }


    /**
     * Outputs given menu with links in DIV-containers
     *
     * @param   array       $menu           The menu as a list (Array)
     */
    private function displayDivList($menu) {

        $menuOutput = array();
        $this->_populateListItems($menuOutput, $menu);
        $this->_populateListSeperators($menuOutput);

        $this->view("tpl_list_div.tpl", array(
            "config" => $this->getConfig(),
            "menu"   => $menuOutput
        ));

    }

    /**
     * Outputs given menu as a tree list (using recursion)
     *
     * @param    Object     $menu           The menu as a tree
     */
    private function displayTreeList($menu) {

        $menuOutput = array();
        $this->_populateTreeListItems($menuOutput, $menu);
        $this->_populateListSeperators($menuOutput);

        $this->view("tpl_list_tree.tpl", array(
            "config" => $this->getConfig(),
            "menu"   => $menuOutput
        ));

    }


    /**
     * Populates given menuOutput with entries from given list of nodes
     *
     * @param   array       $menuOutput     The array with menu items which should be enriched
     * @param   array       $nodes          List of nodes used to populate given menuOutput
     */
    private function _populateListItems(&$menuOutput, $nodes) {

        // Quick reference
        $config = $this->getConfigArray();

        foreach ($nodes as $node) {

            // Limit depth of menu
            if ($config["level_end"] && $node->level > $config["level_end"] - 1) {
                continue;
            }

            // Check for requested start level
            if ($node->level < $config["level_start"] - 1) {
                continue;
            }

            $menuOutput[] = $this->_createLink($node);

        }

    }


    /**
     * Populates given menuOutput with entries from given node (recursive)
     *
     * @param   array       $menuOutput     The array with menu items which should be enriched
     * @param   Object      $node           The node used to populate given menuOutput
     */
    private function _populateTreeListItems(&$menuOutput, $node) {

        // Quick reference
        $config = $this->getConfigArray();

        // Limit depth of menu
        if (!$node || $config["level_end"] && $node->level >= $config["level_end"] - 1) {
            return;
        }

        foreach ($node->children as $child) {

            // Check for requested start level
            if ($child->level >= $config["level_start"] - 1) {

                $menuOutput[] = $this->_createLink($child);

                if (count($child->children)) {
                    $this->_populateTreeListItems(end($menuOutput)->children, $child);
                }

            } else {

                // Otherwise search until search level is found
                $this->_populateTreeListItems($menuOutput, $child);

            }
        }

    }


    /**
     * Adds seperators to the given output array as per configuration
     *
     * @param   array       $menuOutput         The array with menu items which should be enriched
     */
    private function _populateListSeperators(&$menuOutput) {

        if (!$this->config("separator_style")) {
            return;
        }

        $enrichedOutput = array();
        foreach ($menuOutput as $menuEntry) {

            $enrichedOutput[] = $menuEntry;

            // Iterate over children
            if (count($menuEntry->children)) {
                $this->_populateListSeperators($menuEntry->children);
            }

            // Style 1 :: Seperators between items
            if ($this->config("separator_style") && $menuEntry != end($menuOutput)) {
                $enrichedOutput[] = $this->_createSeperator();
            }

        }

        // Style 2 :: Seperators at start / end of items
        if ($this->config("separator_style") == 2) {

            array_unshift($enrichedOutput, $this->_createSeperator());
            array_push($enrichedOutput, $this->_createSeperator());

        }

        $menuOutput = $enrichedOutput;

    }


    /**
     * Returns an object representation of a menu item based on given menu node
     *
     * @param   Object      $node           The menu node used to create the resulting object
     * @return  Object                      The created object representing a menu item type
     */
    private function _createLink($node) {

        $classes = array(
            ($node->active ? "active" : "inactive"),
            "menu-item-" . $node->node_id,
            "menu-item"
        );

        return (object) [
            "type"     => "item",
            "active"   => $node->active,
            "node_id"  => $node->node_id,
            "classes"  => implode(" ", $classes),
            "link"     => anchor($node->target, $node->name, array("title" => $node->name, "class" => implode(" ", $classes))),
            "children" => null
        ];

    }


    /**
     * Returns an object representation of a seperator
     *
     * @return  Object                      The created object representing a seperator type
     */
    private function _createSeperator() {

        return (object)[
            "type" => "seperator"
        ];

    }

}
?>