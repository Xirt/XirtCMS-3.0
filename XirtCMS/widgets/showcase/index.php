<?php

/**
 * XirtCMS Widget for showing a grid of (thumbnail) images
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class xwidget_showcase extends XCMS_Widget {

    /**
     * Shows the content
     */
    public function show() {

        // Prepare variables
        $this->setConfig("ext", explode(",", $this->config("ext")));

        // Validate configuration for thumbnail size
        $size = round($this->config("thumb_size", XCMS_CONFIG::get("THUMBS_DIMS")));
        $this->setConfig("thumb_size", $size ? $size : 150);

        // Show template
        $this->view("template.tpl", array(
            "images" => $this->_getList(),
            "config" => $this->getConfig(),
            "id"     => rand()
        ));

    }


    /**
     * Returns a list with all thumbnail / original combinations
     *
     * @return  array                       List with key/value paris of thumbnails/originals
     */
    private function _getList() {

        $list = array();
        foreach ($this->_getImages() as $file) {
            $list[$this->_getThumbnail($this->config("path"), $file)] = $file;
        }

        return $list;

    }


    /**
     * Returns all images for this instance
     *
     * @return  array                       List containing names of the requested images
     */
    private function _getImages() {

        $list = array();
        $path = $this->config("folder");

        if ($dh = opendir($path)) {

            while (false !== ($file = readdir($dh))) {

                if ($this->_checkValidImage($path, $file)) {
                    $list[] = $file;
                }

            }

            @closedir($dh);

        }

        // Limited lists must be shuffled
        if (count($list) > $this->config("amount") && shuffle($list)) {
            return array_slice($list, 0, $this->config("amount"));
        }

        // Otherwise return sorted
        sort($list);
        return $list;

    }


    /**
     * Checks if the given path / file combination is valid for the current instance
     *
     * @param   String $path                The path to the image folder
     * @param   String $file                The filename of the file to check
     * @return  boolean                     True if the given file is valid for the current instance, false otherwise
     */
    private function _checkValidImage($path, $file) {

        // Skip system paths
        if (in_array($file, array(".", ".."))) {
            return false;
        }

        // Check for required thumbnail prefix
        if ($this->config("prefix_thumb") && strpos($file, $this->config("prefix_thumb")) === 0) {
            return false;
        }

        // Check for required original prefix
        if ($this->config("prefix_ori") && strpos($file, $this->config("prefix_ori")) !== 0) {
            return false;
        }

        // Check for required extension
        $ext = pathinfo($path . $file, PATHINFO_EXTENSION);
        if (!in_array($ext, $this->config("ext"))) {
            return false;
        }

        return true;

    }


    /**
     * Returns the thumbnail location for the given image file
     *
     * @param   String $path                The path to the image folder
     * @param   String $file                The filename of the image for which the thumbnail is requested
     * @return  String                      The path to the thumbnail
     */
    private function _getThumbnail($path, $file) {

        // Check for existing thumbnail (without prefix)
        if (file_exists($path . $name)) {
            return $path . $name;
        }

        // Check for existing thumbnail (with prefix)
        if ($this->config("prefix_thumb")) {

            $name = $this->config("prefix_thumb") . $name;
            if (file_exists($path . $name)) {
                return $path . $name;
            }

        }

        // Create new thumbnail
        return sprintf("helper/thumbnail/%d/?src=%s",
            $this->config("thumb_size"),
            urlencode($this->config("folder") . $file)
        );

    }

}
?>