<?php

/**
 * Base model for retrieving single XirtCMS user
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2018
 * @package     XirtCMS
 */
class UserModel extends XCMS_Model {

    /**
     * Attribute array for this model (valid attributes)
     * @var array
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
     * @param   mixed         $id           The id or username of the user to load
     * @return  mixed                       This instance on success, null otherwise
     */
    public function load($id) {

        // Retrieve data
        $result = $this->_buildQuery($id)->get(XCMS_Tables::TABLE_USERS);
        if ($result->num_rows()) {

            // Populate model
            $this->set($result->row());

            // Load attributes
            $this->attributes->init(XCMS_Tables::TABLE_USERS_ATTR, "user");
            $this->attributes->load($this->get("id"));

            return $this;

        }

        return null;

    }


    /**
     * Loads the requested user by given ID
     *
     * @deprecated          3.0             Deprecated in favor of UserModel::load(id)
     * @param   int         $id             The id of the user to load
     * @return  mixed                       This instance on success, null otherwise
     */
    public function loadByID(int $id) {
        return $this->load($id);
    }


    /**
     * Loads the requested user by given username
     *
     * @deprecated          3.0             Deprecated in favor of UserModel::load(id)
     * @param   String      $username       The username of the user to load
     * @return  mixed                       This instance on success, null otherwise
     */
    public function loadByUsername(String $username) {
        return $this->load($username);
    }


    /**
     * Setter for model attributes
     *
     * @param   mixed       $attr           The key of the attribute to be set or array/object with attributes (key/value)
     * @param   mixed       $value          The value for the attribute to be set
     * @param   mixed       $isComplex      Toggles allowance of complex (array / object) values
     * @return  Object                      Always this instance
     */
    public function set($attr, $value = null, bool $isComplex = false) {

        if ($attr == "password") {

            parent::set("salt", XCMS_Authentication::generateSalt());
            parent::set("password", XCMS_Authentication::hash($value, $this->get("salt")));

            return $this;

        }

        return parent::set($attr, $value, $isComplex);

    }


    /**
     * Validates the data currently in the instance
     *
     * @return  Object                      Always this instance
     * @throws  Exception                   Thrown in case invalid data is detected within the instance
     */
    public function validate() {

        // Validate uniqueness of given username
        $result = $this->db->get_where(XCMS_Tables::TABLE_USERS, array("username" => $this->get("username")));
        if ($result->num_rows() > 0 && (!$this->get("id") || $result->row()->id != $this->get("id"))) {
            throw new ValidationException("The chosen username is already in use by a different user.");
        }

        // Validate uniqueness of given e-mail address
        $result = $this->db->get_where(XCMS_Tables::TABLE_USERS, array("email" => $this->get("email")));
        if ($result->num_rows() > 0 && (!$this->get("id") || $result->row()->id != $this->get("id"))) {
            throw new ValidationException("The chosen e-mail address is already in use by a different user.");
        }

        return $this;

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

        // Updates DB (data & metadata)
        $this->attributes->removeAll($this->get("id"));
        $this->db->delete(XCMS_Tables::TABLE_USERS,  array(
            "id" => $this->get("id")
        ));

    }


    /**
     * Getter for model attribute
     *
     * @param   String      $key            The key of the attribute to be retrieved
     * @param   boolean     $valueOnly      Toggless returning of the value only (optional)
     * @param   mixed       $fallback       The value to return in case the value was not found
     * @return  mixed                       The attribute, its value or null if not found
     */
    public function getAttribute(String $key, bool $valueOnly = false, $fallback = null) {
        return $this->attributes->get($key, $valueOnly, $fallback);
    }


    /**
     * Getter for model attributes (configuration)
     *
     * @return  array                       The list of attributes for this model
     */
    public function getAttributes() {
        return $this->attributes->getArray();
    }


    /**
     * Setter for model attributes
     *
     * @param   String      $attr           The name of the attribute to be set
     * @param   mixed       $value          The value for the attribute to be set
     * @return  boolean                     True on success, false otherwise
     */
    public function setAttribute(String $attr, $value) {
        return $this->attributes->set($attr, $value);
    }


    /**
     * Saves the given instance values in the DB as a new item (create)
     */
    private function _create() {

        $this->db->insert(XCMS_Tables::TABLE_USERS, $this->getArray());
        $this->set("id", $this->db->insert_id());

    }


    /**
     * Saves the given instance values in the DB as a existing item (update)
     */
    private function _update() {

        $this->db->replace(XCMS_Tables::TABLE_USERS, $this->getArray());
        $this->attributes->save();

    }


    /**
     * Creates query (using CI QueryBuilder) for retrieving model content (user)
     *
     * @param   mixed         $id           The id or username of the user to load
     * @return  Object                      CI Database Instance for chaining purposes
     */
    protected function _buildQuery($id) {

        is_numeric($id) ? $this->db->where("id", intval($id)) : $this->db->where("username", $id);

        // Hook for customized filtering
        XCMS_Hooks::execute("user.build_query", array(
            &$this, &$this->db, $id
        ));

        return $this->db;

    }

}
?>