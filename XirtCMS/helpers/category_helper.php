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
class CategoryHelper {

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
                ->update(Query::TABLE_CATEGORIES_RELATIONS);

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
                ->update(Query::TABLE_CATEGORIES_RELATIONS);

        }

    }


    /**
     * Retrieves the sibling with indicated relation to the given item
     *
     * @param   Object      $category       The CategoryModel for which to retrieve the sibling
     * @param   int         $delta          The relation with the sibling (e.g. 1 and -1 are direct siblings)
     * @return  mixed                       Null on failure, CategoryModel of sibling once successful
     */
    public static function getSibling($category, int $relation) {

        $query = get_instance()->db
            ->select("node_id")
            ->where("parent_id", $category->get("parent_id"))
            ->where("ordering",  $category->get("ordering") + $relation)
            ->get(Query::TABLE_CATEGORIES_RELATIONS);

        if ($result = $query->row()) {
            return (new CategoryModel())->load($result->node_id);
        }

        return null;

    }


    /**
     * Returns the category tree as list of category items
     *
     * @param   boolean     $activeOnly     Toggles between all items vs. published items only
     */
    public static function getCategoryTree(bool $activeOnly = true) {

        // Prerequisites
        $CI =& get_instance();
        $CI->load->helper("db_search");
        $CI->load->model("CategoriesModel", "categories");

        $tree = new XCMS_Tree();

        // Start creating menu
        $attr = new searchAttributes();
        $CI->categories->load($attr->setFilter("published", $activeOnly));
        foreach ($CI->categories->toArray() as $category) {

            $tree->add(new XCMS_Node((object)[
                "node_id"   => $category->id,
                "name"      => $category->name,
                "level"     => $category->level,
                "ordering"  => $category->ordering,
                "published" => $category->published,
                "parent_id" => $category->parent_id
            ]));

        }

        return $tree;

    }

}
?>