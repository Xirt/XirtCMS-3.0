<?php

require_once "XCMS_Node.php";
require_once "XCMS_Tree.php";

/**
 * Static utility class for XirtCMS categories
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
                ->update(XCMS_Tables::TABLE_CATEGORIES_RELATIONS);

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
                ->update(XCMS_Tables::TABLE_CATEGORIES_RELATIONS);

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
            ->get(XCMS_Tables::TABLE_CATEGORIES_RELATIONS);

        if ($result = $query->row()) {
            return (new CategoryModel())->load($result->node_id);
        }

        return null;

    }


    /**
     * Returns the category tree as list of category items
     *
     * @return  mixed                       The XCMS_Tree with all known categories for the current request
     */
    public static function getCategoryTree() {

        // Prerequisites
        $CI =& get_instance();
        $CI->load->model("CategoriesModel", false);

        if (!$tree = XCMS_Cache::get("categories")) {

            // Retrieve data
            $tree = new XCMS_Tree();
            foreach ((new CategoriesModel())->load()->toArray() as $category) {

                $tree->add(new XCMS_Node((object)[
                    "node_id"   => $category->get("id"),
                    "name"      => $category->get("name"),
                    "level"     => $category->get("level"),
                    "ordering"  => $category->get("ordering"),
                    "published" => $category->get("published"),
                    "parent_id" => $category->get("parent_id")
                ]));

            }

        }

        XCMS_Cache::set("categories", $tree);
        return $tree;

    }


    /**
     * Returns the category tree as list of category items
     *
     * @param   mixed      $category       The CategoryModel or category ID for which to retrieve the articles
     * @return  Object                      The ArticlesModel with all articles found
     */
    public static function getArticles($category) {
    
        // Prerequisites
        $CI =& get_instance();
        $CI->load->model("ArticlesModel", false);

        return (new ArticlesModel())->set(
            "category", is_numeric($category) ? $category : $category->get("id"), false
        )->load();

    }

}
?>