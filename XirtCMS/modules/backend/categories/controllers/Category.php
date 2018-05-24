<?php
defined("BASEPATH") OR exit("No direct script access allowed");

/**
 * Controller for maintaining single Category
 *
 * @author		A.G. Gideonse
 * @version		3.0
 * @copyright	XirtCMS 2016 - 2017
 * @package		XirtCMS
 */
class Category extends XCMS_Controller {

	/**
	 * CONSTRUCTOR
	 * Instantiates controller with required helpers, libraries and models
	 */
	public function __construct() {

		parent::__construct(75, true);

		// Only allow AJAX requests
		if (!$this->input->is_ajax_request()) {
			return show_404();
		}

		// Load helpers
		$this->load->helper("category");

		// Load libraries
		$this->load->library("form_validation");

		// Load models
		$this->load->model("CategoryModel", "category");

	}


	/**
	 * "View category"-functionality for this controller
	 *
	 * @param	$id			The name of the requested category
	 */
	public function view($id = -1) {

		// Validate given ID
		if (!is_numeric($id) || !$this->category->load($id)) {
			return;
		}

		// Prepare data...
		$data = (object) [
			"id"		=> $this->category->get("id"),
			"name"		=> $this->category->get("name"),
			"category"	=> $this->category->get("category"),
			"parent_id"	=> $this->category->get("parent_id")
		];

		// ...and output it as JSON
		$this->output->set_content_type("application/json");
		$this->output->set_output(json_encode($data));

	}


	/**
	 * "Create category"-functionality for this controller
	 */
	public function create() {

		// Validate provided input
		if (!$this->form_validation->run()) {

			XCMS_JSON::validationFailureMessage();
			return;

		}

		// Validate new parent
		$tree = CategoryHelper::getCategoryTree();
		if (!$parent = $tree->getItemByID($this->input->post("category_parent_id"))) {

			XCMS_JSON::validationFailureMessage("The selected parent item could not be found.");
			return;

		}

		try {

			// Set item values, validate and save
			$this->category->set("parent_id",	$this->input->post("category_parent_id"));
			$this->category->set("name",		$this->input->post("category_name"));
			$this->category->set("ordering",	$parent->count() + 1);
			$this->category->set("level",		$parent->level + 1);
			$this->category->validate();
			$this->category->save();

			// Inform user
			XCMS_JSON::creationSuccessMessage();

		} catch (Exception $e) {

			XCMS_JSON::validationFailureMessage($e->getMessage());

		}

	}


	/**
	 * "Modify category"-functionality for this controller
	 */
	public function modify() {

		// Validate given category ID
		$id = $this->input->post("id");
		if (!is_numeric($id) || !$this->category->load($id)) {

			XCMS_JSON::validationFailureMessage();
			return;

		}

		// Validate provided input
		if (!$this->form_validation->run()) {

			XCMS_JSON::validationFailureMessage();
			return;

		}

		try {

			// Check parent ID changes
			if ($this->input->post("category_parent_id") != $this->category->get("parent_id")) {

				// Validate new parent existence
				$tree = CategoryHelper::getCategoryTree();
				if (!$parent = $tree->getItemByID($this->input->post("category_parent_id"))) {

					XCMS_JSON::validationFailureMessage("The selected parent item could not be found.");
					return;

				}

				// Attempt to update parent item
				if (!$this->_modifyParent($tree->getItemById($id), $parent)) {

					XCMS_JSON::modificationFailureMessage("The item cannot be a descendant of itself.");
					return;

				}

			}

			// Set item values, validate and save
			$this->category->set("name", $this->input->post("category_name"));
			$this->category->validate();
			$this->category->save();

			// Inform user
			XCMS_JSON::modificationSuccessMessage();

		} catch (Exception $e) {

			XCMS_JSON::validationFailureMessage($e->getMessage());

		}

	}


	/**
	 * "Toggle published"-functionality for this controller
	 *
	 * @param	$id			The ID of the affected category
	 */
	public function toggle_published($id) {

		// Validate given item ID
		if (!is_numeric($id) || !$this->category->load($id)) {
			return;
		}

		$this->category->set("published", $this->category->get("published") ? "0" : "1");
		$this->category->validate();
		$this->category->save();

	}


	/**
	 * "Change ordering (move up)"-functionality for this controller
	 *
	 * @param	$id			The ID of the affected category
	 */
	public function move_up($id) {
		$this->_changeOrdering($id, 1);
	}


	/**
	 * "Change ordering (move up)"-functionality for this controller
	 *
	 * @param	$id			The ID of the affected category
	 */
	public function move_down($id) {
		$this->_changeOrdering($id, -1);
	}


	/**
	 * "Remove category"-functionality for this controller
	 *
	 * @param	$id			The ID of the affected category
	 */
	public function remove($id = -1) {

		// Validate given item ID
		if (!is_numeric($id) || !$this->category->load($id)) {
			return;
		}

		// Remove item & descendants
		$tree = CategoryHelper::getCategoryTree();
		if ($item = $tree->getItemByID($id)) {

			// Remove item
			$this->category->remove();
			CategoryHelper::updateOrdering($this->_getSubsequentSiblings($item), -1);

			// Remove children
			foreach ($item->toArray() as $node) {

				$this->category->load($node->node_id);
				$this->category->remove();

			}

		}

	}


	/**
	 * Internal logic for assigning a new parent to given model
	 *
	 * @param	$item		The item for which the parent will be updated
	 * @param	$parent		The new parent of the item
	 * @return	boolean		True on success, false on failure (tree structure issue)
	 */
	private function _modifyParent($item, $parent) {

		// Check tree structure
		if ($item->getItemByID($parent->node_id)) {
			return false;
		}

		// Determine descendants
		$descendants = array();
		foreach ($item->toArray() as $node) {
			$descendants[] = $node->node_id;
		}

		// Retain tree integrity
		CategoryHelper::updateOrdering($this->_getSubsequentSiblings($item), -1);
		CategoryHelper::updateLevels($descendants, $parent->level + 1 - $this->category->get("level"));

		// Finally, update item
		$this->category->set("ordering",  $parent->count() + 1);
		$this->category->set("level",	  $parent->level + 1);
		$this->category->set("parent_id", $parent->node_id);
		$this->category->validate();
		$this->category->save();

		return true;

	}


	/**
	 * "Change ordering"-functionality for this controller
	 *
	 * @param	$id			The ID of the affected category
	 * @param	$delta		The direction in which to change the ordering (e.g. -1 is down / 1 is up)
	 */
	private function _changeOrdering($id, int $delta) {

		// Validate given item ID
		if (!is_numeric($id) || !$this->category->load($id)) {
			return;
		}

		// Check for direct sibling
		if (($sibling = CategoryHelper::getSibling($this->category, -$delta)) === null) {
			return;
		}

		// Update current item
		$this->category->set("ordering", $this->category->get("ordering") - $delta);
		$this->category->validate();
		$this->category->save();

		// Update related sibling
		$sibling->set("ordering", $sibling->get("ordering") + $delta);
		$sibling->validate();
		$sibling->save();

	}


	/**
	 * Retrieves the node IDs of all subsequent siblings of given item
	 *
	 * @param	$item		The item to analyze
	 * @return	array		Array containing node IDs found
	 */
	private function _getSubsequentSiblings($item) {

		$relatives = array();

		$targetFound = false;
		foreach ($item->parent->children as $child) {

			if ($child->node_id == $item->node_id) {
				$targetFound = true;
			} elseif ($targetFound) {
				$relatives[] = $child->node_id;
			}

		}

		return $relatives;

	}

}
?>