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
     * Saves the instance in the DB
     */
    public function save() {

        $this->db->replace(Query::TABLE_ARTICLES_COMMENTS, $this->getArray());
        $this->set("id", $this->db->insert_id());

    }


    /**
     * Removes the instance from the DB
     */
    public function remove() {

        $this->db->delete(Query::TABLE_ARTICLES_COMMENTS,  array(
            "id" => $this->get("id")
        ));

    }

}
?>