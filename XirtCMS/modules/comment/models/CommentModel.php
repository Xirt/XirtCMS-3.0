<?php

/**
 * CommentModel for XirtCMS (single comment)
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class CommentModel extends XCMS_Model {

    /**
     * @var array
     * Attribute array for this model (valid attributes)
     */
    protected $_attr = array(
        "author_id", "author_name", "author_email", "parent_id", "article_id", "content"
    );


    /**
     * Loads the requested comment
     *
     * @param   int         $id             The id of the comment to load
     * @return  mixed                       This instance on success, null otherwise
     */
    public function load($id) {

        // Retrieve data from DB
        $result = $this->db->get_where(XCMS_Tables::TABLE_ARTICLES_COMMENTS, array("id" => $id));
        if ($result->num_rows()) {

            // Populate model
            $this->set($result->row());
            return $this;

        }

        return null;

    }


    /**
     * Saves the instance in the DB
     */
    public function save() {

        $this->db->replace(XCMS_Tables::TABLE_ARTICLES_COMMENTS, $this->getArray());
        $this->set("id", $this->db->insert_id());

    }


    /**
     * Removes the instance from the DB
     */
    public function remove() {

        $this->db->delete(XCMS_Tables::TABLE_ARTICLES_COMMENTS,  array(
            "id" => $this->get("id")
        ));

    }

}
?>