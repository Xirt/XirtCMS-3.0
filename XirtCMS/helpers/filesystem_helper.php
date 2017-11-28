<?php

$this->load->helper("file");


/**
 * Delete Files
 *
 * Deletes all files contained in the supplied directory path.
 * Files must be writable or owned by the system in order to be deleted.
 * If the second parameter is set to TRUE, any directories contained
 * within the supplied base directory will be nuked as well.
 *
 * @param   string          $path		The path to the file to be removed
 * @param   bool            $htdocs		Whether to skip deleting .htaccess, .gitignore and index page files
 * @return  boolean
 */
function delete_file($path, $htdocs = false) {

    $path = rtrim($path, '/\\');    
    if (!file_exists($path)) {
        return false;
    }

    // A.G. Gideonse: Added exclude for git files (27/NOV/2017)
    if ($htdocs !== true || !preg_match('/^(\.gitignore|\.htaccess|index\.(html|htm|php)|web\.config)$/i', $path)) {
        return @unlink($path);
    }

    return false;
}


function getDirectorySize($path) {

    $bytestotal = 0;

    $path = realpath($path);
    if ($path!==false && $path!="" && file_exists($path)){

        foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS)) as $object){
            $bytestotal += $object->getSize();
        }

    }

    return $bytestotal;

}


function formatBytes($bytes, $precision = 2){

    $units = array("B", "KB", "MB", "GB", "TB");

    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);

    return round($bytes, $precision) . " " . $units[$pow];
}
?>