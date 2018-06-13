<?php

/**
 * XirtCMS core class extending CI Model functionality
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2018
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
     * Constructs new instance of model
     */
    public function __construct() {

	    log_message("info", "Model Class Initialized: " . get_class($this));

	}


    /**
     * (Re-)initializes the model with the given attributes
     *
     * @param   array       $attr           The attributes for initialization (optional)
     * @return  Object                      Always this instance
     */
    public function reset(array $attr = array()) {

        $this->_data = array();
        return $this->set($attr);

    }


    /**
     * Setter for model attributes
     *
     * @param   mixed       $attr           The key of the attribute to be set or array/object with attributes (key/value)
     * @param   mixed       $value          The value for the attribute to be set
     * @param   boolean     $validate       Toggles validation against allowed model attributes
     * @return  Object                      Always this instance
     */
    public function set($attr, $value = null, bool $validate = true) {

        // Ensure Array input
        if (is_object($attr) || !is_array($attr)) {
            $attr = is_object($attr) ? (array)$attr : array($attr => $value);
        }

        // Enrich instance with values
        foreach ($attr as $key => $value) {

            // Skip invalid attributes
            if ($validate && array_search($key, $this->_attr) === false) {

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
    public function get(String $key, $fallback = null) {

        // Check  normal attributes
        if (array_key_exists($key, $this->_data)) {
            return $this->_data[$key];
        }

        return $fallback;

    }


    /**
     * Getter for all model attributes as array
     *
     * @param   boolean     $validate       Toggles validation against allowed model attributes
     * @return  array                       The attributes of the model as Array
     */
    public function getArray(bool $validate = false) {

        if (($list = $this->_data) && $validate) {

            foreach ($this->_data as $key => $value) {

                if (!array_search($key, $this->_attr)) {
                    unset($list[$key]);
                }

            }

        }

        return $list;

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
     * Returns the internal data for DB insertion
     *
     * @return  Array                       The attributes of the model ready for DB insertion (e.g. parsed to rigth format)
     */
    protected function _getPreparedData() {

        $result = array();
        foreach ($this->_data as $attr => $value) {

            if (is_a($value, "DateTime")) {
                $value = $value->format("Y-m-d H:i:s");
            }

            $result[$attr] = $value;

        }

        return $result;

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