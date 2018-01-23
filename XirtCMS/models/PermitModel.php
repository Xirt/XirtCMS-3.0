<?php

/**
 * Base model for retrieving single XirtCMS permit
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2018
 * @package     XirtCMS
 */
class PermitModel extends XCMS_Model {

    /**
     * Attribute array for this model (valid attributes)
     * @var array
     */
    protected $_attr = array(
        "type", "id", "dt_start", "dt_expiry", "access_min", "access_max", "active"
    );

    /**
     * Additional (non-saved) attributes for this model
     * @var array
     */
    protected $_ext = array();


    /**
     * CONSTRUCTOR
     * Instantiates controller with required helpers, libraries and models
     */
    public function __construct() {

        parent::__construct();

        // Load helpers
        $this->load->helper("user");

    }


    /**
     * Setter for model attributes
     *
     * @param   mixed       $attr           The key of the attribute to be set or array/object with attributes (key/value)
     * @param   mixed       $value          The value for the attribute to be set
     * @param   boolean     $validate       Toggles validation against allowed model attributes
     * @throws  Exception                   On validation error
     * @return  Object                      Always this instance
     */
    public function set($attr, $value = null, bool $validate = true) {

        // Ensure Array input
        if (is_object($attr) || !is_array($attr)) {
            $attr = is_object($attr) ? (array)$attr : array($attr => $value);
        }

        $this->validateAttributes($attr);
        return parent::set($attr, $value, $validate);

    }


    /**
     * Setter for model parameters (non-saved attributes)
     *
     * @param   mixed       $attr           The key of the attribute to be set or array/object with attributes (key/value)
     * @param   mixed       $value          The value for the attribute to be set
     * @return  Object                      Always this instance
     */
    public function setParameter(String $attr, $value) {

        $this->_ext[$attr] = $value;
        return $this;

    }


    /**
     * Getter for single model parameter
     *
     * @param   String      $key            The key of the attribute value to be retrieved
     * @param   mixed       $fallback       The value to return in case the value was not found
     * @return  mixed                       The attribute, its value or the fallback value if not found
     */
    public function getParameter(String $key, $fallback = null) {

        // Check  normal attributes
        if (array_key_exists($key, $this->_ext)) {
            return $this->_ext[$key];
        }

        return $fallback;

    }


    /**
     * Validates the internal integrity of the model
     *
     * @param   Array       $attr           Array with attributes to check (key/value)
     * @throws  Exception                   On validation error
     * @return  boolean                     Always true
     */
    public function validateAttributes(array &$attr) {

        foreach ($attr as $key => &$value) {

            // VALIDATION :: DateTime
            if (in_array($key, array("dt_start", "dt_expiry")) && !is_a($value, "DateTime")) {

                // Convert to DateTime if possible
                if ($value = DateTime::createFromFormat("Y-m-d G:i:s", $value)) {
                    continue;
                }

                throw new InvalidArgumentException("Value for attr '$key' must be DateTime.");

            }

            // VALIDATION :: Mandatory Integers
            if (in_array($key, array("id", "active")) && !is_numeric($value)) {
                throw new InvalidArgumentException("Value for attr '$key' must be numeric.");
            }

            // VALIDATION :: Optional Integers
            if (in_array($key, array("access_min", "access_max")) && $value && !is_numeric($value)) {
                throw new InvalidArgumentException("Value for optional attr '$key' must be numeric.");
            }

            // VALIDATION :: Strings
            if (in_array($key, array("type")) && !is_string($value)) {
                throw new InvalidArgumentException("Value for attr '$key' must be String.");
            }

        }

        return true;

    }


    /**
     * Loads the requested instance from the DB
     *
     * @param   String      type            The type for which to load the permit
     * @param   int         id              The ID for which to load the permit
     * @return  mixed                       This instance on success, null otherwise
     */
    public function load(String $type, int $id) {

        // Retrieve data from DB
        $result = $this->_buildQuery($type, $id)->get(XCMS_Tables::TABLE_PERMITS);
        if ($result->num_rows()) {

            $this->set($result->row());
            return $this;

        }

        return null;

    }


    /**
     * Saves the instance in the DB
     *
     * @return  Object                      Always this instance
     */
    public function save() {

        $this->db->replace(XCMS_Tables::TABLE_PERMITS, $this->_getPreparedData());
        return $this;

    }


    /**
     * Removes the instance from the DB
     */
    public function remove() {

        $this->db->delete(XCMS_Tables::TABLE_PERMITS, array(
            "type" => $this->_data["type"],
            "id" => $this->_data["id"]
        ));

    }


    /**
     * Getter for all model attributes as Object
     *
     * @param   mixed                       String with requested DateTime format (or null to retrieve as DateTime)
     * @return  Object                      The attributes of the model as Object (or defaults if not present)
     */
    public function getObject(String $format = null) {

        // Prepare start date
        if ($dtStart = $this->get("dt_start") && $format) {
            $dtStart = $this->get("dt_start")->format($format);
        }

        // Prepare expiry date
        if ($dtExpiry = $this->get("dt_expiry") && $format) {
            $dtExpiry = $this->get("dt_expiry")->format($format);
        }

        return (Object) array_merge($this->_ext, [
            "id"         => $this->get("id"),
            "type"       => $this->get("type"),
            "active"     => $this->get("active"),
            "dt_start"   => $dtStart,
            "dt_expiry"  => $dtExpiry,
            "access_min" => $this->get("access_min"),
            "access_max" => $this->get("access_max")
        ]);

    }


    /**
     * Checks whether the current instance is a valid permit (e.g. shown content)
     *
     * @return  boolean                     True if valid, false otherwise
     */
    public function isValid() {

        if ($this->get("active")) {

            // Check permit start / expiry
            if (new DateTime() <= $this->get("dt_start") || new DateTime() >= $this->get("dt_expiry")) {
                return false;
            }

            // Check access rights
            if (!XCMS_Config::get("XCMS_BACKEND")) {

				$level = $currentLevel = UserHelper::getAuthorizationLevel();			
                if ($minLevel = $this->get("access_min") && $minLevel < $level) {
                    return false;
                }

                if ($maxLevel = $this->get("access_min") && $maxLevel > $level) {
                    return false;
                }

            }

            return true;

        }

        return false;

    }


    /**
     * Creates query (using CI QueryBuilder) for retrieving model content (permit)
     *
     * @param   String      type            The type for which to load the permit
     * @param   int         id              The ID for which to load the permit
     * @return  Object                      CI Database Instance for chaining purposes
     */
    protected function _buildQuery(String $type, int $id) {

        $this->db->where("type", $type);
        $this->db->where("id", $id);

        // Hook for customized filtering
        XCMS_Hooks::execute("permit.build_query", array(
            &$this, &$this->db, $id
        ));

        return $this->db;

    }

}
?>