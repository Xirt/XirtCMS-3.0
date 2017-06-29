<?php

/**
 * ArticleModel for XirtCMS (single article)
 *
 * @author		A.G. Gideonse
 * @version		3.0
 * @copyright	XirtCMS 2016 - 2017
 * @package		XirtCMS
 */
class ArticleModel extends XCMS_Model {

	/**
	 * @var array
	 * Attribute array for this model (valid attributes)
	 */
	protected $_attr = array(
		"id", "title", "content", "author", "dt_created", "version"
	);


	/**
	 * @var UserModel|null
	 * The author of the article (UserModel)
	 */
	private $_author = null;


	/**
	 * @var AttributesModel|null
	 * The attributes of the article (AttributesModel)
	 */
	private $_attributes = null;


	/**
	 * @var array
	 * List of categories for this article
	 */
	private $_categories = array();


	/**
	 * CONSTRUCTOR
	 * Instantiates controller with required helpers, libraries and helpers
	 */
	public function __construct() {

		parent::__construct();

		// Load helpers
		$this->load->helper("db_search");

		// Load models
		$this->load->model("UserModel", false);
		$this->load->model("AttributesModel", false);

		// Initialize default attributes
		$this->_author = new UserModel();
		$this->_attributes = (new AttributesModel())->init(
			Query::TABLE_ARTICLES_ATTR, "article"
		);

	}


	/**
	 * Loads the requested article
	 *
	 * @param	int			id			The ID of the article to load
	 * @param	boolean		extArticle	Toggless loading of attributes, categories and author information
	 * @return	mixed					This instance on success, null otherwise
	 */
	public function load($id, $extArticle = true) {

		// Retrieve data from DB
		$result = $this->db->get_where(Query::TABLE_ARTICLES, array("id" => $id));
		if ($result->num_rows()) {

			// Populate model
			$this->set($result->row());

			if ($extArticle) {

				// Load additional data
				$this->_attributes->load($this->get("id"));
				$this->_author->load($this->get("author"));
				$this->_loadCategories();

			}

			return $this;

		}

		return null;

	}


	/**
	 * Saves the instance in the DB
	 *
	 * @return	Object					Always this instance
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
		$this->db->delete(Query::TABLE_ARTICLES, array(
			"id" => $this->_data["id"]
		));

	}


	/**
	 * Setter for article attributes (configuration)
	 *
	 * @param	String		$attr		The name of the attribute to be set
	 * @param	String		$value		The value for the attribute to be set
	 * @return 	boolean					True on success, false otherwise
	 */
	public function setAttribute($attr, $value) {
		return $this->_attributes->set($attr, $value);
	}


	/**
	 * Getter for article attribute (configuration)
	 *
	 * @param	String		$key		The key of the attribute to be retrieved
	 * @param	boolean		$valueOnly	Toggless returning of the value only (optional)
	 * @param	mixed		$fallback	The value to return in case the attribute was not found
	 * @return	mixed					The attribute, its value or null if not found
	 */
	public function getAttribute($key, $valueOnly = false, $fallback = null) {
		return $this->_attributes->get($key, $valueOnly, $fallback);
	}


	/**
	 * Getter for article attributes (configuration)
	 *
	 * @return	array					The list of attributes for this article
	 */
	public function getAttributes() {
		return $this->_attributes->getArray();
	}


	/**
	 * Setter for article author
	 *
	 * @param	UserModel	$author 	The author of the article
	 * @return	Object					Always this instance
	 */
	public function setAuthor(UserModel $author) {

		$this->_author = $author;
		return $this;

	}


	/**
	 * Getter for article author
	 *
	 * @return	UserModel				The author set for this article
	 */
	public function getAuthor() {
		return $this->_author;
	}


	/**
	 * Setter for article categories
	 *
	 * @param	array		$attr 		The categories to add to this article
	 * @return	Object					Always this instance
	 */
	public function setCategories($categories) {

		$this->_categories = $categories;
		return $this;

	}


	/**
	 * Getter for article categories
	 *
	 * @return	array					The list of categories for this article
	 */
	public function getCategories() {
		return $this->_categories;
	}


	/**
	 * Loads categories for this article
	 */
	private function _loadCategories() {

		// Retrieve data from DB
		$query = $this->db->get_where(Query::TABLE_ARTICLES_CATEGORIES, array(
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
	 * @return	Object					Always this instance
	 */
	private function _create() {
		$this->db->insert(Query::TABLE_ARTICLES, $this->_data);
	}


	/**
	 * Saves the given instance values in the DB as a existing item (update)
	 *
	 * @return	Object					Always this instance
	 */
	private function _update() {

		$this->db->replace(Query::TABLE_ARTICLES, $this->_data);
		$this->_attributes->save();
		$this->_saveCategories();

	}


	/**
	 * Saves categories set for this article
	 */
	private function _saveCategories() {

		// Remove old categories
		$this->db->delete(Query::TABLE_ARTICLES_CATEGORIES, array(
			"article_id" => $this->get("id")
		));

		// Add current categories
		foreach ($this->_categories as $category) {

			$this->db->insert(Query::TABLE_ARTICLES_CATEGORIES, array(
				"article_id"  => $this->get("id"),
				"category_id" => $category
			));

		}

	}

}
?>