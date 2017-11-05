<?php

/**
 * Controller for showing a viewing / modifying a XirtCMS menuitem (back end)
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class MenuItem extends XCMS_Controller {

    /**
     * CONSTRUCTOR
     * Instantiates controller with required helpers, libraries and models
     */
    public function __construct() {

        parent::__construct(75, true);

        // Only allow AJAX requests
        if (!$this->input->is_ajax_request()) {
            return show_404();
        }

        // Load helpers
        $this->load->helper("menu");
        $this->load->helper("route");

        // Load libraries
        $this->load->library("form_validation");

        // Loader models
        $this->load->model("MenuitemModel", "menuitem");
        $this->load->model("MenuModel", "menu");
        $this->load->model("RouteModel", false);

    }


    /**
     * "View menuitem"-Page for this controller.
     *
     * @param   int         $id             The name of the requested menuitem
     */
    public function view($id = 0) {

        // Validate given ID
        if (!is_numeric($id) || !$this->menuitem->load($id, true)) {
            return;
        }

        // Prepare data...
        $data = (object) [
            "id"         => $this->menuitem->get("id"),
            "name"       => $this->menuitem->get("name"),
            "type"       => $this->menuitem->get("type"),
            "module"     => $this->menuitem->get("module_config"),
            "parent_id"  => $this->menuitem->get("parent_id"),
            "menu_id"    => $this->menuitem->get("menu_id"),
            "relations"  => $this->menuitem->get("relations"),
            "anchor"     => $this->menuitem->get("anchor"),
            "uri"        => $this->menuitem->get("uri"),
            "extension"  => $this->menuitem->get("uri"),
            "public_url" => $this->menuitem->get("public_url"),
            "target_url" => $this->menuitem->get("target_url")
        ];

        // ...and output it as JSON
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($data));

    }


    /**
     * "Create menuitem"-functionality for this controller
     */
    public function create() {

        // Validate provided input
        if (!$this->form_validation->run()) {

            XCMS_JSON::validationFailureMessage();
            return;

        }

        // Validate given menu ID
        $menuId = $this->input->post("menu_id");
        if (!is_numeric($menuId) || !$this->menu->load($menuId)) {

            XCMS_JSON::validationFailureMessage();
            return;

        }

        // Validate new parent
        $tree = MenuHelper::getMenuTree($menuId, false);
        if (!$parent = $tree->getItemByID($this->input->post("menuitem_parent_id"))) {

            XCMS_JSON::validationFailureMessage("The selected parent item could not be found.");
            return;

        }

        try {

            // Set item values, validate and save
            $this->menuitem->set("parent_id", $this->input->post("menuitem_parent_id"));
            $this->menuitem->set("name",      $this->input->post("menuitem_name"));
            $this->menuitem->set("menu_id",   $this->input->post("menu_id"));
            $this->menuitem->set("ordering",  $parent->count() + 1);
            $this->menuitem->set("level",     $parent->level + 1);
            $this->menuitem->validate();
            $this->menuitem->save();

            // Inform user
            XCMS_JSON::creationSuccessMessage();

        } catch (Exception $e) {

            XCMS_JSON::validationFailureMessage($e->getMessage());

        }

    }


    /**
     * "Modify settings"-functionality for this controller
     */
    public function modify() {

        // Validate given item ID
        $id = $this->input->post("id");
        if (!is_numeric($id) || !$this->menuitem->load($id)) {

            XCMS_JSON::validationFailureMessage();
            return;

        }

        // Validate provided input
        if (!$this->form_validation->run()) {

            XCMS_JSON::validationFailureMessage();
            return;

        }

        try {

            // Check parent ID changes
            if ($this->input->post("menuitem_parent_id") != $this->menuitem->get("parent_id")) {

                // Validate new parent existence
                $tree = MenuHelper::getMenuTree($this->menuitem->get("menu_id"), false);
                if (!$parent = $tree->getItemByID($this->input->post("menuitem_parent_id"))) {

                    XCMS_JSON::validationFailureMessage("The selected parent item could not be found.");
                    return;

                }

                // Attempt to update parent item
                if (!$this->_modifyParent($tree->getItemById($id), $parent)) {

                    XCMS_JSON::modificationFailureMessage("The item cannot be a descendant of itself.");
                    return;

                }

            }

            // Set updates & save
            $this->menuitem->set("name", $this->input->post("menuitem_name"));
            $this->menuitem->validate();
            $this->menuitem->save();

            // Inform user
            XCMS_JSON::modificationSuccessMessage();

        } catch (Exception $e) {

            XCMS_JSON::validationFailureMessage($e->getMessage());

        }

    }


    /**
     * "Modify URL settings"-functionality for this controller
     */
    public function modify_settings() {

        // Validate given item ID
        $id = $this->input->post("id");
        if (!is_numeric($id) || !$this->menuitem->load($id)) {

            XCMS_JSON::validationFailureMessage();
            return;

        }

        try {
            
            // Save details
            switch ($this->input->post("menuitem_type")) {

                case "internal":

                    // Set item details ...
                    $this->menuitem->set("uri",  $this->input->post("menuitem_extension"));
                    $this->menuitem->set("type", "internal");
                    $this->menuitem->save();

                    // ... and update routing
                    $this->_updateRelation(
                        $this->input->post("menuitem_public_url"),
                        $this->input->post("menuitem_target_url"),
                        $this->input->post("menuitem_module")
                    );

                break;

                case "anchor":

                    // Save item details
                    $this->menuitem->set("type", "anchor");
                    $this->menuitem->set("uri",  $this->input->post("menuitem_anchor"));
                    $this->menuitem->save();

                    // Remove any obsolete relations
                    RouteHelper::removeRelation(null, $this->menuitem);

                break;

                default:
                    
                    // Save item details
                    $this->menuitem->set("type", "external");
                    $this->menuitem->set("uri",  $this->input->post("menuitem_uri"));
                    $this->menuitem->save();

                    // Remove any obsolete relations
                    RouteHelper::removeRelation(null, $this->menuitem);

                break;

            }

            // Inform user
            XCMS_JSON::modificationSuccessMessage();

        } catch (Exception $e) {

            XCMS_JSON::validationFailureMessage($e->getMessage());

        }

    }


    /**
     * "Set home"-functionality for this controller
     *
     * @param   int         $id             The ID of the affected menuitem
     */
    public function set_home($id = 0) {

        // Validate given item ID
        if (!is_numeric($id) || !$this->menuitem->load($id)) {
            return;
        }

        MenuHelper::unsetHome();
        $this->menuitem->set("home", "1");
        $this->menuitem->save();

    }


    /**
     * "Toggle sitemap"-functionality for this controller
     *
     * @param   int         $id             The ID of the affected menuitem
     */
    public function toggle_sitemap($id) {

        // Validate given item ID
        if (!is_numeric($id) || !$this->menuitem->load($id)) {
            return;
        }

        $this->menuitem->set("sitemap", $this->menuitem->get("sitemap") ? "0" : "1");
        $this->menuitem->save();

    }


    /**
     * "Toggle published"-functionality for this controller
     *
     * @param   int         $id             The ID of the affected menuitem
     */
    public function toggle_published($id) {

        // Validate given item ID
        if (!is_numeric($id) || !$this->menuitem->load($id)) {
            return;
        }

        $this->menuitem->set("published", $this->menuitem->get("published") ? "0" : "1");
        $this->menuitem->save();

    }


    /**
     * "Change ordering (move up)"-functionality for this controller
     *
     * @param   int         $id             The ID of the affected menuitem
     */
    public function move_up($id) {
        $this->_changeOrdering($id, 1);
    }


    /**
     * "Change ordering (move up)"-functionality for this controller
     *
     * @param   int         $id             The ID of the affected menuitem
     */
    public function move_down($id) {
        $this->_changeOrdering($id, -1);
    }


    /**
     * "Remove menuitem"-functionality for this controller
     *
     * @param   int         $id             The ID of the affected menuitem
     */
    public function remove($id) {

        // Validate given item ID
        if (!is_numeric($id) || !$this->menuitem->load($id)) {
            return;
        }

        // Remove item & descendants
        $tree = MenuHelper::getMenuTree($this->menuitem->get("menu_id"), false);
        if ($item = $tree->getItemByID($id)) {

            // Remove item
            $this->menuitem->remove();
            MenuHelper::updateOrdering($this->_getSubsequentSiblings($item), -1);

            // Remove children
            foreach ($item->toArray() as $node) {

                $this->menuitem->load($node->node_id);
                $this->menuitem->remove();

            }

        }

    }


    /**
     * Internal logic for assigning a new parent to given model
     *
     * @param   Object      $item           The item for which the parent will be updated
     * @param   Object      $parent         The new parent of the item
     * @return  boolean                     True on success, false on failure (tree structure issue)
     */
    private function _modifyParent($item, $parent) {

        // Check tree structure
        if ($item->getItemByID($this->input->post("menuitem_parent_id"))) {
            return false;
        }

        $relatives = array();
        $descendants = array();

        if ($item->count()) {

            $descendants = array();
            foreach ($item->toArray() as $node) {
                $descendants[] = $node->node_id;
            }

        }

        $targetFound = false;
        foreach ($item->parent->children as $child) {

            if ($child->node_id == $item->node_id) {
                $targetFound = true;
            } elseif ($targetFound) {
                $relatives[] = $child->node_id;
            }

        }

        // Retain tree integrity
        MenuHelper::updateOrdering($relatives, -1);
        MenuHelper::updateLevels($descendants, $parent->level + 1 - $this->menuitem->get("level"));

        // Finally, update item
        $this->menuitem->set("ordering",  $parent->count() + 1);
        $this->menuitem->set("level",     $parent->level + 1);
        $this->menuitem->set("parent_id", $parent->node_id);
        $this->menuitem->save();

        return true;

    }


    /**
     * Attempts to update the route relation for this item
     *
     * @param   string      $publicURL      The new public URL for this menu item
     * @param   string      $targetURL      The new target URL for this menu item
     * @param   string      $module         The new module configuration for this menu item
     * @return  boolean                     True on success, false on failure
     */
    private function _updateRelation($publicURL, $targetURL, $module) {

        RouteHelper::init();

        $routeByPublic = RouteList::getByPublic($publicURL);
        $routeByTarget = RouteList::getByTarget($targetURL, $module);
       
        // Complete match found (both public and target URL)
        if ($routeByPublic == $routeByTarget && $routeByPublic) {
            
            RouteHelper::removeRelation(null, $this->menuitem);
            RouteHelper::createRelation($routeByPublic, $this->menuitem);

            return true;

        }

        // No match on target URL, but public URL found (update target)
        if ($routeByPublic) {
            
            // Update route
            if ($route = (new RouteModel())->load($routeByPublic->id)) {

                $route->set("target_url", $targetURL);
                $route->save();

            }

            // Update relation
            RouteHelper::removeRelation(null, $this->menuitem);
            RouteHelper::createRelation($route, $this->menuitem);

            return true;

        }

        // No match on public URL
        if (!$routeByPublic) {

            // Create new route
            $route = (new RouteModel())->set(array(
                "public_url"    => $publicURL,
                "target_url"    => $targetURL,
                "module_config" => $module
            ))->save();

            // Update relation
            RouteHelper::removeRelation(null, $this->menuitem);
            RouteHelper::createRelation($route, $this->menuitem);

            return true;

        }        

        return false;

    }


    /**
     * "Change ordering"-functionality for this controller
     *
     * @param   int         $id             The ID of the affected menuitem
     * @param   int         $delta          The direction in which to change the ordering (e.g. -1 is down / 1 is up)
     */
    public function _changeOrdering($id, int $delta) {

        // Validate given item ID
        if (!is_numeric($id) || !$this->menuitem->load($id)) {
            return;
        }

        // Check for direct sibling
        if (($sibling = MenuHelper::getSibling($this->menuitem, -$delta)) === null) {
            return;
        }

        // Update current item
        $this->menuitem->set("ordering", $this->menuitem->get("ordering") - $delta);
        $this->menuitem->save();

        // Update related sibling
        $sibling->set("ordering", $sibling->get("ordering") + $delta);
        $sibling->save();

    }


    /**
     * Retrieves the node IDs of all subsequent siblings of given item
     *
     * @param    Object     $item           The item to analyze
     * @return   array                      Array containing node IDs found
     */
    private function _getSubsequentSiblings($item) {

        $relatives = array();

        $targetFound = false;
        foreach ($item->parent->children as $child) {

            if ($child->node_id == $item->node_id) {
                $targetFound = true;
            } elseif ($targetFound) {
                $relatives[] = $child->node_id;
            }

        }

        return $relatives;

    }

}
?>