<?php

/**
 * Controller for showing a viewing / modifying a XirtCMS article (back end)
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class Article extends XCMS_Controller {

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
        $this->load->helper("article");

        // Load libaries
        $this->load->library("form_validation");

        // Load helpers
        $this->load->model("ArticleModel", "article");
        $this->load->model("CategoryModel", "category");

    }


    /**
     * "View Article"-functionality for this controller.
     *
     * @param   int         $id             The ID of the requested article
     */
    public function view($id = 0) {

        // Validate given ID
        if (!is_numeric($id) || !$this->article->load($id)) {

            XCMS_JSON::loadingFailureMessage();
            return;

        }

        // Prepare data...
        $data = (Object)array(
            "id"           => $this->article->get("id"),
            "title"        => $this->article->get("title"),
            "published"    => $this->article->get("published"),
            "dt_publish"   => $this->article->get("dt_publish"),
            "dt_unpublish" => $this->article->get("dt_unpublish"),
            "categories"   => $this->article->getCategories(),
            "attributes"   => $this->article->getAttributes(),
            "content"      => ArticleHelper::getContent($this->article)
        );

        // ...and output it as JSON
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($data));

    }


    /**
     * "Create Article"-functionality for this controller
     */
    public function create() {

        try {

            // Validate provided input
            if (!$this->form_validation->run()) {
                throw new UnexpectedValueException();
            }

            // Set & save new updates
            $this->load->library("XCMS_Authentication");
            $this->article->set("author_id", XCMS_Authentication::getUserId());
            $this->article->set("title", $this->input->post("article_title"));
            $this->article->validate();
            $this->article->save();

            // Inform user
            XCMS_JSON::creationSuccessMessage();

        } catch (Exception $e) {

            XCMS_JSON::validationFailureMessage($e->getMessage());

        }

    }


    /**
     * "Modify Article"-functionality for this controller
     */
    public function modify() {

        // Validate given user ID
        $id = $this->input->post("article_id");
        if (!is_numeric($id) || !$this->article->load($id)) {

            XCMS_JSON::loadingFailureMessage();
            return;

        }

        try {

            // Validate provided input
            if (!$this->form_validation->run()) {
                throw new UnexpectedValueException(null);
            }

            // Validate given category ID
            $category = $this->input->post("article_category_id");
            if ($category && (!is_numeric($category) || !$this->category->load($category))) {
                throw new UnexpectedValueException("Provided category ID unknown.");
            }

            // Set & save new updates
            $this->article->set("title",   $this->input->post("article_title"));            
            $this->article->setArticleBlocks((new ArticleBlockModel())->set((object)[
                "ordering" => 0,
                "ref_id"   => $id,
                "settings" => "{}",
                "type"     => "content",
                "content"  => $this->input->post("article_content")
            ]), true);
            $this->article->validate();
            $this->article->save();

            // Inform user
            XCMS_JSON::modificationSuccessMessage();

        } catch (Exception $e) {

            XCMS_JSON::validationFailureMessage($e->getMessage());

        }

    }


    /**
     * "Modify Categories"-functionality for this controller
     */
    public function modify_categories() {

        // Validate given user ID
        $id = $this->input->post("article_id");
        if (!is_numeric($id) || !$this->article->load($id)) {

            XCMS_JSON::loadingFailureMessage();
            return;

        }

        try {

            // Retrieve categories
            $categories = $this->input->post("article_categories");
            $categories = is_array($categories) ? $categories : array();

            // Validate given categories
            foreach ($categories as $category) {

                if (!is_numeric($category) || !$this->category->load($category)) {
                    throw new UnexpectedValueException("Provided category ID unknown.");
                }

            }

            // Set & save category updates
            $this->article->setCategories($categories);
            $this->article->validate();
            $this->article->save();

            // Inform user
            XCMS_JSON::modificationSuccessMessage();

        } catch (Exception $e) {

            XCMS_JSON::validationFailureMessage($e->getMessage());

        }

    }


    /**
     * "Modify META Information"-functionality for this controller
     */
    public function modify_config() {

        // Validate given user ID
        $id = $this->input->post("article_id");
        if (!is_numeric($id) || !$this->article->load($id)) {

            XCMS_JSON::loadingFailureMessage();
            return;

        }

        try {

            // Set & save new attribute values
            foreach ($this->article->getAttributes() as $attribute) {

                $this->article->setAttribute($attribute->name, "");
                if ($value = $this->input->post("attr_" . $attribute->name)) {
                    $this->article->setAttribute($attribute->name, $value);
                }

            }

            $this->article->validate();
            $this->article->save();

            // Inform user
            XCMS_JSON::modificationSuccessMessage();

        } catch (Exception $e) {

            XCMS_JSON::validationFailureMessage($e->getMessage());

        }

    }


    /**
     * "Modify Publification Status"-functionality for this controller
     */
    public function modify_publish() {

        // Validate given user ID
        $id = $this->input->post("article_id");
        if (!is_numeric($id) || !$this->article->load($id)) {

            XCMS_JSON::loadingFailureMessage();
            return;

        }

        try {

            // Validate provided input
            if (!$this->form_validation->run()) {
                throw new UnexpectedValueException(null);
            }

            // Prepare data
            $dtPublish   = DateTime::createFromFormat("d/m/Y", $this->input->post("article_dt_publish"));
            $dtUnpublish = DateTime::createFromFormat("d/m/Y", $this->input->post("article_dt_unpublish"));

            // Set & save new updates
            $this->article->set("published",    is_null($this->input->post("article_published")) ? "0" : "1");
            $this->article->set("dt_publish",   $dtPublish->format("Y-m-d H:i:s"));
            $this->article->set("dt_unpublish", $dtUnpublish->format("Y-m-d H:i:s"));
            $this->article->validate();
            $this->article->save();

            // Inform user
            XCMS_JSON::modificationSuccessMessage();

        } catch (Exception $e) {

            XCMS_JSON::validationFailureMessage($e->getMessage());

        }

    }


    /**
     * "Remove Article"-functionality for this controller.
     *
     * @param   int         $id             The ID of the targetted article
     */
    public function remove($id = 0) {

        if (is_numeric($id) && $this->article->load($id)) {
            $this->article->remove();
        }

    }

}
?>