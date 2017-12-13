<?php

/**
 * Base model for retrieving single XirtCMS article
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class ArticleModel extends XCMS_Model {

    /**
     * Attribute array for this model (valid attributes)
     * @var array
     */
    protected $_attr = array(
        "id", "title", "author_id", "dt_created", "published", "dt_publish", "dt_unpublish", "version"
    );


    /**
     * The author of the article (UserModel)
     * @var UserModel|null
     */
    private $_author = null;


    /**
     * The attributes of the article (AttributesModel)
     * @var AttributesModel|null
     */
    private $_attributes = null;


    /**
     * List of categories for this article
     * @var array
     */
    private $_categories = array();


    /**
     * CONSTRUCTOR
     * Instantiates controller with required helpers, libraries and helpers
     */
    public function __construct() {

        parent::__construct();

        // Load models
        $this->load->model("UserModel", false);
        $this->load->model("AttributesModel", false);
        $this->load->model("ArticleBlocksModel", false);

        // Initialize default attributes
        $this->_author = new UserModel();
        $this->_content = new ArticleBlocksModel();
        $this->_attributes = (new AttributesModel())->init(
            XCMS_Tables::TABLE_ARTICLES_ATTR, "article"
        );

    }


    /**
     * Loads the requested article
     *
     * @param   int         id              The ID of the article to load
     * @param   boolean     extArticle      Toggless loading of attributes, categories and author information
     * @return  mixed                       This instance on success, null otherwise
     */
    public function load($id, $extArticle = true) {

        // Retrieve data from DB
        $result = $this->_buildQuery($id)->get(XCMS_Tables::TABLE_ARTICLES);
        if ($result->num_rows()) {

            // Populate model
            $this->set($result->row());
            $this->_content->load($id);

            if ($extArticle) {

                // Load additional data
                $this->_attributes->load($this->get("id"));
                $this->_author->load($this->get("author_id"));
                $this->_loadCategories();

            }

            return $this;

        }

        return null;

    }


    /**
     * Validates the internal integrity of the model
     *
     * @throws  Exception                   Exception in case validation failed
     * @return  boolean                     Always true
     */
    public function validate() {

        if (!$this->get("author_id")) {
            throw new UnexpectedValueException();
        }

        return true;

    }


    /**
     * Saves the instance in the DB
     *
     * @return  Object                      Always this instance
     */
    public function save() {

        $this->get("id") ? $this->_update() : $this->_create();
        return $this;

    }


    /**
     * Removes the instance from the DB
     */
    public function remove() {

        $this->_attributes->removeAll($this->_data["id"]);
        $this->db->delete(XCMS_Tables::TABLE_ARTICLES, array(
            "id" => $this->_data["id"]
        ));

    }


    /**
     * Setter for article attributes (configuration)
     *
     * @param   String      $attr           The name of the attribute to be set
     * @param   String      $value          The value for the attribute to be set
     * @return  boolean                     True on success, false otherwise
     */
    public function setAttribute($attr, $value) {
        return $this->_attributes->set($attr, $value);
    }


    /**
     * Getter for article attribute (configuration)
     *
     * @param   String      $key            The key of the attribute to be retrieved
     * @param   boolean     $valueOnly      Toggless returning of the value only (optional)
     * @param   mixed       $fallback       The value to return in case the attribute was not found
     * @return  mixed                       The attribute, its value or null if not found
     */
    public function getAttribute($key, $valueOnly = false, $fallback = null) {
        return $this->_attributes->get($key, $valueOnly, $fallback);
    }


    /**
     * Getter for article attributes (configuration)
     *
     * @return  array                       The list of attributes for this article
     */
    public function getAttributes() {
        return $this->_attributes->getArray();
    }


    /**
     * Setter for article author
     *
     * @param   UserModel   $author         The author of the article
     * @return  Object                      Always this instance
     */
    public function setAuthor(UserModel $author) {

        $this->_author = $author;
        return $this;

    }


    /**
     * Getter for article author
     *
     * @return  UserModel                   The author set for this article
     */
    public function getAuthor() {
        return $this->_author;
    }


    /**
     * Setter for article content (blocks)
     *
     * @param   Object      $content        The ArticleBlocksModel to set as new content
     * @param   boolean     $rest           Toggles between adding vs. resetting of article blocks
     * @return  Object                      Always this instance
     */
    public function setArticleBlocks($content, $reset = false) {

        if (get_class($content) == "ArticleBlocksModel") {

            $this->_content = $content;
            return $this;

        } else if ($reset) {

            $this->_content = new ArticleBlocksModel();

        }

        $this->_content->add($content);

        return $this;

    }


    /**
     * Getter for article content (blocks)
     *
     * @return  object                      The ArticleBlocksModel for this article
     */
    public function getArticleBlocks() {
        return $this->_content;
    }


    /**
     * Setter for article categories
     *
     * @param   array       $attr           The categories to add to this article
     * @return  Object                      Always this instance
     */
    public function setCategories($categories) {

        $this->_categories = $categories;
        return $this;

    }


    /**
     * Getter for article categories
     *
     * @return  array                       The list of categories for this article
     */
    public function getCategories() {
        return $this->_categories;
    }


    /**
     * Creates query (using CI QueryBuilder) for retrieving model content (article)
     *
     * @param   int         $id             The id of the article to load
     * @return  Object                      CI Database Instance for chaining purposes
     */
    protected function _buildQuery($id) {

        $this->db->where("id", intval($id));

        // Hook for customized filtering
        XCMS_Hooks::execute("article.build_query", array(
            &$this, &$this->db, $id
        ));

        return $this->db;

    }


    /**
     * Loads categories for this article
     */
    private function _loadCategories() {

        // Retrieve data from DB
        $query = $this->db->get_where(XCMS_Tables::TABLE_ARTICLES_CATEGORIES, array(
            "article_id"  => $this->get("id")
        ));

        // Populate model
        foreach ($query->result() as $row) {
            $this->_categories[] = $row->category_id;
        }

    }


    /**
     * Saves the given instance values in the DB as a new item (create)
     *
     * @return  Object                      Always this instance
     */
    private function _create() {
        $this->db->insert(XCMS_Tables::TABLE_ARTICLES, $this->_data);
    }


    /**
     * Saves the given instance values in the DB as a existing item (update)
     *
     * @return  Object                      Always this instance
     */
    private function _update() {

        $this->db->replace(XCMS_Tables::TABLE_ARTICLES, $this->_data);
        $this->_attributes->save();
        $this->_content->save();
        $this->_saveCategories();

    }


    /**
     * Saves categories set for this article
     */
    private function _saveCategories() {

        // Remove old categories
        $this->db->delete(XCMS_Tables::TABLE_ARTICLES_CATEGORIES, array(
            "article_id" => $this->get("id")
        ));

        // Add current categories
        foreach ($this->_categories as $category) {

            $this->db->insert(XCMS_Tables::TABLE_ARTICLES_CATEGORIES, array(
                "article_id"  => $this->get("id"),
                "category_id" => $category
            ));

        }

    }

}
?>