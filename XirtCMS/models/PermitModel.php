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
     * Setter for model attributes
     *
     * @param   mixed       $attr           The key of the attribute to be set or array/object with attributes (key/value)
     * @param   mixed       $value          The value for the attribute to be set
     * @param   boolean     $validate       Toggles validation against allowed model attributes
     * @throws  Exception                   On validation error
     * @return  Object                      Always this instance
     */
    public function set($attr, $value = null, $validate = true) {

        // Ensure Array input
        if (is_object($attr) || !is_array($attr)) {
            $attr = is_object($attr) ? (array)$attr : array($attr => $value);
        }

        $this->validateAttributes($attr);
        return parent::set($attr, $value, $validate);

    }


    /**
     * Validates the internal integrity of the model
     *
     * @param   mixed       $attr           Array with attributes to check (key/value)
     * @throws  Exception                   On validation error
     * @return  boolean                     Always true
     */
    public function validateAttributes($attr) {

        foreach ($attr as $key => $value) {

            // VALIDATION :: DateTime
            if (in_array($key, array("dt_start", "dt_expiry")) && !is_a($value, "DateTime")) {

                // Convert to DateTime if possible
                if ($value = DateTime::createFromFormat("Y-m-d G:i:s", $value)) {
                    break;
                }

                throw new InvalidArgumentException("Value for attr '$key' must be DateTime.");

            }

            // VALIDATION :: Integers
            if (in_array($key, array("id", "access_min", "access_max", "active")) && !is_numeric($value)) {
                throw new InvalidArgumentException("Value for attr '$key' must be numeric.");
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
    public function load($type, int $id) {

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
     * @return  Object                      The attributes of the model as Object (or defaults if not present)
     */
    public function getObject() {

        return (Object) [
            "id"         => $this->get("id"),
            "type"       => $this->get("type"),
            "dt_start"   => $this->get("dt_start"),
            "dt_expiry"  => $this->get("dt_expiry"),
            "access_min" => $this->get("access_min"),
            "access_max" => $this->get("access_max")
        ];

    }


    /**
     * Creates query (using CI QueryBuilder) for retrieving model content (permit)
     *
     * @param   int         $id             The id of the permit to load
     * @return  Object                      CI Database Instance for chaining purposes
     */
    protected function _buildQuery($type, $id) {

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