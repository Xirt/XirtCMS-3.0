<?php

/**
 * XirtCMS core class extending CI Model functionality
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class XCMS_Model extends CI_Model {

    /**
     * @var array
     * Data array for this model (actual content)
     */
    protected $_data = array();


    /**
     * @var array
     * Attribute array for this model (valid attributes)
     */
    protected $_attr = array();


    /**
     * (Re-)initializes the model with the given attributes
     *
     * @param   array       $attr           The attributes for initialization (optional)
     * @return  Object                      Always this instance
     */
    public function reset($attr = array()) {

        $this->_data = array();
        return $this->set($attr);

    }


    /**
     * Setter for model attributes
     *
     * @param   mixed       $attr           The key of the attribute to be set or array/object with attributes (key/value)
     * @param   mixed       $value          The value for the attribute to be set
     * @return  Object                      Always this instance
     */
    public function set($attr, $value = null) {

        // Ensure Array input
        if (is_object($attr) || !is_array($attr)) {
            $attr = is_object($attr) ? (array)$attr : array($attr => $value);
        }

        // Enrich instance with values
        foreach ($attr as $key => $value) {

            // Skip invalid attributes
            if (array_search($key, $this->_attr) === false) {

                log_message("debug", "[Model] Attempt to set invalid attribute '{$key}'.");
                continue;

            }

            $this->_data[$key] = $value;

        }

        return $this;

    }


    /**
     * Getter for single model attribute
     *
     * @param   String      $key            The key of the attribute value to be retrieved
     * @param   mixed       $fallback       The value to return in case the value was not found
     * @return  mixed                       The attribute, its value or the fallback value if not found
     */
    public function get($key, $fallback = null) {

        // Check  normal attributes
        if (array_key_exists($key, $this->_data)) {
            return $this->_data[$key];
        }

        return $fallback;

    }


    /**
     * Getter for all model attributes as array
     *
     * @return  array                       The attributes of the model as Array
     */
    public function getArray() {
        return $this->_data;
    }


    /**
     * Getter for all model attributes as Object
     *
     * @return  Object                      The attributes of the model as Object
     */
    public function getObject() {
        return (object)$this->_data;
    }


    /**
     * Validates the internal integrity of the model
     *
     * @return  boolean                     Always true (to be overridden)
     */
    public function validate() {
        return true;
    }

}
?>