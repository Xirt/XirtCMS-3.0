<?php

/**
 * Base model for holding XirtCMS attributes (generic usage)
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class AttributesModel extends CI_Model {

    /**
     * Internal reference (ID) to the related model
     * @var int
     */
    private $_ref = -1;


    /**
     * Internal reference to the source type
     * @var string|null
     */
    private $_type = null;


    /**
     * Internal reference to the source table
     * @var string|null
     */
    private $_table = null;


    /**
     * Toggles validation of attributes
     * @var boolean
     */
    private $_validations = null;


    /**
     * Internal reference to actual attributes
     * @var array
     */
    protected $_attr = array();


    /**
     * Initializes the model for use by its parent
     *
     * @param   String      $table          The DB to be used to retrieve the data
     * @param   String      $type           The model for which the attributes are to be retrieved
     * @param   boolean     $validations    Toggles validation of given values against known fields
     * @return  Object                      Always this instance
     */
    public function init($table, $modelType, $validations = true) {

        $this->_table       = $table;
        $this->_type        = $modelType;
        $this->_validations = $validations;
        $this->_attr        = $this->_getFields();

        return $this;

    }


    /**
     * Loads the attributes for given reference ID
     *
     * @param   int         id              The reference ID to check attributes for
     * @return  Object                      Always this instance
     */
    public function load($id) {

        // Reset
        $this->_list = array();

        // Populate list from database
        $query = $this->db->get_where($this->_table, array("ref_id" => $id));
        foreach ($query->result() as $row) {
            $this->set($row->name, $row->value);
        }

        $this->_ref = $id;
        return $this;

    }


    /**
     * Saves the current attributes
     *
     * @return  Object                      Always this instance
     */
    public function save() {

        // Updates all attributes in DB
        foreach ($this->_attr as $attribute) {

            $this->db->replace($this->_table, array(
                "ref_id" => $this->_ref,
                "name"   => $attribute->name,
                "value"  => $attribute->value
            ));

        }

        return $this;

    }


    /**
     * Setter for a single attribute
     *
     * @param   String      $name           The key of the attribute to be set
     * @param   String      $value          The value for the attribute to be set
     * @return  boolean                     True on success, false otherwise
     */
    public function set($name, $value) {

        foreach ($this->_attr as $attribute) {

            if ($attribute->name == $name) {
                return ($attribute->value = $value);
            }

        }

        if (!$this->_validations) {

            $this->_attr[] = (Object) [
                "name"  => $name,
                "value" => $value,
                "type"  => (strlen($value) > 150) ? "text" : "textarea"
            ];

            return true;

        }

        return false;

    }


    /**
     * Getter for single attribute
     *
     * @param   String      $key            The key of the attribute to be retrieved
     * @param   boolean     $valueOnly      Toggless returning of the value only (optional)
     * @param   mixed       $fallback       The value to return in case the attribute was not found
     * @return  mixed                       The attribute, its value or the fallback value if not found
     */
    public function get($key, $valueOnly = false, $fallback = null) {

        foreach ($this->_attr as $attr) {

            if ($attr->name == $key) {
                return $valueOnly ? $attr->value : $attr;
            }

        }

        return $fallback;

    }


    /**
     * Getter for all attributes
     *
     * @return  array                       All attributes saved for this instance
     */
    public function getArray() {
        return $this->_attr;
    }


    /**
     * Removes the attributes for given reference ID
     *
     * @param   int         $ref            The reference ID to remove attributes for
     */
    public function removeAll($ref) {

        // Updates DB (all records)
        $this->db->delete($this->_table, array(
            "ref_id" => $ref
        ));

    }


    /**
     * Returns array of all attributes
     *
     * @return  array                       List of all known attributes
     */
    private function _getFields() {

        $fields = array();
        XCMS_Hooks::execute($this->_type . ".get_fields", array(
            &$fields
        ));

        return $fields;

    }

}
?>