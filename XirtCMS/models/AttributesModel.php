<?php

/**
 * AttributesModel for XirtCMS (generic usage)
 *
 * @author		A.G. Gideonse
 * @version		3.0
 * @copyright	XirtCMS 2016 - 2017
 * @package		XirtCMS
 */
class AttributesModel extends CI_Model {

	/**
	 * @var	int
	 * Internal reference (ID) to the related model
	 */
	private $_ref = -1;


	/**
	 * @var string|null
	 * Internal reference to the source type
	 */
	private $_type = null;


	/**
	 * @var string|null
	 * Internal reference to the source table
	 */
	private $_table = null;


	/**
	 * @var array
	 * Internal reference to actual attributes
	 */
	protected $_attr = array();


	/**
	 * Initializes the model for use by its parent
	 *
	 * @param	String		$table		The DB to be used to retrieve the data
	 * @param	String		$type		The model for which the attributes are to be retrieved
	 * @return	Object					Always this instance
	 */
	public function init($table, $modelType) {

		$this->_table	= $table;
		$this->_type	= $modelType;
		$this->_attr 	= $this->_getFields();

		return $this;

	}


	/**
	 * Loads the attributes for given reference ID
	 *
	 * @param	int			id			The reference ID to check attributes for
	 * @return	Object					Always this instance
	 */
	public function load($id) {

		// Retrieve data from DB and populate instance
		$query = $this->db->get_where($this->_table, array("ref_id" => $id));
		foreach ($query->result_array() as $metaInfo) {

			// Check existence
			foreach ($this->_attr as $attr) {

				// Update if required
				if ($attr->name == $metaInfo["name"]) {

					$attr->value = $metaInfo["value"];
					continue;

				}

			}

		}

		$this->_ref = $id;
		return $this;

	}


	/**
	 * Saves the current attributes
	 *
	 * @return	Object					Always this instance
	 */
	public function save() {

		// Updates all attributes in DB
		foreach ($this->_attr as $cur => $attribute) {

			$this->db->replace($this->_table, array(
				"ref_id" => $this->_ref,
				"name"	 => $attribute->name,
				"value"	 => $attribute->value
			));

		}

		return $this;

	}


	/**
	 * Setter for a single attribute
	 *
	 * @param	String		$name		The key of the attribute to be set
	 * @param	String		$value		The value for the attribute to be set
	 * @return 	boolean					True on success, false otherwise
	 */
	public function set($name, $value) {

		foreach ($this->_attr as $attribute) {

			if ($attribute->name == $name) {
				return ($attribute->value = $value);
			}

		}

		return false;

	}


	/**
	 * Getter for single attribute
	 *
	 * @param	String		$key		The key of the attribute to be retrieved
	 * @param	boolean		$valueOnly	Toggless returning of the value only (optional)
	 * @param	mixed		$fallback	The value to return in case the attribute was not found
	 * @return	mixed					The attribute, its value or the fallback value if not found
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
	 * @return	array					All attributes saved for this instance
	 */
	public function getArray() {
		return $this->_attr;
	}


	/**
	 * Removes the attributes for given reference ID
	 *
	 * @param	int			$ref		The reference ID to remove attributes for
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
	 * @return	array					List of all known attributes
	 */
	private function _getFields() {

		$fields = array();
		XCMS_Hooks::execute($this->_type . ".get_fields", array(&$fields));

		return $fields;

	}

}
?>