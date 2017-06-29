<?php

/**
 * UserModel for XirtCMS (single user)
 *
 * @author		A.G. Gideonse
 * @version		3.0
 * @copyright	XirtCMS 2016 - 2017
 * @package		XirtCMS
 */
class UserModel extends XCMS_Model {

	/**
	 * @var array
	 * Attribute array for this model (valid attributes)
	 */
	protected $_attr = array(
		"id", "username", "real_name", "email", "password", "salt", "usergroup_id", "dt_created"
	);


	/**
	 * CONSTRUCTOR
	 * Instantiates controller with required helpers, libraries and helpers
	 */
	public function __construct() {

		parent::__construct();

		// Load models
		$this->load->model("attributesModel", "attributes");

	}


	/**
	 * Loads the requested user by given ID
	 *
	 * @param 	int			$id			The id of the user to load
	 * @return	mixed					This instance on success, null otherwise
	 */
	public function load($id) {
		return $this->loadById($id);
	}


	/**
	 * Loads the requested user by given ID
	 *
	 * @param 	int			$id			The id of the user to load
	 * @return	mixed					This instance on success, null otherwise
	 */
	public function loadByID($id) {

		// Retrieve data
		$result = $this->db->get_where(Query::TABLE_USERS, array("id" => intval($id)));
		if ($result->num_rows()) {

			// Populate model
			$this->set($result->row());

			// Load attributes
			$this->attributes->init(Query::TABLE_USERS_ATTR, "user");
			$this->attributes->load($id);

			return $this;

		}

		return null;

	}


	/**
	 * Loads the requested user by given username
	 *
	 * @param	String		$username	The username of the user to load
	 * @return	mixed					This instance on success, null otherwise
	 */
	public function loadByUsername($username) {

		// Retrieve data
		$result = $this->db->get_where(Query::TABLE_USERS, array("username" => $username));
		if ($result->num_rows()) {

			// Populate model
			$this->set($result->row());

			// Load attributes
			$this->attributes->init(Query::TABLE_USERS_ATTR, "user");
			$this->attributes->load($id);

			return $this;

		}

		return null;

	}


	/**
	 * Validates the data currently in the instance
	 *
	 * @return	Object					Always this instance
	 * @throws	Exception				Thrown in case invalid data is detected within the instance
	 */
	public function validate() {

		// Validate uniqueness of given username
		$result = $this->db->get_where(Query::TABLE_USERS, array("username" => $this->get("username")));
		if ($result->num_rows() > 0 && (!$this->get("id") || $result->row()->id != $this->get("id"))) {
			throw new Exception("The chosen username is already in use by a different user.");
		}

		// Validate uniqueness of given e-mail address
		$result = $this->db->get_where(Query::TABLE_USERS, array("email" => $this->get("email")));
		if ($result->num_rows() > 0 && (!$this->get("id") || $result->row()->id != $this->get("id"))) {
			throw new Exception("The chosen e-mail address is already in use by a different user.");
		}

		return $this;

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

		// Updates DB (data & metadata)
		$this->attributes->removeAll($this->get("id"));
		$this->db->delete(Query::TABLE_USERS,  array(
			"id" => $this->get("id")
		));

	}


	/**
	 * Getter for model attribute
	 *
	 * @param	String		$key		The key of the attribute to be retrieved
	 * @param	boolean		$valueOnly	Toggless returning of the value only (optional)
	 * @param	mixed		$fallback	The value to return in case the value was not found
	 * @return	mixed					The attribute, its value or null if not found
	 */
	public function getAttribute($key, $valueOnly = false, $fallback = null) {
		return $this->attributes->get($key, $valueOnly, $fallback);
	}


	/**
	 * Getter for model attributes (configuration)
	 *
	 * @return	array					The list of attributes for this model
	 */
	public function getAttributes() {
		return $this->attributes->getArray();
	}


	/**
	 * Setter for model attributes
	 *
	 * @param	String		$attr		The name of the attribute to be set
	 * @param	String		$value		The value for the attribute to be set
	 * @return 	boolean					True on success, false otherwise
	 */
	public function setAttribute($attr, $value) {
		return $this->attributes->set($attr, $value);
	}


	/**
	 * Saves the given instance values in the DB as a new item (create)
	 */
	private function _create() {

		$this->db->insert(Query::TABLE_USERS, $this->getArray());
		$this->set("id", $this->db->insert_id());

	}


	/**
	 * Saves the given instance values in the DB as a existing item (update)
	 */
	private function _update() {

		$this->db->replace(Query::TABLE_USERS, $this->getArray());
		$this->attributes->save();

	}

}
?>