<?php

/**
 * Helper object for DB queries for backend grid
 *
 * @author     A.G. Gideonse
 * @version    3.0
 * @copyright  XirtCMS 2016 - 2017
 * @package    XirtCMS
 */
class GridHelper {

    /**
     * The rows to be returned for the request
     * @var Array
     */
    private $_rows = array();


    /**
     * The total amount of results for the request
     * @var int
     */
    private $_total = 0;


    /**
     * The current page to retrieve (for pagination of Bootgrid)
     * @var int
     */
    private $_current = 1;


    /**
     * The amount of rows to retrieve (default: 9999999, hopefully all)
     * @var int
     */
    private $_rowCount = 10;


    /**
     * The filter to apply to the content (e.g. search)
     * @var String
     */
    private $_searchPhrase = "";


    /**
     * The column to be used for sorting
     * @var String
     */
    private $_sortColumn = "id";


    /**
     * The sort order to be used for sorting on $sortColumn
     * @var String
     */
    private $_sortOrder = "DESC";


    /**
     * Sets the total result parameter (e.g. total row acount) set for this request
     *
     * @param   int         $count          The new value to be set as total
     * @return  Object                      Always this instance (for chaining)
     */
    public function setTotal(int $count) {

        $this->_total = $count;
        return $this;

    }


    /**
     * Adds a row to the result parameters
     *
     * @param   mixed       $row            The row to be added to the result
     * @return  Object                      Always this instance (for chaining)
     */
    public function addRow($row) {

        $this->_rows[] = $row;
        return $this;

    }


    /**
     * Retrieves and validates relevant Grid parameters from request
     *
     * @param   Object      $in             The CodeIgniter input object
     * @return  Object                      Always this instance (for chaining)
     */
    public function parseRequest($in) {

        // Retrieval (item filtering)
        $this->_searchPhrase = trim($in->post("searchPhrase"));

        // Retrieval & validation (current page)
        if (!($this->_current = @intval($in->post("current"))) || $this->_current < 1) {
            $this->_current = 1;
        }

        // Retrieval & validation (items per page)
        if (!($this->_rowCount = @intval($in->post("rowCount")))) {
            $this->_rowCount = 10;
        }

        // Retrieval & validation (ordering)
        if (!is_array($in->post("sort"))) {

            $this->_sortColumn = preg_replace('/[^A-Za-z0-9_]/', '', $this->_sortColumn);

        } else if (($keys = array_keys($in->post("sort"))) && array_key_exists(0, $keys)) {

            $this->_sortOrder  = ($in->post("sort")[$keys[0]] == "desc") ? "DESC" : "ASC";
            $this->_sortColumn = preg_replace('/[^A-Za-z0-9_]/', '', $keys[0]);

        }

        return $this;

    }

    /*
     * Retrieves and validates relevant Grid parameters from request
     *
     * @param   Object      $out            The CodeIgniter out object
     * @return  Object                      Always this instance (for chaining)
     */
    public function generateResponse($out) {

        $out->set_content_type("application/json");
        $out->set_output(json_encode($this->getResponse()));

        return $this;

    }

    /**
     * Returns Array with request parameters
     *
     * @param   Array                       Array with all request parameters
     */
    public function getRequest() {

        return array(
            "current"      => $this->_current,
            "rowCount"     => $this->_rowCount,
            "sortOrder"    => $this->_sortOrder,
            "sortColumn"   => $this->_sortColumn,
            "searchPhrase" => $this->_searchPhrase
        );

    }


    /**
     * Returns Array with response parameters
     *
     * @param   Array                       Array with all response parameters
     */
    function getResponse() {

        return array(
            "rows"         => $this->_rows,
            "total"        => $this->_total,
            "current"      => $this->_current,
            "rowCount"     => $this->_rowCount,
            "sortOrder"    => $this->_sortOrder,
            "sortColumn"   => $this->_sortColumn,
            "searchPhrase" => $this->_searchPhrase
        );

    }

}
?>