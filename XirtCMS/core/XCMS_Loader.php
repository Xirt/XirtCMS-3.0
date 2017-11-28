<?php

require(APPPATH . "third_party/MX/Loader.php");

/**
 * XirtCMS core class extending CI Loader functionality
 *
 * @author      A.G. Gideonse
 * @version     3.0
 * @copyright   XirtCMS 2016 - 2017
 * @package     XirtCMS
 */
class XCMS_Loader extends MX_Loader {

    /**
     * CONSTRUCTOR
     * Initializes controller with models required for further initialization of CI
     */
    public function __construct() {

        parent::__construct();

        if (!class_exists("CI_Model", false)) {

            $app_path = APPPATH . "core" . DIRECTORY_SEPARATOR;

            if (file_exists($app_path . "Model.php")) {

                require_once($app_path . "Model.php");

                if (!class_exists("CI_Model", false)) {
                    throw new RuntimeException($app_path . "Model.php exists, but doesn't declare class CI_Model");
                }

            } elseif (!class_exists("CI_Model", false)) {

                require_once(BASEPATH . "core" . DIRECTORY_SEPARATOR . "Model.php");

            }

            $class = config_item("subclass_prefix") . "Model";
            if (file_exists($app_path . $class . ".php")) {

                require_once($app_path . $class . ".php");

                if (!class_exists($class, false)) {
                    throw new RuntimeException($app_path . $class . ".php exists, but doesn't declare class " . $class);
                }

            }

        }

    }


    /**
     * Loads and optionally provides instantiated model
     *
     * @param   String      $model          The name of the model to load
     * @param   String      $name           An optional object name to assign to or false to trigger loading only
     * @param   boolean     $db_conn        An optional database connection configuration to initialize
     * @return  Object
     */
    public function model($model, $name = "", $db_conn = false) {

        if ($name === false) {

            do {
                $name = "X_" . rand();
            } while (isset(get_instance()->$name));

            // Load model and remove its instance
            parent::model($model, $name, $db_conn);
            unset(get_instance()->$name);

            return get_instance()->load;

        }

        return parent::model($model, $name, $db_conn);

    }



    /**
     * CI Object to Array translator: required for backwards compatibility CI 3.1.3+ vs modular extension
     *
     * @param   object      $object         Object data to translate
     * @return  Array                       The converted Object
     */
    protected function _ci_object_to_array($vars) {
        return $this->_ci_prepare_view_vars($vars);
    }


    /**
     * Loads requested XirtCMS Template
     *
     * @param   String      $folder         XirtCMS Template folder
     * @param   boolean     $return         Toggles capturing of the output
     * @return  mixed                       Loader object or output if requested
     */
    public function template($folder, $return = false) {

        return $this->_ci_load(array(
            "_ci_path" => APPPATH . "templates/" . $folder . "/index.php",
            "_ci_return" => $return
        ));

    }

}
?>