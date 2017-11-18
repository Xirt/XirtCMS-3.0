<?php

/**
 * Sitemap hook to load subitems to sitemap list for given item
 */
XCMS_Hooks::add("sitemap.add_item", function(&$list, &$item) {

    // Check module type
    if (strpos($item->targetURL, "articles") !== 0) {
        return;
    }

    // Check URL completeness
    $parts = explode("/", $item->targetURL);
    if (count($parts) < 2) {
        return;
    }

    // Retrieve articles
    $ci =& get_instance();
    $ci->load->helper("module");
    $ci->load->helper("category");

    switch ($parts[1]) {

        case "category":
            $categoryId = $parts[2] ?? null;
        break;

        default:
            $categoryId = null;
        break;

    }

    $func = function($model, $stmt) {

        if (($id = $model->get("category")) && is_numeric($id)) {

            $stmt->join(XCMS_Tables::TABLE_ARTICLES_CATEGORIES, XCMS_Tables::TABLE_ARTICLES . ".id = article_id")
                ->where("category_id", $id);

        }

    };

    XCMS_Hooks::add("articles.build_article_query", $func);
    $moduleConfig = ModuleHelper::getModuleConfiguration($item->module);
    foreach (CategoryHelper::getArticles($categoryId)->toArray() as $article) {

        $list[] = (Object) [

            "type"   => "internal",
            "target" => RouteList::getByTarget("article/view/" . $article->get("id"), $moduleConfig->getSetting("module_config"))->public_url,
            "name"   => $article->get("title"),
            "level"  => $item->level + 1

        ];

    }

    XCMS_Hooks::remove("articles.build_article_query", $func);

});
?>