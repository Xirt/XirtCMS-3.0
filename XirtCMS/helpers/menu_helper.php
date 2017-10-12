<?php

require_once "XCMS_Node.php";
require_once "XCMS_Tree.php";

/**
 * Static utility class for XirtCMS menus
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class MenuHelper {

    /**
     * Unsets the home menu item
     */
    public static function unsetHome() {

        get_instance()->db
            ->set("home", 0)
            ->update(Query::TABLE_MENUITEMS);

    }


    /**
     * Batch updates the level of given item(s)
     *
     * @param   mixed       $subjects       The ID(s) of the items to update
     * @param   int         $delta          The change in levels to apply
     */
    public static function updateLevels($subjects, int $delta) {

        $subjects = is_array($subjects) ? $subjects : array($subjects);
        if (count($subjects)) {

            get_instance()->db
                ->where_in("node_id", $subjects)
                ->set("level", "level + " . $delta, false)
                ->update(Query::TABLE_MENUITEMS_RELATIONS);

        }

    }


    /**
     * Batch updates the ordering of given item(s)
     *
     * @param   mixed       $subjects       The ID(s) of the items to update
     * @param   int         $delta          The change in ordering to apply
     */
    public static function updateOrdering($subjects, int $delta) {

        $subjects = is_array($subjects) ? $subjects : array($subjects);
        if (count($subjects)) {

            get_instance()->db
                ->where_in("node_id", $subjects)
                ->set("ordering", "ordering + " . $delta, false)
                ->update(Query::TABLE_MENUITEMS_RELATIONS);

        }

    }


    /**
     * Retrieves the sibling with indicated relation to the given item
     *
     * @param   Object      $category       The MenuitemModel for which to retrieve the sibling
     * @param   int         $relation       The relation with the sibling (e.g. 1 and -1 are direct siblings)
     * @return  mixed                       Null on failure, MenuitemModel of sibling once successful
     */
    public static function getSibling($category, int $relation) {

        $query = get_instance()->db
            ->select("node_id")
            ->join(Query::TABLE_MENUITEMS, "id = node_id")
            ->where("menu_id", $category->get("menu_id"))
            ->where("parent_id", $category->get("parent_id"))
            ->where("ordering",  $category->get("ordering") + $relation)
            ->get(Query::TABLE_MENUITEMS_RELATIONS);

        if ($result = $query->row()) {
            return (new MenuitemModel())->load($result->node_id);
        }

        return null;

    }


    /**
     * Returns requested menu as list of menu items
     *
     * @param   int         $id             The ID of the requested menu
     * @param   boolean     $activeOnly     Toggles between all items vs. published items only
     */
    public static function getMenu(int $id, bool $activeOnly = true) {
        return MenuHelper::getMenuTree($id, $activeOnly)->toArray();
    }


    /**
     * Returns requested menu tree as list of menu items
     *
     * @param   int         $id             The ID of the requested menu
     * @param   boolean     $activeOnly     Toggles between all items vs. published items only
     */
    public static function getMenuTree(int $id, bool $activeOnly = true) {

        // Prerequisites
        $CI =& get_instance();
        $CI->load->model("MenuitemsModel", "menuitems");

        $tree = new XCMS_Menu();

        // Start creating menu
        $CI->menuitems->load($id, $activeOnly);
        foreach ($CI->menuitems->toArray() as $item) {

            switch ($item->get("type")) {

                case "module":
                    $target = $item->get("source_url") . $item->get("uri");
                break;

                case "anchor":
                    $target = $item->get("uri");
                break;

                default:
                    $target = $item->get("uri");
                break;

            }

            $tree->add(new XCMS_MenuItem((object)[
                "node_id"   => $item->get("id"),
                "menu_id"   => $item->get("menu_id"),
                "parent_id" => $item->get("parent_id"),
                "ordering"  => $item->get("ordering"),
                "name"      => $item->get("name"),
                "published" => $item->get("published"),
                "sitemap"   => $item->get("sitemap"),
                "home"      => $item->get("home"),
                "type"      => $item->get("type"),
                "target"    => $target
            ]));

        }

        return $tree->setActive($CI->router->getActiveNodes());

    }

}

class XCMS_Menu extends XCMS_Tree {

    /**
     * Sets the given item as active menu item
     *
     * @param    $nodes        Array with node IDs of single node ID
     * @return    XCMS_Menu    A reference to this menu for chaining purposes
     */
    function setActive($nodes = array()) {

        $nodes = is_array($nodes) ? $nodes : array($nodes);

        foreach ($nodes as $nodeId) {

            if ($node = $this->getItemById($nodeId)) {
                $node->setActive();
            }

        }

        return $this;

    }

}

class XCMS_MenuItem extends XCMS_Node {

    /**
     * Indicates whether this is an active menu item
     */
    public $active = false;


    /**
     * Sets the current item as active menu item
     *
     * @return    XCMS_Menu    A reference to the related menu for chaining purposes
     */
    public function setActive() {

        $this->active = true;
        return $this->parent->setActive();

    }

}
?>