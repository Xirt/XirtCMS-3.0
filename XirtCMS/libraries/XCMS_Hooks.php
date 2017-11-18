<?php

/**
 * XirtCMS hook manager (registration and execution)
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class XCMS_Hooks {

    /**
     * @var array
     * The internal list with known hooks
     */
    private static $_list = array();


    /**
     * Initializes the instance with all known hooks
     */
    public static function init() {

        log_message("info", "[XCMS] Loading all known hooks.");
        self::_load(APPPATH . "hooks/");

    }


    /**
     * Loads all hooks in internal list for usage during hook calls
     *
     * @param   String      $path          The path to the folder containing the hooks
     */
    private static function _load($path) {

        if (($content = scandir($path)) === false) {

            log_message("error", "[XCMS] Hooks directory not found.");
            return;

        }

        foreach ($content as $candidate) {

            // Skips obsolete options
            if ($candidate == "." || $candidate == "..") {
                continue;
            }

            // Skip directories
            if (is_dir($path . $candidate)) {
                continue;
            }

            require_once($path . $candidate);

        }

    }


    /**
     * Adds a new hook to the XirtCMS hooks stack
     *
     * @param   String      $id             The unique ID of this hook
     * @param   String      $funcName       The name of the function to be called
     * @param   int         $priority       The priority (ordering) of the hook
     * @param   int         $argCount       The maximum amount of arguments for this hook
     */
    public static function add($id, $funcName, $priority = 10, $argCount = -1) {

        log_message("info", "[XCMS] Registering hook with id '{$id}'.");

        self::$_list[$id][$priority][] = array(
            "valid_args" => $argCount,
            "function"   => $funcName,
            "prio"       => $priority
        );

        ksort(self::$_list[$id]);

    }


    /**
     * Removes a hook from the XirtCMS hooks stack (all parameters should match)
     *
     * @param   String      $id             The unique ID of this hook
     * @param   String      $funcName       The name of the function to be called
     * @param   int         $priority       The priority (ordering) of the hook
     */
    public static function remove($id, $funcName, $priority = 10) {

        log_message("info", "[XCMS] Unregistering hook with id '{$id}'.");

        if (isset(self::$_list[$id][$priority])) {

            foreach (self::$_list[$id][$priority] as $key => $hook) {

                if ($hook["function"] == $funcName) {
                    unset(self::$_list[$id][$priority][$key]);
                }

            }

        }

    }


    /**
     * Removes all references from the XirtCMS hooks stack for the given hook ID
     *
     * @param   String      $id             The unique ID of this hook
     */
    public static function reset($id) {

        log_message("info", "[XCMS] Unregistering all hooks with id '{$id}'.");
        unset(self::$_list[$id]);

    }


    /**
     * Attempt to execute given hook from the internal list using given arguments
     *
     * @param   String      $id             The unique ID of this hook
     * @param   array       $arg            The arguments to use for this call
     */
    public static function execute($id, $args = array()) {

        log_message("info", "[XCMS] Calling hook with id '{$id}'.");

        // Nothing to process
        if (!array_key_exists($id, self::$_list)) {
            return;
        }

        // Reset starting point
        reset(self::$_list[$id]);

        do {

            foreach (current(self::$_list[$id]) as $the_) {

                if (!is_null($the_["function"])) {

                    $args = ($args == abs($args)) ? array_slice($args, 0, (int) $the_["valid_args"]) : $args;
                    call_user_func_array($the_["function"], $args);

                }

            }

        } while (next(self::$_list[$id]) !== false);

    }


    /**
     * Count all references from the XirtCMS hooks stack for the given hook ID
     *
     * @param   String      $id             The unique ID of this hook
     * @return  int                         The amount of references for this hook
     */
    public static function count($id) {
        return count(self::$_list[$id]);
    }

}
?>